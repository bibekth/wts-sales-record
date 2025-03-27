<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'services',
        'paid_amount',
        'due_amount',
        'sales_date',
        'remarks',
        'file',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
