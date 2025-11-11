<template>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <div class="main-container" v-if="admin_info">

    <teleport to="body">
        <AdminProfile @close="close" @save="save" :admin="admin_info" v-if="show_profile_modal"/>
    </teleport>

    <teleport to="body">
        <AdminNotification v-if="show_notif" @close="show_notif = false;" :notifications="notifications"/>
    </teleport>

    <div class="navbar-overlay" v-show="show_navbar">
        <div class="navbar">
            <span class="material-icons close-icon" 
            style=""
            @click="show_navbar = false">
                close
            </span>
            <h3 style="color: white;">ADMIN</h3>

            <!--DASHBOARD-->
            <router-link to="/admin/dashboard" @click="current_page = 'DASHBOARD'">
                <span class="material-icons">
                    dashboard
                </span>
                <label>DASHBOARD</label>
            </router-link>
            <!--ACCOUNT MANAGE-->
            <router-link to="/admin/manage-account" @click="current_page = 'MANAGE ACCOUNT'">
                <span class="material-icons">
                    manage_accounts
                </span>
                <label>MANAGE ACCOUNT</label>
            </router-link>
            <!--FEEDBACK MANAGE-->
            <router-link to="/admin/manage-reviews" @click="current_page = 'MANAGE FEEDBACK'">
                <span class="material-icons">
                    feedback
                </span>
                <label>MANAGE FEEDBACK</label>
            </router-link>
            <!--MAP-->
            <router-link to="/admin/map" @click="current_page = 'MAP'">
                <span class="material-icons">
                    map
                </span>
                <label>MAP</label>
            </router-link>

        </div>
    </div>
    <div class="content">
        <div class="content-header">
            <div class="h">
                <span class="material-icons icon" @click="show_navbar = true;">
                    menu
                </span>
                <div>
                    <label class="current-page">{{ current_page }}</label>
                </div>
            </div>
            <div class="h">
                <span class="material-icons icon" @click="show_notif = !show_notif; show_profile_modal = false;">
                    notifications
                </span>
                <div class="profile-circle" style="cursor: pointer;" @click="show_profile_modal = true; show_notif = false;">
                    <img :src="'/'+admin_info.path" style="width: 100%; height: 100%; position: relative;">
                </div>
            </div>
        </div>
        <div class="content-result">
            <router-view/>
        </div>
    </div>
  </div>
</template>

<script>
import { useDataStore } from './stores/dataStore';
import axios from 'axios';

import AdminProfile from './admin/pages/AdminProfile.vue';
import AdminNotification from './admin/modals/AdminNotification.vue';

export default {
    components: {AdminProfile, AdminNotification},
    data() {
        return{
            show_notif: false,
            show_navbar: true,
            screen_w: window.innerWidth,
            current_page: 'DASHBOARD',
            admin_info: null,
            show_profile_modal: false,
            notifications: [],
        }
    },
    methods: {
        close(){

            this.show_profile_modal = false;
        },

        save(info){
            
            this.admin_info = info;
        },

        async returnAdminInfo(){

            const res = await axios.get('/admin/return-info');

            console.log(res.data.message);

            this.admin_info = res.data.info;

            console.log(this.admin_info);
        },

        async returnNotifications(){

            const res = await axios.get('/admin/return-all/notifications');
            this.notifications = res.data.notif;

            console.log(res.data.notif);
        }
    },
    async mounted() {

        await this.returnAdminInfo();
        await this.returnNotifications();

        //redirect to dashboard after log in
        this.$router.push({name: "AdminDashboard"});
    }
}
</script>

<style scoped>
.h{
     display: flex; flex-direction: row; align-items: center; gap: 10px;
}
.current-page{
    font-size: 18px;
}
.close-icon{
    position: absolute; right: 0; margin-right: 20px; font-weight: bolder; font-size: 20px; cursor: pointer;
}
.navbar a{
    min-width: 90%;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
    color: white;
    text-decoration: none;
    background-color: #ca9d73;
    padding: 10px;
    border-radius: 12px;
    box-shadow: 6px 4px 1px rgba(37, 0, 0, 0.233);
}
.navbar a.router-link-active{
    background-color: #c0673e;
}

.profile-circle{
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background-color: white;
}
.content-header{
 background-color: #DDD0C8;
 display: flex;
 flex-direction: row;
 align-items: center;
 justify-content: space-between;
 padding: 10px;
 box-sizing: border-box;
}
.main-container{
    width: 100%;
    height: auto;
    display: flex;
    flex-direction: row;
    align-items: center;
    position: relative;
}
.navbar-overlay {
    width: 100%;
    background-color: #32323290;
    height: 100vh;
    top: 0;
    position: fixed;
    z-index: 10000;
}
.navbar{
    min-width: 30%;
    background-color: #323232;
    top: 0;
    position: absolute;
    height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    box-sizing: border-box;
    gap: 20px;
}
.navbar span{
    color: white;
    font-size: 20px;
}
.content {
    width: 100%;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}
.content-result {
    width: 100%;
    height: auto;
}
@media screen and (min-width: 768px){

    .navbar{
        min-width: 15%;
        padding: 30px;
        gap: 30px;
    }
    .navbar a{
        font-size: 20px;
        padding: 15px;
    }
    h3{
        font-size: 30px;
    }
    .icon{
        font-size: 40px;
        font-weight: bolder;
        cursor: pointer;
    }
    .content-header{
        padding: 20px;
        padding-left: 70px;
        padding-right: 70px;
    }
    .h{
        display: flex; flex-direction: row; align-items: center; gap: 20px;
    }
    .profile-circle{
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: white;
    }
    .current-page{
        font-size: 25px;
    }
}
</style>