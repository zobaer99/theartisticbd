<?php

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'cart',
        'currency_sign',
        'currency_value',
        'discount',
        'shipping',
        'payment_method',
        'txnid',
        'tax',
        'charge_id',
        'transaction_number',
        'order_status',
        'shipping_info',
        'billing_info',
        'payment_status',
        'state_price',
        'state',
        'courier_tracking_id',
        'courier_status'
    ];

    public function user()
    {
    	return $this->belongsTo('App\Models\User')->withDefault();
    }

    public function tracks()
    {
    	return $this->belongsTo('App\Models\TrackOrder','order_id')->withDefault();
    }

    public function tranaction()
    {
    	return $this->hasOne('App\Models\Transaction','order_id')->withDefault();
    }

    public function tracks_data()
    {
    	return $this->hasMany('App\Models\TrackOrder','order_id');
    }

    public function notificaton()
    {
    	return $this->hasMany('App\Models\Notification','order_id');
    }

}
