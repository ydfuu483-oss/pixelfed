<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class LeesVideoComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'user_id',
        'comment',
        'parent_id'
    ];

    public function video()
    {
        return $this->belongsTo(LeesVideo::class, 'video_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(LeesVideoComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(LeesVideoComment::class, 'parent_id');
    }
}