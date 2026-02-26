<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'name',
        'type',
        'template_path',
        'config'
    ];

    public function news()
    {
        return $this->hasMany(News::class);
    }
}
