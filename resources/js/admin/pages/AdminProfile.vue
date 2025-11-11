<template>
  <div class="profile-modal">

    <!--IS LOADING-->
    <teleport to="body">
        <div class="overlay" v-if="is_loading">
            <img src="../../../images/kOnzy.gif" style="width: 100px; height: 100px;">
        </div>
    </teleport>
    <!-- Header -->
    <div class="modal-header">
      <h3>Admin Profile</h3>
      <span class="material-icons close-icon" @click="$emit('close')">close</span>
    </div>

    <!-- Profile Image -->
    <div class="profile-image-section">
      <img :src="previewImage || '/'+admin.path" alt="Profile" class="profile-pic" />
      <label for="imageUpload" class="upload-btn">Change Photo</label>
      <input id="imageUpload" type="file" accept="image/*" @change="onImageChange" hidden />
    </div>

    <!-- Info Fields -->
    <div class="form-group">
      <label>Email</label>
      <input type="email" v-model="form.email" disabled />
    </div>

    <div class="form-group">
      <label>Username</label>
      <input type="text" v-model="form.username" />
    </div>

    <div class="form-group">
      <label>New Password</label>
      <input type="password" v-model="form.password" placeholder="Enter new password" />
    </div>

    <div class="form-group">
      <label>Confirm Password</label>
      <input type="password" v-model="form.confirmPassword" placeholder="Confirm password" />
    </div>

    <label style="cursor: pointer;">Logout</label>

    <!-- Save Button -->
    <div class="button-container">
      <button @click="saveChanges" class="save-btn">Save Changes</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
    props: ['admin'],
  data() {
    return {
      previewImage: null,
      file_photo: null,
      is_loading: false,
      form: {
        email: 'aurjohn@gmail.com',
        username: 'admin123',
        password: '',
        confirmPassword: ''
      }
    };
  },
  methods: {
    onImageChange(e) {
      const file = e.target.files[0];
      if (file) {
        this.previewImage = URL.createObjectURL(file);
      }

      this.file_photo = file;
    },
    async saveChanges() {

      if (this.form.password !== this.form.confirmPassword) {
        alert('Passwords do not match!');
        return;
      }

      this.is_loading = true;

      const payload = {
        username: this.form.username,
        password: this.form.password,
      };

      const data = new FormData();
      data.append('data', JSON.stringify(payload));
      data.append('file_photo', this.file_photo);

      const res = await axios.post('/admin/edit-info', data);

      console.log(res.data.message);

      if(res.data.message === 'success'){
        
        this.$emit('save', res.data.admin);

        window.alert("YOU SUCCESSFULLY UPDATE YOUR INFO");
      }
      else{

        window.alert("SOMETHING WENT WRONG!");
      }

      this.is_loading = false;
      
    }
  },
  mounted(){

    this.form = this.admin;
  }
};
</script>

<style scoped>
.overlay{
    position: fixed;
    left: 0;
    top: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.841);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
}
.profile-modal {
  position: absolute;
  top: 70px;
  right: 20px;
  width: 300px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 3px 15px rgba(0, 0, 0, 0.15);
  padding: 20px;
  z-index: 200;
  border: 1px solid #e0e0e0;
}

/* Header */
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}
.modal-header h3 {
  font-size: 18px;
  margin: 0;
  color: #333;
}
.close-icon {
  cursor: pointer;
  color: #666;
  font-size: 22px;
  transition: 0.2s;
}
.close-icon:hover {
  color: #111;
}

/* Profile Section */
.profile-image-section {
  text-align: center;
  margin-bottom: 20px;
}
.profile-pic {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #ccc;
}
.upload-btn {
  display: inline-block;
  margin-top: 8px;
  font-size: 13px;
  color: #323232;
  cursor: pointer;
}
.upload-btn:hover {
  text-decoration: underline;
}

/* Input Fields */
.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 12px;
}
label {
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 4px;
  color: #444;
}
input {
  padding: 8px;
  font-size: 14px;
  border-radius: 6px;
  border: 1px solid #ccc;
  outline: none;
  transition: 0.2s;
}
input:focus {
  border-color: #323232;
}
input:disabled {
  background: #f3f3f3;
  color: gray;
}

/* Save Button */
.button-container {
  text-align: right;
}
.save-btn {
  background: #323232;
  color: white;
  border: none;
  padding: 8px 15px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.2s;
}
.save-btn:hover {
  background: #32323290;
}

@media screen and (min-width: 600px) {
    .profile-modal {
        position: absolute;
        top: 100px;
    }
}
</style>
