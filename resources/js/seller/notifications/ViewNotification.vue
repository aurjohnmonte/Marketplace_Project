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

      goViewProduct(productId){
        this.$router.push({ name: 'ViewProduct', query: { id: productId } });
      },

      goView(notify){

        switch(notify.type){
              case 'message':

                this.goViewMessage(notify.users.name);
                break;
              case 'rate shop':
                this.$router.push({name: 'Profile'});
                break;
              case 'rate product':
                if (notify.products && notify.products.id) {
                  this.goViewProduct(notify.products.id);
                } else {
                  console.error('Product ID not found for notification:', notify);
                }
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

/* ===== RESPONSIVE DESIGN ===== */

/* Small mobile devices */
@media (max-width: 480px) {
  .notification-wrapper {
    left: 5%;
    top: 20%;
    width: 90%;
  }

  .notification-card {
    width: 100%;
    padding: 1.5rem;
    gap: 0.8rem;
  }

  .notification-title {
    font-size: 14px;
  }

  .notification-body {
    gap: 0.4rem;
  }

  .username {
    font-size: 12px;
  }

  .message {
    font-size: 0.9em;
    padding: 0.6em;
  }

  .timestamp {
    font-size: 10px;
  }

  .close-btn {
    font-size: 1.1rem;
  }

  .rate-star img {
    width: 16px;
    height: 16px;
  }

  .rate-star label {
    font-size: 10px;
    padding-left: 8px;
  }
}

/* Mobile - Medium devices */
@media (max-width: 768px) {
  .notification-wrapper {
    left: 10%;
    top: 25%;
    width: 80%;
  }

  .notification-card {
    width: 100%;
    padding: 1.8rem;
    gap: 0.9rem;
  }

  .notification-title {
    font-size: 15px;
  }

  .notification-body {
    gap: 0.5rem;
  }

  .username {
    font-size: 13px;
  }

  .message {
    font-size: 0.95em;
    padding: 0.7em;
  }

  .timestamp {
    font-size: 11px;
  }

  .close-btn {
    font-size: 1.2rem;
  }

  .rate-star img {
    width: 18px;
    height: 18px;
  }

  .rate-star label {
    font-size: 11px;
    padding-left: 9px;
  }
}

/* Tablet devices */
@media (min-width: 769px) and (max-width: 1024px) {
  .notification-wrapper {
    left: 25%;
    top: 28%;
    width: 50%;
  }

  .notification-card {
    width: 100%;
    padding: 2.2rem;
    gap: 1.1rem;
  }

  .notification-title {
    font-size: 16px;
  }

  .notification-body {
    gap: 0.6rem;
  }

  .username {
    font-size: 14px;
  }

  .message {
    font-size: 1em;
    padding: 0.8em;
  }

  .timestamp {
    font-size: 12px;
  }

  .close-btn {
    font-size: 1.25rem;
  }

  .rate-star img {
    width: 20px;
    height: 20px;
  }

  .rate-star label {
    font-size: 12px;
    padding-left: 10px;
  }
}

/* Desktop devices */
@media (min-width: 1025px) {
  .notification-wrapper {
    left: 40%;
    top: 30%;
    width: 400px;
  }

  .notification-card {
    width: 400px;
    padding: 2rem;
    gap: 1rem;
  }

  .notification-title {
    font-size: 16px;
  }

  .notification-body {
    gap: 0.5rem;
  }

  .username {
    font-size: 14px;
  }

  .message {
    font-size: 1em;
    padding: 0.8em;
  }

  .timestamp {
    font-size: 12px;
  }

  .close-btn {
    font-size: 1.25rem;
  }

  .rate-star img {
    width: 20px;
    height: 20px;
  }

  .rate-star label {
    font-size: 12px;
    padding-left: 10px;
  }
}

/* Large desktop devices */
@media (min-width: 1440px) {
  .notification-wrapper {
    left: 42%;
    top: 28%;
    width: 450px;
  }

  .notification-card {
    width: 450px;
    padding: 2.5rem;
    gap: 1.2rem;
  }

  .notification-title {
    font-size: 18px;
  }

  .notification-body {
    gap: 0.6rem;
  }

  .username {
    font-size: 15px;
  }

  .message {
    font-size: 1.1em;
    padding: 1em;
  }

  .timestamp {
    font-size: 13px;
  }

  .close-btn {
    font-size: 1.4rem;
  }

  .rate-star img {
    width: 22px;
    height: 22px;
  }

  .rate-star label {
    font-size: 13px;
    padding-left: 12px;
  }
}

/* Landscape orientation for mobile */
@media (max-width: 768px) and (orientation: landscape) {
  .notification-wrapper {
    left: 15%;
    top: 15%;
    width: 70%;
  }

  .notification-card {
    padding: 1.5rem;
  }
}

/* High DPI displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  .notification-card {
    border-radius: 12px;
  }

  .rate-star img {
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
  }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
  .close-btn {
    min-height: 44px;
    min-width: 44px;
  }

  .notification-card {
    min-height: 120px;
  }
}

/* Print styles */
@media print {
  .notification-wrapper {
    position: static;
    width: 100%;
    height: auto;
    left: auto;
    top: auto;
  }

  .notification-card {
    box-shadow: none;
    border: 1px solid #000;
  }

  .close-btn {
    display: none;
  }
}
</style>
