<template>
  <div class="deactivate-account">


    <div class="overlay" v-if="show_loading">
      <img src="../../../images/kOnzy.gif" style="width: 100px; height: 100px;">
    </div>

    <header class="header">
      <h1>Delete Account</h1>
    </header>

    <section class="info">
      <p>
        Deleting your account will result a permanent  deletion of your data which means after deletion it cannot be restored neither be used again. 
      </p>
      <p>
        This will completely remove all your data in our platform unfortunately.
      </p>

      <p>
        Do you still want to proceed on the deletion of your account?
      </p>
    </section>

    <form @submit.prevent="handleDeactivate" class="form">
      <label for="email">Enter your email to proceed</label>
      <input
        id="email"
        type="email"
        v-model="email"
        placeholder="you@example.com"
        required
      />
      <button type="submit" class="deactivate-btn">
        Delete Account
      </button>
    </form>

    <!-- OTP Modal -->
    <div v-if="showOtpModal" class="modal-backdrop">
      <div class="modal">
        <h2>Enter OTP Code</h2>
        <p>We have sent a verification code to your email.</p>
        <input
          type="text"
          v-model="otpCode"
          maxlength="6"
          placeholder="Enter 6-digit code"
        />
        <div class="modal-actions">
          <button @click="verifyOtp" class="confirm-btn">Confirm</button>
          <button @click="closeModal" class="cancel-btn">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { useDataStore } from "../../stores/dataStore";

export default {
  name: "DeactivateAccount",
  data() {
    return {
      email: "",
      otpCode: "",
      showOtpModal: false,
      show_loading: false,
    };
  },
  methods: {
    async handleDeactivate() {
      if (!this.email) {
        alert("Please enter your email before deactivating.");
        return;
      }

      this.show_loading = true;

      const data = new FormData();
      data.append("email", this.email);

      try {
        const res = await axios.post("/create-otp", data);

        if (res.data.message === "Successfully") {
          this.showOtpModal = true;
        } else {
          alert(res.data.message || "Something went wrong.");
        }
      } catch (err) {
        console.error(err);
        alert("Error sending OTP.");
      }
      finally{
        this.show_loading = false;
      }
    },
    closeModal() {
      this.showOtpModal = false;
      this.otpCode = "";
    },
    async verifyOtp() {
      if (!this.otpCode) {
        alert("Please enter the OTP code.");
        return;
      }

      this.show_loading = true;

      try {
        const data = new FormData();
        data.append("email", this.email);
        data.append("otp", this.otpCode);
        data.append('type', 'delete');

        const res = await axios.post("/verify-otp", data);

        console.log(res.data.message);

        if (res.data.message === "successful") {

          const store = useDataStore();
          let id = store.currentUser_info.id;

          const data = new FormData();
          data.append('id', id);

          const res = await axios.post('/buyer/delete-account', data);

          console.log('RES: ', res.data.message);

          if(res.data.message === 'successful'){
            alert("Account deleted successfully.");
            this.closeModal();
            this.email = "";
            this.$emit('stopLocation');
            store.reset();
            window.location.href=`/logout`;
          }
          
        } else {
          alert(res.data.message || "Invalid OTP code.");
        }
      } catch (err) {
        console.error(err);
        alert("Error verifying OTP.");
      }
      finally{
        this.show_loading = false;
      }
    }
  }
};
</script>

<style scoped>
.overlay{
  position: fixed;
  background-color: rgba(0, 0, 0, 0.672);
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
}
.deactivate-account {
  max-width: 500px;
  margin: 0 auto;
  padding: 20px;
  font-family: Arial, sans-serif;
  background: #f8f9fa;
}

.header {
  text-align: center;
  margin-bottom: 20px;
}

.header h1 {
  font-size: 1.5rem;
  color: #dc3545;
}

/* Info box */
.info {
  background: #fff3cd;
  border: 1px solid #ffeeba;
  padding: 15px;
  border-radius: 6px;
  font-size: 0.95rem;
  margin-bottom: 20px;
  color: #856404;
}

.info ul {
  margin: 10px 0;
  padding-left: 20px;
}

.info li {
  margin-bottom: 5px;
}

/* Form styles */
.form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

label {
  font-size: 0.9rem;
  font-weight: bold;
}

input {
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
}

.deactivate-btn {
  background: #ffc107;
  color: #333;
  padding: 14px;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
}

.deactivate-btn:hover {
  background: #e0a800;
}

/* Mobile */
@media (max-width: 500px) {
  .deactivate-account {
    padding: 15px;
  }

  .header h1 {
    font-size: 1.3rem;
  }
}
/* Modal styles */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: 90%;
  max-width: 350px;
  text-align: center;
  margin: 10px;
}

.modal h2 {
  margin-bottom: 10px;
  font-size: 1.3rem;
}

.modal p {
  font-size: 0.9rem;
  color: #555;
  margin-bottom: 15px;
}

.modal input {
  width: 90%;
  padding: 12px;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  margin-bottom: 15px;
}

.modal-actions {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}

.confirm-btn {
  background: #28a745;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  flex: 1;
}

.cancel-btn {
  background: #dc3545;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  flex: 1;
}
</style>
