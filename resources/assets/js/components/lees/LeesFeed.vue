<template>
  <div class="lees-feed">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>
        
        <div v-else-if="videos.length === 0" class="text-center py-5">
          <h4>No Lees videos yet</h4>
          <p class="text-muted">Be the first to share a Lees video!</p>
        </div>
        
        <div v-else>
          <lees-video-card
            v-for="video in videos"
            :key="video.id"
            :video="video"
            @like="handleLike"
            @comment="handleComment"
            @share="handleShare"
            @view="handleView"
          />
          
          <div v-if="hasMore" class="text-center py-3">
            <button 
              class="btn btn-outline-primary" 
              @click="loadMore"
              :disabled="loadingMore"
            >
              <span v-if="loadingMore">Loading...</span>
              <span v-else>Load More</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LeesVideoCard from './LeesVideoCard.vue';

export default {
  name: 'LeesFeed',
  components: {
    LeesVideoCard
  },
  data() {
    return {
      videos: [],
      loading: true,
      loadingMore: false,
      currentPage: 1,
      hasMore: true
    };
  },
  mounted() {
    this.loadFeed();
  },
  methods: {
    async loadFeed() {
      try {
        this.loading = true;
        const response = await axios.get(`${window.App.config.apiUrl}/lees/feed`);
        this.videos = response.data.videos;
        this.hasMore = response.data.pagination.current_page < response.data.pagination.last_page;
        this.currentPage = response.data.pagination.current_page;
      } catch (error) {
        console.error('Error loading feed:', error);
        this.$toast.error('Failed to load Lees feed');
      } finally {
        this.loading = false;
      }
    },
    
    async loadMore() {
      if (!this.hasMore || this.loadingMore) return;
      
      try {
        this.loadingMore = true;
        const response = await axios.get(`${window.App.config.apiUrl}/lees/feed?page=${this.currentPage + 1}`);
        this.videos.push(...response.data.videos);
        this.hasMore = response.data.pagination.current_page < response.data.pagination.last_page;
        this.currentPage = response.data.pagination.current_page;
      } catch (error) {
        console.error('Error loading more videos:', error);
        this.$toast.error('Failed to load more videos');
      } finally {
        this.loadingMore = false;
      }
    },
    
    async handleLike(videoId) {
      try {
        const response = await axios.post(`${window.App.config.apiUrl}/lees/like`, {
          video_id: videoId
        });
        
        const video = this.videos.find(v => v.id === videoId);
        if (video) {
          video.likes_count = response.data.likes_count;
          video.user_liked = response.data.liked;
        }
      } catch (error) {
        console.error('Error liking video:', error);
        this.$toast.error('Failed to like video');
      }
    },
    
    async handleComment(data) {
      try {
        const response = await axios.post(`${window.App.config.apiUrl}/lees/comment`, {
          video_id: data.videoId,
          comment: data.comment,
          parent_id: data.parentId
        });
        
        const video = this.videos.find(v => v.id === data.videoId);
        if (video) {
          video.comments_count++;
          if (!video.comments) video.comments = [];
          video.comments.push(response.data.comment);
        }
        
        this.$toast.success('Comment added successfully');
      } catch (error) {
        console.error('Error adding comment:', error);
        this.$toast.error('Failed to add comment');
      }
    },
    
    async handleShare(videoId) {
      try {
        const response = await axios.post(`${window.App.config.apiUrl}/lees/share`, {
          video_id: videoId
        });
        
        const video = this.videos.find(v => v.id === videoId);
        if (video) {
          video.shares_count = response.data.shares_count;
        }
        
        this.$toast.success('Video shared successfully');
      } catch (error) {
        console.error('Error sharing video:', error);
        this.$toast.error('Failed to share video');
      }
    },
    
    async handleView(videoId) {
      try {
        await axios.post(`${window.App.config.apiUrl}/lees/view`, {
          video_id: videoId
        });
        
        const video = this.videos.find(v => v.id === videoId);
        if (video) {
          video.views_count++;
        }
      } catch (error) {
        console.error('Error recording view:', error);
      }
    }
  }
};
</script>

<style scoped>
.lees-feed {
  min-height: 500px;
}
</style>