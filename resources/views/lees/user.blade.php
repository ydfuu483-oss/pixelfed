@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <img 
                        src="{{ $user->avatar ?? '/img/default-avatar.png' }}" 
                        alt="{{ $user->username }}"
                        class="rounded-circle me-3"
                        width="60"
                        height="60"
                    >
                    <div>
                        <h2 class="mb-0">{{ $user->name ?? $user->username }}</h2>
                        <p class="text-muted mb-0">@{{ $user->username }}</p>
                        <small class="text-muted">{{ $videosCount }} Lees videos</small>
                    </div>
                </div>
                
                @auth
                    @if(auth()->id() === $user->id)
                        <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
                            <i class="fas fa-plus"></i> Upload Video
                        </button>
                    @else
                        <div>
                            @if(auth()->user()->following($user))
                                <button class="btn btn-outline-primary" onclick="followUser({{ $user->id }})">
                                    Following
                                </button>
                            @else
                                <button class="btn btn-primary" onclick="followUser({{ $user->id }})">
                                    Follow
                                </button>
                            @endif
                        </div>
                    @endif
                @endauth
            </div>
            
            @if($user->bio)
                <div class="mb-4">
                    <p>{{ $user->bio }}</p>
                </div>
            @endif
            
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    @if($videos->count() > 0)
                        <div class="row">
                            @foreach($videos as $video)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card lees-video-thumbnail">
                                        <div class="position-relative">
                                            <video
                                                class="card-img-top"
                                                style="height: 300px; object-fit: cover; cursor: pointer;"
                                                poster="{{ $video->thumbnail_url }}"
                                                onclick="openVideoModal('{{ $video->id }}')"
                                            >
                                                <source src="{{ $video->video_url }}" type="video/mp4">
                                            </video>
                                            
                                            <div class="video-overlay position-absolute w-100 h-100 d-flex align-items-center justify-content-center">
                                                <i class="fas fa-play-circle text-white" style="font-size: 3rem; opacity: 0.8;"></i>
                                            </div>
                                            
                                            @if($video->duration)
                                                <div class="video-duration position-absolute">
                                                    {{ gmdate('i:s', $video->duration) }}
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="card-body p-2">
                                            @if($video->description)
                                                <p class="card-text small mb-1">
                                                    {{ Str::limit($video->description, 50) }}
                                                </p>
                                            @endif
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="text-muted small">
                                                    <i class="fas fa-heart"></i> {{ $video->likes_count ?? 0 }}
                                                    <i class="fas fa-comment ms-2"></i> {{ $video->comments_count ?? 0 }}
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $video->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            {{ $videos->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-video text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">No Lees videos yet</h4>
                            @auth
                                @if(auth()->id() === $user->id)
                                    <p class="text-muted">Share your first Lees video!</p>
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
                                        <i class="fas fa-plus"></i> Upload Video
                                    </button>
                                @else
                                    <p class="text-muted">{{ $user->username }} hasn't shared any Lees videos yet.</p>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoModalLabel">Lees Video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div id="video-modal-content">
                    <!-- Video content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@auth
    @if(auth()->id() === $user->id)
        <!-- Upload Modal -->
        <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">Upload Lees Video</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <lees-upload-form></lees-upload-form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endauth
@endsection

@push('styles')
<style>
.lees-video-thumbnail {
    transition: transform 0.2s;
}

.lees-video-thumbnail:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.video-overlay {
    background: rgba(0,0,0,0.3);
    opacity: 0;
    transition: opacity 0.2s;
    top: 0;
    left: 0;
}

.lees-video-thumbnail:hover .video-overlay {
    opacity: 1;
}

.video-duration {
    bottom: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
}
</style>
@endpush

@push('scripts')
<script>
function openVideoModal(videoId) {
    // Load video content via AJAX
    fetch(`/api/v1.1/lees/${videoId}`)
        .then(response => response.json())
        .then(data => {
            const video = data.video;
            const content = `
                <div class="lees-video-card">
                    <div class="card-header d-flex align-items-center">
                        <img src="${video.user.avatar || '/img/default-avatar.png'}" 
                             alt="${video.user.username}"
                             class="rounded-circle me-2"
                             width="40" height="40">
                        <div>
                            <h6 class="mb-0">${video.user.name || video.user.username}</h6>
                            <small class="text-muted">@${video.user.username}</small>
                        </div>
                        <div class="ms-auto">
                            <small class="text-muted">${formatDate(video.created_at)}</small>
                        </div>
                    </div>
                    
                    <video class="w-100" controls autoplay style="max-height: 500px;">
                        <source src="${video.video_url}" type="video/mp4">
                    </video>
                    
                    <div class="card-body">
                        ${video.description ? `<p>${video.description}</p>` : ''}
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <button class="btn btn-sm ${video.user_liked ? 'btn-danger' : 'btn-outline-danger'}" 
                                        onclick="likeVideo('${video.id}')">
                                    <i class="fas fa-heart"></i> ${video.likes_count || 0}
                                </button>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-comment"></i> ${video.comments_count || 0}
                                </button>
                                <button class="btn btn-sm btn-outline-success" onclick="shareVideo('${video.id}')">
                                    <i class="fas fa-share"></i> ${video.shares_count || 0}
                                </button>
                            </div>
                            <div class="text-muted">
                                <small><i class="fas fa-eye"></i> ${video.views_count || 0} views</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('video-modal-content').innerHTML = content;
            $('#videoModal').modal('show');
            
            // Record view
            recordView(videoId);
        })
        .catch(error => {
            console.error('Error loading video:', error);
            alert('Failed to load video');
        });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) {
        return 'just now';
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes}m ago`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours}h ago`;
    } else {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days}d ago`;
    }
}

function likeVideo(videoId) {
    fetch('/api/v1.1/lees/like', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ video_id: videoId })
    })
    .then(response => response.json())
    .then(data => {
        // Update like button and count
        location.reload(); // Simple reload for now
    })
    .catch(error => {
        console.error('Error liking video:', error);
    });
}

function shareVideo(videoId) {
    fetch('/api/v1.1/lees/share', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ video_id: videoId })
    })
    .then(response => response.json())
    .then(data => {
        alert('Video shared successfully!');
        location.reload();
    })
    .catch(error => {
        console.error('Error sharing video:', error);
    });
}

function recordView(videoId) {
    fetch('/api/v1.1/lees/view', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ video_id: videoId })
    })
    .catch(error => {
        console.error('Error recording view:', error);
    });
}

function followUser(userId) {
    fetch(`/api/v1.1/accounts/${userId}/follow`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        location.reload();
    })
    .catch(error => {
        console.error('Error following user:', error);
    });
}

window.App = window.App || {};
window.App.config = {
    apiUrl: '{{ config("app.url") }}/api/v1.1',
    csrfToken: '{{ csrf_token() }}'
};
</script>
@endpush