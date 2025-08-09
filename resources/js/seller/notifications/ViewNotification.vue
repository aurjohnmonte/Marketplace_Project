<template>
    <div class="notification-wrapper">
        <div class="notification-card">
            <div class="notification-content" v-if="notificationData">
                <div class="notification-header">
                    <span class="notification-title">Notification</span>
                    <button class="close-btn" @click="hidenotify">×</button>
                </div>
                <div class="notification-body">
                    <div v-if="notificationData.users" class="username">
                        <strong>{{ notificationData.users.firstname }} {{ notificationData.users.lastname }}</strong>
                    </div>
                    <p class="message">{{ notificationData.text || notifymessage || 'Notification details will be displayed here' }}</p>
                    <div v-if="notificationData.created_at" class="timestamp">
                        {{ returnFormatTime(notificationData.created_at) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import { useDataStore } from '../../stores/dataStore';
import axios from 'axios';

export default{
    data(){
        return{
          store: useDataStore(),
          notificationData: null,
        }
    },  
    computed: {
        notificationTypeClass() {
            return `notification-${this.notificationData.type || 'chat'}`;
        },
        notificationIcon() {
            const icons = {
                'chat': 'fa-solid fa-message',
                'notif': 'fa-solid fa-bell',
                'system': 'fa-solid fa-info-circle',
                'warning': 'fa-solid fa-exclamation-triangle',
                'error': 'fa-solid fa-times-circle'
            };
            return icons[this.notificationData.type] || icons['chat'];
        }
    },
    methods: {
        returnFormatTime(date){

            return new Date(date).toLocaleDateString();
        },
        hidenotify(){
            this.$router.push({ name: 'Notification' });
        },
    },
    mounted(){
      this.notificationData = this.store.selectedNotification;
    }
}
</script>

<style scoped>
.notification-wrapper {
  position: fixed;
  width: 100%;
  height: 100%;
  left: 40%;
  top: 30%;
}

.notification-card {
  background-color: #cabab0;
  border: 1px solid #ccc;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 1rem;
  align-items: center;
  width: 400px;
}

.notification-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.notification-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.notification-title {
  font-size: 16px;
  font-weight: bold;
  color: #444444;
}

.notification-body {
  display: flex;
  flex-direction: column;
  gap: .5rem;
}

.username {
  color: #5e5e5e;
  font-size: 14px;
  opacity: 0.9;
}

.message {
  flex: 1;
  font-size: 1em;
  color: #706a6a;
}

.timestamp {
  font-size: 12px;
  color: #2b2a2a;
  opacity: 0.8;
  font-style: italic;
}

.close-btn {
  background: transparent;
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
  color: #ffffff;
  padding: 0;
  margin: 0;
  line-height: 1;
}

.close-btn:hover {
  opacity: 0.8;
}
</style>
