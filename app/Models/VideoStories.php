<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoStories extends Model
{
    protected $fillable = ['title', 'description', 'youtube_url'];

    public function getYoutubeIdAttribute()
    {
        preg_match(
            '/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/))([\w-]{11})/',
            $this->youtube_url,
            $matches
        );
        return $matches[1] ?? null;
    }
}
