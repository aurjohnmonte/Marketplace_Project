<template>
  <div class="message-page" @click="show_option = false">
    <div class="view-full-container" v-if="view_image" @click.stop="view_image = null; view_img = null">
      <div class="view-full-img">
        <img :src="'/'+view_img" v-if="!is_blob">
        <img :src="view_img" v-else>
      </div>
    </div>
    <div class="more-option" v-if="show_option">
      <label style="color: blue;" @click.stop="goEdit">Edit</label>
      <label style="color: red;" @click.stop="goDelete">Delete</label>
      <label v-if="from_image" @click="viewImagefromUser">View</label>
    </div>
    <!-- Header -->
    <div class="header">
      <button @click="$router.back()" class="back-btn">
        ←
      </button>
      <div class="shop-info">
        <div class="avatar">
          <img :src="'/'+shop_info.profile_photo"/>
        </div>
        <div>
          <div class="shop-name">{{ shop_info.name }}</div>
          <div class="last-active">Active 5 minutes ago</div>
        </div>
      </div>
      <a class="rate-shop" href="#">Rate Shop</a>
    </div>

    <!-- Chat messages -->
    <div class="messages">
      <div v-if="loading" class="loading-content">
          <img src="../../../images/kOnzy.gif"/>
      </div>
      <div style="width: 100%; text-align: center;">
        <label style="color: gray; font-size: 12px;">Send {{ shop_info.name }} a message</label>
      </div><br>
      <div class="message-content">
        <div v-for="(m, index) in messages" :key="index">

          <div style="display: flex; width: 100%; flex-direction: column;" v-if="m.from_id !== currentUser.id">
            <div style="display: flex; flex-direction: row; align-items: center;">
              <div class="receiver-content" v-if="m.messages">
                <label>{{ m.messages }}</label>
              </div>
            </div>
            <img :src="'/'+m.message_pic" v-if="m.message_pic" class="message-pic" @click="viewImage(m.message_pic)">
            <label class="time">{{ returnTime(m.updated_at) }}</label>
          </div>
          <div style="display: flex; width: 100%; justify-content: end; flex-direction: column; align-items: end;" v-else>
            <div style="display: flex; flex-direction: row; align-items: center;">
              <img src="../../../images/more (2).png" style="padding-right: 10px; width: 20px; height: 20px;" v-if="hover === index" @mouseenter="hover = index" @mouseleave="hover = null" @click.stop="more_option(m.id, index)"/>
              <div class="sender-content" @mouseenter="hover = index; from_image = false;" @mouseleave="hover = null" v-if="m.messages">
                <label>{{ m.messages }}</label>
              </div>
            </div>
            <img :src="'/'+m.message_pic" v-if="m.message_pic" class="message-pic" @mouseenter="hover = index; 
                                                                                                from_image = true;
                                                                                                view_img = m.message_pic;
                                                                                                is_blob = false;"
                                                                                    @mouseleave="hover = null">

            <img v-if="m.message_blob" :src="m.message_blob" class="message-pic" @mouseenter="hover = index;
                                                                                              from_image = true;
                                                                                              view_img = m.message_blob;
                                                                                              is_blob = true;" 
                                                                                  @mouseleave="hover = null"/>
            <label class="time">{{ returnTime(m.updated_at) }}</label>
          </div>
        </div>
      </div>
    </div>

    <!-- Message input -->
    <form @submit.prevent="goMessageSent">
      <div class="message-input">
        <div class="edit-info" v-if="selected_edit_message">
          <label style="color: blue; font-weight: bolder;">EDIT</label>
          <label>{{ selected_edit_message.messages }}</label>
          <img src="../../../images/cancel.png" style="width: 10px; height: 10px;" @click.prevent="selected_edit_message = null; message_text = '';"/>
        </div>
        <div class="send-img" v-if="photo">
          <div style="position: relative;">
            <img @click="goCancelImgSent" src="../../../images/cancel.png" style="z-index: 1000; width: 10px; height: 10px; position: absolute; padding: 2px; border: 1px solid gray; border-radius: 10px; background-color: white; right: 0; top: 0;">
            <img :src="photo" />
          </div>
        </div>
        <div class="message-i">
          <label class="image-upload">
          <input type="file" hidden  @change="getFile" accept="image/*" :disabled="selected_edit_message ? true : false"/>
          <span>＋</span>
          </label>
          <input type="text" placeholder="Write a message..." v-model="message_text"/>
          <button class="send-btn">➤</button>
        </div>
      </div>
    </form>
  </div>
</template>
<script>
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore';

export default{
  data(){
    return{
      view_image: false,
      is_blob: false,
      from_image: false,
      view_img: null,
      selected_id: null,
      selected_edit_message: null,
      selected_index: null,
      show_option: false,
      loading: false,
      hover: null,
      message_text: "",
      photo: null,
      send_photo: null,
      shop_info: [],
      currentUser: null,
      messages: [],
    }
  },
  methods: {
    viewImagefromUser(){
      this.view_image = true;
    },
    viewImage(img){
      this.is_blob = false;

      this.view_img = img;
      this.view_image = true;
    },
  goCancelImgSent(){
    this.photo = null;
    this.send_photo = null;
  },

    getFile(event){
      this.send_photo = event.target.files[0];
      this.photo = URL.createObjectURL(event.target.files[0]);
      console.log(this.photo);
    },
    async goEdit(){

      this.selected_edit_message = this.messages[this.selected_index];
      console.log(this.selected_edit_message);
      this.message_text = this.selected_edit_message.messages;
    },
    async goDelete(){

      this.show_option = false;

      console.log(this.selected_id);
      const data = new FormData();
      data.append('id', this.selected_id);

      const res = await axios.post('/delete/message', data);
      
      this.messages.splice(this.selected_index, 1);
    },

    more_option(id, index){

      console.log(id);
      this.selected_id = id;
      this.selected_index = index;
      this.show_option = true;
    },
    returnTime(updated_at){
      const date = new Date(updated_at);

      
      return date.toLocaleTimeString()
    },
    async editMessage(){

      const data = new FormData();
      data.append("id", this.selected_edit_message.id);
      data.append("text", this.message_text);

      const res = await axios.post("/edit/message", data);

      window.alert(res.data.message);

      this.messages[this.selected_index].messages = this.message_text;
      this.selected_edit_message = null;
      this.message_text = "";
    },
    async goMessageSent(){
      if((this.message_text === "" || this.message_text === null) && this.send_photo === null){
        window.alert("Please send your message");
        return;
      }
      if(this.selected_edit_message){
        await this.editMessage();
        return;
      }
      const data = new FormData();
      const date = new Date();
      data.append('receiver_id', this.shop_info.user_id);
      data.append('sender_id', this.currentUser.id);
      data.append('message_text', this.message_text);
      data.append('photo', this.send_photo);

      console.log('photo: ', this.send_photo);

      const res = await axios.post("/send-message/from-buyer", data);
      console.log(res.data.message);
      if(res.data.message === "successful"){
        let id = this.$route.params.id;
        await this.returnUserInfo(id);
      }

      this.reset();
    },

    reset(){
      this.message_text = "";
      this.photo = null;
      this.send_photo = null;
    },

    async returnUserInfo(id){
      try{
        this.loading = true;
        const data = new FormData();
        data.append('id', id);
        data.append('sender_id', this.currentUser.id);

        const res = await axios.post("/shop/return-info", data);
        this.shop_info = res.data.info;
        this.messages = res.data.messages;
        console.log(this.messages);
        this.loading = false;
      }
      catch(error){
        this.loading = false;
        console.log(error);
      }
    }
  },
  async mounted(){
    const store = useDataStore();
    this.currentUser = store.currentUser_info;

    let id = this.$route.params.id;
    await this.returnUserInfo(id);

    let path = "message";
    this.$emit("changepathtext", path);
    console.log(path);
  }
}
</script>
<style scoped>
.view-full-container{
  position: fixed;
  left: 0;
  right: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  padding-top: 20%;
  background-color: rgba(0, 0, 0, 0.699);
  z-index: 3000;
}
.view-full-img{
  position: relative;
  display: flex;
  width: 300px;
  height: 300px;
  left: 0;
  right: 0;
  background-color: white;
  padding: 20px;
  border-radius: 20px;
  overflow: hidden;
}
.view-full-img img{
  width: 100%;
  height: 100%;
  position: relative;
  display: flex;
  object-fit: contain;
}
.message-pic{
  width: 100px;
  height: 100px;
  padding-right: 10px;
  padding-top: 5px;
}
.edit-info label{
  font-size: 12px;
}
.edit-info{
  background-color: rgb(221, 221, 221);
  width: 100%;
  height: auto;
  border-radius: 20px;
  margin-bottom: 10px;
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  gap: 20px;
  padding: 10px;
  box-sizing: border-box;
}
.send-img{
  background-color: rgb(255, 255, 255);
  height: auto;
  display: flex;
  flex-direction: row;
  width: 70px;
  height: 70px;
  margin-bottom: 10px;
  overflow: hidden;
  box-sizing: border-box;
  border: 1px solid gray;
  padding: 5px;
  border-radius: 10px;
}
.send-img img{
  object-fit: contain;
  width: 100%;
  height: 100%;
}
.more-option{
  background-color: white;
  border-radius: 5px;
  position: absolute;
  left: 40%;
  top: 40%;
  padding: 10px;
  width: auto;
  box-shadow: 0 1px 4px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  gap: 10px;
}
.more-option label{
  text-decoration: underline;
}
.time{
  font-size: 10px;
  color: gray;
  padding-left: 10px;
  padding-right: 10px;
}
.message-content{
  width: 100%;
  height: auto;
  padding-bottom: 20px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.receiver-content{
  padding: 5px;
  padding-left: 15px;
  font-size: 10px;
  width: 150px;
  border: 1px solid rgb(212, 212, 212);
  border-radius: 20px;
  display: flex;
  flex-direction: column;
}
.sender-content{
  padding: 5px;
  padding-left: 15px;
  font-size: 10px;
  right: 0;
  width: 150px;
  border: 1px solid rgb(212, 212, 212);
  border-radius: 20px;
  background-color: rgb(10, 121, 225);
  color: white;
  display: flex;
  flex-direction: column;
}
.loading-content img{
    width: 100px;
    height: 100px;
}
.loading-content{
    width: 100%;
    height: auto;
    display: flex;
    align-items: center;
    justify-content: center;
}
.message-page {
  display: flex;
  flex-direction: column;
  height: 80vh;
  font-family: "Segoe UI", sans-serif;
  background-color: #f9f9f9;
  padding-top: 60px;
}

.header {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  background-color: white;
  box-shadow: 0 1px 4px rgba(0,0,0,0.1);
  justify-content: space-between;
}

.back-btn {
  font-size: 18px;
  background: none;
  border: none;
  cursor: pointer;
  margin-right: 10px;
}

.shop-info {
  display: flex;
  align-items: center;
  flex-grow: 1;
  gap: 10px;
}

.avatar {
  width: 36px;
  height: 36px;
  background-color: #ddd;
  border-radius: 50%;
  position: relative;
  display: flex;
  overflow: hidden;
}

.avatar img{
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.shop-name {
  font-weight: bold;
  font-size: 15px;
}

.last-active {
  font-size: 12px;
  color: #888;
}

.rate-shop {
  font-size: 13px;
  color: #D25E27;
  text-decoration: none;
  margin-left: auto;
  text-decoration: underline;
}

.messages {
  flex: 1;
  padding: 10px;
  overflow-y: auto;
}

.message-input {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 10px 12px;
  background-color: white;
  border-top: 1px solid #eee;
  box-shadow: 0 -1px 4px rgba(0, 0, 0, 0.2);
  position: fixed;
  bottom: 75px;
  width: 100%;
  left: 0;
  box-sizing: border-box;
}

.image-upload {
  margin-right: 10px;
  cursor: pointer;
  font-size: 22px;
}
.message-i {
  width: 100%;
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
}
.message-i input[type="text"] {
  flex-grow: 1;
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 20px;
  font-size: 14px;
  outline: none;
}

.send-btn {
  background-color: #D25E27;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 50%;
  margin-left: 10px;
  cursor: pointer;
}
</style>
