<template>
  <div class="main-container">


      <div class="overlay" v-if="show_profile" @click="show_profile = false;">
        <img :src="'/'+this.store.currentUser_info.profile">
      </div>




      <header>
        <h3>Profile</h3>
      </header>
      <main v-if="user">
        <div class="main1">
          <img :src="'/'+user.profile" @click="show_profile = true;">
          <label class="change-text" for="profile">Change Profile Photo <input type="file" id="profile" hidden @change="handleUpload" accept="image/*"></label>
          <label style="margin-top: 10px;">Profile Information</label>
          <form @submit.prevent="gosave_info">
            <div class="user-info">
              <label>Name: </label><input type="text" :value="user.firstname + ' ' + user.lastname" disabled><label></label>
              <label>Email: </label><input type="text" :value="user.email" disabled><label></label>
              <label>Gender: </label><input type="text" v-model="user_info.gender" :disabled="!edit_info.includes('gender')">
              <div>
                <img src="../../../images/cancel (1).png" style="width: 10px; height: 10px;" v-if="edit_info.includes('gender')" @click="cancelEdit('gender')">
                <label class="change-text" @click="addEditInfo('gender')" v-else>Change</label>
              </div>
              <label>Birthday: </label><input type="date" v-model="user_info.birthday" :disabled="!edit_info.includes('birthday')">
              <div>
                <img src="../../../images/cancel (1).png" style="width: 10px; height: 10px;" v-if="edit_info.includes('birthday')" @click="cancelEdit('birthday')">
                <label class="change-text" @click="addEditInfo('birthday')" v-else>Change</label>
              </div>
              <label>Age: </label><input type="number" v-model="user_info.age" :disabled="!edit_info.includes('age')">
              <div>
                <img src="../../../images/cancel (1).png" style="width: 10px; height: 10px;" v-if="edit_info.includes('age')" @click="cancelEdit('age')">
                <label class="change-text" @click="addEditInfo('age')" v-else>Change</label>
              </div>
              <label>Contact: </label><input type="text" v-model="user_info.contact_no" :disabled="!edit_info.includes('contact')">
              <div>
                <img src="../../../images/cancel (1).png" style="width: 10px; height: 10px;" v-if="edit_info.includes('contact')" @click="cancelEdit('contact')">
                <label class="change-text" @click="addEditInfo('contact')" v-else>Change</label>
              </div>
              <label>Nearby Shop (km): </label><input type="number" v-model="user_info.nearby_km" :disabled="!edit_info.includes('nearby_km')">
              <div>
                <img src="../../../images/cancel (1).png" style="width: 10px; height: 10px;" v-if="edit_info.includes('nearby_km')" @click="cancelEdit('nearby_km')">
                <label class="change-text" @click="addEditInfo('nearby_km')" v-else>Change</label>
              </div>
            </div>
            <input type="submit" style="margin-top: 20px; font-size: 12px; background-color: green; color: white;" v-if="edit_info.length > 0">
          </form>


          <label style="margin-top: 20px;">Credentials</label>
          <form @submit.prevent="gosave_credential">
            <div class="user-info">
              <label>Username: </label><input type="text" v-model="user_credential.username" :disabled="!edit_credential.includes('username')">
              <div>
                <img src="../../../images/cancel (1).png" style="width: 10px; height: 10px;" v-if="edit_credential.includes('username')" @click="cancel_editCredential('username')">
                <label class="change-text" @click="addEditCredential('username')" v-else>Change</label>
              </div>
              <label>New Password: </label><input type="password" v-model="user_credential.password" :disabled="!edit_credential.includes('password')" style="background-color: #F6F6F6;">
              <div>
                <img src="../../../images/cancel (1).png" style="width: 10px; height: 10px;" v-if="edit_credential.includes('password')" @click="cancel_editCredential('password')">
                <label class="change-text" @click="addEditCredential('password')" v-else>Change</label>
              </div>
              <label v-if="show_confirm_pass">Confirm Password: </label><input type="password" v-model="user_credential.confirm_password"  v-if="show_confirm_pass" style="background-color: #F6F6F6;">
            </div><br>
            <label class="message-pass" v-if="show_confirm_pass" :style="{color: returnColor}">{{ message_text }}</label><br>
            <input type="submit" style="margin-top: 20px; font-size: 12px;" v-if="edit_credential.length > 0" :disabled="!valid_password">
          </form>
        </div>
      </main>

      <main class="main1">
        <label style="margin-top: 10px; margin-bottom: 10px;">Account Setting</label>
        <label class="label-setting" @click="$router.push({name: 'AccountSetting'})" style="color: yellowgreen;">Deactivate Account</label>
        <label class="label-setting" @click="$router.push({name: 'DeleteAccount'})" style="color: red;">Delete Account</label><br>
        <label class="label-setting">Logout</label>
      </main>
  </div>
</template>

<script>
import { useDataStore } from '../../stores/dataStore';

export default {
  data(){
    return{
      valid_password: true,
      message_text: "",
      show_profile: false,
      profile: {
        file: null,
        preview: null,
      },
      user_credential: {
        password: '',
        confirm_password: '',
        username: '',
      },
      user_info: {
        gender: '',
        birthday: '',
        age: 0,
        contact_no: '',
        nearby_km: 0,
      },
      store: useDataStore(),
      user: null,
      show_confirm_pass: false,
      edit_info: [],
      edit_credential: [],
    }
  },
  computed: {
    returnColor(){

      if(this.valid_password){
        return "green";
      }
      else{
        return "red";
      }
    }
  },
  methods: {
    async gosave_credential(){
      
      const data = new FormData();
      data.append('user_info', JSON.stringify(this.user_credential));
      data.append('email', this.user.email);
      console.log(this.user.email);

      const res = await axios.post('/buyer/edit-credential', data);

      this.message_text = "";

      this.user_credential = {
        password: '',
        confirm_password: '',
        username: res.data.username,
      };

      this.store.setUserInfo(res.data.user);
      this.user = this.store.currentUser_info;

      this.edit_credential = [];

      this.valid_password = false;
    },

    async gosave_info(){

      const data = new FormData();
      data.append('user_info', JSON.stringify(this.user_info));
      data.append('email', this.user.email);
      console.log(this.user.email);

      const res = await axios.post('/buyer/edit-info', data);

      this.user_info = res.data.info;

      this.store.setUserInfo(res.data.user);
      this.user = this.store.currentUser_info;

      this.edit_info = [];
    },

    async changeProfile(){

    },
    async handleUpload(e){

      const data = new FormData();

      console.log(e.target.files[0]);

      //return if no selected image or canceled;
      if(!e.target.files[0]){
        return;
      }

      data.append('file', e.target.files[0]);

      data.append('id', this.user.id);

      const res = await axios.post('/user/change-profile', data);

      this.store.currentUser_info.profile = res.data.message;

      console.log(res.data.message);
    },
    cancel_editCredential(info){
      if(info === 'password'){
        this.valid_password = true;
        this.show_confirm_pass = false;
        
        this.user_credential.password = "";
        this.user_credential.confirm_password = "";
      }
      else{
        this.user_credential[info] = this.user[info];  
      }
      
      if(this.edit_credential.includes(info)){
        let arr = this.edit_credential.filter(i => i !== info);
        this.edit_credential = arr;
      }
    },

    addEditCredential(credential){
      if(credential === 'password'){
        this.valid_password = false;
        this.show_confirm_pass = true;
      }
      if(this.edit_credential.includes(credential)){
        return;
      }
      this.edit_credential.unshift(credential);
    },

    cancelEdit(info){
      if(this.edit_info.includes(info)){
        this.user_info[info] = this.user[info];
        let arr = this.edit_info.filter(i => i !== info);
        this.edit_info = arr;
      }
    },

    addEditInfo(info){
      if(this.edit_info.includes(info)){
        return;
      }
      this.edit_info.unshift(info);
    },

    checkPassword(newval, password){

      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

      if(newval === ""){
        this.message_text = "";
        return;
      }

      if(!passwordRegex.test(newval)){
        this.valid_password = false;
        this.message_text = "Password is not strong";
        return;
      }

      if(newval !== password){
        this.valid_password = false;
        this.message_text = "Password is not matched";
      }
      else{
        this.valid_password = true;
        this.message_text = "Password is matched";
      }
    }
  },
  watch: {  
    'user_credential.password': function(newval) {

      this.checkPassword(newval, this.user_credential.confirm_password);
    },
    'user_credential.confirm_password': function(newval) {

      this.checkPassword(newval, this.user_credential.password);
    }
  },
  mounted(){
    this.user = this.store.currentUser_info;
    for(let key in this.user_info){
      this.user_info[key] = this.user[key];
    }

    this.user_credential.username = this.user.name;
    
    let path = this.$route.path;
    let new_path = path.slice(7);
    this.$emit("changepathtext", new_path);
  }
}
</script>

<style scoped>
.message-pass{
  font-size: 12px;
}
.overlay{
  position: fixed;
  width: 100%;
  height: 100vh;
  top: 0;
  left: 0;
  background-color: rgba(0, 0, 0, 0.483);
  z-index: 100;
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
}
.overlay img{
  width: 200px;
  height: 200px;
  padding: 20px;
  border-radius: 20px;
  background-color: white;
}
.label-setting{
  font-size: 12px;
  color: #D25E27;
  text-decoration: underline;
}
.user-info{
  margin-top: 10px;
  display: grid;
  grid-template-columns: 20% 60% 10%;
  align-items: center;
  gap: 5px;
}
.user-info input{
  font-size: 12px;
  border: none;
}
.user-info label{
  font-size: 12px;
}
.main-container{
  color: rgb(53, 51, 51);
}

.main1{
  display: flex;
  flex-direction: column;
  padding: 20px;
  gap: 10px;
  border-bottom: 10px solid rgb(202, 202, 202);
}
.change-text{
  font-size: 10px;
  text-decoration: underline;
  color: #D25E27;
}
.main1 img{
  width: 70px;
  height: 70px;
  padding: 5px;
  border: 5px solid rgb(222, 222, 222);
  border-radius: 70px;
}
.main{
  display: flex;
  flex-direction: column;
  height: 300px;
  background-color: red;
}
.main-container header{
  padding-top: 60px;
  padding-left: 20px;
}
header{
  margin-top: 20px;
  padding-bottom: 10px;
  border-bottom: 10px solid rgb(202, 202, 202);
}
</style>