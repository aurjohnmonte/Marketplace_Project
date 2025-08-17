<template>
    <div class="notification-wrapper">
        <div class="notification-card">
            <div class="notification-content" v-if="notificationData">
                <div class="notification-header">
                    <span class="notification-title"><label>{{ returnLabel(notificationData.type) }}</label></span>
                    <button class="close-btn" @click="hidenotify">×</button>
                </div>
                <div class="notification-body">
                    <div v-if="notificationData.users" class="username">
                       <img :src="'/'+notificationData.users.profile" style="width: 50px; height: 50px; background-color: white; border-radius: 50%; padding: 5px; margin-right: 10px;"> <strong>{{ notificationData.users.firstname }} {{ notificationData.users.lastname }}</strong>
                    </div>
                    <p class="message">{{ returnText(notificationData)}}</p>
                    <div class="rate-star" v-if="notificationData.type === 'rate shop' || notificationData.type === 'rate product'">
                      <img src="../../../images/star.png" class="star-rate" v-for="turn in returnStar('whole',notificationData.reviews.rate)" :key="turn">
                      <img src="../../../images/half-star.png" class="star-rate" v-for="turn in returnStar('half',notificationData.reviews.rate)" :key="turn">
                      <img src="../../../images/no-star.png" class="star-rate" v-for="turn in returnStar('none',notificationData.reviews.rate)" :key="turn">
                      <label>{{ notificationData.reviews.rate }} star</label>
                    </div>
                    <div v-if="notificationData.created_at" class="timestamp">
                        {{ returnFormatTime(notificationData.created_at) }} 
                    </div>
                    <label style="text-align: end; color: blue; text-decoration: underline; font-style: italic; cursor: pointer;" @click.stop="goView(notificationData)">View</label>
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
      goViewMessage(username){

        this.$router.push({name: 'Chats', query: {username: username}});
      },
      goView(notify){

        switch(notify.type){
              case 'message':

                this.goViewMessage(notify.users.name);
                break;
              case 'rate shop':
                break;
              case 'rate product':
                break;
          }
      },
      isFloat(num){
          return (Number(num) === num) && (num % 1 !== 0);
        },

    returnStar(type_star, rate){

          let num = Math.floor(rate);
          let is_float = this.isFloat(rate);

          switch(type_star){

            case 'whole':
              if(rate === 0){
                return 0;
              }
              return num;

            case 'half':
              
              return is_float ? 1 : 0;

            case 'none':

              return is_float ? (5-(num+1)) : 5-num;

          }
        },
      returnText(notify){
          switch(notify.type){
              case 'message':
                return notify.messages.messages;
              case 'rate shop':
                return notify.reviews.comment;
              case 'rate product':
                return notify.reviews.comment;
          }
      },
      returnLabel(status){

        switch(status){
          case 'message':
            return "Sent a message";
          case 'rate shop':
            return "Rate Shop";
          case 'rate product':
            return "Rate Product";
        }
      },
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
.rate-star{
  display: flex;
  flex-direction: row;
  align-items: center;
}
.rate-star img{
  width: 20px; height: 20px;
}
.rate-star label{
  padding-left: 10px;
  margin: 0;
  font-size: 12px;
}
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
  padding: .8em;
  background-color: #e0ccc0;
  border-radius: .5em;
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
