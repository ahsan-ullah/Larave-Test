<?php

namespace App\Models;

use App\Models\PostLike;
use App\Models\PostUnlike;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'title',
        'description',
        'image',
        'total_like',
        'total_unlike',
        'created_at'
    ];

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function unlikes()
    {
        return $this->hasMany(PostUnlike::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
