<template>
    <!-- Display if signup is clicked -->
    <div v-if="currentStep === 'user'" class="user-type"> <!-- Buttons for determining the userType -->
        <button
            @click="userType = 'seller'"
            class="typebox"
            id="seller"
            :class="{
            active: userType === 'seller',
            inactive: userType && userType !== 'seller'
            }"
        >Seller</button>
        <button
            @click="userType = 'buyer'"
            class="typebox"
            id="buyer"
            :class="{
            active: userType === 'buyer',
            inactive: userType && userType !== 'buyer'
            }"
        >Buyer</button>
    </div>

    <!-- User Signup Form -->
    <form @submit.prevent="handleNext" class="form-content" v-if="userType && currentStep === 'user'">
        <h1>Sign up</h1>
            <div
                v-for="(pair, idx) in inputPairs"
                :key="idx"
                class="input-row"
            >
                <div
                    v-for="info in pair"
                    :key="info.id"
                    class="input-box"
                >
                    <template v-if="info.type === 'select'">
                        <select
                            :name="info.name"
                            :id="info.id"
                            v-model="formData[info.name]"
                            required
                        >
                            <option v-for="option in info.options" :key="option.value" :value="option.value">{{ option.text }}</option>
                        </select>
                        <label :for="info.id" :class="{ floated: formData[info.name] }">{{ info.label }}</label>
                    </template>
                    <template v-else>
                        <input
                            :type="info.type"
                            :name="info.name"
                            :id="info.id"
                            v-model="formData[info.name]"
                            required
                        />
                        <label :for="info.id" :class="{ floated: formData[info.name] }">{{ info.label }}</label>
                    </template>
                </div>
            </div>

            <button v-if="userType==='buyer'" class="form-btn" type="submit">Sign Up</button>
            <button v-if="userType==='seller'" class="form-btn" type="submit">Next</button>
    </form>

    <!-- Shop Signup Form -->
    <ShopSignup v-if="currentStep === 'shop'" :userType="userType" :userData="formData" />

    <Login v-if="currentStep === 'login'" :userType="userType" :userData="formData" />

</template>

<script>
import ShopSignup from '../forms/ShopSignup.vue';
import Login from '../forms/Login.vue';

export default {
    components: {
        ShopSignup,
        Login
    },
    data() {
        return {
            action: 'signup',
            userType: null,
            currentStep: 'user', // 'user' or 'shop'
            formData: {
                firstname: '',
                m_initial: '',
                lastname: '',
                gender: '',
                birthday: '',
                age: '',
                email: '',
                contact_no: '',
                username: '',
                password: ''
            },
            requiredInfo: [
                { label: 'Firstname', type: 'text', name: 'firstname', id: 'firstname' },
                { label: 'Middle Initial', type: 'text', name: 'm_initial', id: 'm_initial' },
                { label: 'Lastname', type: 'text', name: 'lastname', id: 'lastname' },
                { label: 'Gender', type: 'select', name: 'gender', id: 'gender', options: [ { value: '', text: 'Select Gender' }, { value: 'male', text: 'Male' }, { value: 'female', text: 'Female' } ] },
                { label: 'Birthday', type: 'date', name: 'birthday', id: 'birthday' },
                { label: 'Age', type: 'number', name: 'age', id: 'age' },
                { label: 'Email', type: 'email', name: 'email', id: 'email' },
                { label: 'Contact Number', type: 'tel', name: 'contact_no', id: 'contact_no' },
                { label: 'Username', type: 'text', name: 'username', id: 'username'},
                { label: 'Password', type: 'password', name: 'password', id: 'password'},
                { label: 'Confirm Password', type: 'confirm_password', name: 'confirm_password', id: 'confirm_password'}
            ]
        }
    },
    computed: {
        inputPairs() {
            const pairs = [];
            for (let i = 0; i < this.requiredInfo.length; i += 2) {
                pairs.push(this.requiredInfo.slice(i, i + 2));
            }
            return pairs;
        },
        isFormValid() {
            return Object.values(this.formData).every(value => value !== '');
        }
    },
    methods: {
        handleNext(event) {
            event.preventDefault(); // Prevent form submission

            if (!this.isFormValid) {
                alert('Please fill in all required fields');
                return;
            }

            if (this.userType === 'seller') {
                // For sellers, go to shop signup
                this.currentStep = 'shop';
            } else {
                // For buyers, submit the form
                this.submitBuyerForm();
            }
        },
        submitBuyerForm() {
            // send the data to the backend
            console.log('Buyer signup data:', this.formData);
            this.currentStep = 'login'
        }
    }
}
</script>

<style>
/* Main container */
.signup-container  {
  background-color:	#F5F5DC;
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 40%;
  border: 1px solid #000;
  border-radius: 10px;
}

/* conteiner containing the buttons */
.user-type {
  display: flex;
  width: 100%;
}

/* for the buttons (seller/buyer) */
.typebox {
  flex: 1;
  text-align: center;
  padding: 1rem 0;
  border: 1px solid #ddd;
  border-radius: 8px;
  cursor: pointer;
}
.typebox#buyer{
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
}
.typebox#seller{
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}

/* for active and inactive button designs */
.typebox.active {
  background-color: #F5F5DC !important;
  border: none;
  font-weight: 700;
}
.typebox.inactive {
  background-color: #49494975 !important;
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
  border: none;
}
.typebox.inactive#buyer{
  border-top-left-radius: 0;
}
.typebox.inactive#seller{
  border-top-right-radius: 0;
}

/* div containing the form */
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

.form-header{
    margin-bottom: 1rem;
    text-align: left;
    width: 100%;
}

.form-header h1{
    text-transform: uppercase;
    text-align: center;
    margin-bottom: 1rem;
}

.form-header p {
    padding-left: 15%;
}
.form-header strong {
    text-transform: uppercase;
}

.form-content form{
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
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
</style>
