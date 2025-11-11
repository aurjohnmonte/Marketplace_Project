<template>
  <div class="overlay">
    <div class="modal">
      <div class="modal-header">
        <h3>⚠️ Confirm Action</h3>
        <button class="close-btn" @click="$emit('close')">✕</button>
      </div>

      <div class="modal-body">
        <p>{{ question }}</p>
      </div>

      <div v-if="action_type === 'block' || action_type === 'unblock'" style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;">
        <div style="width: 90%; text-align: start; margin-bottom: 5px;">
            <label>Send message (Optional)</label>
        </div>
        <textarea placeholder="You can send a message if yes ..." style="width: 90%; height: 100px;" v-model="send_message">

        </textarea>
      </div>

      <div class="modal-footer">
        <button class="cancel-btn" @click="$emit('close')">Cancel</button>
        <button class="delete-btn" @click="$emit('confirm', send_message)">Yes</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['question', 'action_type'],
  data(){
    return{
        send_message: '',
    }
  }
}
</script>

<style scoped>
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(50, 50, 50, 0.705);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

.modal {
  width: 350px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #323232;
  color: #fff;
  padding: 10px 16px;
}

.modal-body {
  padding: 20px;
  text-align: center;
  color: #323232;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 10px 16px 16px;
}

.close-btn {
  background: transparent;
  border: none;
  color: white;
  font-size: 18px;
  cursor: pointer;
}

.cancel-btn {
  background-color: #ca9d73;
  border: none;
  color: white;
  padding: 8px 14px;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.2s;
}
.cancel-btn:hover {
  background-color: #c0673e;
}

.delete-btn {
  background-color: #e63946;
  border: none;
  color: white;
  padding: 8px 14px;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.2s;
}
.delete-btn:hover {
  background-color: #b71c1c;
}
</style>
