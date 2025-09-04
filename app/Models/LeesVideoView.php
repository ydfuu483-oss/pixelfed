<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class LeesVideoView extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'user_id',
        'ip_address',
        'user_agent'
    ];

    public function video()
    {
        return $this->belongsTo(LeesVideo::class, 'video_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}