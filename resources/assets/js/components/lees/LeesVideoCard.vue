<template>
  <div class="lees-video-card mb-4">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <img 
          :src="video.user.avatar || '/img/default-avatar.png'" 
          :alt="video.user.username"
          class="rounded-circle me-2"
          width="40"
          height="40"
        >
        <div>
          <h6 class="mb-0">{{ video.user.name || video.user.username }}</h6>
          <small class="text-muted">@{{ video.user.username }}</small>
        </div>
        <div class="ms-auto">
          <small class="text-muted">{{ formatDate(video.created_at) }}</small>
        </div>
      </div>
      
      <div class="video-container position-relative">
        <video
          :ref="`video-${video.id}`"
          :src="video.video_url"
          :poster="video.thumbnail_url"
          class="w-100"
          controls
          preload="metadata"
          @play="onVideoPlay"
          @pause="onVideoPause"
          @loadedmetadata="onVideoLoaded"
          style="max-height: 500px; min-height: 300px; object-fit: cover; border-radius: 8px;"
        >
          Your browser does not support the video tag.
        </video>
        
        <div v-if="video.duration" class="video-duration">
          {{ formatDuration(video.duration) }}
        </div>
      </div>
      
      <div class="card-body">
        <div v-if="video.description" class="mb-2">
          <p class="mb-1">{{ video.description }}</p>
        </div>
        
        <div v-if="video.hashtags && video.hashtags.length" class="mb-2">
          <span 
            v-for="hashtag in video.hashtags" 
            :key="hashtag"
            class="badge badge-secondary me-1"
          >
            #{{ hashtag }}
          </span>
        </div>
        
        <div v-if="video.song" class="mb-2">
          <small class="text-muted">
            <i class="fas fa-music"></i> {{ video.song }}
          </small>
        </div>
        
        <div class="d-flex justify-content-between align-items-center">
          <div class="btn-group" role="group">
            <button 
              type="button" 
              class="btn btn-sm"
              :class="video.user_liked ? 'btn-danger' : 'btn-outline-danger'"
              @click="$emit('like', video.id)"
            >
              <i class="fas fa-heart"></i> {{ video.likes_count || 0 }}
            </button>
            
            <button 
              type="button" 
              class="btn btn-sm btn-outline-primary"
              @click="toggleComments"
            >
              <i class="fas fa-comment"></i> {{ video.comments_count || 0 }}
            </button>
            
            <button 
              type="button" 
              class="btn btn-sm btn-outline-success"
              @click="$emit('share', video.id)"
            >
              <i class="fas fa-share"></i> {{ video.shares_count || 0 }}
            </button>
          </div>
          
          <div class="text-muted">
            <small>
              <i class="fas fa-eye"></i> {{ video.views_count || 0 }} views
            </small>
          </div>
        </div>
        
        <!-- Comments Section -->
        <div v-if="showComments" class="mt-3">
          <div class="border-top pt-3">
            <div class="mb-3">
              <div class="input-group">
                <input
                  v-model="newComment"
                  type="text"
                  class="form-control"
                  placeholder="Add a comment..."
                  @keyup.enter="submitComment"
                >
                <div class="input-group-append">
                  <button 
                    class="btn btn-primary" 
                    type="button"
                    @click="submitComment"
                    :disabled="!newComment.trim()"
                  >
                    Post
                  </button>
                </div>
              </div>
            </div>
            
            <div v-if="video.comments && video.comments.length" class="comments-list">
              <div 
                v-for="comment in video.comments.slice(0, 3)" 
                :key="comment.id"
                class="comment mb-2"
              >
                <div class="d-flex">
                  <img 
                    :src="comment.user.avatar || '/img/default-avatar.png'" 
                    :alt="comment.user.username"
                    class="rounded-circle me-2"
                    width="24"
                    height="24"
                  >
                  <div class="flex-grow-1">
                    <strong>{{ comment.user.username }}</strong>
                    <span class="ms-2">{{ comment.comment }}</span>
                    <div class="text-muted small">
                      {{ formatDate(comment.created_at) }}
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-if="video.comments.length > 3" class="text-center">
                <button class="btn btn-sm btn-link">
                  View all {{ video.comments.length }} comments
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LeesVideoCard',
  props: {
    video: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      showComments: false,
      newComment: '',
      hasViewed: false
    };
  },
  methods: {
    toggleComments() {
      this.showComments = !this.showComments;
    },
    
    submitComment() {
      if (!this.newComment.trim()) return;
      
      this.$emit('comment', {
        videoId: this.video.id,
        comment: this.newComment.trim()
      });
      
      this.newComment = '';
    },
    
    onVideoPlay() {
      if (!this.hasViewed) {
        this.$emit('view', this.video.id);
        this.hasViewed = true;
      }
    },
    
    onVideoPause() {
      // Handle pause if needed
    },
    
    onVideoLoaded() {
      // Handle video loaded if needed
    },
    
    formatDate(dateString) {
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
    },
    
    formatDuration(seconds) {
      const minutes = Math.floor(seconds / 60);
      const remainingSeconds = seconds % 60;
      return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    }
  }
};
</script>

<style scoped>
.lees-video-card {
  max-width: 600px;
  margin: 0 auto;
}

.video-container {
  background: #000;
}

.video-duration {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 12px;
}

.comment {
  padding: 8px 0;
  border-bottom: 1px solid #eee;
}

.comment:last-child {
  border-bottom: none;
}

.comments-list {
  max-height: 300px;
  overflow-y: auto;
}
</style>