<template>
    <!-- Display if signup is clicked -->
    <div v-if="currentStep === 'user'" class="user-type"> <!-- Buttons for determining the userType -->
        <button
            @click="changeType('seller')"
            class="typebox"
            id="seller"
            :class="{
            active: userType === 'seller',
            inactive: userType && userType !== 'seller'
            }"
        >Seller</button>
        <button
            @click="changeType('buyer')"
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
        <div class="form-header">
            <h1>Sign up</h1>
            <p>Sign up as a <strong>{{ userType }}</strong></p>
        </div>

        <div v-if="allCapsError" class="error-message" style="color: red; margin-bottom: 1rem;">
            Please avoid using ALL CAPS in the following fields: {{ allCapsFields.join(', ') }}
        </div>

        <!-- Personal Information Section -->
        <div class="section-header"><strong>Personal Information</strong></div>
        <div>
            <div v-for="(pair, idx) in personalInfoPairs" :key="'personal-row-' + idx" class="input-row">
                <div v-for="info in pair" :key="info.id" class="input-box">
                    <!-- template for select type input with options-->
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

                    <!-- for tel type inputs like contact numbers -->
                    <template v-else-if="info.type === 'tel'">
                        <input
                            :type="info.type"
                            :name="info.name"
                            :id="info.id"
                            :pattern="info.pattern"
                            v-model="formData[info.name]"
                            required
                        />
                        <label :for="info.id" :class="{ floated: formData[info.name] }">{{ info.label }}</label>
                    </template>

                    <!-- for other input types that is not mentioned above -->
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
        </div>

        <!-- Account Information Section -->
        <div class="section-header"><strong>Account Information</strong></div>
        <div>
            <div v-for="(pair, idx) in accountInfoPairs" :key="'account-row-' + idx" class="input-row">
                <div v-for="info in pair" :key="info.id" class="input-box">
                    <input
                        :type="info.name === 'confirm_password' ? 'password' : info.type"
                        :name="info.name"
                        :id="info.id"
                        v-model="formData[info.name]"
                        required
                    />
                    <label :for="info.id" :class="{ floated: formData[info.name] }">{{ info.label }}</label>

                </div>
            </div>
        </div>

        <div class="terms" v-if="userType === 'buyer'">
            <div class="checkbox">
                <input type="checkbox" name="location_access" id="location_access" v-model="formData.location_access" :true-value="'yes'" :false-value="''">
                <label for="location_access">Allow location access to the nearest shop.</label>
            </div>

            <div class="checkbox">
                <input type="checkbox" name="terms" id="terms" v-model="formData.terms" :true-value="'yes'" :false-value="''">
                <label for="terms">I agree to the <strong>Terms</strong> and <strong>Conditions</strong></label>
            </div>
        </div>

        <button v-if="userType==='buyer'" class="form-btn" type="submit">Sign Up</button>
        <button v-if="userType==='seller'" class="form-btn" type="submit">Next</button>
    </form>

    <!-- Shop Signup Form -->
    <ShopSignup v-if="currentStep === 'shop'" :userType="userType" :userData="formData" />

    <!-- Buyer Signup Form -->
    <Login v-if="currentStep === 'login'" :userType="userType" :userData="formData" />

</template>

<script>
import ShopSignup from '../forms/ShopSignup.vue';
import Login from '../forms/Login.vue';

function groupInPairs(arr) {
    const pairs = [];
    for (let i = 0; i < arr.length; i += 2) {
        pairs.push(arr.slice(i, i + 2));
    }
    return pairs;
}

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
                password: '',
                location_access: '',
                terms: ''
            },
            requiredInfo: {
                personalInformation: [
                    { label: 'Firstname', type: 'text', name: 'firstname', id: 'firstname' },
                    { label: 'Middle Initial', type: 'text', name: 'm_initial', id: 'm_initial' },
                    { label: 'Lastname', type: 'text', name: 'lastname', id: 'lastname' },
                    { label: 'Gender', type: 'select', name: 'gender', id: 'gender', options: [ { value: '', text: 'Select Gender' }, { value: 'male', text: 'Male' }, { value: 'female', text: 'Female' } ] },
                    { label: 'Birthday', type: 'date', name: 'birthday', id: 'birthday' },
                    { label: 'Age', type: 'number', name: 'age', id: 'age' },
                    { label: 'Email', type: 'email', name: 'email', id: 'email' },
                    { label: 'Contact Number', type: 'tel', name: 'contact_no', id: 'contact_no', pattern: '[0-9]{11}' },
                ],
                accountInformation: [
                    { label: 'Username', type: 'text', name: 'username', id: 'username'},
                    { label: 'Password', type: 'password', name: 'password', id: 'password'},
                    { label: 'Confirm Password', type: 'confirm_password', name: 'confirm_password', id: 'confirm_password'}
                ]
            },
            allCapsError: false,
            allCapsFields: []
        }
    },
    computed: {
        isFormValid() {
            // Only check required fields for the current userType
            const requiredFields = [
                'firstname', 'm_initial', 'lastname', 'gender', 'birthday', 'age', 'email', 'contact_no',
                'username', 'password', 'confirm_password'
            ];
            /* this checks if all input fields are filled */
            for (const field of requiredFields) {
                if (!this.formData[field]) return false;
            }
            if (this.userType === 'buyer') {
                if (!this.formData.location_access) return false;
                if (!this.formData.terms) return false;
            }
            // Passwords must match
            if (this.formData.password !== this.formData.confirm_password) return false;
            // Check for all caps error
            if (this.allCapsError) return false;
            return true;
        },
        allCapitalData() {

        },
        personalInfoPairs() {
            return groupInPairs(this.requiredInfo.personalInformation);
        },
        accountInfoPairs() {
            return groupInPairs(this.requiredInfo.accountInformation);
        }
    },
    methods: {
        handleNext(event) {
            event.preventDefault(); // Prevent form submission

            this.checkAllCaps();
            if (this.allCapsError) {
                alert('Please avoid using ALL CAPS in the following fields: ' + this.allCapsFields.join(', '));
                return;
            }

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
        },
        changeType(newType) {
            this.userType = newType;
            // Reset all form fields
            Object.keys(this.formData).forEach(key => {
                this.formData[key] = '';
            });
        },
        checkAllCaps() {
            // Check all form fields for all caps (excluding fields like gender, birthday, age, location_access, terms)
            const skipFields = ['gender', 'birthday', 'age', 'contact_no', 'location_access', 'terms', 'password', 'confirm_password'];
            this.allCapsFields = [];
            for (const key in this.formData) {
                if (skipFields.includes(key)) continue;
                const value = this.formData[key];
                if (value && value === value.toUpperCase() && /[A-Z]/.test(value) && value !== value.toLowerCase()) {
                    this.allCapsFields.push(key.replace('_', ' '));
                }
            }
            this.allCapsError = this.allCapsFields.length > 1;
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
  width: 40%;
  border: 1px solid #000;
  border-radius: 10px;
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

/* Responsive Design */
@media (max-width: 900px) {
  .signup-container {
    width: 70%;
    min-width: 280px;
    border-radius: 8px;
  }
  .form-content {
    padding: 1.5rem 0.5rem;
  }
  .input-row {
    gap: 1.5rem;
  }
}

@media (max-width: 600px) {
  .signup-container {
    width: 100%;
    min-width: 0;
    border-radius: 0;
    border-width: 1px 0 1px 0;
    box-sizing: border-box;
  }
  .form-content {
    padding: 1rem 0.2rem;
  }
  .form-header h1 {
    font-size: 1.2rem;
  }
  .form-header p {
    padding-left: 0;
    font-size: 0.95rem;
  }
  .input-row {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  .input-box input,
  .input-box select {
    font-size: 11px;
    padding: 8px;
  }
  .input-box label {
    font-size: 11px;
    padding-left: 8px;
  }
  .form-btn {
    font-size: 0.95rem;
    padding: 7px;
  }
  .user-type {
    flex-direction: column;
    gap: 0.5rem;
  }
  .typebox {
    padding: 0.7rem 0;
    font-size: 0.98rem;
  }
}

</style>
