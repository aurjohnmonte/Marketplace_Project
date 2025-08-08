<template>
  <div class="message-container">
    <div class="message-header" >
        <h4>Timber Chats</h4>

        <div class="search-bar">
            <input type="search" name="search" id="search" @input="searchChat">
            <i class="fa fa-search search-icon"></i>
        </div>
    </div>

    <div class="messages">
        <div class="chat-box" v-for="chat in filteredChats" @click="openChat(chat)">
            <img :src="chat.profile" alt="Profile">
            <div class="chat-details">
                <h6>{{ chat.username }}</h6>
                <p>{{ chat.message }}</p>
            </div>
        </div>

        <div v-if="filteredChats.length === 0" style="margin: 0 1em;">
            <p v-if="searchTerm.trim() === ''">No chats available</p>
            <p v-else>No chats found matching "{{ searchTerm }}"</p>
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
            ],
            searchTerm: ''
        }
    },
    computed: {
        filteredChats() {
            const term = this.searchTerm.trim().toLowerCase();
            if (!term) return this.chats;
            return this.chats.filter(chat =>
                (chat.username && chat.username.toLowerCase().includes(term))
            );
        }
    },
    methods: {
        openChat(chat) {
            // Redirect to Chats component with chat data as query parameters
            console.log('Opening chat:', chat);
            console.log('Current route:', this.$route);

            const queryParams = {
                username: chat.username,
                profile: chat.profile,
                message: chat.message
            };

            console.log('Query params:', queryParams);

            // Try using route name first, then fallback to path
            this.$router.push({
                name: 'Chats',
                query: queryParams
            }).then(() => {
                console.log('Navigation successful');
            }).catch(err => {
                console.error('Navigation error with name, trying path:', err);
                // Fallback to path
                this.$router.push({
                    path: '/seller/messages/chats',
                    query: queryParams
                }).then(() => {
                    console.log('Navigation successful with path');
                }).catch(pathErr => {
                    console.error('Navigation error with path:', pathErr);
                });
            });
        },
        searchChat(event) {
            this.searchTerm = event.target.value;
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
    justify-content: space-between;
    gap: 1em;
    padding: .5em 1.5em;
    background-color: #ddd0c8e5;
    border-radius: 1.2em;
}
.message-header h4 {
    margin: 0;
    padding: 0;
    font-family: 'Montserrat', Arial, Helvetica, sans-serif;
    font-weight: 600;
    font-style: normal;
    font-size: 1.2em;
}

.search-bar {
  position: relative;
  display: inline-block;
  justify-self: flex-end;
}

.search-bar input[type="search"] {
  padding: 0.5em 2em 0.5em 1.3em;
  margin: .5em 0;
  font-size: .95em;
  border-radius: 15px;
  border: none;
  width: 100%;
  box-sizing: border-box;
  background-color: #f5f5f5;
  outline: none;
  transition: all 0.3s ease;
  box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.582);
}

.search-icon {
  position: absolute;
  right: 0.7em;
  top: 50%;
  transform: translateY(-50%);
  color: #aaa;
  pointer-events: none;
  font-size: 1em;
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
