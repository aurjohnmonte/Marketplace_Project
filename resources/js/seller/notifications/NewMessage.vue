<template>
  <div class="notification-wrapper">
    <div class="notification-card">
      <span class="message">{{ notifymessage || 'Notification details will be displayed here' }}</span>
      <button class="close-btn" @click="hidenotify">×</button>
    </div>
  </div>
</template>

<script>
export default{
    props: {
        notifymessage: {
            type: String,
            default: 'Notification details will be displayed here'
        }
    },
    data(){
        return{

        }
    },
    methods: {
      hidenotify(){
        this.$emit('hidenotify');
      }
    }
}
</script>

<style scoped>
.notification-wrapper {
  position: relative;
  margin: 2rem 0;
  padding: 1rem;
}

.notification-card {
  background-color: #17b09f;
  border: 1px solid #ccc;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 1rem;
  align-items: center;
  min-width: 200px;
  max-width: 600px;
  margin: 0 auto;
}

.message {
  flex: 1;
  font-size: 14px;
  color: #ffffff;
  font-weight: bolder;
}

.close-btn {
  background: transparent;
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
  color: #ffffff;
}
</style>
