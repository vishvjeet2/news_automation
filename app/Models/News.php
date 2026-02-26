<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'category_id',
        'template_id',
        'description',
        'heading',
        'news_date',
        'place',
        'news_type',
        'status',
        'audio_path'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function media()
    {
        return $this->hasMany(NewsMedia::class);
    }

    public function outputs()
    {
        return $this->hasMany(NewsOutput::class);
    }
}
