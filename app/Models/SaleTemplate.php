<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleTemplate extends Model
{
    protected $fillable = [
        'name',
        'cart_items',
        'form_state',
    ];
}
