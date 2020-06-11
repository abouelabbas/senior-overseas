<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale_quote extends Model
{
    protected $fillable = [
        'quote_date', 'quote_code', 'client_id','ocean_air_type',
       
        'clearance_price', 'clearance_currency_id','clearance_notes',
         'door_door_price','door_door_currency_id','door_door_notes'
     
    ];
    public function client()
    {
        return $this->belongsTo('App\Models\Client','client_id');

    }
    public function clearance()
    {
        return $this->belongsTo('App\Models\Currency','clearance_currency_id');

    }
    public function door()
    {
        return $this->belongsTo('App\Models\Currency','door_door_currency_id');

    }

}