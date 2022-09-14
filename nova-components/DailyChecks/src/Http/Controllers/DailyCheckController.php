<?php

namespace Ooredoo\DailyChecks\Http\Controllers;

use App\Models\DailyCheck;
use App\Models\DailyCheckItem;
use App\Notifications\TicketAssigned;
use App\Models\Shop;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Storage;

class DailyCheckController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        if ($user->access_level=='Admin') {
            $shops = Shop::get();
        } else {
            $shops = Shop::whereHas('ip_addresses', function ($query) {
                $query->where('ip_address', request()->ip());
            })->get();
        }

        $data = [];
        foreach ($shops as $shop) {
            $data[] = ['value'=> $shop->id, 'label' => $shop->name];
        }
        
        return $data;
    }

    public function shopDetail($id)
    {
        $shop = Shop::with('checklist.items')->find($id);
        if (!$shop) {
            abort(404);
        }

        return $shop;
    }

    public function startDailyCheck(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        $this->validate(request(), [
            'shop' => 'required',
            'date' => 'date|required',
        ]);

        if ($user->access_level=='Admin') {
            $shop = Shop::with('checklist.items')->where('id', request('shop'))->first();
        } else {
            $shop = Shop::with('checklist.items')->where('id', request('shop'))->whereHas('ip_addresses', function ($query) {
                $query->where('ip_address', request()->ip());
            })->first();
        }

        if (!$shop) {
            return json_response(['type' => 'error', 'message' => 'Invalid shop or you are not authorised to access this shop.'], 412);
        }

        $daily_check = DailyCheck::where('shop_id', $shop->id)->whereDate('checking_date', request('date'))->where('is_submission_complete', 1)->first();
        if ($daily_check) {
            $redirect_to = config('app.url') . '/app/resources/daily-checks/' . $daily_check->id;
            return json_response(['type' => 'warning', 'message' => 'Daily check already filed for the selected date. Redirecting..', 'redirect' => $redirect_to]);
        }

        return json_response(['type' => 'success', 'message' => 'Please use this form to submit daily checklist', 'shop' => $shop]);
    }

    public function saveDailyCheckItem(Request $request)
    {
        info('Save daily check item - ' . json_encode($request->all()));
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        $daily_check = DailyCheck::firstOrCreate([
            'shop_id'=>request('shop'),
            'checking_date' => request('date'),
        ]);

        $daily_check_item = DailyCheckItem::firstOrCreate([
            'daily_check_id'=>  $daily_check->id,
            'checklist_item_id' => request('checklist_item_id'),
        ]);

        $daily_check_item->status = request('status');
        $daily_check_item->checklist_item_name = request('checklist_item_name');
        $daily_check_item->save();


        return $daily_check_item;
    }

    public function createDailyCheckIssue(Request $request)
    {
        info('Create daily check issue - ' . json_encode($request->all()));
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        $shop = Shop::with('checklist.items')->where('id', request('shop'))->first();

        $daily_check_item = $this->saveDailyCheckItem($request);

        $support_ticket = SupportTicket::firstOrCreate([
            'status'=>'Open',
            'created_by' => $user->id,
            'daily_check_id' => $daily_check_item->daily_check_id,
            'daily_check_item_id' => $daily_check_item->id,
            'checklist_item_id' => $daily_check_item->checklist_item_id,
            'shop_id' => $daily_check_item->daily_check->shop_id,
            'checking_date' => $daily_check_item->daily_check->checking_date,
        ]);

        $support_ticket->subject = 'Daily Check - ' . $daily_check_item->daily_check->checking_date->toFormattedDateString() . ' - ' . $daily_check_item->checklist_item_name;
        $support_ticket->description = request('description');

        $file = $request->file('file');
        if ($request->hasFile('file')) {
            $file_name = Str::slug($support_ticket->subject, '-') . '-attachment-' . time() . '.' . $file->getClientOriginalExtension();
            if (Storage::disk('public')->put($file_name, file_get_contents($file))) {
                $support_ticket->attachment = $file_name;
            }
        }

        $support_ticket->save();
        $daily_check_item->support_ticket_id = $support_ticket->id;

        $daily_check_item->save();

        if ($shop) {
            $support_ticket->assigned_to = $shop->assigned_to;
            $support_ticket->save();
        }

        return json_response(['type' => 'success', 'message' => 'Support ticket raised for Checklist Item: ' . $daily_check_item->checklist_item_name, 'support_ticket' => $support_ticket]);
    }

    public function fileDailyCheck(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        $this->validate(request(), [
            'shop' => 'required',
            'date' => 'required|before_or_equal:today',
        ]);

        if ($user->access_level=='Admin') {
            $shop = Shop::with('checklist.items')->where('id', request('shop'))->first();
        } else {
            $shop = Shop::with('checklist.items')->where('id', request('shop'))->whereHas('ip_addresses', function ($query) {
                $query->where('ip_address', request()->ip());
            })->first();
        }

        if (!$shop) {
            return json_response(['type' => 'error', 'message' => 'Invalid shop or you are not authorised to access this shop.'], 412);
        }

        $daily_check = DailyCheck::where('shop_id', $shop->id)->whereDate('checking_date', request('date'))->first();
        if ($daily_check->is_submission_complete===true) {
            return json_response(['type' => 'error', 'message' => 'Daily check already filed for the selected date'], 412);
        }

        if ($daily_check->items->count() < $shop->checklist->items->count()) {
            return json_response(['type' => 'error', 'message' => 'Please mark all items before filing the daily check'], 412);
        }

        $daily_check->is_submission_complete = 1;
        $daily_check->completed_at = now();
        $daily_check->submitted_by = $user->id;
        $daily_check->save();

        $redirect_to = config('app.url') . '/app/resources/daily-checks/' . $daily_check->id;

        return json_response(['type' => 'success', 'message' => 'Successfully filed. Redirecting..', 'redirect' => $redirect_to]);
    }
}
