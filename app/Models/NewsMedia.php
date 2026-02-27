<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsMedia extends Model
{
    // this table is for user uploded images/ videos
    protected $table = 'news_media';

    protected $fillable = [
        'news_id',
        'file_path',
        'file_type',
        'is_primary'
    ];

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
