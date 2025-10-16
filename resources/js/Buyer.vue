<template>
  <div class="main-container" v-if="user">
    <ProfileModal v-if="show_profile" @goexit="goexit" @changepathtext="changepathtext" @stopLocation="stopLocation"/>
    <BuyerNotification v-if="show_notify" @goexit="goexit" @changepathtext="changepathtext" @modifyseen="modifyseen" :notifications="notifications"/>
    <header :class="{hidden: ishidden}">
      <div class="header1" style="cursor: pointer;" @click="show_profile = !show_profile; show_notify = false;">
        <div class="profile-pic">
          <img :src="'/'+store.currentUser_info.profile">

        </div>
        <div class="profile-info" style="cursor: pointer;">
          <label style="cursor: pointer;">{{ user.name }}</label>
          <label style="cursor: pointer;">{{ user.email }}</label>
        </div>
        <img src="../images/arrow-down.png" class="arrow-down" :class="{reverseArrow : show_profile}">
      </div>
      <div class="header2">
        <img src="../images/logo.png" class="bell-icon">
        <img src="../images/maps-and-flags.png" class="locate-icon" @click="locateCurrent">

        <img :src="bell_type" class="bell-icon" style="cursor: pointer" @click="show_notify = !show_notify; show_profile = false;">
      </div>
    </header>
    <div class="content" v-if="show_content">
      <router-view @changepathtext="changepathtext" @click="goexit" @stopLocation="stopLocation"/>
    </div>

    <div class="navbar">

      <router-link to="/buyer/home" @click="clicked = 'home'">
        <div class="nav-group">
            <img src="../images/home (1).png" class="navbar-icon" v-if="clicked === 'home'">
            <img src="../images/home.png" class="navbar-icon" v-else>
            <label :class="clicked === 'home' ? 'clicked' : 'notclicked'">Home</label>
        </div>
      </router-link>

      <router-link to="/buyer/message" @click="clicked = 'message'">
        <div class="nav-group">
            <img src="../images/paper-plane (1).png" class="navbar-icon" v-if="clicked === 'message'">
            <img src="../images/paper-plane.png" class="navbar-icon" v-else>
            <label :class="clicked === 'message' ? 'clicked' : 'notclicked'">Message</label>
        </div>
      </router-link>

      <router-link to="/buyer/map" @click="clicked = 'map'">
        <div class="nav-group">
            <img src="../images/map (1).png" class="navbar-icon" v-if="clicked === 'map'">
            <img src="../images/map.png" class="navbar-icon" v-else>
            <label :class="clicked === 'map' ? 'clicked' : 'notclicked'">Map</label>
        </div>
      </router-link>

      <router-link to="/buyer/following" @click="clicked = 'following'">
        <div class="nav-group">
            <img src="../images/friends (1).png" class="navbar-icon" v-if="clicked === 'following'">
            <img src="../images/friends grey.png" class="navbar-icon" v-else>
            <label :class="clicked === 'following' ? 'clicked' : 'notclicked'">Following</label>
        </div>
      </router-link>

      <router-link to="/buyer/profile" @click="clicked = 'profile'">
        <div class="nav-group">
            <img src="../images/user (2).png" class="navbar-icon" v-if="clicked === 'profile'">
            <img src="../images/user (3).png" class="navbar-icon" v-else>
            <label :class="clicked === 'profile' ? 'clicked' : 'notclicked'">Profile</label>
        </div>
      </router-link>
    </div>
  </div>
</template>

<script>
import ProfileModal from './buyer/profile/ProfileModal.vue';
import BuyerNotification from './buyer/pages/BuyerNotification.vue';
import { useDataStore } from './stores/dataStore';
import L from "leaflet";
import axios from 'axios';
import { watch } from 'vue';

export default {
    components: {
      ProfileModal,
      BuyerNotification
    },
    data(){
        return{
            store: useDataStore(),
            bell_type: '/images/bell (1).png',
            show_content: false,
            show_profile: false,
            show_notify: false,
            clicked: 'home',
            lastScroll: 0,
            ishidden: false,
            notifications: [],
            shops: null,
            user: null,
            watchID: null,
            messageListener: null,
            notifyListener: null,
        }
    },

    async mounted(){
      
        await this.returnUserInfo();
        await this.returnShops();
        await this.returnNotifications();

        console.log(this.$route.path)
        let path = this.$route.path;
        console.log(path);

        console.log(`message.${this.user.name}`)

        this.messageListener = Echo.channel(`message.${this.user.name}`);

        this.messageListener.listen('.message.sent', async (event) => {
                console.log('HAAAAAAAAAAA NEH AGI DIRIAAAAAAAAAAAAAAAAAAAA');
                await this.returnNotifications();
        });

        this.notifyListener = Echo.channel(`notify.${this.user.name}`);
        this.notifyListener.listen('.notify.sent', async(event) => {
            console.log('neh agi diria soysoy pangit');
            await this.returnNotifications();
        });

        // Echo.channel(`notify.${this.user.name}`)
        //     .listen('.notify.sent', async(event) => {
        //         console.log('neh agi diria soysoy pangit');
        // });

        window.addEventListener('scroll', this.handleScroll);
        this.show_content = true;

        watch(
          () => this.store.user_location,
          (newloc) => {
            if(newloc.latitude === null && newloc.longitude === null){
                
                this.stopLocation();
            }
          }
        )
    },
    unmounted(){
      this.notifyListener = null;
      this.messageListener = null;
    },
    methods: {

      checkNotify(){
        for(let notify of this.notifications){
          if(notify.seen === 0){
            this.bell_type = "/images/notification.png";
            return;
          }
        }

        this.bell_type = "/images/bell (1).png";
        return;
      },

      modifyseen(notif_id){
        
        for(let notif of this.notifications){
          
          if(notif.id === notif_id){
            notif.seen = 1;
          }
        }
      
        this.checkNotify();
      },
      async returnNotifications(){

        const res = await axios.get('/return/notifications', {
          params: {
            id: this.store.currentUser_info.id,
            type: 'buyer',
          }
        });
        this.notifications = res.data.message;

        console.log('notifications: ', this.notifications);

        this.store.setNotifications(res.data.message);
        
        console.log(res.data.message);
        this.checkNotify();
      },

      async returnShops(){
        
        const res = await axios.get("/return/shop");

        console.log(res.data.shops);
        this.shops = res.data.shops;
      },

      detectNearbyShops(lat, lng){

        const store = useDataStore();

        if(this.shops){

          let nearbyShops = [];

          const userLatLng = L.latLng(lat, lng);
          nearbyShops = this.shops.filter(shop => {
            const shopLatLng = L.latLng(shop.latitude, shop.longitude);
            const distance = userLatLng.distanceTo(shopLatLng); // in meters

            return distance <= store.currentUser_info.nearby_km * 1000; // x km
          });

          console.log("nearby shops: ", nearbyShops);

          this.store.setNearbyShops(nearbyShops);
        }
      },

      stopLocation(){
        console.log('STOOOOOOOOOOOOOOOOOOOOOOP')
        navigator.geolocation.clearWatch(this.watchID);
      },

      locateCurrent(){

        console.log('locate');
        if (!navigator.geolocation) {
          alert("Geolocation is not supported by your browser.")
          return
        }

        //i need to clearWatch because it need to stop the previous watchPosition before starting again to avoid error.
        navigator.geolocation.clearWatch(this.watchID);

        this.watchID = navigator.geolocation.watchPosition(
          (position) => {
            console.log('hello');
            
            this.store.setUser_location(position.coords.latitude, position.coords.longitude);

            console.log('user location: ',this.store.user_location);

            this.detectNearbyShops(position.coords.latitude, position.coords.longitude);
          },
          (error) => {

            alert("Unable to retrieve your location. You may have denied permission.")
            console.error(error)
          },
          {

            enableHighAccuracy: true,
            maximumAge: 1000,     // Accept cached position max 1 second old
            timeout: 10000        // Timeout for getting position
          }
        )
      },

      async returnUserInfo(){
        const res = await axios.post("/return/user-buyer/info");
        
        this.store.setUserInfo(res.data.message);

        console.log("set info in store: ", this.store.currentUser_info);

        this.user = res.data.message;
      },

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
          console.log("clicked: ", this.clicked);
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
  background: #323232;
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

.notclicked {
  font-size: 10px;
  font-weight: bolder;
  color: gray;
}

.clicked{
  color: #D25E27;
  font-size: 10px;
  font-weight: bolder;
}

.router-link-active label{
    color: #D25E27;
}
.main-container header{
  background: #323232;
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

@media (min-width: 768px){
  .main-container header{
    padding-left: 100px;
    padding-right: 100px;
    padding-top: 10px;
    padding-bottom: 10px;
  }
  .header2{
    gap: 20px;
  }
}

</style>
