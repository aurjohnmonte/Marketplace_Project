<template>
    <div class="seller-container">
        <nav class="navbar navbar-light justify-content-between">
            <img src="../images/Logo-2.png" alt="Logo" class="logo">
            <a class="navbar-brand">{{ page }}</a>
            <div class="navbar-right">
                <div class="profile-circle" @click="toggleProfileBox">
                    <i class="fa-regular fa-circle-user"></i>
                </div>
                <div v-if="showProfileBox" class="floating-box profile-box">
                    <p>Profile Options</p>
                    <router-link to="/seller/profile">View Profile</router-link>
                    <router-link to="/seller/settings">Settings</router-link>
                </div>
                <span @click="toggleNotifBox" style="cursor:pointer">
                  <i class="fa-solid fa-bell"></i>
                </span>
                <div v-if="showNotifBox" class="floating-box notif-box">
                    <p>Notifications</p>
                    <router-link to="/seller/notifications">View All</router-link>
                </div>
                <form method="GET" action="/seller/logout">
                    <button type="submit">Logout</button>
                </form>
            </div>
        </nav>


        <div class="sidebar">
            <button @click="changePage('Dashboard')">
              <router-link to="/seller/dashboard">Dashboard</router-link>
            </button>
            <button @click="changePage('Products')">
              <router-link to="/seller/products">Products</router-link>
            </button>
            <button @click="changePage('Notifications')">
              <router-link to="/seller/notifications">Notifications</router-link>
            </button>
            <button @click="changePage('Messages')">
              <router-link to="/seller/messages">Messages</router-link>
            </button>
            <button @click="changePage('Map')">
              <router-link to="/seller/map">Map</router-link>
            </button>
            <button @click="changePage('Profile')">
              <router-link to="/seller/profile">Profile</router-link>
            </button>
            <button @click="changePage('Followers')">
              <router-link to="/seller/followers">Followers</router-link>
            </button>
        </div>
        <!-- THIS IS WHERE THE COMPONENT APPEAR WHEN CLICKED ROUTER-LINK / OR THE RESULT WHEN CLICKED. -->
        <div class="content">
            <router-view></router-view>
        </div>

        <!---- THIS IS THE EXAMPLE OF MAP. FREE TO REMOVE THIS
         <h1>example of map</h1>
        <examplemap/>
        END HERE FOR EXAMPLE OF MAP-->

    </div>
</template>
<script>
import examplemap from './maps/examplemap.vue';
export default{
    components: {
        examplemap
    },
    data(){
        return{
            page: 'Dashboard',
            showProfileBox: false,
            showNotifBox: false
        }
    },
    methods: {
        changePage(switchPage) {
            this.page = switchPage;
        },
        toggleProfileBox() {
            this.showProfileBox = !this.showProfileBox;
            this.showNotifBox = false;
        },
        toggleNotifBox() {
            this.showNotifBox = !this.showNotifBox;
            this.showProfileBox = false;
        }
    }
}
</script>
<style scoped>
.floating-box {
  position: absolute;
  top: 3.5em;
  right: 2em;
  min-width: 180px;
  background: #fff;
  color: #333;
  border-radius: 8px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.15);
  z-index: 1000;
  padding: 1em;
  display: flex;
  flex-direction: column;
  gap: 0.5em;
}
.profile-box {
  top: 3.5em;
  right: 7em;
}
.notif-box {
  top: 3.5em;
  right: 2em;
}
.logo {
    width: 50px;
}
.seller-container {
    display: grid;
    grid-template-areas:
        'navbar navbar'
        'sidebar content';
    grid-template-columns: 15em 1fr;
    grid-template-rows: auto 1fr;
    gap: 0;
    height: 100vh;
}

.navbar {
    grid-area: navbar;
    background-color: #4A4947;
    color: #FAF7F0;
}

.navbar-right {
    display: flex;
    gap: 1em;
}
.navbar-right button {
    background-color: transparent;
    border: none;
    color: #FAF7F0;
}

.sidebar {
    grid-area: sidebar;
    display: flex;
    flex-direction: column;
    justify-content: space-evenly;
    width: 100%;
    min-width: 15em;
    align-items: stretch;
    height: 100%;
    background: #B17457;
    padding: 1rem 0;
}


.sidebar button {
  width: 80%;
  margin: 0 auto;
  padding: 0.75rem 0;
  font-size: 1rem;
  background: #fff;
  color: #5c7691;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}

.sidebar button:hover {
  background: #e0e6ed;
  color: #3a4a5a;
}

.content {
    grid-area: content;
    height: 100%;
    background-color: #FAF7F0;
    width: 100%;
}

.sidebar a,
.floating-box a {
  color: #4A4947; /* your desired color */
  text-decoration: none;
}


</style>
