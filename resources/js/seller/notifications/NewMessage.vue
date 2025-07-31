<template>
  <div class="notification-wrapper">
    <div class="notification-card">
      <span class="message">{{ notifymessage }}</span>
      <button class="close-btn" @click="hidenotify">×</button>
    </div>
  </div>
</template>

<script>
export default{
    props: ['notifymessage'],
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
  position: absolute;
  top: 5rem;
  right: 20%;
  z-index: 9999;
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
}
</style>
