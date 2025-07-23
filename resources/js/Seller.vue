<template>
  <!-- Montserrat Google Fonts import for modern navbar font -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <div :class="['seller-container', showMenu ? 'with-sidebar' : 'no-sidebar']">
        <nav class="navbar navbar-light justify-content-between">
            <div class="navbar-left">
                <h3>Timbershoppe</h3>
            </div>
            <a class="navbar-brand">{{ page }}</a>
            <div class="navbar-right">
                <div class="profile-circle" @click="toggleProfileBox">
                    <i class="fa-regular fa-circle-user" style="text-shadow: 0px 2px 2px rgba(0,0,0,0.4);"></i>
                </div>
                <div v-if="showProfileBox" class="floating-box profile-box">
                    <p>Profile Options</p>
                    <router-link to="/seller/settings">Account Setting</router-link>
                    <form method="GET" action="/seller/logout">
                        <button type="submit">Logout</button>
                    </form>
                </div>
                <span @click="toggleNotifBox" style="cursor:pointer">
                  <i class="fa-solid fa-bell" style="text-shadow: 0px 2px 2px rgba(0,0,0,0.5);"></i>
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


        <div v-if="showMenu" class="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <img src="../images/Logo-2.png" alt="Logo" class="logo">
                </div>
                <div v-if="showMenu === true" class="menu-icon">
                    <i class="fa-solid fa-xmark" @click="toggleMenu"></i>
                </div>
            </div>
            <div class="sidebar-btn">
                <router-link to="/seller/dashboard">
                    <button
                        :class="{ active: page === 'Dashboard' }"
                        @click="changePage('Dashboard')">
                    <img src="../images/apps.png" alt="">
                    Dashboard</button>
                </router-link>
                <router-link to="/seller/products">
                    <button :class="{ active: page === 'Products' }" @click="changePage('Products')">
                        <i class="fa-solid fa-bag-shopping"></i>
                        Products
                    </button>
                </router-link>
                <router-link to="/seller/notifications">
                    <button :class="{ active: page === 'Notifications' }" @click="changePage('Notifications')">
                        <i class="fa-regular fa-bell"></i>
                        Notifications
                    </button>
                </router-link>
                <router-link to="/seller/messages">
                    <button :class="{ active: page === 'Messages' }" @click="changePage('Messages')">
                        <i class="fa-regular fa-message"></i>
                        Messages
                    </button>
                </router-link>
                <router-link to="/seller/map">
                    <button :class="{ active: page === 'Map' }" @click="changePage('Map')">
                        <i class="fa-solid fa-location-dot"></i>
                        Map
                    </button>
                </router-link>
                <router-link to="/seller/profile">
                    <button :class="{ active: page === 'Profile' }" @click="changePage('Profile')">
                        <i class="fa-regular fa-user"></i>
                        Profile
                    </button>
                </router-link>
                <router-link to="/seller/followers">
                    <button :class="{ active: page === 'Followers' }" @click="changePage('Followers')">
                        <img src="../images/friends.png" alt="">
                        Followers</button>
                </router-link>
            </div>
        </div>
        <div v-else class="sidebar-minimized">
            <div class="sidebar-minimized-header">
                <img src="../images/Logo-2.png" alt="Logo" class="logo-minimized">
            </div>
            <div class="sidebar-minimized-btns">
                <router-link to="/seller/dashboard">
                    <i class="fa-solid fa-table-cells-large" :class="{ active: page === 'Dashboard' }" @click="changePage('Dashboard')" title="Dashboard"></i>
                </router-link>
                <router-link to="/seller/products">
                    <i class="fa-solid fa-bag-shopping" :class="{ active: page === 'Products' }" @click="changePage('Products')" title="Products"></i>
                </router-link>
                <router-link to="/seller/notifications">
                    <i class="fa-regular fa-bell" :class="{ active: page === 'Notifications' }" @click="changePage('Notifications')" title="Notifications"></i>
                </router-link>
                <router-link to="/seller/messages">
                    <i class="fa-regular fa-message" :class="{ active: page === 'Messages' }" @click="changePage('Messages')" title="Messages"></i>
                </router-link>
                <router-link to="/seller/map">
                    <i class="fa-solid fa-location-dot" :class="{ active: page === 'Map' }" @click="changePage('Map')" title="Map"></i>
                </router-link>
                <router-link to="/seller/profile">
                    <i class="fa-regular fa-user" :class="{ active: page === 'Profile' }" @click="changePage('Profile')" title="Profile"></i>
                </router-link>
                <router-link to="/seller/followers">
                    <i class="fa-solid fa-user-group" :class="{ active: page === 'Followers' }" @click="changePage('Followers')" title="Followers"></i>
                </router-link>
            </div>
            <div class="sidebar-minimized-footer">
                <i class="fa-solid fa-bars" @click="toggleMenu" title="Expand"></i>
            </div>
        </div>
        <!-- THIS IS WHERE THE COMPONENT APPEAR WHEN CLICKED ROUTER-LINK / OR THE RESULT WHEN CLICKED. -->
        <div class="content">
            <router-view></router-view>
        </div>

    </div>
</template>

<script>
import examplemap from './maps/examplemap.vue';
import '../css/app.css';

export default{
    components: {
        examplemap
    },
    data(){
        return{
            page: 'Dashboard',
            showProfileBox: false,
            showNotifBox: false,
            showMenu: true
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
        },
        toggleMenu() {
            this.showMenu = !this.showMenu;
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

/* parent container */
.seller-container {
    display: grid;
    grid-template-rows: auto 1fr;
    height: 100vh;
    overflow: hidden;
}

.seller-container.with-sidebar {
    grid-template-areas:
        'sidebar navbar'
        'sidebar content';
    grid-template-columns: 12em 1fr;
}

.seller-container.no-sidebar {
    grid-template-areas:
        'sidebar navbar'
        'sidebar content';
    grid-template-columns: 3.5em 1fr;
}

/* navbar designs */
.navbar {
    grid-area: navbar;
    background-color: #DDD0C8;
    color: #232323;
    font-family: 'Montserrat', Arial, Helvetica, sans-serif;
    font-weight: 600;
    font-style: normal;
    letter-spacing: 0.08em;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    min-height: 60px;
    align-items: center;
}

.navbar-right,
.navbar-left {
    display: flex;
    gap: 1em;
}

.navbar-left {
    align-items: center;
}
.navbar-left h3 {
    font-size: 1.6em;
    font-weight: 700;
    margin: 0 0.5em 0 0.2em;
    letter-spacing: 0.12em;
    color: #232323;
    text-transform: uppercase;
    line-height: 1;
}

.navbar-brand {
    font-size: 1.2em;
    font-weight: 500;
    color: #6d4c2c;
    margin: 0 auto;
    text-align: center;
    text-transform: capitalize;
}

.navbar-right button {
    background-color: transparent;
    border: none;
    color: #323232;
}

.navbar-right i,
.menu-icon i {
  font-size: 1.2rem;
}

.profile-circle i {
  font-size: 1.2rem;
}

.profile-box i,
.notif-box i {
  font-size: 3.7rem;
}

/* sidebar designs */
.sidebar {
    grid-area: sidebar;
    min-width: 10em;
    height: 100%;
    background: #323232;
    padding: .5rem;
    box-shadow: 2px 0 12px rgba(0,0,0,0.08);
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 2em;
  width: 100%;
}

.logo-container {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
}

.logo {
    width: 60px;
}

.menu-icon {
  color: #FAF7F0;
  cursor: pointer;
  margin-right: .5em;
}

.sidebar-btn {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    width: 100%;
    height: 100%;
    gap: 1.2rem;
}

.sidebar-btn button {
  width: 8rem;
  padding: 1rem;
  font-size: 13px;
  color: #FAF7F0;
  background-color: #ca9d73;
  border: none;
  border-radius: 10px;
  transition: background 0.2s, color 0.2s, box-shadow 0.2s;
  margin-bottom: 0.5rem;
  outline: none;
  box-shadow: none;
  outline: none;
  text-align: center;
  box-shadow: 6px 4px 1px rgba(37, 0, 0, 0.233);
}

.sidebar-btn button:hover {
  background-color: #DDD0C8;
}

.sidebar button.active {
  background-color: #c0673e;
  box-shadow: 8px 5px 1px rgba(58, 1, 1, 0.527);
}

.sidebar-btn button img {
    width: 16px;
    margin: 0 auto 0.3em auto;
}

.content {
    grid-area: content;
    height: 100%;
    background-color: #faf4f0;
    width: 100%;
}

.sidebar a,
.floating-box a {
  color: #4A4947;
  text-decoration: none;
}


.sidebar-minimized {
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  width: 3.5em;
  background: #323232;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  z-index: 1100;
  box-shadow: 2px 0 12px rgba(0,0,0,0.08);
}
.sidebar-minimized-header {
  margin-top: 1em;
}
.logo-minimized {
  width: 32px;
  height: 32px;
}
.sidebar-minimized-btns {
  display: flex;
  flex-direction: column;
  gap: 2em;
  margin-top: 2em;
}
.sidebar-minimized-btns i {
  color: #FAF7F0;
  font-size: 1.4em;
  cursor: pointer;
  transition: color 0.2s;
}
.sidebar-minimized-btns i.active {
  color: #c0673e;
}
.sidebar-minimized-btns i:hover {
  color: #DDD0C8;
}
.sidebar-minimized-footer {
  margin-bottom: 1.5em;
}
.sidebar-minimized-footer i {
  color: #FAF7F0;
  font-size: 1.3em;
  cursor: pointer;
}

</style>
