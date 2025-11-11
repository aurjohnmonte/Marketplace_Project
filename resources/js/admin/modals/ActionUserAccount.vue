<template>
  <div class="overlay" @click.self="closeModal">

    <div v-if="is_loading">
      <img src="../../../images/kOnzy.gif" style="width: 100px; height: 100px;">
    </div>

    <div class="action-modal" v-else>
      <div class="modal-header">
        <h3>Account Action</h3>
      </div>

      <div class="modal-body">
        <p>
          Are you sure you want to <strong>deactivate</strong> or <strong>delete</strong> this account?
        </p>
        <p class="user-name">
          User: <span>{{ user.name }}</span> <br />
          Email: <span>{{ user.email }}</span>
        </p>

        <div class="message-box">
          <label for="message">Message to user (optional but recommended):</label>
          <textarea
            id="message"
            v-model="message"
            placeholder="Write your reason or explanation here..."
            rows="4"
          ></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button class="cancel-btn" @click="closeModal">Cancel</button>
        <button class="deactivate-btn" @click="deactivateUser" v-if="!user.is_deactivate">Deactivate</button>
        <button class="deactivate-btn" @click="activateUser" v-else>Activate</button>
        <button class="delete-btn" @click="deleteUser">Delete</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: ['user'],
  emits: ['close', 'deactivate', 'delete'],
  data() {
    return {
      message: '',
      is_loading: false,
    }
  },
  methods: {
    async activateUser(){

      this.is_loading = true;
      const data = new FormData();
      data.append('id', this.user.id);
      data.append('message', this.message);

      const res = await axios.post('/admin/activate-user', data);

      console.log(res.data.message);

      if(res.data.message === 'success'){
        window.alert('YOU HAVE SUCCESSFULLY ACTIVATE THE ACCOUNT');
        
        this.$emit('save');
      }
      else{
        window.alert('SOMETHING WENT WRONG');
      }

      this.is_loading = false;
    },    
    closeModal() {
      this.$emit('close')
    },
    async deactivateUser() {
      
      this.is_loading = true;
      const data = new FormData();
      data.append('id', this.user.id);
      data.append('message', this.message);

      const res = await axios.post('/admin/deactivate-user', data);

      console.log(res.data.message);

      if(res.data.message === 'success'){
        window.alert('YOU HAVE SUCCESSFULLY DEACTIVATE THE ACCOUNT');
        
        this.$emit('save');
      }
      else{
        window.alert('SOMETHING WENT WRONG');
      }

      this.is_loading = false;
    },



    async deleteUser() {

      this.is_loading = true;
      const data = new FormData();
      data.append('id', this.user.id);
      data.append('message', this.message);

      console.log('ROLE: ', this.user.role);
      
      let res = null;

      if(this.user.role === 'seller'){
        res = await axios.post('/admin/delete-seller/account', data);
      }
      else{
        res = await axios.post('/admin/delete-buyer/account', data);
      }
      

      console.log(res.data.message);


      if(res.data.message === 'success'){
        window.alert('YOU SUCCESSFULLY DELETED AN ACCOUNT');
        this.$emit('save');
      }
      else{
        window.alert('SOMETHING WENT WRONG');
      }


      this.is_loading = false;
    },



  }
}
</script>

<style scoped>
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.75);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999999;
}

.action-modal {
  background-color: #fff;
  width: 380px;
  border-radius: 12px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
  padding: 20px;
  animation: fadeIn 0.3s ease-in-out;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.3rem;
  color: #323232;
  border-bottom: 1px solid #ddd;
  padding-bottom: 8px;
}

.modal-body {
  margin-top: 15px;
  color: #323232;
}

.user-name {
  margin-top: 10px;
  font-size: 0.95rem;
  color: #555;
}

.user-name span {
  color: #c0673e;
  font-weight: bold;
}

.message-box {
  margin-top: 15px;
  display: flex;
  flex-direction: column;
}

.message-box label {
  font-weight: bold;
  color: #323232;
  margin-bottom: 6px;
  font-size: 0.9rem;
}

.message-box textarea {
  resize: none;
  padding: 8px;
  border-radius: 8px;
  border: 1px solid #ccc;
  outline: none;
  font-size: 0.9rem;
  transition: 0.2s;
}

.message-box textarea:focus {
  border-color: #c0673e;
  box-shadow: 0 0 3px #c0673e80;
}

.modal-footer {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

button {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
  transition: 0.2s;
}

.cancel-btn {
  background-color: #32323290;
  color: white;
}

.cancel-btn:hover {
  background-color: #323232;
}

.deactivate-btn {
  background-color: #ca9d73;
  color: white;
}

.deactivate-btn:hover {
  background-color: #c0673e;
}

.delete-btn {
  background-color: #d9534f;
  color: white;
}

.delete-btn:hover {
  background-color: #c9302c;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
