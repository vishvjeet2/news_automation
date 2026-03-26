<?php
// app/Models/Design.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'thumbnail',
        'design_data',
        'width',
        'height'
    ];

    protected $casts = [
        'design_data' => 'array',
    ];
}