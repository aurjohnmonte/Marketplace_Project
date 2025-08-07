<template>
  <div class="message-container">
    <div class="message-header">
        <i class="fa-solid fa-arrow-left"></i>
        <h5>Chats</h5>
    </div>

    <div class="messages">
        <div class="chat-box" v-for="chat in chats">
            <img :src="chat.profile" alt="Profile">
            <div class="chat-details">
                <h6>{{ chat.username }}</h6>
                <p>{{ chat.message }}</p>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
export default {
    data() {
        return {
            chats: [
                {username: 'buyer1', profile: "https://tse1.mm.bing.net/th/id/OIP.airZynZaLzvgWLOJFbVF6QHaE8?rs=1&pid=ImgDetMain&o=7&rm=3", message: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vel nisi sed felis dictum lobortis. Vivamus ut justo in diam malesuada vestibulum sed in lorem. Praesent imperdiet enim in eros porta, sit amet semper nunc maximus. Phasellus mauris ligula, volutpat pretium dolor ut, mattis pulvinar felis. Duis vehicula massa velit, non sollicitudin leo facilisis et. Aliquam leo nisl, elementum sit amet sapien vitae, mattis suscipit risus. Curabitur sollicitudin fermentum mauris non pellentesque. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Aenean vehicula semper felis id posuere. Aenean finibus enim et fermentum volutpat. Etiam ac finibus sem, eget elementum ante.'},
                {username: 'buyer2', profile: 'https://wallpapercave.com/wp/wp1996490.jpg', message: 'sdaf dsfa'}
            ]
        }
    }
}
</script>

<style scoped>
.message-container {
    display: flex;
    flex-direction: column;
    gap: 2em;
    padding: 1em;
}

.message-header {
    display: flex;
    align-items: center;
    gap: 1em;
    padding: .5em 1em;
    background-color: #dab086d0;
    border-radius: 1.2em;
}
.message-header h5 {
    margin: 0;
    padding: 0;
}

.messages {
    display: flex;
    flex-direction: column;
    gap: 2em;
    padding: 1em;
    border-radius: 1em;
}

.chat-box {
    display: flex;
    gap: 1em;
    align-items: center;
    padding: 1em;
    border-radius: .8em;
    background-color: #dfdfdf;
}

.chat-box img {
    width: 5em;
    height: 5em;
    object-fit: cover;
    border-radius: 50%;
    border-top: .2px solid #111;
    box-shadow: 0px 1px 1px 1px rgba(0,0,0,0.585);
}

.chat-details {
    display: flex;
    flex-direction: column;
    width: 100%;
    overflow: hidden;
}

.chat-details p {
    font-size: .85em;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    min-width: 0;
}
</style>
