<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class LeesVideo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'user_id',
        'video_url',
        'thumbnail_url',
        'description',
        'hashtags',
        'song',
        'likes_count',
        'comments_count',
        'shares_count',
        'views_count',
        'duration',
        'is_public',
        'is_sensitive',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'string',
        'hashtags' => 'array',
        'is_public' => 'boolean',
        'is_sensitive' => 'boolean',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
        'views_count' => 'integer',
        'duration' => 'integer'
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(LeesVideoLike::class, 'video_id');
    }

    public function comments()
    {
        return $this->hasMany(LeesVideoComment::class, 'video_id');
    }

    public function shares()
    {
        return $this->hasMany(LeesVideoShare::class, 'video_id');
    }

    public function views()
    {
        return $this->hasMany(LeesVideoView::class, 'video_id');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getHashtagsStringAttribute()
    {
        return is_array($this->hashtags) ? implode(' ', array_map(function($tag) {
            return '#' . $tag;
        }, $this->hashtags)) : '';
    }

    public function incrementLikes()
    {
        $this->increment('likes_count');
    }

    public function decrementLikes()
    {
        $this->decrement('likes_count');
    }

    public function incrementComments()
    {
        $this->increment('comments_count');
    }

    public function decrementComments()
    {
        $this->decrement('comments_count');
    }

    public function incrementShares()
    {
        $this->increment('shares_count');
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }
}