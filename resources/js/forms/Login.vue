<template>
    <div class="form-content">
        <label style="color: green; font-weight: bolder;">{{ this.message }}</label>
        <form method="POST" @submit.prevent="handleSubmit">
            <h1 style="margin-bottom: .5em;">LOGIN</h1>
            <div
                v-for="field in requiredInfo"
                :key="field.id"
                class="input-group"
            >
            <div class="input-box">
                <input
                v-model="data[field.id]"
                :type="field.type"
                :name="field.id"
                :id="field.id"
                required
                @blur="validateField(field.id)"
                @input="validateField(field.id)"
                />
                <label class="label" :for="field.id">{{ field.label }}</label>
                <div v-if="errors[field.id]" class="error-message">{{ errors[field.id] }}</div>
            </div>
            </div>
            <button
                class="form-btn style"
            >Login</button>
        </form>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    props: [
        'message',
    ],
    data() {
        return {
            action: 'login',
            requiredInfo: [
                { id: 'username', label: 'Username', type: 'text' },
                { id: 'password', label: 'Password', type: 'password' }
            ],
            data: {
                username: '',
                password: ''
            },
            errors: {}
        }
    },
    methods: {
        validateField(field) {
            if (field === 'username') {
                if (!this.data.username) {
                    this.errors.username = 'Username is required.';
                } else if (this.data.username.length < 4) {
                    this.errors.username = 'Username must be at least 4 characters.';
                } else {
                    delete this.errors.username;
                }
            }
            if (field === 'password') {
                if (!this.data.password) {
                    this.errors.password = 'Password is required.';
                } else if (this.data.password.length < 8) {
                    this.errors.password = 'Password must be at least 8 characters.';
                } else {
                    delete this.errors.password;
                }
            }
        },
        validateForm() {
            this.validateField('username');
            this.validateField('password');
            return Object.keys(this.errors).length === 0;
        },
        async handleSubmit() {
            if (this.validateForm()) {

                const formdata = new FormData();
                formdata.append('username', this.data.username);
                formdata.append('password', this.data.password);

                const res = await axios.post('/user/login', formdata);

                if(res.data.message === 'successful'){

                    if(res.data.type === 'seller'){
                        window.location.href = "/seller";
                        return;
                    }
                    else{
                        window.location.href = "/buyer";
                        return;
                    }
                }
                else if(res.data.message === 'user not found'){
                    window.alert('USER NOT FOUND');
                }
                else{
                    window.alert("PASSWORD INCORRECT");
                }
            }
        }
    }
}
</script>

<style>
/* Main container */
.login-container  {
  background-color:	#F5F5DC;
  display: flex;
  flex-direction: column;
  width: 30%;
  min-width: 320px;
  max-width: 600px;
  margin: 0 auto;
  padding: 3em 0;
  border: 1px solid #000;
  border-radius: 10px;
  border-radius: 10px;
  box-sizing: border-box;
}

.form-content{
  flex: 1;
  width: 100%;
  height: 100%;
  padding: 2rem 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.form-content form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* for each row */
.input-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  margin-bottom: 1rem;
}

/* submit/next button */
.form-btn {
  padding: 8px;
  border-radius: 10px;
}
.error-message {
  color: #b00020;
  font-size: 12px;
  margin-top: 2px;
}
</style>
