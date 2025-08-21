<template>
    <div class="chat-container">
        <!-- Chat Header -->
         <div class="overlay-showpic" v-if="show_pic" @click="show_pic = null;">
            <img :src="'/'+show_pic">
         </div>

        <div class="chat-header">
            <button @click="goBack" class="back-button">
                <i class="fa fa-arrow-left"></i>
            </button>
            <div class="chat-info" v-if="chatData">
                <img :src="'/'+user_info.profile" :alt="chatData.username" class="chat-avatar">
                <div class="user-details">
                    <h4>{{ chatData.username }}</h4>
                    <span class="status" :class="{ 'online': isOnline }">
                        <div style="background-color: green; height: 14px; width: 14px; border-radius: 50%;" v-if="active_status === 1"></div>
                        <label v-if="active_status === 1">Online now</label>
                        <label v-else>Offline</label>
                    </span>
                </div>
            </div>
        </div>

        <!-- SHOW OPTION-->
        <div class="more-option" v-if="show_moreOption">
            <img src="../../../images/cancel (1).png" style="cursor: pointer; position: absolute; width: 15px; height: 15px; top: 0; right: 0;" @click="closeOption">
            <label @click="goEdit">Edit</label>
            <label @click="goDelete">Delete</label>
         </div>

        <!-- Messages Area -->
        <div class="messages-area" ref="messagesArea">
            <div v-if="messages.length === 0 && !show_loading" class="no-messages">
                <p>No messages yet. Start the conversation!</p>
            </div>

            <div class="overlay" v-if="show_loading">
                <img src="../../../images/kOnzy.gif">
            </div>

            <div class="messages-list" v-else>
                <div
                    v-for="(message, index) in messages"
                    :key="index"
                    class="message-item"
                    :class="{ 'sent': message.from_id === current_id, 'received': message.to_id === current_id }"
                >
                    <div class="message-content" style="cursor: pointer;" @click="show_option(message.from_id === current_id, message, index)" :class="{selected: clicked === index}">
                        <label style="color: blue; text-decoration: underline;" v-if="message.mention" @click="goMention(message.mention)">View</label>
                        <p>{{ message.messages }}</p>
                        <div style="display: flex; flex-direction: column;">
                            <img v-if="message.message_pic" :src="'/'+message.message_pic" style="width: 200px; height: 200px; margin-top: 20px; margin-bottom: 20px;" @click.stop="show_pic = message.message_pic">
                            <span class="message-time">{{ formatTime(message.updated_at) }}</span>
                        </div>
                    </div><br>
                </div>
            </div>
        </div>

        <!-- Message Input -->
        <div class="message-input-container">
            <div v-if="this.photo.file" style="width: 100%; display: flex; align-items: center; justify-content: center; padding-bottom: 10px; position: relative;">
                <img src="../../../images/cancel (1).png" style="width: 20px; height: 20px; position: absolute; top: 0; right: 0; cursor: pointer;" @click="photo = {file: null, photo: null};">
                <img :src="this.photo.preview" style="width: 150px; height: 150px;">
            </div>
            <div v-if="is_edit && selected_message" style="width: 100%; display: flex; align-items: center; justify-content: center; padding-bottom: 10px; position: relative;">
                <img src="../../../images/cancel (1).png" style="width: 20px; height: 20px; position: absolute; top: 0; right: 0; cursor: pointer;" @click="closeOption">
                <label>{{ selected_message.messages }}</label>
                <img :src="'/'+selected_message.message_pic" style="width: 50px; height: 50px;" v-if="selected_message.message_pic">
            </div>
            <div class="input-wrapper">
                <span>
                    <label for="file" style="font-size: 20px; cursor: pointer;">
                        ＋
                        <input type="file" hidden id="file" @change="handleFileUpload">
                    </label>
                </span>
                <input
                    v-model="newMessage"
                    @keyup.enter="sendMessage"
                    type="text"
                    placeholder="Type your message..."
                    class="message-input"
                    :disabled="!chatData"
                >
                <button
                    @click="sendMessage"
                    class="send-button"
                    :disabled="!newMessage.trim() || !chatData"
                >
                    <i class="fa fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { useDataStore } from '../../stores/dataStore';
import axios from 'axios';

export default {
    props: ['active_status'],
    data() {
        return {
            show_pic: null,
            current_id: useDataStore().currentUser_info.id,
            chatData: null,
            user_info: null,
            messages: [],
            newMessage: '',
            is_edit: false,
            clicked: null,
            isOnline: true,
            show_loading: false,
            messageId: 1,
            timeUpdateInterval: null,
            selected_message: null,
            show_moreOption: false,
            username: '',
            photo: {
                file: null,
                photo: null,
            },
        }
    },
    async mounted() {
        if(!this.$route.query.username){
            this.$router.go(-1);
        }
        this.username = this.$route.query.username;
        console.log(this.username);

        await this.returnConversation();

        // Get chat data from query parameters
        this.chatData = {
            username: this.$route.query.username,
            profile: this.$route.query.profile,
            message: this.$route.query.message
        };

        console.log('Chat data:', this.chatData);

        // Scroll to bottom
        this.$nextTick(() => {
            this.scrollToBottom();
        });

        // Start timer to update timestamps every minute
        this.timeUpdateInterval = setInterval(() => {
            this.$forceUpdate();
        }, 60000);

        const store = useDataStore();



        Echo.channel(`message.${store.currentUser_info.name}`)
            .listen('.message.sent', async (event) => {
                console.log('ne agi diria');
                await this.returnConversation();
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
        });

    },
    methods: {
        async goDelete(){

            const res = await axios.get('/seller/delete-message', {
                params: {
                    message_id: this.selected_message.id
                }
            });

            console.log(res.data.message);

            this.messages = this.messages.filter(e => this.selected_message.id !== e.id);

            this.closeOption();
        },
        goEdit(){

            this.show_moreOption = false;
            this.newMessage = this.selected_message.messages;
            this.is_edit = true;
        },
        closeOption(){

            this.clicked = null;
            this.selected_message = null;
            this.show_moreOption = false;
            this.newMessage = "";
            this.is_edit = false;
        },
        show_option(status, message, index){

            if(!status){
                return;
            }

            this.clicked = index;

            this.selected_message = message;
            this.show_moreOption = true;
        },
        handleFileUpload(e){

            const file = e.target.files[0];

            const reader = new FileReader();

            reader.onload = (e) => {

                this.photo = {
                    file,
                    preview: e.target.result
                };

                console.log('photo: ', this.photo);
            };

            reader.readAsDataURL(file);

        },
        async goMention(mention_id){

            console.log(mention_id);
        },
        async returnConversation(){


            const store = useDataStore();

            const res = await axios.get('/return/conversation', {
                params: {
                    username: this.username,
                    id: store.currentUser_info.id
                }
            })

            console.log(res.data.messages);
            console.log(res.data.user_info);
            this.user_info = res.data.user_info;
            this.messages = res.data.messages;
        },
        goBack() {
            this.$router.push({ name: 'Messages' });
        },

        loadMessages() {
            this.messages = [
                {
                    id: 1,
                    text: this.chatData?.message,
                    sender: 'user',
                    timestamp: new Date(Date.now() - 3600000)
                },
                {
                    id: 2,
                    text: "Unsay ato?",
                    sender: 'seller',
                    timestamp: new Date(Date.now() - 1800000)
                },
                {
                    id: 3,
                    text: "Pila boss?",
                    sender: 'user',
                    timestamp: new Date(Date.now() - 900000)
                }
            ];
        },

        async save_Edit(){

            const res = await axios.get('/message/seller-edit', {
                params: {
                    message_id: this.selected_message.id,
                    text: this.newMessage,
                }
            });

            console.log(res.data.message);

            window.alert(res.data.message);

            for(let message of this.messages){
                if(this.selected_message.id === message.id){
                    message.messages = this.newMessage;
                    break;
                }
            }

            this.closeOption();
        },

        async sendMessage() {
            if (!this.newMessage.trim() || !this.chatData) return;

            //if sending a message is for Edit
            if(this.is_edit){
                this.save_Edit();
                return;
            }

            const data = new FormData();
            data.append('from_id', this.current_id);
            data.append('username', this.username);
            data.append('message', this.newMessage);
            data.append('photo', this.photo.file);

            const res = await axios.post('/seller/sent-message', data);

            console.log(res.data.message);

            if(res.data.message === 'successful'){

            }

            await this.returnConversation();

            this.newMessage = "";
            this.photo = {
                file: null,
                preview: null,
            };

            this.$nextTick(() => {
                this.scrollToBottom();
            });
        },

        generateUserResponse() {
            const responses = [
                "That sounds great!",
                "Thank you for the information.",
                "I'll think about it.",
                "Can you tell me more?",
                "Perfect! I'm interested.",
                "What about shipping costs?",
                "Do you accept credit cards?",
                "When can I expect delivery?"
            ];
            return responses[Math.floor(Math.random() * responses.length)];
        },

        scrollToBottom() {
            const messagesArea = this.$refs.messagesArea;
            if (messagesArea) {
                messagesArea.scrollTop = messagesArea.scrollHeight;
            }
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;

            if (diff < 60000) { // Less than 1 minute
                return 'Just now';
            } else if (diff < 3600000) { // Less than 1 hour
                const minutes = Math.floor(diff / 60000);
                return `${minutes}m ago`;
            } else if (diff < 86400000) { // Less than 1 day
                const hours = Math.floor(diff / 3600000);
                return `${hours}h ago`;
            } else {
                return date.toLocaleDateString();
            }
        }
    },
    beforeUnmount() {
        // Clean up the interval when component is destroyed
        if (this.timeUpdateInterval) {
            clearInterval(this.timeUpdateInterval);
        }
    }
}
</script>

<style scoped>
.overlay-showpic{
    position: fixed;
    left: 0;
    top: 0;
    width: 100vw;
    height: 100vh;
    z-index: 4000;
    background-color: rgba(0, 0, 0, 0.404);
    display: flex;
    align-items: center;
    justify-content: center;
}
.overlay-showpic img{
    background-color: white;
    margin: 150px;
    width: 500px;
    height: 500px;
    border-radius: 50px;
}
.selected{
    border: 5px solid rgb(186, 186, 186);
}
.more-option{
    position: absolute;
    background-color: white;
    padding: 20px;
    gap: 10px;
    z-index: 3000;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 10px;
    left: 50%;
    top: 30%;
}
.more-option label{
    border: 1px solid gray;
    width: 100px;
    padding: 5px;
    text-align: center;
}
.overlay{
    width: 100%;
    padding: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.overlay img{
    width: 100px;
    height: 100px;
}
.chat-container {
    display: flex;
    flex-direction: column;
    height: 100%;
    background-color: #f5f5f5;
    overflow-y: hidden;
}

.chat-header {
    display: flex;
    align-items: center;
    gap: 1em;
    padding: 1em;
    background-color: #ddd0c857;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 10;
    position: sticky;
    top: 0;
}

.back-button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0 0 0 1em;
    border-radius: 0.5em;
    background-color: transparent;
    transition: background-color 0.3s ease;
}

.back-button:hover {
    background-color: #e0e0e0;
}

.chat-info {
    display: flex;
    align-items: center;
    gap: 1em;
    flex: 1;
}

.chat-avatar {
    width: 3em;
    height: 3em;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
}

.user-details {
    display: flex;
    flex-direction: column;
}

.user-details h4 {
    margin: 0;
    font-size: 1.1em;
    font-weight: 600;
}

.status {
    font-size: 14px;
    color: #666;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 5px;
    margin-top: 5px;
}

.status label{
    margin: 0;
}

.messages-area {
    flex: 1;
    overflow-y: auto;
    padding: 1.5em;
    background-color: #f9f9f9;
}

.no-messages {
    display: flex;
    justify-content: center;
    align-items: center;
    height: auto;
    color: #666;
    font-style: italic;
}

.messages-list {
    display: flex;
    flex-direction: column;
    gap: 1em;
}

.message-item {
    display: flex;
    margin-bottom: 1em;
}

.message-item.sent {
    justify-content: flex-end;
}

.message-item.received {
    justify-content: flex-start;
}

.message-content {
    max-width: 70%;
    padding: 0.8em 1em;
    border-radius: 1.2em;
    position: relative;
}

.message-item.sent .message-content {
    background-color: #977355;
    color: white;
    border-bottom-right-radius: 0.3em;
}

.message-item.received .message-content {
    background-color: white;
    color: #333;
    border-bottom-left-radius: 0.3em;
    box-shadow: 1px 2px 4px rgba(0,0,0,0.3);
}

.message-content p {
    margin: 0 0 0.3em 0;
    word-wrap: break-word;
}

.message-time {
    font-size: 0.7em;
    opacity: 0.7;
}

.message-input-container {
    padding: 1em;
    background-color: #ddd0c857 ;
    border-top: 1px solid #e0e0e0;
    position: sticky;
    bottom: 0;
}

.input-wrapper {
    display: flex;
    gap: 0.5em;
    align-items: center;
}

.message-input {
    flex: 1;
    padding: 0.8em 1em;
    border: 1px solid #ddd;
    border-radius: 2em;
    outline: none;
    font-size: 0.9em;
    transition: border-color 0.3s ease;
}

.message-input:focus {
    border-color: #977355;
}

.message-input:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
}

.send-button {
    background-color: #977355;
    color: white;
    border: none;
    padding: 0.8em;
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 2.5em;
    height: 2.5em;
    display: flex;
    align-items: center;
    justify-content: center;
}

.send-button:hover:not(:disabled) {
    background-color: #0056b3;
}

.send-button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.send-button i {
    font-size: 0.9em;
}

/* ===== RESPONSIVE DESIGN ===== */

/* Small mobile devices */
@media (max-width: 480px) {
  .chat-container {
    padding: 0.5rem;
  }

  .chat-header {
    padding: 0.8rem;
    gap: 0.8rem;
  }

  .chat-header h3 {
    font-size: 1.1rem;
  }

  .chat-header p {
    font-size: 0.8rem;
  }

  .chat-header img {
    width: 2.5rem;
    height: 2.5rem;
  }

  .messages-container {
    padding: 0.5rem;
    gap: 0.8rem;
  }

  .message-item {
    gap: 0.5rem;
    margin-bottom: 0.8rem;
  }

  .message-content {
    max-width: 80%;
    padding: 0.6em 0.8em;
    border-radius: 1em;
  }

  .message-content p {
    font-size: 0.85em;
    margin-bottom: 0.2em;
  }

  .message-time {
    font-size: 0.6em;
  }

  .message-input-container {
    padding: 0.8rem;
  }

  .input-wrapper {
    gap: 0.4rem;
  }

  .message-input {
    padding: 0.6em 0.8em;
    font-size: 0.85em;
    border-radius: 1.5em;
  }

  .send-button {
    width: 2.2em;
    height: 2.2em;
    padding: 0.6em;
  }

  .send-button i {
    font-size: 0.8em;
  }
}

/* Mobile - Medium devices */
@media (max-width: 768px) {
  .chat-container {
    padding: 1rem;
  }

  .chat-header {
    padding: 1rem;
    gap: 1rem;
  }

  .chat-header h3 {
    font-size: 1.2rem;
  }

  .chat-header p {
    font-size: 0.9rem;
  }

  .chat-header img {
    width: 3rem;
    height: 3rem;
  }

  .messages-container {
    padding: 1rem;
    gap: 1rem;
  }

  .message-item {
    gap: 0.6rem;
    margin-bottom: 1rem;
  }

  .message-content {
    max-width: 75%;
    padding: 0.7em 0.9em;
    border-radius: 1.1em;
  }

  .message-content p {
    font-size: 0.9em;
    margin-bottom: 0.25em;
  }

  .message-time {
    font-size: 0.65em;
  }

  .message-input-container {
    padding: 1rem;
  }

  .input-wrapper {
    gap: 0.5rem;
  }

  .message-input {
    padding: 0.7em 0.9em;
    font-size: 0.9em;
    border-radius: 1.8em;
  }

  .send-button {
    width: 2.4em;
    height: 2.4em;
    padding: 0.7em;
  }

  .send-button i {
    font-size: 0.85em;
  }
}

/* Tablet devices */
@media (min-width: 769px) and (max-width: 1024px) {
  .chat-container {
    padding: 1.5rem;
  }

  .chat-header {
    padding: 1.2rem;
    gap: 1.2rem;
  }

  .chat-header h3 {
    font-size: 1.4rem;
  }

  .chat-header p {
    font-size: 1rem;
  }

  .chat-header img {
    width: 3.5rem;
    height: 3.5rem;
  }

  .messages-container {
    padding: 1.2rem;
    gap: 1.2rem;
  }

  .message-item {
    gap: 0.8rem;
    margin-bottom: 1.2rem;
  }

  .message-content {
    max-width: 70%;
    padding: 0.8em 1em;
    border-radius: 1.2em;
  }

  .message-content p {
    font-size: 0.95em;
    margin-bottom: 0.3em;
  }

  .message-time {
    font-size: 0.7em;
  }

  .message-input-container {
    padding: 1.2rem;
  }

  .input-wrapper {
    gap: 0.6rem;
  }

  .message-input {
    padding: 0.8em 1em;
    font-size: 0.95em;
    border-radius: 2em;
  }

  .send-button {
    width: 2.6em;
    height: 2.6em;
    padding: 0.8em;
  }

  .send-button i {
    font-size: 0.9em;
  }
}

/* Large desktop devices */
@media (min-width: 1440px) {
  .chat-container {
    padding: 2.5rem;
  }

  .chat-header {
    padding: 2rem;
    gap: 2rem;
  }

  .chat-header h3 {
    font-size: 1.7rem;
  }

  .chat-header p {
    font-size: 1.2rem;
  }

  .chat-header img {
    width: 4.5rem;
    height: 4.5rem;
  }

  .messages-container {
    padding: 2rem;
    gap: 2rem;
  }

  .message-item {
    gap: 1.2rem;
    margin-bottom: 2rem;
  }

  .message-content {
    max-width: 70%;
    padding: 1em 1.2em;
    border-radius: 1.5em;
  }

  .message-content p {
    font-size: 1.1em;
    margin-bottom: 0.4em;
  }

  .message-time {
    font-size: 0.8em;
  }

  .message-input-container {
    padding: 2rem;
  }

  .input-wrapper {
    gap: 1rem;
  }

  .message-input {
    padding: 1em 1.2em;
    font-size: 1.1em;
    border-radius: 2.5em;
  }

  .send-button {
    width: 3em;
    height: 3em;
    padding: 1em;
  }

  .send-button i {
    font-size: 1em;
  }
}

/* Landscape orientation for mobile */
@media (max-width: 768px) and (orientation: landscape) {
  .chat-header {
    padding: 0.8rem;
    gap: 0.8rem;
  }

  .chat-header img {
    width: 2.5rem;
    height: 2.5rem;
  }

  .messages-container {
    padding: 0.8rem;
    gap: 0.8rem;
  }

  .message-input-container {
    padding: 0.8rem;
  }
}

/* High DPI displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  .chat-header img {
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
  }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
  .send-button {
    min-height: 44px;
    min-width: 44px;
  }

  .message-input {
    min-height: 44px;
  }

  .chat-header img {
    min-height: 44px;
    min-width: 44px;
  }
}

/* Print styles */
@media print {
  .chat-container {
    padding: 0;
  }

  .message-input-container {
    display: none;
  }

  .message-item {
    break-inside: avoid;
  }

  .message-content {
    box-shadow: none;
    border: 1px solid #ddd;
  }
}
</style>
