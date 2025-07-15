<template>
    <div class="form-content">
        <form method="POST" @submit.prevent="handleSubmit">
            <h1>LOGIN</h1>
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
                <label :for="field.id">{{ field.label }}</label>
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
export default {
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
        handleSubmit() {
            if (this.validateForm()) {
                alert('Login successful! (Validation passed)');
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
  height: auto;
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

/* for the input */
.input-box {
  display: flex;
  flex-direction: column;
  position: relative;
}

.input-box input,
.input-box select {
  width: 100%;
  padding:  10px;
  font-size: 12px;
  border: 1px solid #9e363a;
  border-radius: 8px;
  background-color: #ffffff;
  color: #000000;
  box-sizing: border-box;
}
/* for the label */
.input-box label {
  position: absolute;
  top: 12px;
  padding-left: 12px;
  font-size: 13px;
  color: #575454;
  background-color: #ffffff;
  width: 70%;
  margin: 0 10px;
  pointer-events: none;
  transition: 0.2s ease all;
}

/* input and label animation */
.input-box label.floated {
  top: -8px;
  left: 10px;
  width: fit-content;
  font-size: 10px;
  padding: 0 10px;
  background-color: #091f36;
  border: #9e363a 1px solid;
  border-radius: 10px;
  color: #ffffff;
}

.input-box input:focus + label,
.input-box input.has-content + label {
  top: -8px;
  left: 10px;
  width: fit-content;
  font-size: 10px;
  padding: 0 10px;
  background-color: #091f36;
  border: #9e363a 1px solid;
  border-radius: 10px;
  color: #ffffff;
}

/* input type="date" */
  .input-box input:focus + label,
  .input-box input:valid + label {
    top: -8px;
    left: 10px;
    width: fit-content;
    font-size: 10px;
    padding: 0 10px;
    background-color: #091f36;
    border: #9e363a 1px solid;
    border-radius: 10px;
    color: #ffffff;
  }

  input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
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
