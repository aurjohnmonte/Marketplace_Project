<template>
    <div class="notification-wrapper">
        <div class="notification-card">
            <div class="notification-content">
                <div class="notification-header">
                    <span class="notification-title">{{ notificationData.title || 'New Notification' }}</span>
                    <button class="close-btn" @click="hidenotify">×</button>
                </div>
                <div class="notification-body">
                    <div v-if="notificationData.username" class="username">
                        <strong>{{ notificationData.username }}</strong>
                    </div>
                    <p class="message">{{ notificationData.message || notifymessage || 'Notification details will be displayed here' }}</p>
                    <div v-if="notificationData.timestamp" class="timestamp">
                        {{ notificationData.timestamp }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>

export default{
    props: {
        notificationData: {
            type: Object,
            default: () => ({
                title: 'Message',
                username: 'Customer1',
                message: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vel nisi sed felis dictum lobortis. Vivamus ut justo in diam malesuada vestibulum sed in lorem. Praesent imperdiet enim in eros porta, sit amet semper nunc maximus. Phasellus mauris ligula, volutpat pretium dolor ut, mattis pulvinar felis. Duis vehicula massa velit, non sollicitudin leo facilisis et. Aliquam leo nisl, elementum sit amet sapien vitae, mattis suscipit risus. Curabitur sollicitudin fermentum mauris non pellentesque. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Aenean vehicula semper felis id posuere. Aenean finibus enim et fermentum volutpat. Etiam ac finibus sem, eget elementum ante.',
                timestamp: 'August 1',
                type: 'chat'
            })
        }
    },
    data(){
        return{
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
        hidenotify(){
            this.$router.push({ name: 'Notification' });
        }
    }
}
</script>

<style scoped>
.notification-wrapper {
  position: relative;
  margin: 2rem 0;
  padding: 1rem;
  width: 100%;
  height: 100%;
  align-content: center;
  justify-content: center;
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
  min-width: 400px;
  max-width: 600px;
  margin: 0 auto;
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
