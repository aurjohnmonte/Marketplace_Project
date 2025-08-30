<template>
  <div class="message-container">
    <div class="message-header" >
        <h4>Timber Chats</h4>

        <div class="search-bar">
            <input type="search" name="search" id="search" v-model="searchTerm">
            <i class="fa fa-search search-icon"></i>
        </div>
    </div>

    <div class="overlay" v-if="!chats">
        <img src="../../../images/kOnzy.gif">
    </div>

    <div class="messages" v-else>
        <div class="chat-box" v-for="chat in chats" :key="chat" @click="openChat(chat)">
            <div style="display: flex; flex-direction: row; align-items: center; gap: 20px;">
                <img :src="'/'+chat[1].profile" alt="Profile">
                <div class="chat-details">
                    <h6>{{ chat[1].firstname }} {{ chat[1].lastname }}</h6>
                    <p>{{ chat[0].messages }}</p>

                    <div style="display: flex; flex-direction: row; gap: 8px; align-items: center;">
                        <div style="width: 15px; height: 15px; border-radius: 15px; background-color: green;" v-if="chat[1].is_active === 1"></div>
                        <label style="font-size: 12px; margin: 0;">Active {{ (chat[1].is_active === 1 ? "now" : returnFormatActivedTime(chat[1].time_logout)) }}</label>
                    </div>
                </div>
            </div>
            <label style="font-size: 12px;">{{ returnFormatDate(chat[0].updated_at) }}</label>
        </div>

        <div v-if="chats.length === 0" style="margin: 0 1em;">
            <p v-if="searchTerm.trim() === ''">No chats available</p>
            <p v-else>No chats found matching "{{ searchTerm }}"</p>
        </div>
    </div>
  </div>
</template>

<script>
import { useDataStore } from '../../stores/dataStore';
import axios from 'axios';

export default {
    data() {
        return {
            show_conversation: true,
            chats: null,
            orig_chats: null,
            searchTerm: '',
            store: useDataStore(),
            conversation: [],
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
        returnFormatActivedTime(datetime) {
            if(!datetime){
                return "";
            }
      const current = new Date();
      const time = new Date(datetime);

      const diffInMs = current - time;
      const diffInSeconds = Math.floor(diffInMs / 1000);

      if (diffInSeconds < 60) {
        return `${diffInSeconds} seconds ago`;
      }

      const diffInMinutes = Math.floor(diffInSeconds / 60);
      if (diffInMinutes < 60) {
        return `${diffInMinutes} minutes ago`;
      }

      const diffInHours = Math.floor(diffInMinutes / 60);
      if (diffInHours < 24) {
        return `${diffInHours} hours ago`;
      }

      const diffInDays = Math.floor(diffInHours / 24);
      return `${diffInDays} days ago`;
    },
        returnFormatDate(date){


            return new Date(date).toLocaleDateString();
        },
        async returnMessageList(id){

            console.log('seller: ',this.store.currentUser_info);

            const res = await axios.get('/seller/return-message/list', {
                params: {
                    id: id
                }
            });

            console.log(res.data.messages);
            this.chats = res.data.messages;
            this.orig_chats = res.data.messages;

        },
        openChat(chat) {
            // Redirect to Chats component with chat data as query parameters
            this.$emit('returnActiveStatus', chat[1].is_active);

            console.log('Opening chat:', chat);
            console.log('Current route:', this.$route);

            const username = chat[1].name;

            console.log('Query:', username);

            // Try using route name first, then fallback to path

            this.$router.push({name: 'Chats', query: {username: username}});
        },
        searchChat(event) {
            this.searchTerm = event.target.value;
        }
    },
    watch: {
        searchTerm(newval){

            let searchtext = newval.toLowerCase();

            console.log(newval);
            if(newval === ""){
                this.chats = this.orig_chats;
                return;
            }
            console.log(this.orig_chats);
            console.log(this.orig_chats[1][1].firstname);

            this.chats = this.orig_chats.filter(e => `${e[1].firstname} ${e[1].lastname}`.toLowerCase().includes(searchtext));
            console.log('chats: ', this.chats);
        }
    },
    async mounted(){
        await this.returnMessageList(this.store.currentUser_info.id);
        console.log(this.store.currentUser_info);

        let id = this.store.currentUser_info.id;
        let username = this.store.currentUser_info.name;


        Echo.channel(`active-notify`)
        .listen('.active.sent', async (event) => {

            await this.returnMessageList(id);
            console.log('NEEEH AGIIIIIII');
        });

        Echo.channel(`message.${username}`)
            .listen('.message.sent', async (event) => {
                console.log('ne agi diria');
                await this.returnMessageList(id);
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
    padding: 50px;
}
.overlay img{
    width: 100px;
    height: 100px;

}
.message-container {
    display: flex;
    flex-direction: column;
    gap: 1em;
    padding: 1.5em;
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
    gap: 1.5em;
    padding: 1em 0;
    border-radius: 1em;
}

.chat-box {
    display: flex;
    gap: 1em;
    align-items: center;
    justify-content: space-between;
    padding: 1em;
    border-radius: .8em;
    background-color: #dfdfdf;
}

.chat-box img {
    width: 5em;
    height: 5em;
    object-fit: cover;
    border-radius: 50%;
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

/* ===== RESPONSIVE DESIGN ===== */

/* Small mobile devices */
@media (max-width: 480px) {
    .message-container {
        padding: 0.5em;
        gap: 1em;
    }

    .message-header {
        flex-direction: column;
        gap: 0.8em;
        padding: 0.8em 1em;
        text-align: center;
    }

    .message-header h4 {
        font-size: 1rem;
    }

    .search-bar {
        width: 100%;
    }

    .search-bar input[type="search"] {
        padding: 0.6em 2em 0.6em 1em;
        font-size: 0.9em;
        border-radius: 12px;
    }

    .search-icon {
        right: 0.8em;
        font-size: 0.9em;
    }

    .messages {
        gap: 1em;
        padding: 0.5em;
    }

    .chat-box {
        gap: 0.8em;
        padding: 0.8em;
        border-radius: 0.6em;
    }

    .chat-box img {
        width: 3.5em;
        height: 3.5em;
    }

    .chat-details p {
        font-size: 0.8em;
    }
}

/* Mobile - Medium devices */
@media (max-width: 768px) {
    .message-container {
        padding: 1em;
        gap: 1.5em;
    }

    .message-header {
        flex-direction: column;
        gap: 1em;
        padding: 1em 1.5em;
        text-align: center;
    }

    .message-header h4 {
        font-size: 1.1rem;
    }

    .search-bar {
        width: 100%;
    }

    .search-bar input[type="search"] {
        padding: 0.7em 2.2em 0.7em 1.2em;
        font-size: 0.95em;
        border-radius: 15px;
    }

    .search-icon {
        right: 0.8em;
        font-size: 0.95em;
    }

    .messages {
        gap: 1.5em;
        padding: 1em;
    }

    .chat-box {
        gap: 1em;
        padding: 1em;
        border-radius: 0.8em;
    }

    .chat-box img {
        width: 4em;
        height: 4em;
    }

    .chat-details p {
        font-size: 0.85em;
    }
}

/* Tablet devices */
@media (min-width: 769px) and (max-width: 1024px) {
    .message-container {
        padding: 1.5em;
        gap: 2em;
    }

    .message-header {
        padding: 1em 2em;
    }

    .message-header h4 {
        font-size: 1.3rem;
    }

    .search-bar input[type="search"] {
        padding: 0.8em 2.5em 0.8em 1.5em;
        font-size: 1em;
        border-radius: 18px;
    }

    .search-icon {
        right: 1em;
        font-size: 1em;
    }

    .messages {
        gap: 2em;
        padding: 1.5em;
    }

    .chat-box {
        gap: 1.2em;
        padding: 1.2em;
        border-radius: 1em;
    }

    .chat-box img {
        width: 4.5em;
        height: 4.5em;
    }

    .chat-details p {
        font-size: 0.9em;
    }
}



/* Landscape orientation for mobile */
@media (max-width: 768px) and (orientation: landscape) {
    .message-header {
        flex-direction: row;
        text-align: left;
        padding: 1em 1.5em;
    }

    .search-bar {
        width: auto;
        min-width: 200px;
    }
}

/* High DPI displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .chat-box img {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    .chat-box {
        min-height: 44px;
    }

    .search-bar input[type="search"] {
        min-height: 44px;
    }
}

/* Print styles */
@media print {
    .message-container {
        padding: 0;
    }

    .search-bar {
        display: none;
    }

    .chat-box {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>
