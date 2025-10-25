<template>
  <!-- Montserrat Google Fonts import for modern navbar font -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <div :class="['seller-container', showMenu ? 'with-sidebar' : 'no-sidebar']">
        <teleport to="body">
            <NewMessage :notifymessage="notifymessage" v-if="is_visible" @hidenotify="is_visible = false"/>

            <EditModals
                v-if="showEditModal"
                :type="editType"
                :shop="user.shop"
                :seller="user"
                @close="showEditModal = false"
            />
        </teleport>
        <nav class="navbar navbar-light justify-content-between">
            <div class="navbar-left">
                <button class="mobile-menu-toggle" @click="toggleMenu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h3 class="brand-title">Craftify</h3>
            </div>
            <a class="navbar-brand">{{ displayTitle }}</a>
            <div class="navbar-right" v-if="user.shop">
                <router-link to="/seller/messages" title="Messages" class="nav-icon">
                    <i class="fa-solid fa-message"></i>
                </router-link>

                <div class="profile-circle" @click="toggleProfileBox">
                    <img style="width: 30px; height: 30px; cursor: pointer;" :src="'/' + (user.shop && user.shop.profile_photo ? user.shop.profile_photo : (user.profile_photo || ''))" />
                </div>
                <div v-if="showProfileBox" class="floating-box profile-box">
                    <div class="profile-header" style="display:flex; gap:1em; align-items:center;">
                        <img :src="'/' + (user.shop && user.shop.profile_photo ? user.shop.profile_photo : (user.profile_photo || ''))" alt="Profile" style="width:60px; height:60px; border-radius:50%; object-fit:cover; background:#eee;" />
                        <div style="display:flex; flex-direction:column;">
                            <div style="font-weight:700; font-size:1rem; color:#333">{{ user.shop && user.shop.name ? user.shop.name : user.name }}</div>
                            <div style="font-size:12px; color:#666">{{ user.email || '' }}</div>
                        </div>
                    </div>

                    <div class="profile-details" style="margin-top:1rem; display:flex; flex-direction:column; gap:0.25rem; font-size:13px; color:#333;">
                        <ProfileInfo :shop="selectedShop" @edit-seller-info="changeSellerInformation"/>
                    </div>

                    <div class="change-info">
                        <div class="menu-item" @click="changeCoverPhoto">
                            <span>Change Cover Photo</span>
                        </div>
                        <div class="menu-item" @click="changeProfilePhoto">
                            <span>Change Profile Photo</span>
                        </div>

                        <!-- This accesses the EditModals.vue when clicked -->
                        <div class="menu-item" @click.stop="openEditModal('shop')">
                            <span>Change Shop Details</span>
                        </div>
                        <div class="menu-item" @click.stop="openEditModal('account')">
                            <span>Change Account Info</span>
                        </div>
                    </div>

                </div>
                <span @click="toggleNotifBox" class="notif-bell-container">
                    <img src="../images/bell (1).png" style="width: 1.5em;" alt="">
                    <span v-if="unread_notif" class="notif-dot"></span>
                </span>

                <div v-if="showNotifBox" class="floating-box notif-box">
                    <div style="width: 100%; display: flex; align-items: center; justify-content: space-between">
                        <p style="font-size: 20px;">Notifications</p>
                        <router-link to="/seller/notifications" style="text-decoration: underline;">View All</router-link>
                    </div>
                    <div class="notify-content">
                        <div class="notify-item" v-for="notif in notifications" :key="notif" @click="goNavigate(notif)">
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <div style="width: 5px; height: 5px; margin-bottom: .6em; border-radius: 10px; background-color: red;" v-if="notif.seen === 0"></div>
                                <label class="notify-text">{{ notif.text }}</label>
                            </div>
                            <label class="notify-time">{{ returnFormatTime(notif.created_at) }}</label>
                        </div>
                    </div>
                </div>
                <button @click="goLogout">Logout</button>
            </div>
        </nav>

        <!-- Mobile overlay for sidebar -->
        <div v-if="showMenu && isMobile" class="mobile-overlay" @click="toggleMenu"></div>

        <div :class="['sidebar', { 'show': showMenu && isMobile }]" v-if="showMenu || (!isMobile && showMenu)">
            <div class="sidebar-header">
                <div class="logo-container">
                    <img src="../images/Logo-minimized.svg" alt="Logo" class="logo">
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
                    <button :class="{ active: $route.name === 'Products' || $route.name === 'AddProduct' || $route.name === 'AddProduct' || $route.name === 'ViewProduct' }">
                        <i class="fa-solid fa-bag-shopping"></i>
                        Products
                    </button>
                </router-link>

                <router-link to="/seller/transaction-record">
                    <button :class="{ active: $route.name === 'TransactionRecord' || $route.name === 'AddRecord'}">
                        <i class="fa-solid fa-list"></i>
                        Transaction Record
                    </button>
                </router-link>

            <!-- Notification button (remove comment here as well as the router-link below)
                <router-link to="/seller/notifications">
                    <button :class="{ active: $route.name === 'Notification' || $route.name === 'ViewNotification' }" style="position: relative;">
                        <div style="width: 25px; height: 25px; border-radius: 25px; background-color: red; position: absolute; top: -10px; right: -10px;" v-if="unread_notif">

                        </div>
                        <i class="fa-solid fa-bell"></i>
                        Notifications
                    </button>
                </router-link>
             -->

            <!-- Message btn
                <router-link to="/seller/messages">
                    <button :class="{ active: $route.name === 'Messages' || $route.name === 'Chats' }">
                        <i class="fa-solid fa-message"></i>
                        Messages
                    </button>
                </router-link>
            -->
                <router-link to="/seller/map">
                    <button :class="{ active: $route.name === 'Map' }">
                        <i class="fa-solid fa-location-dot"></i>
                        Map
                    </button>
                </router-link>
            <!-- Router link to follower
                <router-link to="/seller/profile">
                    <button :class="{ active: $route.name === 'Profile' }">
                        <i class="fa-solid fa-user"></i>
                        Profile
                    </button>
                </router-link>
            -->

            <!--  Follower button
                <router-link to="/seller/followers">
                    <button :class="{ active: $route.name === 'Followers' }">
                        <img src="../images/friends (white).png" alt="">
                        Followers</button>
                </router-link>
            -->
            </div>
        </div>
        <div v-else-if="!isMobile" class="sidebar-minimized">
            <div class="sidebar-minimized-header">
                <img src="../images/Logo-minimized.svg" alt="Logo" class="logo-minimized">
            </div>
            <div class="sidebar-minimized-btns">
                <router-link to="/seller/dashboard">
                    <i class="fa-solid fa-table-cells-large" :class="{ active: $route.name === 'Dashboard' }" title="Dashboard"></i>
                </router-link>
                <router-link to="/seller/products">
                    <i class="fa-solid fa-bag-shopping" :class="{ active: $route.name === 'Products' || $route.name === 'AddProduct' || $route.name === 'ViewProduct' }" title="Products"></i>
                </router-link>


                <router-link to="/seller/transaction-record">
                    <i class="fa-solid fa-list" :class="{ active: $route.name === 'TransactionRecord' || $route.name === 'AddRecord'}" title="TransactionRecord"></i>
                </router-link>

            <!-- Notification button
                <router-link to="/seller/notifications">
                    <i class="fa-regular fa-bell" :class="{ active: $route.name === 'Notification' || $route.name === 'ViewNotification' }" title="Notifications"></i>
                </router-link>
            -->

            <!-- Message btn
                <router-link to="/seller/messages">
                    <i class="fa-regular fa-message" :class="{ active: $route.name === 'Messages' || $route.name === 'Chats' }" title="Messages"></i>
                </router-link>
                <router-link to="/seller/map">
                    <i class="fa-solid fa-location-dot" :class="{ active: $route.name === 'Map' }" title="Map"></i>
                </router-link>
            -->
            <!-- Router link to follower
                <router-link to="/seller/profile">
                    <i class="fa-regular fa-user" :class="{ active: $route.name === 'Profile' }" title="Profile"></i>
                </router-link>
            -->

            <!-- Router link to follower
                <router-link to="/seller/followers">
                    <i class="fa-solid fa-user-group" :class="{ active: $route.name === 'Followers' }" title="Followers"></i>
                </router-link>
            -->
            </div>
            <div class="sidebar-minimized-footer">
                <i class="fa-solid fa-bars" @click="toggleMenu" title="Expand"></i>
            </div>
        </div>
        <!-- THIS IS WHERE THE COMPONENT APPEAR WHEN CLICKED ROUTER-LINK / OR THE RESULT WHEN CLICKED. -->
        <div class="content">
            <router-view @checknotif="checknotif" @returnActiveStatus="returnActiveStatus" :active_status="active_status"></router-view>
        </div>

    </div>
</template>

<script>
import examplemap from './maps/examplemap.vue';
import '../css/app.css';
import axios from 'axios';
import NewMessage from './seller/notifications/NewMessage.vue';
import EditModals from './seller/profile/EditModals.vue';
import { useDataStore } from './stores/dataStore';
import ProfileInfo from './seller/profile/ProfileInfo.vue';
import router from './router';

export default{
    components: {
        examplemap,
        NewMessage,
        ProfileInfo,
        EditModals
    },
    data(){
        return{
            is_visible: false,
            notifymessage: 'You have new notification',
            page: 'Dashboard',
            showMenu: true,
            user: [],
            showProfileBox: false,
            showNotifBox: false,
            unread_notif: false,
            isMobile: false,
            active_status: null,
            messageEventListener: null,
            notifyEventListener: null,
            notifications: [],
            showEditModal: false,
            editType: null,
            recentNotifications: [],
        }
    },
    computed: {
        displayTitle(){
            if(this.$route.name === 'AddProduct' || this.$route.name === 'ViewProduct'){
                return 'Products';
            }
            if(this.$route.name === 'ViewNotification'){
                return 'Notification';
            }
            return this.$route.name;
        },
        selectedShop(){
            try{
                const store = useDataStore();
                return store.selected_shop || null;
            }catch(e){
                return null;
            }
        }
    },
    async mounted() {
        this.checkScreenSize();
        window.addEventListener('resize', this.checkScreenSize);
        await this.initializeUser();

        this.checkNotifications();

        // Ensure sidebar is expanded on desktop by default
        if (!this.isMobile) {
            this.showMenu = true;
        }

        // Use setTimeout to ensure DOM is ready
        setTimeout(() => {
        document.addEventListener('click', this.handleClickOutside);
        }, 100);
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.checkScreenSize);
        document.removeEventListener('click', this.handleClickOutside);
    },
    unmounted(){
        this.notifyEventListener = null;
        this.messageEventListener = null;
    },
    methods: {
        markAsRead(index) {
            if (this.recentNotifications[index].status === 'unread') {
                this.recentNotifications[index].status = 'read';
            }
        },
        returnColor(notif){
            if(!notif.seen){
                return '#F0AE73';
            }
        },

        openEditModal(type) {
            this.showProfileBox = false; // close dropdown
            this.editType = type;
            this.showEditModal = true;
        },
        returnFormatTime(datetime) {
            if (!datetime) return ''; // avoid undefined errors

            // Replace space with 'T' to make it ISO compliant
            const formatted = datetime.replace(' ', 'T');

            const date = new Date(formatted);

            if (isNaN(date.getTime())) {
                console.warn('Invalid datetime:', datetime);
                return 'Invalid Date';
            }

            const datePart = date.toLocaleDateString();
            const timePart = date.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true,
            });

            return `${datePart} - ${timePart}`;
        },

        returnActiveStatus(active_status){
            this.active_status = active_status;
            console.log('active status: ',this.active_status);
        },
        changeCoverPhoto() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = async (event) => {
                const file = event.target.files[0];
                if (file) {
                    const store = useDataStore();
                    const shop = store.selected_shop;

                    if (!shop || !shop.id) {
                        console.error("Shop not found or missing ID");
                        return;
                    }

                    const data = new FormData();
                    data.append('id', shop.id);
                    data.append('file', file);

                    try {
                        const res = await axios.post('/seller/change/cover-photo', data);
                        console.log(res.data.message);

                        shop.cover_photo = res.data.path;
                        this.user.shop.cover_photo = res.data.path;

                        // Optionally force reload image if cached
                        this.$forceUpdate();
                    } catch (error) {
                        console.error("Error updating cover photo:", error);
                    }
                }
            };
            input.click();
        },

        changeProfilePhoto() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = async (event) => {
                const file = event.target.files[0];
                if (file) {
                    const store = useDataStore();
                    const shop = store.selected_shop;

                    if (!shop || !shop.id) {
                        console.error("Shop not found or missing ID");
                        return;
                    }

                    const data = new FormData();
                    data.append('id', shop.id);
                    data.append('file', file);

                    try {
                        const res = await axios.post('/seller/change/profile-photo', data);
                        console.log(res.data.message);

                        shop.profile_photo = res.data.path;
                        this.user.shop.profile_photo = res.data.path;

                        this.$forceUpdate();
                    } catch (error) {
                        console.error("Error updating profile photo:", error);
                    }
                }
            };
            input.click();
        },

        checknotif(){
            this.checkNotifications();
        },

        async checkNotifications(){
            const store = useDataStore();
            const res = await axios.get('/check/notification', {
                params: {
                    id: store.currentUser_info.id,
                    type: 'seller',
                }
            });

            console.log(res.data.message);

            this.notifications = res.data.notifications;
            console.log('notifications: ',this.notifications);
            this.unread_notif = res.data.message;
        },

        goLogout(){

            console.log('here')
            const store = useDataStore();
            let id = store.currentUser_info.id;

            window.location.href = `/seller/logout?id=${id}`;
        },
        async returnUserInfo(){
            const res = await axios.post("/return/user-seller/info");

            const store = useDataStore();

            console.log('user: ',res.data.message);
            console.log('shop: ', res.data.shop);
            this.user = res.data.message;
            store.setUserInfo(res.data.message);
            store.setSelectedShop(res.data.shop);
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

        handleClickOutside(event) {
            // Check if click is on toggle buttons
            const profileToggle = event.target.closest('.profile-circle');
            const notifToggle = event.target.closest('.notif-bell-container');

            if (profileToggle || notifToggle) {
                return; // Don't close if clicking toggle buttons
            }

            // Check if click is inside the floating boxes
            const profileBox = this.$refs.profileBox;
            const notifBox = this.$refs.notifBox;

            if (profileBox && profileBox.contains(event.target)) return;
            if (notifBox && notifBox.contains(event.target)) return;

            // Close both boxes
            this.showProfileBox = false;
            this.showNotifBox = false;
            },

        checkScreenSize() {
            this.isMobile = window.innerWidth <= 768;
            if (this.isMobile) {
                this.showMenu = false;
            } else {
                // Ensure sidebar is expanded on desktop by default
                this.showMenu = true;
            }
        },

        async goNavigate(notif) {
            this.notif = notif;

            // Instantly mark as seen (frontend only)
            this.notifications = this.notifications.map(n =>
                n.id === notif.id ? { ...n, seen: 1 } : n
            );

            // Optionally notify backend (no need to wait)
            axios.get('/seen-notify', { params: { id: notif.id } }).catch(() => {
                console.warn('Failed to mark notification as seen (backend)');
            });

            const hasUnread = this.notifications.some(n => n.seen === 0);
            this.unread_notif = hasUnread;

            // Emit event if parent needs to react
            this.$emit('modifyseen', notif.id);

            // Handle navigation by type
            if (notif.type === 'message') {
                this.$router.push('/seller/messages');
            } else if (notif.type === 'order') {
                this.$router.push('/seller/transaction-record');
            } else if (notif.type === 'product') {
                this.$router.push('/seller/products');
            } else if (notif.type === 'review') {
                this.$router.push('/seller/notifications');
            } else {
                // Default fallback
                this.$router.push('/seller/notifications');
            }
        },

        async initializeUser(){
            await this.returnUserInfo();
            console.log(`message.${this.user.name}`)

            this.messageEventListener = Echo.channel(`message.${this.user.name}`);

            this.messageEventListener.listen('.message.sent', (event) => {
                                            this.unread_notif = true;
                                            console.log('NEEEH AGIIIIIII');
                                            this.notifymessage = "You have new notification. Check it out.";
                                            this.is_visible = true;
                                        });

            this.notifyEventListener = Echo.channel(`sellernotify.${this.user.name}`);

            this.notifyEventListener.listen('.sellernotify.sent', async(event) => {
                                            console.log('HELLO WORLDDDDDD');
                                            this.unread_notif = true;
                                            console.log('NEEEH AGIIIIIII');
                                            await this.checkNotifications();
                                            this.notifymessage = "You have new notification. Check it out.";
                                            this.is_visible = true;

                                            //update product
                                            const store = useDataStore();
                                            let id = store.currentUser_info.id;

                                            const data = new FormData();
                                            data.append('id', id);
                                            const res = await axios.post('/seller/return/products', data);

                                            const products = res.data.message;

                                            store.selected_shop.products = products;

                                            console.log('MAOH NEH PRODUCT SOY: ', store.selected_shop.products);
                                        });
        }
    }
}
</script>

<style scoped>
.menu-item{
    cursor: pointer;
}
.is_unread{
    background-color: rgb(168, 166, 166);
}
.floating-box {
    position: absolute;
    top: 3.5em;
    right: 2em;
    min-width: 450px;
    height: 300px;
    background: #fff;
    color: #333;
    border-radius: 8px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.15);
    z-index: 1000;
    padding: 2em 3em;
    display: flex;
    flex-direction: column;
    gap: 0.5em;
    overflow: auto;
    font-size: 12px;
}
.profile-box {
    top: 4.5em;
    right: 7em;
}

.change-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    place-content: center;
    place-items: center;
}
.notif-box {
    top: 4.5em;
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

/* Small mobile devices */
@media (max-width: 480px) {
    .seller-container {
        grid-template-rows: auto 1fr;
    }

    .navbar {
        min-height: 50px;
        padding: 0 0.25rem;
    }

    .brand-title {
        font-size: 1em !important;
        margin: 0 0.25em 0 0 !important;
    }

    .navbar-right {
        gap: 0.25em;
    }

    .floating-box {
        right: 0.5em;
        min-width: 140px;
        font-size: 12px;
    }

    .profile-box {
        right: 4em;
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

.nav-icon {
  color: #5A4635;
  display: flex;
  align-items: center;
}

.profile-circle img {
  border-radius: 50%;
}

.notif-bell-container {
  position: relative;
  cursor: pointer;
  display: inline-block;
  text-shadow: 0px 2px 2px rgba(0, 0, 0, 0.5);
}

.notif-bell-container img {
  filter: drop-shadow(0 1px 1px rgb(0, 0, 0));
}

/* The red dot */
.notif-dot {
  position: absolute;
  top: -2px;
  right: -2px;
  width: 10px;
  height: 10px;
  background-color: red;
  border-radius: 50%;
  border: 1px solid white; /* for a clean border on light backgrounds */
}

.notify-time{
    font-size: 1em;
    color: #64220385;
}
.notify-item{
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    padding: 10px;
    border-radius: 1em;
    gap: 2em;
}
.notify-item:hover{
  background-color: rgba(138, 138, 138, 0.134);
}
.notify-text{
    font-size: 10px;
}
.notify-content{
    display: flex;
    flex-direction: column;
}
.notify-header{
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    color: rgb(44, 44, 44);
    align-items: center;
}
.overlay{
  position: fixed;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.568);
  left: 0;
  top: 0;
  z-index: 999;
  user-select: none;
}
.notify-container{
  position: fixed;
  width: 300px;
  height: 300px;
  background-color: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  top: 10%;
  right: 2%;
  border-radius: 5px;
  overflow-y: auto;
  padding: 20px;
  z-index: 1000;
}
@media (min-width: 768px){
  .notify-container{
    width: 500px;
    height: 400px;
    right: 5%;
  }
}

/* Mobile navbar adjustments */
@media (max-width: 768px) {
    .navbar {
        padding: 0 0.5rem;
    }

    .brand-title {
        font-size: 1em !important;
        margin: 0;
    }

    .navbar-brand {
        font-size: .5em;
        display: none;
    }

    .navbar-right {
        gap: 0em;
        flex-wrap: wrap;
        justify-content: flex-end;
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

    .navbar-right i {
        font-size: 1rem;
    }

    .profile-circle {
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
    }

    .profile-circle img {
        width: 22px !important;
        height: 22px !important;
    }
    .navbar-right i.fa-bell {
        margin-right: .2em;
    }

    .navbar-right button {
        font-size: .9em;
        font-weight: 500;
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
    display: flex;
    flex-direction: column;
}

@media screen and (min-width: 992px) {
    .sidebar {
    grid-area: sidebar;
    min-width: 12em;
    padding: .5rem;
    }

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

    .sidebar-header {
        margin-bottom: 1.5em;
    }

    .logo {
        width: 50px;
    }

    .sidebar-btn {
        gap: 1rem;
        padding-bottom: 1rem;
    }

    .sidebar-btn button {
        width: 7rem;
        padding: 0.8rem;
        font-size: 11px;
    }
}

/* Small mobile sidebar adjustments */
@media (max-width: 480px) {
    .sidebar {
        width: 180px;
    }

    .sidebar-btn button {
        width: 6rem;
        padding: 0.6rem;
        font-size: 10px;
    }

    .logo {
        width: 40px;
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
    width: 90px;
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
    padding-bottom: 2rem;
    overflow-y: auto;
}

/* Custom scrollbar styling to match sidebar background */
.sidebar-btn::-webkit-scrollbar {
    width: 8px;
}

.sidebar-btn::-webkit-scrollbar-track {
    background: #323232;
}

.sidebar-btn::-webkit-scrollbar-thumb {
    background: #ca9d73;
    border-radius: 4px;
}

.sidebar-btn::-webkit-scrollbar-thumb:hover {
    background: #c0673e;
}

/* Firefox scrollbar styling */
.sidebar-btn {
    scrollbar-width: thin;
    scrollbar-color: #ca9d73 #323232;
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

@media screen and (min-width: 992px) {
    .sidebar-btn {
        font-size: 11px;
        gap: 1em;
    }

    .sidebar-btn button {
        width: 8rem;
    }
}

/* Mobile sidebar buttons */
@media (max-width: 768px) {
    .sidebar-btn button {
        padding: 1rem .5rem;
        font-size: 12px;
    }

    .sidebar-btn button img {
        width: 14px;
        margin: 0 auto 0.2em auto;
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

/* Small mobile content adjustments */
@media (max-width: 480px) {
    .content {
        padding: 0.25rem;
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

    .navbar {
        padding: 0 0.75rem;
    }

    .navbar-brand {
        font-size: 1.1em;
    }

    .sidebar {
        min-width: 10em;
    }

    .logo {
        width: 55px;
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

    .navbar {
        padding: 0 1.5rem;
    }

    .brand-title {
        font-size: 1.8em !important;
        margin: 0 0.8em 0 0.3em !important;
    }

    .navbar-brand {
        font-size: 1.3em;
    }

    .sidebar {
        min-width: 14em;
    }

    .logo {
        width: 100px;
        height: 50px;
    }

    .sidebar-btn button img {
        width: 18px;
        margin: 0 auto 0.4em auto;
    }
}

/* Landscape orientation adjustments for mobile */
@media (max-width: 768px) and (orientation: landscape) {
    .navbar {
        min-height: 50px;
        padding: 0 0.5rem;
    }

    .brand-title {
        font-size: 1.1em !important;
    }

    .sidebar {
        width: 160px;
    }

    .sidebar-btn button {
        width: 6rem;
        padding: 0.6rem;
        font-size: 10px;
        margin-bottom: 0.3rem;
    }

    .sidebar-btn {
        gap: 0.8rem;
        padding-bottom: 0.5rem;
    }
}

/* High DPI displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .sidebar-btn button {
        border-radius: 12px;
    }

    .logo,
    .logo-minimized {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    .sidebar-btn button {
        min-height: 44px;
        min-width: 44px;
    }

    .profile-circle,
    .mobile-menu-toggle {
        min-height: 44px;
        min-width: 44px;
    }

    .floating-box {
        min-height: 44px;
    }
}
</style>
