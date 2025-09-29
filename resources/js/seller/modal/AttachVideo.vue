<template>
  <div class="attached-video-container" @click="$emit('exit_modal')">
    <div class="attached-modal" @click.stop>
      <!-- Header -->
      <div class="modal-header">
        <h5>Attach a Video</h5>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <label for="video" class="label">Choose a video file:</label>
        <input id="video" type="file" accept="video/*" class="file-input" @change="previewVideo"/>

        <video
          v-if="videoUrl"
          :src="videoUrl"
          controls
          class="preview"
        ></video>
      </div>

      <!-- Footer -->
      <div class="modal-actions">
        <button class="btn cancel" @click="$emit('goExit')">Cancel</button>
        <button class="btn confirm" @click="goAttach()">Attach</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
    props: ['product'],
  data() {
    return { 
        videoUrl: null,
        file: null,
    }
  },
  methods: {
    previewVideo(e) {
      this.file = e.target.files[0]
      if (this.file) {
        this.videoUrl = URL.createObjectURL(this.file)
      }
    },
    async goAttach(){
        console.log('file: ', this.file);
        if(!this.file){
            window.alert("Please select a video file.");
            return;
        }
        const data = new FormData();
        data.append('video', this.file);
        data.append('product_id', this.product.id);
        const res = await axios.post('/seller/attach-video', data);

        console.log(res.data.message);

        if(res.data.message === 'successful'){
            this.$emit('exitModal', res.data.video);
        }
    }
  }
}
</script>

<style scoped>
/* Overlay */
.attached-video-container {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
}

/* Modal */
.attached-modal {
  background: #fff;
  width: 500px;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  animation: fadeIn 0.3s ease-in-out;
}

/* Header */
.modal-header {
  padding: 15px 20px;
  border-bottom: 1px solid #e5e7eb;
  text-align: center;
}

.modal-header h5 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #111827;
}

/* Body */
.modal-body {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.label {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}

.file-input {
  border: 1px solid #d1d5db;
  padding: 6px;
  border-radius: 6px;
}

.preview {
  width: 100%;
  max-height: 180px;
  border-radius: 6px;
  margin-top: 10px;
  background: #000;
}

/* Footer */
.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 15px 20px;
  border-top: 1px solid #e5e7eb;
}

.btn {
  padding: 8px 16px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-size: 14px;
  transition: background 0.2s, transform 0.1s;
}

.btn.cancel {
  background: #e5e7eb;
  color: #111827;
}

.btn.cancel:hover {
  background: #d1d5db;
}

.btn.confirm {
  background: #3b82f6;
  color: #fff;
}

.btn.confirm:hover {
  background: #2563eb;
}

.btn:hover {
  transform: translateY(-1px);
}

/* Animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
