<template>
  <div class="notif-header">
    <h5>All Notifications</h5>
    <div class="reader">
        <div class="checkbox">
            <input type="checkbox" name="all" id="all">
            <p>All</p>
        </div>
        <button>Mark as Read</button>
    </div>
  </div>

  <hr>

  <div class="notif-contents">
    <div class="notif-box" v-for="notif in notifications" :key="notif">
        <div class="checkbox">
            <input type="checkbox" name="select" id="select">
        </div>
        <div class="fav">
            <button v-if="notif.favorite === false" @click="notif.favorite = true" class="star-btn unfavorited">
                <i class="fa-regular fa-star"></i>
            </button>
            <button v-else-if="notif.favorite === true" @click="notif.favorite = false" class="star-btn favorited">
                <i class="fa-solid fa-star"></i>
            </button>
        </div>

        <div class="main-section" @click="openMessage(notif)">
            <div class="profile">
                <div v-if="notif.type === 'chat'">
                    <img :src="profile" alt="Profile Picture">
                </div>
                <div v-else-if="notif.type === 'notif'">
                    <img :src="systemProfile" alt="">
                </div>
            </div>

            <div class="message">
                <div>
                    <h6>{{ notif.recipient }}</h6>
                    <p>{{ notif.message }}</p>
                </div>
                <p>{{ notif.time }}</p>
            </div>
        </div>

    </div>
  </div>

  <!-- Add router-view for child routes -->
  <router-view></router-view>
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
                {recipient: 'Customer1', type: 'chat',favorite: false, message: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vel nisi sed felis dictum lobortis. Vivamus ut justo in diam malesuada vestibulum sed in lorem. Praesent imperdiet enim in eros porta, sit amet semper nunc maximus. Phasellus mauris ligula, volutpat pretium dolor ut, mattis pulvinar felis. Duis vehicula massa velit, non sollicitudin leo facilisis et. Aliquam leo nisl, elementum sit amet sapien vitae, mattis suscipit risus. Curabitur sollicitudin fermentum mauris non pellentesque. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Aenean vehicula semper felis id posuere. Aenean finibus enim et fermentum volutpat. Etiam ac finibus sem, eget elementum ante.', time: '1 August 2026'},
                {recipient: 'Timbershoppe', type: 'notif',favorite: true, message: '2', time: '5 August 2020'},
                {recipient: 'Timbershoppe', type: 'notif',favorite: true, message: '1', time: '1 August 2025'}
            ]
        }
    },
    methods: {
        openMessage(notification) {
            this.selectedNotification = this.selectedNotification === notification ? null : notification;
            this.$router.push({ name: 'ViewNotification' });
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
}

.checkbox {
    display: flex;
    gap: .5em;
    align-items: center;
}

.checkbox input[type="checkbox"] {
    margin: 0;
    vertical-align: middle;
    width: 18px;
    height: 18px;
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
}

.star-btn:hover {
    transform: scale(1.1);
}

.star-btn.unfavorited {
    color: #3f3f3f;
}

.star-btn.favorited {
    color: #6d4c2c;
    background-color: rgba(255, 215, 0, 0.1);
}

.star-btn.favorited:hover {
    background-color: rgba(255, 215, 0, 0.2);
}

.star-btn i {
    font-size: 1.1em;
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
</style>
