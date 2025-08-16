<template>
  <div class="deactivate-account">
    <header class="header">
      <h1>Deactivate Account</h1>
    </header>

    <section class="info">
      <p>
        When you deactivate your account, your profile and information will be
        temporarily removed from our platform.
      </p>
      <p>
        This will remove all your past interactions such as:
      </p>
      <ul>
        <li>Shop ratings and reviews</li>
        <li>Product ratings</li>
        <li>Messages sent to shops</li>
        <li>Other public activity</li>
      </ul>
      <p>
        You can reactivate your account anytime by logging in again with your
        email and password.
      </p>
    </section>

    <form @submit.prevent="handleDeactivate" class="form">
      <label for="email">Enter your email to confirm</label>
      <input
        id="email"
        type="email"
        v-model="email"
        placeholder="you@example.com"
        required
      />
      <button type="submit" class="deactivate-btn">
        Deactivate Account
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
      showOtpModal: false
    };
  },
  methods: {
    async handleDeactivate() {
      if (!this.email) {
        alert("Please enter your email before deactivating.");
        return;
      }

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

      try {
        const data = new FormData();
        data.append("email", this.email);
        data.append("otp", this.otpCode);
        data.append('type', 'deactivate');

        const res = await axios.post("/verify-otp", data);

        if (res.data.message === "successful") {
          alert("Account deactivated successfully.");
          this.closeModal();
          this.email = "";
          const store = useDataStore();
          let id = store.currentUser_info.id;
          this.$emit('stopLocation');
          store.reset();
          window.location.href=`/buyer/logout?id=${id}`;
        } else {
          alert(res.data.message || "Invalid OTP code.");
        }
      } catch (err) {
        console.error(err);
        alert("Error verifying OTP.");
      }
    }
  }
};
</script>

<style scoped>
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
