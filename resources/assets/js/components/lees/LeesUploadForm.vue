<template>
  <div class="lees-upload-form">
    <form @submit.prevent="uploadVideo">
      <div class="mb-3">
        <label for="video" class="form-label">Video File *</label>
        <input
          id="video"
          ref="videoInput"
          type="file"
          class="form-control"
          accept="video/mp4,video/mov,video/avi"
          @change="onVideoSelect"
          required
        >
        <div class="form-text">
          Maximum file size: 100MB. Supported formats: MP4, MOV, AVI
        </div>
        <div v-if="videoPreview" class="mt-2">
          <video
            :src="videoPreview"
            class="img-thumbnail"
            style="max-width: 200px; max-height: 150px;"
            controls
          ></video>
        </div>
      </div>
      
      <div class="mb-3">
        <label for="thumbnail" class="form-label">Thumbnail (Optional)</label>
        <input
          id="thumbnail"
          ref="thumbnailInput"
          type="file"
          class="form-control"
          accept="image/jpeg,image/png,image/jpg"
          @change="onThumbnailSelect"
        >
        <div class="form-text">
          Maximum file size: 10MB. Supported formats: JPEG, PNG, JPG
        </div>
        <div v-if="thumbnailPreview" class="mt-2">
          <img
            :src="thumbnailPreview"
            class="img-thumbnail"
            style="max-width: 200px; max-height: 150px;"
            alt="Thumbnail preview"
          >
        </div>
      </div>
      
      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea
          id="description"
          v-model="form.description"
          class="form-control"
          rows="3"
          maxlength="500"
          placeholder="Tell us about your video..."
        ></textarea>
        <div class="form-text">
          {{ form.description.length }}/500 characters
        </div>
      </div>
      
      <div class="mb-3">
        <label for="hashtags" class="form-label">Hashtags</label>
        <input
          id="hashtags"
          v-model="hashtagsInput"
          type="text"
          class="form-control"
          placeholder="#fun #video #lees"
          @input="parseHashtags"
        >
        <div class="form-text">
          Separate hashtags with spaces. Maximum 10 hashtags.
        </div>
        <div v-if="form.hashtags.length" class="mt-2">
          <span 
            v-for="hashtag in form.hashtags" 
            :key="hashtag"
            class="badge badge-secondary me-1"
          >
            #{{ hashtag }}
          </span>
        </div>
      </div>
      
      <div class="mb-3">
        <label for="song" class="form-label">Song/Music</label>
        <input
          id="song"
          v-model="form.song"
          type="text"
          class="form-control"
          maxlength="100"
          placeholder="Song title or artist name"
        >
      </div>
      
      <div class="mb-3">
        <div class="form-check">
          <input
            id="is_public"
            v-model="form.is_public"
            class="form-check-input"
            type="checkbox"
          >
          <label class="form-check-label" for="is_public">
            Make this video public
          </label>
        </div>
      </div>
      
      <div class="mb-3">
        <div class="form-check">
          <input
            id="is_sensitive"
            v-model="form.is_sensitive"
            class="form-check-input"
            type="checkbox"
          >
          <label class="form-check-label" for="is_sensitive">
            Mark as sensitive content
          </label>
        </div>
      </div>
      
      <div v-if="uploadProgress > 0" class="mb-3">
        <div class="progress">
          <div
            class="progress-bar"
            role="progressbar"
            :style="{ width: uploadProgress + '%' }"
            :aria-valuenow="uploadProgress"
            aria-valuemin="0"
            aria-valuemax="100"
          >
            {{ uploadProgress }}%
          </div>
        </div>
      </div>
      
      <div class="d-flex justify-content-end">
        <button
          type="button"
          class="btn btn-secondary me-2"
          @click="resetForm"
          :disabled="uploading"
        >
          Reset
        </button>
        <button
          type="submit"
          class="btn btn-primary"
          :disabled="uploading || !form.video"
        >
          <span v-if="uploading">
            <i class="fas fa-spinner fa-spin"></i> Uploading...
          </span>
          <span v-else>
            <i class="fas fa-upload"></i> Upload Video
          </span>
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'LeesUploadForm',
  data() {
    return {
      form: {
        video: null,
        thumbnail: null,
        description: '',
        hashtags: [],
        song: '',
        is_public: true,
        is_sensitive: false
      },
      hashtagsInput: '',
      videoPreview: null,
      thumbnailPreview: null,
      uploading: false,
      uploadProgress: 0
    };
  },
  methods: {
    onVideoSelect(event) {
      const file = event.target.files[0];
      if (file) {
        // Validate file size (100MB)
        if (file.size > 100 * 1024 * 1024) {
          this.$toast.error('Video file is too large. Maximum size is 100MB.');
          this.$refs.videoInput.value = '';
          return;
        }
        
        this.form.video = file;
        this.videoPreview = URL.createObjectURL(file);
      }
    },
    
    onThumbnailSelect(event) {
      const file = event.target.files[0];
      if (file) {
        // Validate file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
          this.$toast.error('Thumbnail file is too large. Maximum size is 10MB.');
          this.$refs.thumbnailInput.value = '';
          return;
        }
        
        this.form.thumbnail = file;
        this.thumbnailPreview = URL.createObjectURL(file);
      }
    },
    
    parseHashtags() {
      const hashtags = this.hashtagsInput
        .split(/\s+/)
        .map(tag => tag.replace(/^#+/, '').trim())
        .filter(tag => tag.length > 0)
        .slice(0, 10); // Maximum 10 hashtags
      
      this.form.hashtags = hashtags;
    },
    
    async uploadVideo() {
      if (!this.form.video) {
        this.$toast.error('Please select a video file.');
        return;
      }
      
      try {
        this.uploading = true;
        this.uploadProgress = 0;
        
        const formData = new FormData();
        formData.append('video', this.form.video);
        
        if (this.form.thumbnail) {
          formData.append('thumbnail', this.form.thumbnail);
        }
        
        formData.append('description', this.form.description);
        formData.append('hashtags', JSON.stringify(this.form.hashtags));
        formData.append('song', this.form.song);
        formData.append('is_public', this.form.is_public ? '1' : '0');
        formData.append('is_sensitive', this.form.is_sensitive ? '1' : '0');
        
        const response = await axios.post(
          `${window.App.config.apiUrl}/lees/upload`,
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data',
              'X-CSRF-TOKEN': window.App.config.csrfToken
            },
            onUploadProgress: (progressEvent) => {
              this.uploadProgress = Math.round(
                (progressEvent.loaded * 100) / progressEvent.total
              );
            }
          }
        );
        
        this.$toast.success('Video uploaded successfully!');
        this.resetForm();
        
        // Close modal
        $('#uploadModal').modal('hide');
        
        // Emit event to refresh feed
        this.$emit('video-uploaded', response.data.video);
        
        // Refresh the page to show new video
        window.location.reload();
        
      } catch (error) {
        console.error('Upload error:', error);
        
        if (error.response && error.response.data && error.response.data.errors) {
          const errors = error.response.data.errors;
          Object.keys(errors).forEach(key => {
            errors[key].forEach(message => {
              this.$toast.error(message);
            });
          });
        } else {
          this.$toast.error('Failed to upload video. Please try again.');
        }
      } finally {
        this.uploading = false;
        this.uploadProgress = 0;
      }
    },
    
    resetForm() {
      this.form = {
        video: null,
        thumbnail: null,
        description: '',
        hashtags: [],
        song: '',
        is_public: true,
        is_sensitive: false
      };
      
      this.hashtagsInput = '';
      this.videoPreview = null;
      this.thumbnailPreview = null;
      this.uploadProgress = 0;
      
      if (this.$refs.videoInput) {
        this.$refs.videoInput.value = '';
      }
      if (this.$refs.thumbnailInput) {
        this.$refs.thumbnailInput.value = '';
      }
    }
  },
  
  beforeDestroy() {
    // Clean up object URLs
    if (this.videoPreview) {
      URL.revokeObjectURL(this.videoPreview);
    }
    if (this.thumbnailPreview) {
      URL.revokeObjectURL(this.thumbnailPreview);
    }
  }
};
</script>

<style scoped>
.lees-upload-form {
  max-width: 600px;
}

.progress {
  height: 20px;
}

.badge {
  font-size: 0.8em;
}
</style>