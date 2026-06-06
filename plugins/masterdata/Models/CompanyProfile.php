<?php

namespace Plugins\masterdata\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $fillable = [
        'name', 'legal_name', 'tax_number',
        'address', 'city', 'country',
        'phone', 'email', 'website',
    ];

    public static function instance(): self
    {
        return static::firstOrCreate(['id' => 1], ['name' => config('app.name', 'ERP System')]);
    }
}
