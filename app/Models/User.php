<?php

namespace App\Models;

use App\Models\ShopIpAddress;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Agent\Agent as UserAgent;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'access_level', 'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function shops()
    {
        return $this->belongsToMany(Shop::class);
    }

    public function daily_checks()
    {
        return $this->hasMany(DailyCheck::class, 'submitted_by');
    }

    public function cluster()
    {
        return $this->hasOne(Cluster::class, 'assigned_to', 'id');
    }

    public function login_history()
    {
        return $this->hasMany(UserLoginHistory::class, 'email', 'email')->orderBy('id', 'DESC');
    }

    public function cluster_shop_ids()
    {
        $shops = [];
        $cluster = $this->cluster;
        if ($cluster) {
            foreach ($cluster->shops as $shop) {
                $shops[] = $shop->id;
            }
        }
        return $shops;
    }

    public function create_login_history($type, $ip_address, $user_agent)
    {
        $data['type'] = $type;
        $data['ip_address'] = $ip_address;
        $data['user_agent'] = $user_agent;

        if ($data['user_agent']!='') {
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

        if ($data['ip_address']!='') {
            $shop_from_ip = ShopIpAddress::where('ip_address', $data['ip_address'])->first();
            if ($shop_from_ip) {
                $data['shop_id'] = $shop_from_ip->shop_id;
            }
        }

        $this->login_history()->create($data);
    }

    public function leave()
    {
        return $this->hasMany(Leave::class,'user_id','id');
    }
}
