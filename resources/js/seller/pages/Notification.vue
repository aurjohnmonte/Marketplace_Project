<template>
    <div class="notification-container">
        <div class="notif-header">
            <h5>All Notifications</h5>
            <div class="reader">
                <div class="filter-options">
                    <button
                        @click="setFilter('all')"
                        :class="{ active: currentFilter === 'all' }"
                        class="filter-btn"
                    >
                        All ({{ notifications.length }})
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
                        v-model="selectAll"
                        @change="toggleSelectAll"
                    >
                    <p>Select All</p>
                </div>
                <button @click="markAsRead" :disabled="!hasSelectedNotifications">Mark as Read</button>
                <button @click="toggleFavoriteSelected" :disabled="!hasSelectedNotifications">
                    {{ hasFavoritedSelected ? 'Unfavorite' : 'Favorite' }} Selected
                </button>
            </div>
        </div>

        <hr>

        <div class="notif-contents">
            <div class="notif-box" v-for="notif in filteredNotifications" :key="notif.id">
                <div class="checkbox">
                    <input
                        type="checkbox"
                        name="select"
                        id="select"
                        v-model="notif.selected"
                        @change="updateSelectAll"
                    >
                </div>
                <div class="fav">
                    <button
                        v-if="!notif.favorite"
                        @click="toggleFavorite(notif)"
                        class="star-btn unfavorited"
                    >
                        <i class="fa-regular fa-star"></i>
                    </button>
                    <button
                        v-else
                        @click="toggleFavorite(notif)"
                        class="star-btn favorited"
                    >
                        <i class="fa-solid fa-star"></i>
                    </button>
                </div>

                <div class="main-section" @click="openMessage(notif)">
                    <div class="profile">
                        <div v-if="notif.type === 'chat'">
                            <img :src="profile" alt="Profile Picture">
                        </div>
                        <div v-else-if="notif.type === 'notif'">
                            <img :src="systemProfile" alt="System Notification">
                        </div>
                    </div>

                    <div class="message" :class="{ unread: !notif.read }">
                        <div>
                            <h6>{{ notif.recipient }} - </h6>
                            <p>{{ notif.message }}</p>
                        </div>
                        <p>{{ notif.time }}</p>
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

export default {
    data() {
        return {
            selectedNotification: null,
            profile: "https://tse1.mm.bing.net/th/id/OIP.airZynZaLzvgWLOJFbVF6QHaE8?rs=1&pid=ImgDetMain&o=7&rm=3",
            systemProfile: systemLogo,
            notifications: [
                {id: 1, recipient: 'Customer1', type: 'chat', favorite: false, message: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vel nisi sed felis dictum lobortis. Vivamus ut justo in diam malesuada vestibulum sed in lorem. Praesent imperdiet enim in eros porta, sit amet semper nunc maximus. Phasellus mauris ligula, volutpat pretium dolor ut, mattis pulvinar felis. Duis vehicula massa velit, non sollicitudin leo facilisis et. Aliquam leo nisl, elementum sit amet sapien vitae, mattis suscipit risus. Curabitur sollicitudin fermentum mauris non pellentesque. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Aenean vehicula semper felis id posuere. Aenean finibus enim et fermentum volutpat. Etiam ac finibus sem, eget elementum ante.', time: '1 August 2026', read: false, selected: false},
                {id: 2, recipient: 'Timbershoppe', type: 'notif', favorite: true, message: '2', time: '5 August 2020', read: true, selected: false},
                {id: 3, recipient: 'Timbershoppe', type: 'notif', favorite: true, message: '1', time: '1 August 2025', read: false, selected: false}
            ],
            currentFilter: 'all',
            selectAll: false
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
                return this.notifications.filter(notif => !notif.read);
            } else if (this.currentFilter === 'favorited') {
                return this.notifications.filter(notif => notif.favorite);
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
        closeModal() {
            this.$router.push({ name: 'Notification' });
        },
        openMessage(notification) {
            this.selectedNotification = this.selectedNotification === notification ? null : notification;
            this.$router.push({ name: 'ViewNotification' });
        },
        removeNotification(index) {
            this.notifications.splice(index, 1);
        },
        setFilter(filter) {
            this.currentFilter = filter;
            this.selectAll = false; // Reset select all when changing filter
            // Reset all selections when changing filter
            this.notifications.forEach(notif => {
                notif.selected = false;
            });
        },
        toggleSelectAll() {
            this.filteredNotifications.forEach(notif => {
                notif.selected = this.selectAll;
            });
        },
        updateSelectAll() {
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
        toggleFavorite(notification) {
            notification.favorite = !notification.favorite;

            // Show feedback to user
            const action = notification.favorite ? 'favorited' : 'unfavorited';
            console.log(`Notification ${action}: ${notification.recipient}`);
        },
        toggleFavoriteSelected() {
            const selectedNotifications = this.notifications.filter(notif => notif.selected);
            if (selectedNotifications.length === 0) return;

            // Determine if we should favorite or unfavorite based on current state
            const shouldFavorite = !this.hasFavoritedSelected;

            selectedNotifications.forEach(notif => {
                notif.favorite = shouldFavorite;
                notif.selected = false; // Deselect after action
            });

            this.selectAll = false; // Reset select all after action

            // Show feedback
            const action = shouldFavorite ? 'favorited' : 'unfavorited';
            console.log(`${selectedNotifications.length} notification(s) ${action}`);
        },
        // Method to get favorite count for display
        getFavoriteCount() {
            return this.notifications.filter(notif => notif.favorite).length;
        },
        // Method to get unread count for display
        getUnreadCount() {
            return this.notifications.filter(notif => !notif.read).length;
        }
    }
}
</script>

<style scoped>
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
    border: 1px solid #eee;
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
