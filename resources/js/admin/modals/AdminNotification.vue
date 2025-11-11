<template>
  <div class="notif-dropdown">
    <!-- Header -->
    <div class="notif-header">
      <h4>Notifications</h4>
      <span class="see-all" @click="$router.push({name: 'AdminNotificationPage'}); $emit('close')">See All</span>
    </div>
    <hr />

    <!-- Notification List -->
    <div class="notif-list">
      <div
        v-for="(notif, index) in notifications"
        :key="index"
        class="notif-item"
        :class="{ unread: !notif.seen }"
      >
        <div class="notif-icon">
          <span class="material-icons">{{ notif.icon || 'notifications' }}</span>
        </div>
        <div class="notif-content">
          <p class="notif-message">{{ notif.text }} ({{ notif.users.firstname }} {{ notif.users.lastname }})</p>
          <small class="notif-time">{{ returnFormatDate(notif.created_at) }}</small>
        </div>
      </div>

      <p v-if="!notifications.length" class="no-notifs">No notifications yet.</p>
    </div>
  </div>
</template>

<script>
export default {
    props: ['notifications'],
    data(){
        return{

        }
    },
    methods: {

        returnFormatDate(d){

            const date = new Date(d);

            return `${date.toLocaleDateString()} - ${date.toLocaleTimeString()}`;
        }
    }
};
</script>

<style scoped>
.notif-dropdown {
  position: absolute;
  top: 100px;
  right: 70px; /* adjust based on navbar layout */
  width: 500px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 3px 15px rgba(0, 0, 0, 0.15);
  border: 1px solid #e0e0e0;
  z-index: 300;
  overflow: hidden;
}

/* Header */
.notif-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 14px;
}
.notif-header h4 {
  margin: 0;
  font-size: 16px;
  color: #333;
}
.see-all {
  font-size: 13px;
  color: #1976d2;
  cursor: pointer;
  font-weight: 500;
}
.see-all:hover {
  text-decoration: underline;
}

/* List */
.notif-list {
  max-height: 260px;
  overflow-y: auto;
}
.notif-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 10px 14px;
  cursor: pointer;
  transition: background 0.2s;
}
.notif-item:hover {
  background-color: #f7f7f7;
}
.notif-item.unread {
  background-color: #eef4ff;
}
.notif-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background-color: #e6f0ff;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-shrink: 0;
}
.notif-icon .material-icons {
  font-size: 18px;
  color: #1976d2;
}
.notif-content {
  flex: 1;
}
.notif-message {
  margin: 0;
  font-size: 14px;
  color: #333;
  line-height: 1.4;
}
.notif-time {
  font-size: 12px;
  color: gray;
}
.no-notifs {
  text-align: center;
  color: #777;
  font-size: 13px;
  padding: 15px;
}

/* Scrollbar */
.notif-list::-webkit-scrollbar {
  width: 6px;
}
.notif-list::-webkit-scrollbar-thumb {
  background-color: #ccc;
  border-radius: 3px;
}
</style>
