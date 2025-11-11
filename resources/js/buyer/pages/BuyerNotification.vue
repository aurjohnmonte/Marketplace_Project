<template>
    <teleport to="body">
      <!-- <div class="overlay" @click="goexit()"></div> -->
      <div class="notify-container">
        <div class="notify-header">
            <label style="font-weight: bolder;">Notification</label>
            <label style="font-size: 12px; text-decoration: underline;" @click="$router.push({name: 'ViewAllNotification'}); $emit('goexit')">View all</label>
        </div><br>
        <div class="notify-content">
            <div class="notify-item" v-for="notif in filterNotif" :key="notif" @click="goNavigate(notif)">
                <div style="display: flex; flex-direction: row; flex-direction: row; align-items: center; gap: 5px;">
                  <div style="width: 5px; height: 5px; border-radius: 10px; background-color: red;" v-if="notif.seen === 0"></div>
                  <label class="notify-text">{{ notif.text }}</label>
                </div>
                <label class="notify-time">{{ returnFormatTime(notif.created_at) }}</label>
            </div>
        </div>
      </div>
    </teleport>
</template>

<script>
import { useDataStore } from '../../stores/dataStore';
export default {
    props: ['notifications'],
    data(){
      return{

        store: useDataStore(),
        notif: null,
      }
    },
    computed: {
      filterNotif(){

        return this.notifications.filter(notif => notif.users.shop.user.is_deactivate === 0);
      }
    },
    methods: {
    returnFormatTime(datetime){

      return new Date(datetime).toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
      });
    },
      goexit(){
        this.$emit("goexit");
      },
      async goNavigate(notif){

        this.notif = notif;

        if(notif.seen === 0){
          const res = await axios.get('/seen-notify', {
            params: {
              id: notif.id
            }
          });
        }

        this.$emit('modifyseen', notif.id);


        console.log(notif);

        switch(notif.type){

          case 'product':
            this.store.setSelectedProduct(notif.products);
            this.$router.push({name: 'BuyerProduct', params: {id: notif.products.id}});
            this.$emit("goexit");
            break;
          case 'message':
            this.$router.push({name: 'BuyerConversation', params: {id: notif.users.id}});
            this.$emit("goexit");
            break;
          case 'customer record':
            this.$router.push({name: 'ViewAllNotification'});
            this.$emit("goexit");
            break;
        }
      }
    },
    
    mounted(){
    }
}
</script>

<style scoped>
.notify-time{
    font-size: 10px;
    color: #D25E27;
    font-weight: bolder;
}
.notify-item{
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    padding-top: 10px;
    padding-bottom: 10px;
}
.notify-item:hover{
  background-color: rgba(0, 0, 0, 0.134);
}
.notify-text{
    font-size: 10px;
}
.notify-content{
    display: flex;
    flex-direction: column;
}
.notify-header{
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    color: rgb(44, 44, 44);
    align-items: center;
}
.overlay{
  position: fixed;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.568);
  left: 0;
  top: 0;
  z-index: 999; 
}
.notify-container{
  position: fixed;
  width: 300px;
  height: 300px;
  background-color: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  top: 10%;
  right: 2%;
  border-radius: 5px;
  overflow-y: auto;
  padding: 20px;
  z-index: 1000; 
}
@media (min-width: 768px){
  .notify-text{
    font-size: 15px;
  }
  .notify-time{
    font-size: 15px;
  }
  .notify-container{
    width: 500px;
    height: 400px;
    right: 5%;
  }
}
</style>