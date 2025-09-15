<template>
  <div class="buyer-message-container">
    <div class="buyer-message-header">
      <h2>Chats</h2>
      <input type="text" placeholder="Search name..." v-model="search_text"/>
    </div>

    <div class="buyer-message-list">
      <div v-if="loading" class="loading-content">
          <img src="../../../images/kOnzy.gif"/>
      </div>
      <div class="user-message" v-for="(message, index) in messages" :key="index" @click="goMessage(message[0].user_id)" v-else>
        <div class="profile-info">
          <img :src="'/'+message[0].profile_photo" alt="User profile">
          <div class="profile-info-text">
            <div class="name-text">{{ message[0].name }}</div>
            <div class="message-text">{{ message[1].messages }}</div>
            <div class="last-active">
              <div style="width: 7px; height: 7px; border-radius: 12px; background-color: green;" v-if="message[0].user.is_active === 1"></div>
              <label>Active {{ (message[0].user.is_active === 1 ? "now" : returnFormatActivedTime(message[0].user.time_logout)) }}</label>
            </div>
          </div>
        </div>
        <div class="minute-text">{{ returnTime(message[1].updated_at) }}</div>
      </div>
    </div>
  </div>
</template>

<script>
import { useDataStore } from '../../stores/dataStore';
import axios from "axios";

export default {
  data(){
    return{
      loading: false,
      store: useDataStore(),
      messages: [],
      orig_messages: [],
      search_text: "",
      activeListener: null,
      messageListener: null,
    }
  },
  watch: {
    search_text(){

      let dummy = [];

      this.orig_messages.forEach(mes => {

        if(mes[0].name.includes(this.search_text)){
          dummy.unshift(mes);
        }
      });

      this.messages = dummy;
    }
  },
  methods: {
    returnFormatActivedTime(datetime) {
      const current = new Date();
      const time = new Date(datetime);

      const diffInMs = current - time;
      const diffInSeconds = Math.floor(diffInMs / 1000);

      if (diffInSeconds < 60) {
        return `${diffInSeconds} seconds ago`;
      }

      const diffInMinutes = Math.floor(diffInSeconds / 60);
      if (diffInMinutes < 60) {
        return `${diffInMinutes} minutes ago`;
      }

      const diffInHours = Math.floor(diffInMinutes / 60);
      if (diffInHours < 24) {
        return `${diffInHours} hours ago`;
      }

      const diffInDays = Math.floor(diffInHours / 24);
      return `${diffInDays} days ago`;
    },


    returnTime(updated_at){

      const date = new Date(updated_at);

      return date.toLocaleTimeString();
    },
    goMessage(user_id){
      this.$router.push({name: "BuyerConversation", params: {"id": user_id}});
    },
    goConversation() {
      this.$router.push({ name: "BuyerConversation", params: { id: 1 } });
    },

    reArrangeMessage(list) {
      this.messages = []; // start fresh

      for (let i = 0; i < list.length; i++) {
        let currentDate = list[i][1].updated_at;

        let is_latest = this.checkTime(currentDate, list);

        if (is_latest) {
          this.messages.push(list[i]); // insert at front
          list.splice(i, 1);              // remove from original
          i = -1;                         // reset loop (next iteration = 0)
        }
      }

      this.orig_messages = this.messages;
    },


   checkTime(date, list) {
      let checkdate = new Date(date);

      for (let message of list) {
        let date2 = new Date(message[1].updated_at);
        if (checkdate < date2) {
          return false; // This works correctly
        }
      }
      return true;
    },


    async returMessage(){
      try{
        this.loading = true;
        const store = useDataStore();
        const user_info = store.currentUser_info;
        const data = new FormData();
        data.append('id', user_info.id);
        const res = await axios.post("/return/list-messages", data);
        
        console.log("messages: ",res.data.messages);

        let data_messages = [... res.data.messages];

        this.reArrangeMessage(data_messages);
        this.loading = false;
      }
      catch(error){
        console.log(error);
      }
    }
  },
  async mounted() {

    let username = this.store.currentUser_info.name;

    this.activeListener = Echo.channel(`active-notify`);
    this.messageListener = Echo.channel(`message.${username}`);

    this.activeListener.listen('.active.sent', async (event) => {

          await this.returMessage();
          console.log('NEEEH AGIIIIIII');
    });

    this.messageListener.listen('.message.sent', async (event) => {
            console.log('ne agi diria');
            
            await this.returMessage();
    });
    
    await this.returMessage();
    console.log('hey neh agi sab')

    let path = this.$route.path;
    let new_path = path.slice(7);
    this.$emit("changepathtext", new_path);

  },
  unmounted(){

    if(this.activeListener){
      Echo.leave('active-notify');
      this.activeListener = null;
    }
    
    if(this.messageListener){
      let username = this.store.currentUser_info.name;
      Echo.leave(`message.${username}`);
      this.messageListener = null;
    }
  }
};
</script>

<style scoped>
.last-active {
  gap: 5px;
  display: flex;
  flex-direction: row;
  align-items: center;
  font-size: 12px;
  color: #888;
}
.loading-content img{
    width: 100px;
    height: 100px;
}
.loading-content{
  padding-top: 50px;
    width: 100%;
    height: auto;
    display: flex;
    align-items: center;
    justify-content: center;
}
.buyer-message-container {
  background-color: #f9f9f9;
  min-height: 100vh;
  font-family: Arial, sans-serif;
  font-size: 12px;
  margin-top: 80px;
}

.buyer-message-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid #ccc;
  background-color: #fff;
}

.buyer-message-header h2 {
  font-size: 14px;
  color: #333;
  margin: 0;
}

.buyer-message-header input {
  padding: 5px 10px;
  font-size: 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  width: 180px;
}

.buyer-message-list {
  display: flex;
  flex-direction: column;
}

.user-message {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid #e0e0e0;
  background-color: #fff;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.user-message:hover {
  background-color: #f0f0f0;
}

.profile-info {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 80%;
}

.profile-info img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.profile-info-text {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.name-text {
  font-weight: bold;
  color: #222;
}

.message-text {
  color: #666;
  font-size: 11px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 220px;
}

.minute-text {
  font-size: 10px;
  font-weight: 600;
  color: #d25e27;
  white-space: nowrap;
}
@media (min-width: 768px){
  .buyer-message-header h2{
    font-size: 25px;
  }
  .buyer-message-header input{
    font-size: 15px;
    width: 250px;
    padding: 10px;
  }
  .profile-info{
    font-size: 15px;
  }
  .profile-info img{
    width: 80px;
    height: 80px;
  }
  .profile-info-text .name-text{
    font-size: 20px;
  }
  .message-text{
    font-size: 15px;
  }
  .last-active{
    font-size: 15px;
  }
  .minute-text{
    font-size: 15px;
  }
  .buyer-message-container {
    margin: 100px;
  }
}
</style>
