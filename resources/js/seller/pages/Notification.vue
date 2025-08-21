<template>
    <div class="notification-container">
        <div class="overlay" v-if="!orig_notifications">
            <img src="../../../images/kOnzy.gif">
        </div>
        <div class="notif-header" v-else>
            <h5>All Notifications</h5>
            <div class="reader">
                <div class="filter-options">
                    <button
                        @click="setFilter('all')"
                        :class="{ active: currentFilter === 'all' }"
                        class="filter-btn"
                    >
                        All ({{ orig_notifications.length }})
                    </button>
                    <button
                        @click="setFilter('unread')"
                        :class="{ active: currentFilter === 'unread' }"
                        class="filter-btn"
                    >
                        Unread ({{ getUnreadCount() }})
                    </button>
                    <button
                        @click="setFilter('favorited')"
                        :class="{ active: currentFilter === 'favorited' }"
                        class="filter-btn"
                    >
                        Favorited ({{ getFavoriteCount() }})
                    </button>
                </div>
                <div class="checkbox">
                    <input
                        type="checkbox"
                        name="all"
                        id="all"
                        @change="toggleSelectAll"
                    >
                    <p>Select All</p>
                </div>
                <!-- <button @click="markAsRead" :disabled="!hasSelectedNotifications">Mark as Read</button> -->
                <button @click="goDelete" :disabled="!hasSelectedNotifications">
                    Delete
                </button>
            </div>
        </div>

        <hr>
        <div class="notif-contents">
            <div class="overlay" v-if="!orig_notifications">
                <img src="../../../images/kOnzy.gif">
            </div>
            <div v-else-if="orig_notifications.length === 0">
                <h3 style="color: red;">No notification</h3>
            </div>
            <div class="notif-box" :style="{backgroundColor: returnColor(notif)}" v-for="notif in filteredNotifications" :key="notif.id" v-else>
                <div class="checkbox">
                    <input
                        type="checkbox"
                        name="select"
                        id="select"
                        v-model="notif.selected"
                        :value="notif.id"
                        @change="updateSelectAll"
                    >
                </div>
                <div class="fav">
                    <button
                        v-if="notif.favorite === 0"
                        @click="toggleFavorite(notif, 'fav')"
                        class="star-btn unfavorited"
                    >
                        <i class="fa-regular fa-star"></i>
                    </button>
                    <button
                        v-else
                        @click="toggleFavorite(notif, 'notfav')"
                        class="star-btn favorited"
                    >
                        <i class="fa-solid fa-star"></i>
                    </button>
                </div>

                <div class="main-section" @click="openMessage(notif)">
                    <div class="profile">
                        <div v-if="notif.type === 'message'">
                            <img :src="'/'+notif.users.profile" alt="Profile Picture">
                        </div>
                        <div v-else>
                            <img :src="'/'+notif.users.profile" alt="System Notification">
                        </div>
                    </div>

                    <div class="message" :class="{ unread: !notif.read }">
                        <div>
                            <h6>{{ notif.users.firstname }} {{ notif.users.lastname }} - </h6>
                            <p>{{ notif.text }}</p>
                        </div>
                        <p>{{ returnFormatTime(notif.created_at) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add router-view for child routes -->
        <div v-if="showOverlay" class="overlay-background" @click="closeModal">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
import systemLogo from '../../../images/Logo1.svg'
import { useDataStore } from '../../stores/dataStore';
import axios from 'axios';

export default {
    data() {
        return {
            selectedNotification: null,
            systemProfile: systemLogo,
            notifications: [],
            selected_notif: [],
            currentFilter: 'all',
            orig_notifications: null,
            selectAll: false,
            store: useDataStore(),
        }
    },
    computed: {
        showOverlay() {
            return this.$route.name === 'ViewNotification';
        },
        filteredNotifications() {
            if (this.currentFilter === 'all') {
                return this.notifications;
            } else if (this.currentFilter === 'unread') {
                return this.notifications.filter(notif => notif.seen === 0);
            } else if (this.currentFilter === 'favorited') {
                return this.notifications.filter(notif => notif.favorite === 1);
            }
            return this.notifications;
        },
        hasSelectedNotifications() {
            return this.notifications.some(notif => notif.selected);
        },
        hasFavoritedSelected() {
            const selectedNotifications = this.notifications.filter(notif => notif.selected);
            return selectedNotifications.length > 0 && selectedNotifications.every(notif => notif.favorite);
        }
    },
    methods: {
        returnFormatTime(date){

            return new Date(date).toLocaleDateString();
        },
        async changeSeen(id){

          const res = await axios.get('/change-seen', {
            params: {
              id: id
            }
          });

          console.log(res.data.message);

          if(res.data.message === 'successful'){

            this.$emit('checknotif');

                for(let notify of this.orig_notifications){

                    if(notify.id === id){
                        notify.seen = 1;
                        return;
                    }
                }
            }
        },
        async openMessage(notification) {

            console.log(notification);

            this.store.setSelectedNotification(notification);

            if(notification.seen === 0){
                await this.changeSeen(notification.id);
            }

            this.$router.push({ name: 'ViewNotification' });
        },
        closeModal() {
            this.$router.push({ name: 'Notification' });
        },
        removeNotification(index) {
            this.notifications.splice(index, 1);
        },
        setFilter(filter) {
            this.currentFilter = filter;
            this.selectAll = false; // Reset select all when changing filter
            // Reset all selections when changing filter
            if(filter === 'favorited'){
                let dummy = [];
                this.orig_notifications.forEach(notif => {

                    if(notif.favorite === 1){
                        dummy.push(notif);
                    }
                });
                this.notifications = dummy;
            }
            else if(filter === 'all'){
                this.notifications = this.orig_notifications;
            }
            else{
                let dummy = [];
                this.orig_notifications.forEach(notif => {

                    if(notif.seen === 0){
                        dummy.push(notif);
                    }
                });
                this.notifications = dummy;
            }
        },
        toggleSelectAll(e) {

            let value;

            if(e.target.checked){
                value = true;
            }
            else{
                value = false;
            }

            this.filteredNotifications.forEach(notif => {
                notif.selected = value;

                if(value){
                    this.selected_notif.push(notif.id);
                }
                else{
                    this.selected_notif = [];
                }
            });

            console.log('selected notif: ', this.selected_notif);
        },
        updateSelectAll(e) {
            console.log(e.target.checked);

            if(e.target.checked){
                this.selected_notif.push(e.target.value);
            }
            else{
                this.selected_notif = this.selected_notif.filter(id => e.target.value !== id);
            }

            console.log('selected notif: ', this.selected_notif);

            const visibleNotifications = this.filteredNotifications;
            this.selectAll = visibleNotifications.length > 0 && visibleNotifications.every(notif => notif.selected);
        },
        markAsRead() {
            this.notifications.forEach(notif => {
                if (notif.selected) {
                    notif.read = true;
                    notif.selected = false;
                }
            });
            this.selectAll = false; // Reset select all after marking
        },

        async toggleFavorite(notification, type) {

            let star;

            console.log(type);

            const data = new FormData();
            data.append('notif_id', notification.id);
            data.append('type', type);

            const res = await axios.post('/action-notification', data);

            if(res.data.message === 'successful'){

                if(type === 'fav'){
                    star = 1;
                }
                else{
                    star = 0;
                }

                for(let notify of this.notifications){

                    if(notification.id === notify.id){
                        notify.favorite = star;
                    }
                }
            }

            console.log(res.data.message);

            return;

            // Show feedback to user
            const action = notification.favorite ? 'favorited' : 'unfavorited';
            console.log(`Notification ${action}: ${notification.recipient}`);
        },
        async goDelete() {

            if(this.selected_notif < 1){
                window.alert("No notification selected for deletion");
                return;
            }

            console.log('selected notif: ', this.selected_notif);

            const data = new FormData();

            data.append('data', JSON.stringify(this.selected_notif));

            const res = await axios.post('/delete-notif', data);

            window.alert(res.data.message);

            await this.returnNotifications();

            this.$emit('checknotif');
        },
        // Method to get favorite count for display
        getFavoriteCount() {
            return this.orig_notifications.filter(notif => notif.favorite).length;
        },
        // Method to get unread count for display
        getUnreadCount() {
            return this.orig_notifications.filter(notif => !notif.seen).length;
        },
        //return notifications
        async returnNotifications(){
            const store = useDataStore();
            const res = await axios.get('/return/notifications', {
                params: {
                    id: store.currentUser_info.id,
                    type: 'seller',
                }
            });
            console.log(res.data.message);
            this.notifications = res.data.message;
            this.orig_notifications = res.data.message;
        },

        returnColor(notif){

            if(notif.seen === 0){
                return '#dfdfdf';
            }
            else{
                return 'transparent';
            }
        }
    },
    async mounted(){

        await this.returnNotifications();

        const store = useDataStore();

        console.log(store.currentUser_info);
        console.log(store.selected_shop);


        Echo.channel(`message.${this.store.currentUser_info.name}`)
            .listen('.message.sent', async (event) => {
                await this.returnNotifications();
        });

        Echo.channel(`sellernotify.${this.store.currentUser_info.name}`)
            .listen('.sellernotify.sent', async(event) => {
                await this.returnNotifications();
        });
    }
}
</script>

<style scoped>
.overlay{
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.overlay img{
    width: 100px;
    height: 100px;
}
.notif-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1em;
    background-color: #ddd0c89d;
    border-radius: .5em;
}

.reader {
    display: flex;
    gap: 1em;
    align-items: center;
    font-size: .8em;
}

.filter-options {
    display: flex;
    gap: 0.5em;
}

.filter-btn {
    padding: 0.4em 1em;
    border: none;
    border-radius: 0.8em;
    box-shadow: 0px 1px 1px 1px rgba(0,0,0,0.585);
    background-color: #f0f0f0;
    color: #333;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.filter-btn:hover {
    background-color: #e0e0e0;
}

.filter-btn.active {
    background-color: #6d4c2c;
    color: white;
}

.filter-btn.active:hover {
    background-color: #5a3f24;
}

.checkbox {
    display: flex;
    gap: .5em;
    align-items: center;
}

.checkbox input[type="checkbox"] {
    margin: 0;
    vertical-align: middle;
    width: 15px;
    height: 15px;
    accent-color: #4CAF50;
    cursor: pointer;
}

.checkbox p {
    margin: 0;
    vertical-align: middle;
}

.reader button {
    padding: .4em 1em;
    border: none;
    border-radius: .8em;
    box-shadow: 0px 1px 1px 1px rgba(0,0,0,0.585);
}

.notif-box {
    display: flex;
    gap: 1em;
    align-items: center;
    padding: 1em;
    border: 1px solid #cdcdcd;
    background-color: #dfdfdf;
    margin-bottom: 0.5em;
    border-radius: 0.5em;
    height: 4em;
    min-width: 0;
    overflow: hidden;
}

.notif-box .checkbox input[type="checkbox"] {
    flex: 1;
    width: 1em;
    height: 1em;
    accent-color: #2196F3;
    border: 1px solid #ccc;
    border-radius: 3px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.notif-box .checkbox input[type="checkbox"]:checked {
    background-color: #6d4c2c;
}

.notif-box .checkbox input[type="checkbox"]:hover {
    border-color: #2196F3;
    transform: scale(1.05);
}

.star-btn {
    flex: 1;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5em;
    border: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1em;
    height: 1em;
    border-radius: 50%;
}

.star-btn:hover {
    transform: scale(1.2);
    background-color: rgba(255, 215, 0, 0.1);
}

.star-btn.unfavorited {
    color: #3f3f3f;
}

.star-btn.unfavorited:hover {
    color: #6d4c2c;
    background-color: rgba(255, 215, 0, 0.1);
}

.star-btn.favorited {
    color: #6d4c2c;
    background-color: rgba(255, 215, 0, 0.2);
    box-shadow: 0 2px 4px rgba(255, 215, 0, 0.3);
}

.star-btn.favorited:hover {
    background-color: rgba(255, 215, 0, 0.3);
    box-shadow: 0 4px 8px rgba(255, 215, 0, 0.4);
}

.star-btn i {
    font-size: 1.1em;
    transition: all 0.3s ease;
}

.main-section {
    flex: 1;
    display: flex;
    gap: 1.5em;
    align-items: center;
    min-width: 0;
    overflow: hidden;
}

.main-section img {
    width: 2em;
    height: 2em;
    margin: .2em .1em;
    object-fit: cover;
    border-radius: 50%;
    box-shadow: 0px 1px 1px 1px rgba(0,0,0,0.585);
}


.message {
    width: 100%;
    font-size: .8em;
    color: #3a3a3a;
    display: flex;
    justify-content: space-between;
    gap: 0.8em;
    min-width: 0;
    overflow: hidden;
}

.message.unread {
    font-weight: 500;
    color: #000;
}

.message div {
    display: flex;
    gap: 1em;
    align-items: center;
    flex: 1;
    min-width: 0;
    overflow: hidden;
}

.message div h6,
.message div p {
    margin: 0;
    padding: 0;
    line-height: 1.2;
}

.message div p {
    flex: 1 1 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    min-width: 0;
}

.message > p {
    width: auto;
    min-width: 1em;
    margin: 0;
    padding: 0;
    color: #666;
    font-size: 0.9em;
    text-align: right;
    flex-shrink: 0;
}

.view-link {
    display: inline-block;
    padding: 0.5em 1em;
    background-color: #6d4c2c;
    color: white;
    text-decoration: none;
    border-radius: 0.3em;
    margin-top: 0.5em;
    font-size: 0.9em;
    transition: background-color 0.3s ease;
}

.view-link:hover {
    background-color: #5a3f24;
    color: white;
    text-decoration: none;
}

.notification-container {
    position: relative;
    height: 100%;
    width: 100%;
    padding: 1em;
}

.overlay-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.342);
    z-index: 9999;
}
</style>
