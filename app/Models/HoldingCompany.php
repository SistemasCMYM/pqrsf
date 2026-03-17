<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoldingCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'intro',
        'logo_path',
        'animation_path',
        'support_booking_url',
        'is_default',
        'active',
        'theme',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'active' => 'boolean',
            'theme' => 'array',
        ];
    }
}
