<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsOutput extends Model
{
    protected $table = 'news_outputs';

    protected $fillable = [
        'news_id',
        'output_type',
        'file_path'
    ];

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
