<?php

namespace Plugins\SalesOrder\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $table    = 'sales_orders';
    protected $fillable = ['name'];
}