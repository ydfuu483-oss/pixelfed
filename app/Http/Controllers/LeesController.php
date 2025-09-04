<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\LeesVideo;
use App\Models\LeesVideoLike;
use App\Models\LeesVideoComment;
use App\Models\LeesVideoShare;
use App\Models\LeesVideoView;
use App\User;

class LeesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the Lees feed
     */
    public function index()
    {
        return view('lees.index');
    }

    /**
     * Show user's Lees videos
     */
    public function userVideos($userId)
    {
        $user = User::findOrFail($userId);
        $videos = LeesVideo::byUser($userId)
            ->public()
            ->with(['user', 'likes', 'comments'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'avatar' => $user->avatar
            ],
            'videos' => $videos->items(),
            'pagination' => [
                'current_page' => $videos->currentPage(),
                'last_page' => $videos->lastPage(),
                'per_page' => $videos->perPage(),
                'total' => $videos->total()
            ]
        ]);
    }

    /**
     * Upload a new video
     */
    public function uploadVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video' => 'required|file|mimes:mp4,mov,avi|max:102400', // 100MB max
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // 10MB max
            'description' => 'nullable|string|max:500',
            'hashtags' => 'nullable|array|max:10',
            'hashtags.*' => 'string|max:50',
            'song' => 'nullable|string|max:100',
            'is_public' => 'boolean',
            'is_sensitive' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = Auth::user();
            
            // Upload video to S3
            $videoFile = $request->file('video');
            $videoPath = 'lees/videos/' . $user->id . '/' . uniqid() . '.' . $videoFile->getClientOriginalExtension();
            $videoUrl = Storage::disk('s3')->putFileAs('', $videoFile, $videoPath, 'public');
            
            // Upload thumbnail if provided
            $thumbnailUrl = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailFile = $request->file('thumbnail');
                $thumbnailPath = 'lees/thumbnails/' . $user->id . '/' . uniqid() . '.' . $thumbnailFile->getClientOriginalExtension();
                $thumbnailUrl = Storage::disk('s3')->putFileAs('', $thumbnailFile, $thumbnailPath, 'public');
            }

            // Create video record
            $video = LeesVideo::create([
                'user_id' => $user->id,
                'video_url' => Storage::disk('s3')->url($videoUrl),
                'thumbnail_url' => $thumbnailUrl ? Storage::disk('s3')->url($thumbnailUrl) : null,
                'description' => $request->input('description'),
                'hashtags' => $request->input('hashtags', []),
                'song' => $request->input('song'),
                'is_public' => $request->input('is_public', true),
                'is_sensitive' => $request->input('is_sensitive', false),
                'duration' => $this->getVideoDuration($videoFile) // You'll need to implement this
            ]);

            return response()->json([
                'message' => 'Video uploaded successfully',
                'video' => $video->load('user')
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to upload video'], 500);
        }
    }

    /**
     * Like a video
     */
    public function likeVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|exists:lees_videos,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $videoId = $request->input('video_id');
        
        $existingLike = LeesVideoLike::where('video_id', $videoId)
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            LeesVideo::find($videoId)->decrementLikes();
            $liked = false;
        } else {
            LeesVideoLike::create([
                'video_id' => $videoId,
                'user_id' => $user->id
            ]);
            LeesVideo::find($videoId)->incrementLikes();
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => LeesVideo::find($videoId)->likes_count
        ]);
    }

    /**
     * Comment on a video
     */
    public function commentVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|exists:lees_videos,id',
            'comment' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:lees_video_comments,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        
        $comment = LeesVideoComment::create([
            'video_id' => $request->input('video_id'),
            'user_id' => $user->id,
            'comment' => $request->input('comment'),
            'parent_id' => $request->input('parent_id')
        ]);

        LeesVideo::find($request->input('video_id'))->incrementComments();

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load('user')
        ], 201);
    }

    /**
     * Share a video
     */
    public function shareVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|exists:lees_videos,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $videoId = $request->input('video_id');
        
        $existingShare = LeesVideoShare::where('video_id', $videoId)
            ->where('user_id', $user->id)
            ->first();

        if ($existingShare) {
            return response()->json(['message' => 'Already shared'], 200);
        }

        LeesVideoShare::create([
            'video_id' => $videoId,
            'user_id' => $user->id
        ]);

        LeesVideo::find($videoId)->incrementShares();

        return response()->json([
            'message' => 'Video shared successfully',
            'shares_count' => LeesVideo::find($videoId)->shares_count
        ]);
    }

    /**
     * Get video feed
     */
    public function getFeed(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);

        $videos = LeesVideo::public()
            ->with(['user', 'likes', 'comments'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'videos' => $videos->items(),
            'pagination' => [
                'current_page' => $videos->currentPage(),
                'last_page' => $videos->lastPage(),
                'per_page' => $videos->perPage(),
                'total' => $videos->total()
            ]
        ]);
    }

    /**
     * Record video view
     */
    public function recordView(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|exists:lees_videos,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $videoId = $request->input('video_id');
        
        // Check if user already viewed this video recently (within 1 hour)
        $recentView = LeesVideoView::where('video_id', $videoId)
            ->where('user_id', $user->id)
            ->where('created_at', '>', now()->subHour())
            ->first();

        if (!$recentView) {
            LeesVideoView::create([
                'video_id' => $videoId,
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            LeesVideo::find($videoId)->incrementViews();
        }

        return response()->json(['message' => 'View recorded']);
    }

    /**
     * Get video duration (placeholder - you'll need to implement this)
     */
    private function getVideoDuration($videoFile)
    {
        // This is a placeholder. You would need to use FFmpeg or similar
        // to get the actual video duration
        return 30; // Default to 30 seconds
    }
    
    /**
     * Show user's Lees videos
     */
    public function userVideos($id)
    {
        $user = User::findOrFail($id);
        
        $videos = LeesVideo::with(['user', 'likes', 'comments', 'shares', 'views'])
            ->where('user_id', $user->id)
            ->where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        $videosCount = LeesVideo::where('user_id', $user->id)
            ->where('is_public', true)
            ->count();
        
        return view('lees.user', compact('user', 'videos', 'videosCount'));
    }
    
    /**
     * Get a single video
     */
    public function getVideo($id)
    {
        $video = LeesVideo::with(['user', 'likes', 'comments.user', 'shares', 'views'])
            ->findOrFail($id);
        
        if (!$video->is_public && $video->user_id !== auth()->id()) {
            return response()->json(['error' => 'Video not found'], 404);
        }
        
        // Add user interaction flags
        $user = auth()->user();
        if ($user) {
            $video->user_liked = $video->likes()->where('user_id', $user->id)->exists();
        } else {
            $video->user_liked = false;
        }
        
        return response()->json(['video' => $video]);
    }
}