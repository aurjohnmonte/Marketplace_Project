<template>
    <div class="chat-container">
        <!-- Chat Header -->
        <div class="chat-header">
            <button @click="goBack" class="back-button">
                <i class="fa fa-arrow-left"></i>
            </button>
            <div class="chat-info" v-if="chatData">
                <img :src="chatData.profile" :alt="chatData.username" class="chat-avatar">
                <div class="user-details">
                    <h4>{{ chatData.username }}</h4>
                    <span class="status" :class="{ 'online': isOnline }">
                        {{ isOnline ? 'Online' : 'Offline' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="messages-area" ref="messagesArea">
            <div v-if="messages.length === 0" class="no-messages">
                <p>No messages yet. Start the conversation!</p>
            </div>

            <div v-else class="messages-list">
                <div
                    v-for="message in messages"
                    :key="message.id"
                    class="message-item"
                    :class="{ 'sent': message.sender === 'seller', 'received': message.sender === 'user' }"
                >
                    <div class="message-content">
                        <p>{{ message.text }}</p>
                        <span class="message-time">{{ formatTime(message.timestamp) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Input -->
        <div class="message-input-container">
            <div class="input-wrapper">
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
export default {
    data() {
        return {
            chatData: null,
            messages: [],
            newMessage: '',
            isOnline: true,
            messageId: 1,
            timeUpdateInterval: null
        }
    },
    mounted() {
        console.log('Chats component mounted');
        console.log('Route query:', this.$route.query);

        // Get chat data from query parameters
        this.chatData = {
            username: this.$route.query.username,
            profile: this.$route.query.profile,
            message: this.$route.query.message
        };

        console.log('Chat data:', this.chatData);

        // Load initial messages
        this.loadMessages();

        // Scroll to bottom
        this.$nextTick(() => {
            this.scrollToBottom();
        });

        // Start timer to update timestamps every minute
        this.timeUpdateInterval = setInterval(() => {
            this.$forceUpdate();
        }, 60000);
    },
    methods: {
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

        sendMessage() {
            if (!this.newMessage.trim() || !this.chatData) return;

            const message = {
                id: ++this.messageId,
                text: this.newMessage.trim(),
                sender: 'seller',
                timestamp: new Date()
            };

            this.messages.push(message);
            this.newMessage = '';


            this.$nextTick(() => {
                this.scrollToBottom();
            });


            setTimeout(() => {
                const userResponse = {
                    id: ++this.messageId,
                    text: this.generateUserResponse(),
                    sender: 'user',
                    timestamp: new Date()
                };
                this.messages.push(userResponse);

                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            }, 2000);
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
    font-size: 0.8em;
    color: #666;
}

.status.online {
    color: #4CAF50;
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
    height: 100%;
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
</style>
