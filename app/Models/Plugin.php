<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'version',
        'description',
        'author',
        'github_url',
        'installed_path',
        'is_active',
        'is_core',
        'installed_at',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'is_core'      => 'boolean',
        'installed_at' => 'datetime',
    ];
}