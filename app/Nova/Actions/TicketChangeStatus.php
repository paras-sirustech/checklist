<?php

namespace App\Nova\Actions;

use App\Models\User;
use App\Notifications\TicketResolved;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Textarea;
use Notification;

class TicketChangeStatus extends Action
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $showOnTableRow = true;

    public $name = 'Change Status';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            $model->status = $fields->status;
            if ($fields->status=='Resolved') {
                $model->resolved_date = $fields->resolved_date;

                // Notify the user who created the ticket
                $creator = User::find($model->created_by);
                if ($creator) {
                    $to_email[] = $creator->email;
                    //$creator->notify(new TicketResolved($model));
                }
                foreach ($model->escalations as $escalation) {
                    $to_email[] = $escalation->email;
                }

                if (count($to_email)>0) {
                    Notification::route('mail', $to_email)->notify(new TicketResolved($model, $model->shop));
                }
            } else {
                $model->resolved_date = null;
            }
            if ($fields->comments!='') {
                $model->replies()->create(['description'=>$fields->comments]);
            }
            $model->save();
        }

        return Action::message('Status was updated successfully');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        // Shop staff = open, closed
        // support staff = In progress, closed
    
        $options = [
            'Open' => 'Open',
            'Closed' => 'Closed',
        ];

        if (auth()->user()->access_level=='Admin') {
            $options += [
                'In Progress' => 'In Progress',
                'Resolved' => 'Resolved',
            ];
        }

        if (auth()->user()->access_level=='Support Staff') {
            $options = [
                'In Progress' => 'In Progress',
                'Resolved' => 'Resolved',
            ];
        }

        return [
            Select::make('Status')->options($options)->rules('required')->sortable(),
            Date::make('Date Resolved', 'resolved_date')->rules('nullable', 'required_if:status,Resolved', 'date'),
            Textarea::make('Comments', 'comments')->rules('nullable', 'required_if:status,Resolved'),
        ];
    }
}
