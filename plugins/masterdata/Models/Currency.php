<?php

namespace Plugins\masterdata\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['code', 'name', 'symbol', 'exchange_rate', 'is_default'];

    protected $casts = ['exchange_rate' => 'decimal:6', 'is_default' => 'boolean'];
}
