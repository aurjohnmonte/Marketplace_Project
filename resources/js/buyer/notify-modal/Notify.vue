<template>
  <div class="notify-modal">
    <div class="icon">✔</div>
    <p>{{ message }}</p>
  </div>
</template>

<script>
export default {
    props: ['message'],
  name: 'FollowSuccessModal'
}
</script>

<style scoped>
.notify-modal {
  position: fixed;
  top: 100px;
  right: 20px;
  background: #4caf50;
  color: white;
  padding: 10px 20px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  font-weight: 100;
  display: flex;
  align-items: center;
  gap: 10px;
  animation: slideIn 0.4s ease, fadeOut 0.4s ease 3s forwards;
  z-index: 10000;
}

.icon {
  background: white;
  color: #4caf50;
  border-radius: 50%;
  width: 22px;
  height: 22px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: bold;
}

/* Slide in and fade out animations */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeOut {
  to {
    opacity: 0;
    transform: translateY(-10px);
  }
}
</style>
