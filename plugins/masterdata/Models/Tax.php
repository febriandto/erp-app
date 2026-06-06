<?php

namespace Plugins\masterdata\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = ['name', 'code', 'rate', 'is_active'];

    protected $casts = ['rate' => 'decimal:2', 'is_active' => 'boolean'];
}
