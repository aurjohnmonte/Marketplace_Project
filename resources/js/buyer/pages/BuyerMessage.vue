<template>
  <div class="buyer-message-container">
    <div class="buyer-message-header">
      <h2>Chats</h2>
      <input type="text" placeholder="Search name..." />
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
      messages: [],
    }
  },
  methods: {
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
    await this.returMessage();
    window.scrollTo(0, 0);
    let path = this.$route.path;
    let new_path = path.slice(7);
    this.$emit("changepathtext", new_path);

  }
};
</script>

<style scoped>
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
  padding-top: 75px;
  background-color: #f9f9f9;
  min-height: 100vh;
  font-family: Arial, sans-serif;
  font-size: 12px;
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
  gap: 3px;
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
</style>
