<template>
  <div class="main-container">
    <BuyerProfile v-if="show_profile" @goexit="goexit" @changepathtext="changepathtext"/>
    <BuyerNotification v-if="show_notify" @goexit="goexit" @changepathtext="changepathtext"/>
    <header :class="{hidden: ishidden}">
      <div class="header1" style="cursor: pointer;" @click="show_profile = true; show_notify = false;">
        <div class="profile-pic">
          <img src="../images/man.jpg">
        </div>
        <div class="profile-info" style="cursor: pointer;">
          <label style="cursor: pointer;">aurjohnmonte143</label>
          <label style="cursor: pointer;">aurjohnmonte2003@gmail.com</label>
        </div>
        <img src="../images/arrow-down.png" class="arrow-down" :class="{reverseArrow : show_profile}">
      </div>
      <div class="header2">
        <img src="../images/logo.png" class="bell-icon">
        <img src="../images/maps-and-flags.png" class="locate-icon">
        <img src="../images/bell.png" class="bell-icon" style="cursor: pointer" @click="show_notify = true; show_profile = false;">
      </div>
    </header>
    <div class="content">
      <router-view @changepathtext="changepathtext" @click="goexit"/>
    </div>

    <div class="navbar">

      <router-link to="/buyer/home" @click="clicked = 'home'">
        <div class="nav-group">
            <img src="../images/home (1).png" class="navbar-icon" v-if="clicked === 'home'">
            <img src="../images/home.png" class="navbar-icon" v-else>
            <label>Home</label>
        </div>
      </router-link>

      <router-link to="/buyer/message" @click="clicked = 'message'">
        <div class="nav-group">
            <img src="../images/paper-plane (1).png" class="navbar-icon" v-if="clicked === 'message'">
            <img src="../images/paper-plane.png" class="navbar-icon" v-else>
            <label>Message</label>
        </div>
      </router-link>

      <router-link to="/buyer/map" @click="clicked = 'map'">
        <div class="nav-group">
            <img src="../images/map (1).png" class="navbar-icon" v-if="clicked === 'map'">
            <img src="../images/map.png" class="navbar-icon" v-else>
            <label>Map</label>
        </div>
      </router-link>

      <router-link to="/buyer/following" @click="clicked = 'following'">
        <div class="nav-group">
            <img src="../images/friends (1).png" class="navbar-icon" v-if="clicked === 'following'">
            <img src="../images/friends.png" class="navbar-icon" v-else>
            <label>Following</label>
        </div>
      </router-link>

      <router-link to="/buyer/profile" @click="clicked = 'profile'">
        <div class="nav-group">
            <img src="../images/user (2).png" class="navbar-icon" v-if="clicked === 'profile'">
            <img src="../images/user (3).png" class="navbar-icon" v-else>
            <label>Profile</label>
        </div>
      </router-link>
    </div>
  </div>
</template>

<script>
import BuyerProfile from './buyer/pages/BuyerProfile.vue';
import BuyerNotification from './buyer/pages/BuyerNotification.vue';
export default {
    components: {
      BuyerProfile,
      BuyerNotification
    },
    data(){
        return{
            show_profile: false,
            show_notify: false,
            clicked: 'home',
            lastScroll: 0,
            ishidden: false,
        }
    },
    mounted(){
        console.log(this.$route.path)
        let path = this.$route.path;
        console.log(path)

        window.addEventListener('scroll', this.handleScroll);
    },
    methods: {
      hide_notify(){
        this.show_notify = false;
      },
      hide_profile(){
        this.show_profile = false;
      },
      goNotification(){

      },
      goexit(){
        this.show_profile = false;
        this.show_notify = false;
      },
      changepathtext(new_path){
          this.clicked = new_path;
      },

      handleScroll(){
        const currentScrollY = window.scrollY;

        if(currentScrollY > this.lastScroll){
          this.goexit();
        }

        this.lastScroll = currentScrollY;
      }
    }
}
</script>

<style scoped>
.reverseArrow{
  transform: scaleY(-1);
}
.main-container {
  position: relative;
  min-height: 100vh;
  padding-bottom: 60px;
  transition: transform 0.3s ease-in-out;
}

.content {
  
}
.locate-icon{
  border: 1px solid gray;
  width: 20px;
  height: 20px;
  padding: 5px;
  border-radius: 20px;
}
.hidden{
  transform: translateY(-100%);
}
.navbar {
  width: 100vw;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  padding: 20px;
  position: fixed;
  bottom: 0;
  left: 0;
  display: flex;
  justify-content: space-around;
  align-items: center;
  z-index: 50;
  box-sizing: border-box;
  align-items: center;
  background-color: white;
}
.navbar a{
    text-decoration: none;
    color: black;
}

.navbar-icon {
  width: 20px;
  height: 20px;
}

.nav-group {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 3px;
}

.nav-group label {
  font-size: 10px;
  font-weight: bolder;
  color: gray;
}

.router-link-active label{
    color: #D25E27;
}
.main-container header{
  background-color: white;
  border-bottom: 3px solid #D25E27;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  padding: 10px;
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  top: 0;
  position: fixed;
  width: 100vw;
  padding-left: 20px;
  padding-right: 20px;
  box-sizing: border-box;
  display: flex;
  z-index: 100;
}
.profile-pic{
  width: 40px;
  height: 40px;
  border-radius: 30px;
  overflow: hidden;
  position: relative;
}
.profile-pic img{
  height: 100%;
  width: 100%;
  object-fit: cover;
}
.profile-info{
  font-size: 10px;
  display: flex;
  flex-direction: column;
  color: gray;
  justify-content: end;
}
.header1{
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
}
.arrow-down{
  width: 10px;
  height: 10px;
}
.bell-icon{
  width: 25px;
  height: 25px;
}
.header2{
  gap: 10px;
  display: flex;
  flex-direction: row;
  align-items: center;
}

</style>
