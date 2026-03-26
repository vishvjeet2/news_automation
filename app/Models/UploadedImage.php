<?php
// app/Models/UploadedImage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'path',
        'mime_type',
        'size'
    ];
}