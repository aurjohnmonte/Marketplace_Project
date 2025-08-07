<template>
  <!-- Montserrat Google Fonts import for modern navbar font -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <div :class="['seller-container', showMenu ? 'with-sidebar' : 'no-sidebar']">
        <teleport to="body">
            <NewMessage :notifymessage="notifymessage" v-if="is_visible" @hidenotify="is_visible = false"/>
        </teleport>
        <nav class="navbar navbar-light justify-content-between">
            <div class="navbar-left">
                <button class="mobile-menu-toggle" @click="toggleMenu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h3 class="brand-title">Timbershoppe</h3>
            </div>
            <a class="navbar-brand">{{ $route.name === 'AddProduct' ? 'Products' : $route.name }}</a>
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
                <form method="GET" action="/seller/logout" class="desktop-logout">
                    <button type="submit">Logout</button>
                </form>
            </div>
        </nav>

        <!-- Mobile overlay for sidebar -->
        <div v-if="showMenu && isMobile" class="mobile-overlay" @click="toggleMenu"></div>

        <div :class="['sidebar', { 'show': showMenu && isMobile }]" v-if="showMenu || (!isMobile && showMenu)">
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
                        :class="{ active: $route.name === 'Dashboard' }">
                    <img src="../images/apps.png" alt="">
                    Dashboard</button>
                </router-link>
                <router-link to="/seller/products">
                    <button :class="{ active: $route.name === 'Products' || $route.name === 'AddProduct' }">
                        <i class="fa-solid fa-bag-shopping"></i>
                        Products
                    </button>
                </router-link>
                <router-link to="/seller/notifications">
                    <button :class="{ active: $route.name === 'Notification' }">
                        <i class="fa-solid fa-bell"></i>
                        Notifications
                    </button>
                </router-link>
                <router-link to="/seller/messages">
                    <button :class="{ active: $route.name === 'Messages' }">
                        <i class="fa-solid fa-message"></i>
                        Messages
                    </button>
                </router-link>
                <router-link to="/seller/map">
                    <button :class="{ active: $route.name === 'Map' }">
                        <i class="fa-solid fa-location-dot"></i>
                        Map
                    </button>
                </router-link>
                <router-link to="/seller/profile">
                    <button :class="{ active: $route.name === 'Profile' }">
                        <i class="fa-solid fa-user"></i>
                        Profile
                    </button>
                </router-link>
                <router-link to="/seller/followers">
                    <button :class="{ active: $route.name === 'Followers' }">
                        <img src="../images/friends (white).png" alt="">
                        Followers</button>
                </router-link>
            </div>
        </div>
        <div v-else-if="!isMobile" class="sidebar-minimized">
            <div class="sidebar-minimized-header">
                <img src="../images/Logo-2.png" alt="Logo" class="logo-minimized">
            </div>
            <div class="sidebar-minimized-btns">
                <router-link to="/seller/dashboard">
                    <i class="fa-solid fa-table-cells-large" :class="{ active: $route.name === 'Dashboard' }" title="Dashboard"></i>
                </router-link>
                <router-link to="/seller/products">
                    <i class="fa-solid fa-bag-shopping" :class="{ active: $route.name === 'Products' || $route.name === 'AddProduct' }" title="Products"></i>
                </router-link>
                <router-link to="/seller/notifications">
                    <i class="fa-regular fa-bell" :class="{ active: $route.name === 'Notification' }" title="Notifications"></i>
                </router-link>
                <router-link to="/seller/messages">
                    <i class="fa-regular fa-message" :class="{ active: $route.name === 'Messages' }" title="Messages"></i>
                </router-link>
                <router-link to="/seller/map">
                    <i class="fa-solid fa-location-dot" :class="{ active: $route.name === 'Map' }" title="Map"></i>
                </router-link>
                <router-link to="/seller/profile">
                    <i class="fa-regular fa-user" :class="{ active: $route.name === 'Profile' }" title="Profile"></i>
                </router-link>
                <router-link to="/seller/followers">
                    <i class="fa-solid fa-user-group" :class="{ active: $route.name === 'Followers' }" title="Followers"></i>
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
import axios from 'axios';
import NewMessage from './seller/notifications/NewMessage.vue';
import { useDataStore } from './stores/dataStore';

export default{
    components: {
        examplemap,
        NewMessage
    },
    data(){
        return{
            is_visible: false,
            notifymessage: 'Notification: New Message',
            page: 'Dashboard',
            showMenu: true,
            user: [],
            showProfileBox: false,
            showNotifBox: false,
            isMobile: false
        }
    },
    mounted() {
        this.checkScreenSize();
        window.addEventListener('resize', this.checkScreenSize);
        this.initializeUser();
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.checkScreenSize);
    },
    methods: {
        async returnUserInfo(){
            const res = await axios.post("/return/user-seller/info");

            const store = useDataStore();

            console.log(res.data.message);
            this.user = res.data.message;
            store.setUserInfo(res.data.message);
            console.log('id: ', store.currentUser_info.id);
        },
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
        },
        checkScreenSize() {
            this.isMobile = window.innerWidth <= 768;
            if (this.isMobile) {
                this.showMenu = false;
            }
        },
        async initializeUser(){
            await this.returnUserInfo();
            console.log(`message.${this.user.name}`)

            Echo.channel(`message.${this.user.name}`)
                .listen('.message.sent', (event) => {
                    console.log('NEEEH AGIIIIIII');
                    this.notifymessage = "Notification: New Message";
                    this.is_visible = true;
            });
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

/* Mobile responsive grid */
@media (max-width: 768px) {
    .seller-container {
        grid-template-areas:
            'navbar'
            'content';
        grid-template-columns: 1fr;
        grid-template-rows: auto 1fr;
    }

    .seller-container.with-sidebar {
        grid-template-areas:
            'navbar'
            'content';
        grid-template-columns: 1fr;
    }

    .seller-container.no-sidebar {
        grid-template-areas:
            'navbar'
            'content';
        grid-template-columns: 1fr;
    }
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
    padding: 0 1rem;
}

.navbar-right,
.navbar-left {
    display: flex;
    gap: 1em;
    align-items: center;
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

/* Mobile navbar adjustments */
@media (max-width: 768px) {
    .navbar {
        padding: 0 0.5rem;
    }

    .brand-title {
        font-size: 1.2em !important;
        margin: 0 0.5em 0 0 !important;
    }

    .navbar-brand {
        font-size: 1em;
        display: none;
    }

    .navbar-right {
        gap: 0.5em;
    }

    .desktop-logout {
        display: none;
    }

    .mobile-menu-toggle {
        display: block;
        background: none;
        border: none;
        color: #323232;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0.5rem;
    }
}

@media (min-width: 769px) {
    .mobile-menu-toggle {
        display: none;
    }
}

/* sidebar designs */
.sidebar {
    grid-area: sidebar;
    min-width: 10em;
    height: 100%;
    background: #323232;
    padding: .5rem;
    box-shadow: 2px 0 12px rgba(0,0,0,0.08);
    z-index: 1200;
}

/* Mobile sidebar */
@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 200px;
        height: 100vh;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 1500;
        display: block;
    }

    .sidebar.show {
        transform: translateX(0);
    }

    .mobile-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1400;
    }
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

/* Mobile sidebar buttons */
@media (max-width: 768px) {
    .sidebar-btn button {
        padding: 1rem .5rem;
        font-size: 12px;
    }
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

/* Hide minimized sidebar on mobile */
@media (max-width: 768px) {
    .sidebar-minimized {
        display: none;
    }
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

.content {
    grid-area: content;
    height: 100%;
    background-color: #faf4f0;
    width: 100%;
    overflow-y: auto;
}

/* Mobile content adjustments */
@media (max-width: 768px) {
    .content {
        padding: 0.5rem;
    }
}

/* Floating boxes responsive */
@media (max-width: 768px) {
    .floating-box {
        right: 1em;
        min-width: 160px;
        font-size: 14px;
    }

    .profile-box {
        right: 5em;
    }
}

/* Tablet responsive adjustments */
@media (min-width: 769px) and (max-width: 1024px) {
    .seller-container.with-sidebar {
        grid-template-columns: 10em 1fr;
    }

    .sidebar-btn button {
        width: 7rem;
        padding: 0.8rem;
        font-size: 12px;
    }

    .brand-title {
        font-size: 1.4em !important;
    }
}

/* Large screen adjustments */
@media (min-width: 1025px) {
    .seller-container.with-sidebar {
        grid-template-columns: 14em 1fr;
    }

    .sidebar-btn button {
        width: 9rem;
        padding: 1.2rem;
        font-size: 14px;
    }
}
</style>
