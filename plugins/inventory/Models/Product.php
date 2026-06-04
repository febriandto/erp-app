<?php

namespace Plugins\inventory\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'stock', 'price', 'description'];
}