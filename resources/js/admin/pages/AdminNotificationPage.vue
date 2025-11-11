<template>
  <div class="manage-account-container">
    <h1>🔔 Notifications</h1>

    <!-- Top Actions -->
    <div class="notif-actions">
      <button class="mark-all" @click="markAllRead">Mark All as Read</button>
      <button class="clear-all" @click="clearAll">Clear All</button>
    </div>

    <!-- Notification List -->
    <div class="notif-list">
      <div
        v-for="(notif, index) in notifications"
        :key="index"
        class="notif-card"
        :class="{ unread: !notif.seen }"
      >
        <div class="notif-left">
          <span class="material-icons notif-icon">{{ notif.icon }}</span>
        </div>

        <div class="notif-info">
          <div style="width: 100%; display: flex; gap: 20px;">
            <p class="notif-message">{{ notif.text }} -</p>
            <p class="notif-message">{{ notif.users.firstname }} {{ notif.users.lastname }} ({{ notif.users.name }})</p>
          </div>
          <small class="notif-time">{{ returnFormatDate(notif.created_at) }}</small>
        </div>

        <div class="notif-actions-right">
          <button class="delete-btn" @click="deleteNotif(notif, index)">
            <span class="material-icons">delete</span>
          </button>
        </div>
      </div>

      <p v-if="!notifications.length" class="no-notifs">No notifications available.</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      notifications: [],
    };
  },
  methods: {
    async markAllRead() {

        const res = await axios.post('/admin/mark-all-read/notif');

        console.log(res.data.message);
        if(res.data.message === 'success'){

            window.alert(res.data.message);
            this.notifications.forEach((n) => (n.seen = 1));
        }
    },
    async clearAll() {
        const res = await axios.post('/admin/delete-all/notif');

        console.log(res.data.message);
        
        if(res.data.message === 'success'){

            window.alert(res.data.message);
            this.notifications = [];
        }
    },
    async returnNotifications(){

        const res = await axios.get('/admin/return-all/notifications');
        this.notifications = res.data.notif;

        console.log(res.data.notif);
    },
    returnFormatDate(d){

        const date = new Date(d);

        return `${date.toLocaleDateString()} - ${date.toLocaleTimeString()}`;
    },

    async deleteNotif(notif, index){
        
        const data = new FormData();
        data.append('id', notif.id);

        const res = await axios.post('/admin/delete-notif', data);

        console.log(res.data.message);

        if(res.data.message === 'success'){

            this.notifications.splice(index, 1);
            window.alert(res.data.message);
        }
    }
  },

  async mounted(){

    await this.returnNotifications();
  }
};
</script>

<style scoped>
.manage-account-container {
  width: 100%;
  box-sizing: border-box;
  padding: 20px 10px;
}

h1 {
  margin-bottom: 20px;
  font-size: 24px;
  color: #11480b;
  font-weight: 600;
}

/* Actions */
.notif-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-bottom: 20px;
}

.mark-all,
.clear-all {
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: 0.2s;
}

.mark-all {
  background-color: #eaf3e9;
  color: #11480b;
}

.mark-all:hover {
  background-color: #d2ebcf;
}

.clear-all {
  background-color: #fbeaea;
  color: #a91515;
}

.clear-all:hover {
  background-color: #f4dada;
}

/* List */
.notif-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.notif-card {
  display: flex;
  align-items: center;
  gap: 15px;
  background-color: #ffffff;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 12px 16px;
  transition: background-color 0.2s ease;
}

.notif-card.unread {
  background-color: #f0f7f0;
}

.notif-card:hover {
  background-color: #f9f9f9;
}

.notif-left {
  display: flex;
  align-items: center;
  justify-content: center;
}

.notif-icon {
  font-size: 28px;
  color: #11480b;
}

.notif-info {
  flex: 1;
}

.notif-message {
  margin: 0;
  font-size: 15px;
  font-weight: 500;
  color: #333;
}

.notif-time {
  font-size: 13px;
  color: #777;
}

.notif-actions-right {
  display: flex;
  align-items: center;
}

.delete-btn {
  border: none;
  background: transparent;
  color: #a91515;
  cursor: pointer;
  padding: 5px;
  border-radius: 5px;
  transition: 0.2s;
}

.delete-btn:hover {
  background-color: #fdeaea;
}

.no-notifs {
  text-align: center;
  color: gray;
  font-size: 14px;
  margin-top: 30px;
}

/* Responsive */
@media screen and (min-width: 600px) {
  .manage-account-container {
    padding: 20px;
    padding-left: 70px;
    padding-right: 70px;
  }
}
</style>
