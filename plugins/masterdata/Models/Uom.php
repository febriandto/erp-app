<?php

namespace Plugins\masterdata\Models;

use Illuminate\Database\Eloquent\Model;

class Uom extends Model
{
    protected $fillable = ['name', 'symbol', 'category'];

    public static array $categories = [
        'piece'  => 'Piece / Count',
        'weight' => 'Weight',
        'volume' => 'Volume',
        'length' => 'Length',
        'area'   => 'Area',
        'time'   => 'Time',
        'other'  => 'Other',
    ];
}
