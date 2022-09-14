<?php

namespace App\Listeners;

use App\Models\ShopIpAddress;
use App\Models\UserLoginHistory;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Agent as UserAgent;

class LogFailedLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $data['type'] = 'Failed';
        $data['ip_address'] = Request::ip();
        $data['user_agent'] = Request::userAgent();
        $data['email'] = request('email');
        
        if($data['user_agent']!=''){
            $agent = new UserAgent();
            $agent->setUserAgent($data['user_agent']);
            $browser = $agent->browser();
            $platform = $agent->platform();

            $data['is_desktop'] = $agent->isDesktop();
            $data['is_phone'] = $agent->isPhone();
            $data['is_tablet'] = $agent->isTablet();
            $data['is_bot'] = $agent->isRobot();
            $data['os_name'] = $platform;
            $data['os_version'] = $agent->version($platform);
            $data['device_name'] = $agent->device();
            $data['browser_name'] = $browser;
            $data['browser_version'] = $agent->version($browser);
            if ($agent->robot()) {
                $data['bot_name'] = $agent->robot();
            }
        }
        
        if($data['ip_address']!=''){
            $shop_from_ip = ShopIpAddress::where('ip_address',$data['ip_address'])->first();
            if($shop_from_ip){
                $data['shop_id'] = $shop_from_ip->shop_id;
            }
        }

        UserLoginHistory::create($data);
    }
}
