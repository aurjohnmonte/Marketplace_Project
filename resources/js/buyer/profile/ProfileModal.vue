<template>
    <teleport to="body">
      <!-- <div class="overlay" @click="goexit()"></div> -->
      <div class="profile-container">
        <div class="profile-header">
          <label class="header-profile-text">Profile</label>
        </div>
        <div class="profile-content">
          <label @click="$router.push({name: 'BuyerProfile'})">Account Information</label>
          <label @click="$router.push({name: 'BuyerProfile'})">Account Setting</label>
        </div>
        <div class="profile-content" style="position: absolute; bottom: 0; margin-bottom: 20px;">
          <label @click="goLogout">Logout</label>
        </div>
      </div>
    </teleport>
</template>

<script>
import { useDataStore } from '../../stores/dataStore';

export default {
    methods: {
      goexit(){
        this.$emit("goexit");
      },
      goLogout(){
        const store = useDataStore();
        const id = store.currentUser_info.id;
        this.$emit('stopLocation');
        store.reset();
        window.location.href=`/buyer/logout?id=${id}`;
      }
    },
    mounted(){
        let path = this.$route.path;
        let new_path = path.slice(7);
        this.$emit("changepathtext", new_path);
    }
}
</script>

<style scoped>
.profile-content{
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.profile-content label{
  text-decoration: underline;
  color: rgb(78, 78, 78);
  font-size: 12px;
}

.profile-header{
  color: rgb(44, 44, 44);
  display: flex;
  flex-direction: row;
  align-items: center;
  margin-bottom: 20px;
}
.header-profile-text{
  font-weight: bolder;
}
.overlay{
  position: fixed;
  width: 100vw;
  height: 100vh;
  left: 0;
  top: 0;
  z-index: 999; 
}
.profile-container{
  position: fixed;
  width: 200px;
  height: 120px;
  background-color: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  top: 10%;
  left: 2%;
  border-radius: 5px;
  z-index: 1000;
  padding: 20px;
  display: flex;
  flex-direction: column;
}
@media (min-width: 768px){
  .profile-container{
    left: 8%;
    top: 8%;
    width: 300px;
    height: 220px;
    font-size: 20px;
  }
  .profile-content label{
    font-size: 15px;
  }
}
</style>