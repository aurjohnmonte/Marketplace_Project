<template>
    <div class="form-content">
        <form method="POST" @submit.prevent="handleSubmit">
            <h1 style="margin-top: .5em;">LOGIN</h1>
            <div class="form-success" v-if="message">
                <svg data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                </svg>
                <label>{{ this.message }}</label>
            </div>
            <div
                v-for="field in requiredInfo"
                :key="field.id"
                class="input-group"
            >
                <div class="input-box">
                    <!-- Normal fields -->
                    <div v-if="field.id !== 'password'" class="wrapper">
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
                    </div>

                    <!-- Password field with toggle -->
                    <div v-else class="wrapper">
                        <input
                            v-model="data.password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            id="password"
                            required
                            @blur="validateField('password')"
                            @input="validateField('password')"
                        />
                        <label class="label" :for="field.id">{{ field.label }}</label>
                        <span class="toggle-icon" @click="togglePassword">
                        <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <!-- Eye-off icon -->
                            <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.98 8.223A10.477 10.477 0 0 0 1.5 12c1.36 4.45 5.58 7.5 10.5 7.5
                                1.83 0 3.55-.43 5.06-1.223M6.223 6.223A10.477 10.477 0 0 1
                                12 4.5c4.92 0 9.14 3.05 10.5 7.5a10.477 10.477 0 0 1-2.223
                                3.277M6.223 6.223 3 3m3.223 3.223 12.554 12.554" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <!-- Eye icon -->
                            <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12c0 0 4.5-7.5 9.75-7.5S21.75 12
                                21.75 12s-4.5 7.5-9.75 7.5S2.25 12 2.25 12Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 15.75a3.75 3.75 0 1 0 0-7.5
                                3.75 3.75 0 0 0 0 7.5Z" />
                        </svg>
                        </span>
                    </div>
                </div>
            </div>
            <button class="form-btn style"
                :disabled="Object.keys(this.errors).length > 0"
                >Login
            </button>
            <router-link to="/admin" class="admin-btn form-btn">Login as Admin</router-link>
        </form>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    props: [
        'message',
    ],
    mounted(){
        this.errors.username = 'Username must be at least 4 characters.';
        this.errors.password = 'Password must be at least 8 characters.';
    },
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
            errors: {},
            showPassword: false
        }
    },
    computed: {
        activeErrors() {
            return Object.values(this.errors);
        }
    },
    watch: {
        activeErrors: {
            handler(newVal) {
                this.$emit('update-errors', newVal);
            },
            immediate: true,
            deep: true
        }
    },
    methods: {
        togglePassword() {
            this.showPassword = !this.showPassword;
        },
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
                    this.errors.password = 'User not found!';
                }
                else{
                    this.errors.password = "Incorrect Password!";
                }
            }
        }
    }
}
</script>

<style scoped>
.admin-btn{
    background-color: transparent;
    outline: none;
    border: none;
    color: #555;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    font-size: .75em;
    font-weight: 500;
    letter-spacing: 0.2px;
    text-decoration: none;
    text-transform: uppercase;
    cursor: pointer;
    transition: all .25s ease;
}

.admin-btn:hover {
    color: #333;
}

.admin-btn:active {
    color: rgba(0, 0, 0, 0.05);
}

.form-success {
    display: flex;
    align-items: center;
    padding: .5em .8em;
    border-radius: .8em;
    font-size: .7em;
    background-color: rgb(163, 224, 163);
    margin-bottom: 1em;
}

.form-success svg {
    width: 2em;
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

.wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.wrapper input {
  width: 100%;
  padding-right: 2.5em; /* space for the icon */
}

.toggle-icon {
  position: absolute;
  right: 0.5em;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.toggle-icon svg {
  width: 20px;
  height: 20px;
  color: #555;
}

/* input type="date" */
  .input-box input:focus + .label,
  .input-box input:valid + .label {
    top: -8px;
    left: 5px;
    width: fit-content;
    font-size: 10px;
    padding: 0 10px;
    background-color: #091f36;
    border: #9e363a 1px solid;
    border-radius: 10px;
    color: #ffffff;
  }

/* Disabling the browser's password icon feature */
/* For Microsoft Edge */
.wrapper input[type="password"]::-ms-reveal {
  display: none;
}

/* For other browsers that might introduce a similar icon */
.wrapper input[type="password"]::-webkit-password-toggle-button {
  -webkit-appearance: none;
  appearance: none;
  display: none;
}

/* Also, set -moz-appearance to none just in case */
.wrapper input[type="password"] {
  -moz-appearance: none;
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

@media (max-width: 780px) {
    .login-container{
        padding: 1em;
        margin: 0;
    }
    .form-btn {
        margin-bottom: 1em;
    }
}
</style>
