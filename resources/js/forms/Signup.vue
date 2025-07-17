<template>
     <teleport to="body">
        <div class="loading-overlay" v-if="is_loading">
            <img src="../../images/kOnzy.gif" style="width: 100px; height: 100px;" v-if="!show_otpContainer">

            <div class="otp-container" v-if="show_otpContainer">
                <form @submit.prevent="gosubmit()">
                    <div class="otp-box">
                        <h2 class="otp-title">Verify Your Email</h2>
                        <p class="otp-instructions">
                            Please check your gmail <label style="text-decoration: underline;">{{ this.formData.email }}</label> for the 6-digit verification code we just sent to continue register. <br><br>
                            <strong style="font-size: 20px;">Do not reload this page</strong> to avoid returned back to start.<br>
                            <label style="color: red; font-size: 12px;">If failed to verify the OTP. You will be returned to the beginning of the process.</label>
                        </p>
                        <label style="color: red; font-weight: bolder;">{{errormessage_otp}}</label>
                        <input
                            v-model="otpCode"
                            class="otp-input"
                            type="text"
                            maxlength="6"
                            placeholder="Enter OTP code"
                            required
                        />
                        <button class="otp-submit" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
     </teleport>
    <div v-if="currentStep === 'user'" class="user-type">
        <button
            @click="changeType('seller')"
            class="typebox style"
            id="seller"
            :class="{
            active: userType === 'seller',
            inactive: userType && userType !== 'seller'
            }"
        >Seller</button>
        <button
            @click="changeType('buyer')"
            class="typebox style"
            id="buyer"
            :class="{
            active: userType === 'buyer',
            inactive: userType && userType !== 'buyer'
            }"
        >Buyer</button>
    </div>

    <!-- User Signup Form -->
    <form @submit.prevent="handleNext" class="form-content" v-if="userType && currentStep === 'user'">
        <div>

        </div>
        <teleport to="body">
            <PermissionInfo v-if="show_permission" @goCancel="goCancel" @goAllow="goAllow"/>
        </teleport>
        <div class="form-header">
            <h1>Sign up</h1>
            <p>Sign up as a <strong>{{ userType }}</strong></p>
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
                        >
                            <option v-for="option in info.options" :key="option.value" :value="option.value">{{ option.text }}</option>
                        </select>
                        <label :for="info.id" class="label" :class="{ floated: formData[info.name] }">{{ info.label }}</label>
                    </template>

                    <!-- for tel type inputs like contact numbers -->
                    <template v-else-if="info.type === 'tel'">
                        <input
                            :type="info.type"
                            :name="info.name"
                            :id="info.id"
                            :pattern="info.pattern"
                            v-model="formData[info.name]"
                            @input="validateField(info.name)"
                            required
                        />
                        <label :for="info.id" class="label" >{{ info.label }}</label>
                        <span v-if="validationMessages[info.name] && validationMessages[info.name].length" class="error-message">
                            <ul style="padding-left: 1.2em; margin: 0;">
                                <li v-for="(msg, idx) in validationMessages[info.name]" :key="idx">{{ msg }}</li>
                            </ul>
                        </span>
                    </template>

                    <!-- for other input types that is not mentioned above -->
                    <template v-else>
                        <input
                            :type="info.type"
                            :name="info.name"
                            :id="info.id"
                            :maxlength="info.max > 0 ? info.max : null"
                            v-model="formData[info.name]"
                            @input="validateField(info.name, (info.name === 'age' ? true : false))"
                            required
                        />
                        <label :for="info.id" class="label" >{{ info.label }}</label>
                        <span v-if="validationMessages[info.name] && validationMessages[info.name].length" class="error-message">
                            <ul style="padding-left: 1.2em; margin: 0;">
                                <li v-for="(msg, idx) in validationMessages[info.name]" :key="idx">{{ msg }}</li>
                            </ul>
                        </span>
                    </template>
                </div>
            </div>
        </div>

        <!-- Account Information Section -->
        <div class="section-header"><strong>Account Information</strong></div>
        <div>
            <div v-for="(pair, idx) in accountInfoPairs" :key="'account-row-' + idx" class="input-row">
                <div v-for="info in pair" :key="info.id" class="input-box">
                    <template v-if="info.type === 'file'">
                        <input
                            :type="info.type"
                            :name="info.name"
                            :id="info.id"
                            :accept="info.accept"
                            @change="handleFileUpload($event, info.name)"
                            :ref="'fileInput_' + info.name"
                            style="display: none;"
                            required
                        />
                        <label
                            :for="info.id"
                            class="label"
                            tabindex="0"
                        >
                            {{ info.label }}
                        </label>
                        <button
                            type="button"
                            class="img-btn"
                            @click="triggerFileInput(info.name)"
                            style="margin-top: 5px;"
                        >
                            Upload Image
                        </button>
                        <span v-if="formData[info.name]" class="file-name">
                            {{ formData[info.name]?.name }}
                        </span>
                    </template>
                    <template v-else>
                        <input
                            :type="info.name === 'confirm_password' ? 'password' : info.type"
                            :name="info.name"
                            :id="info.id"
                            v-model="formData[info.name]"
                            required
                            @input="validateField(info.name)"
                        />
                        <label :for="info.id" class="label" >{{ info.label }}</label>
                        <span v-if="validationMessages[info.name]" class="error-message" >{{ validationMessages[info.name].join(', ') }}</span>
                    </template>
                </div>
            </div>
        </div>

        <div class="terms" v-if="userType === 'buyer'">
            <div class="checkbox">
                <input type="checkbox" name="location_access" id="location_access" v-model="formData.location_access" :true-value="'yes'" :false-value="''">
                <label @click="show_permission = true">Allow location access to the nearest shop.</label>
            </div>

            <div class="checkbox">
                <input type="checkbox" name="terms" id="terms" v-model="formData.terms" :true-value="'yes'" :false-value="''">
                <label @click="show_permission = true">I agree to the <strong>Terms</strong> and <strong>Conditions</strong></label>
            </div>
        </div>

        <button v-if="userType==='buyer'" class="form-btn style" type="submit">Sign Up</button>
        <button v-if="userType==='seller'" class="form-btn style" type="submit">Next</button>
    </form>

    <!-- Shop Signup Form -->
    <ShopSignup v-if="currentStep === 'shop'" :userType="userType" :formData="formData" @shopinfosubmit="shopinfosubmit" @goloading="goloading"/>

    <!-- Buyer Signup Form -->
    <Login v-if="currentStep === 'login'" :userType="userType" :userData="formData" :message="message"/>

</template>

<script>
import ShopSignup from '../forms/ShopSignup.vue';
import Login from '../forms/Login.vue';
import PermissionInfo from './PermissionInfo.vue';

import axios from 'axios';

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
        Login,
        PermissionInfo
    },
    data() {
        return {
            message: '',
            errormessage_otp: "",
            otpCode: null,
            show_otpContainer: false,
            show_permission: false,
            is_loading: false,
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
                confirm_password: '',
                profile_image: null,
                location_access: '',
                terms: ''
            },
            requiredInfo: {
                personalInformation: [
                    { label: 'Firstname', type: 'text', name: 'firstname', id: 'firstname'},
                    { label: 'Middle Initial', type: 'text', name: 'm_initial', id: 'm_initial', max: 1},
                    { label: 'Lastname', type: 'text', name: 'lastname', id: 'lastname'},
                    { label: 'Gender', type: 'select', name: 'gender', id: 'gender', options: [ { value: '', text: 'Select Gender' }, { value: 'male', text: 'Male' }, { value: 'female', text: 'Female' } ] },
                    { label: 'Birthday', type: 'date', name: 'birthday', id: 'birthday',},
                    { label: 'Age', type: 'number', name: 'age', id: 'age', value: null,},
                    { label: 'Email', type: 'email', name: 'email', id: 'email'},
                    { label: 'Contact Number', type: 'tel', name: 'contact_no', id: 'contact_no', pattern: '[0-9]{11}'},
                ],
                accountInformation: [
                    { label: 'Username', type: 'text', name: 'username', id: 'username'},
                    { label: 'Profile Image', type: 'file', name: 'profile_image', id: 'profile_image', accept: 'image/*' },
                    { label: 'Password', type: 'password', name: 'password', id: 'password'},
                    { label: 'Confirm Password', type: 'confirm_password', name: 'confirm_password', id: 'confirm_password'}
                ]
            },
            allCapsError: false,
            allCapsFields: [],
            validationMessages: {
                firstname: [],
                m_initial: [],
                lastname: [],
                birthday: [],
                age: [],
                email: [],
                contact_no: [],
                username: [],
                password: [],
                confirm_password: []
            },
            fieldStatus: {
                firstname: null,
                m_initial: null,
                lastname: null,
                birthday: null,
                age: null,
                email: null,
                contact_no: null,
                username: null,
                password: null,
                confirm_password: null
            },
            debounceTimers: {},
            // Simulated existing data for async checks
            existingEmails: ['test@example.com'],
            existingUsernames: ['user1'],
            commonPasswords: ['password', '12345678', 'qwerty']
        }
    },
    computed: {
        isFormValid() {
            // List of required fields for each user type
            const requiredFields = this.userType === 'buyer'
                ? [
                    'firstname', 'm_initial', 'lastname', 'gender', 'birthday', 'age',
                    'email', 'contact_no', 'username', 'password', 'confirm_password', 'profile_image',
                    'location_access', 'terms'
                ]
                : [
                    'firstname', 'm_initial', 'lastname', 'gender', 'birthday', 'age',
                    'email', 'contact_no', 'username', 'password', 'confirm_password', 'profile_image'
                ];

            if(this.userType === "buyer"){
                return requiredFields.every(
                                        key => (this.validationMessages[key]?.length === 0) && (this.fieldStatus[key] === true)
                                    ) && this.userType && (this.userType === 'buyer' || (this.formData.location_access && this.formData.terms));
            }
            else{
                return requiredFields.every(
                        key => (this.validationMessages[key]?.length === 0) && (this.fieldStatus[key] === true)
                    );   
            }
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

        goAllow(){
            const arr = ['terms','location_access'];

            arr.forEach(element => {
                document.getElementById(element).checked = true;
            })
            this.goCancel();
        },

        goloading(status){
            if(status){
                this.is_loading = true;
            }
            else{
                this.is_loading = false;
            }
        },

        shopinfosubmit(){
            this.show_otpContainer = true;
        },

        async gosubmit(){
            this.errormessage_otp = "";
            const data = new FormData();
            this.show_otpContainer = false;
            data.append('code', this.otpCode);

            const res = await axios.post('/send/otp', data);
            console.log('message: ', res.data.message);
            this.show_otpContainer = true;
            if(res.data.message === 'successful'){
                this.errormessage_otp = "";
                this.is_loading = false;
                this.show_otpContainer = false;
                this.message = "You have successfully created an account. Please log in.";
                this.currentStep = 'login';
            }
            else{
                this.errormessage_otp = "Code not exist. Please check and try again.";
            }
        },

        goCancel(){
            this.show_permission = false;
        }, 
        // --- Validation Rules Config ---
        validationRules(field, value) {
            const rules = {
                firstname: [
                    v => v.trim().length >= 2 || 'Firstname must be at least 2 characters',
                    v => !/^\s|\s$/.test(v) || 'No leading or trailing spaces',
                    v => !/[0-9]/.test(v) || 'No numbers allowed',
                    v => !/[^a-zA-Z\s]/.test(v) || 'No symbols allowed.',
                    v => v.split(/\s+/).length === new Set(v.split(/\s+/)).size || 'No repeated words.',
                    v => /^[A-Z][a-z]*(\s[A-Z][a-z]*)*$/.test(v) || 'Each word must start with a capital letter.'
                ],
                m_initial: [
                    v => v === '' || /^[A-Z]\.?$/.test(v) || 'Middle initial must be a single uppercase letter (optionally with a dot).'
                ],
                lastname: [
                    v => v.trim().length >= 2 || 'Lastname must be at least 2 characters.',
                    v => !/^\s|\s$/.test(v) || 'No leading or trailing spaces.',
                    v => !/[0-9]/.test(v) || 'No numbers allowed.',
                    v => !/[^a-zA-Z\s]/.test(v) || 'No symbols allowed.',
                    v => /^[A-Z][a-z]*(\s[A-Z][a-z]*)*$/.test(v) || 'Each word must start with a capital letter.'
                ],
                birthday: [
                    v => !!v || 'Birthday is required.',
                    v => new Date(v) <= new Date() || 'Birthday cannot be in the future.',
                    v => {
                        const age = this.calculateAge(v);
                        return (age >= 0 && age <= 120) || 'Age must be between 0 and 120.';
                    }
                ],
                age: [
                    v => v !== '' && !isNaN(v) || 'Age is required.',
                ],
                email: [
                    v => /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/.test(v) || 'Invalid email format.',
                    v => !/@(mailinator|tempmail|10minutemail)\./.test(v) || 'Disposable emails are not allowed.'
                ],
                contact_no: [
                    v => /^09[0-9]{9}$/.test(v) || 'Contact number must start with 09 and be 11 digits.'
                ],
                username: [
                    v => v.length >= 4 || 'Username must be at least 4 characters.',
                    v => v.length <= 20 || 'Username must be at most 20 characters.',
                    v => /^[a-zA-Z0-9_]+$/.test(v) || 'Only letters, numbers, and underscores allowed.'
                ],
                password: [
                    v => v.length >= 8 || 'Must be at least 8 characters.',
                    v => /[A-Z]/.test(v) || 'Must contain an uppercase letter.',
                    v => /[a-z]/.test(v) || 'Must contain a lowercase letter.',
                    v => /[0-9]/.test(v) || 'Must contain a number.',
                    v => /[^a-zA-Z0-9]/.test(v) || 'Must contain a special character.',
                    v => !/\s/.test(v) || 'No spaces allowed.',
                    v => !this.commonPasswords.includes(v) || 'Password is too common.'
                ],
                confirm_password: [
                    v => v === this.formData.password || 'Passwords do not match.'
                ]
            };
            return rules[field] || [];
        },
        // --- Main Validation Method ---
        validateField(field, isage=false) {

            //empty birthday if age is inputted manually
            if(isage){
                this.requiredInfo.personalInformation[4].value = null;
                this.formData['birthday'] = null;
            }

            console.log('isage: ', isage);
            console.log(field);
            // Only validate terms/location_access for buyers
            if ((field === 'terms' || field === 'location_access') && this.userType !== 'buyer') {
                this.validationMessages[field] = [];
                this.fieldStatus[field] = null;
                return;
            }

            let value = this.formData[field];
            console.log("value: ", value);

            if(field === 'age'){
                if(value <= 0){
                    window.alert("The age should be not 0.")
                    this.requiredInfo.personalInformation[5].value = null;
                    this.requiredInfo.personalInformation[4].value = null;
                    this.formData[field] = null;
                    this.formData['birthday'] = null;
                    this.fieldStatus.age = false;
                    return;
                }
                console.log('neh agi')
                this.requiredInfo.personalInformation[5].value = value;
                this.fieldStatus.age = true;
                return;
            }

            // Only trim if value is a string
            if (typeof value === 'string') {
                if (field === 'email') value = value.trim().toLowerCase();
                else value = value.trim();
                this.formData[field] = value;
            }

            // If the field is empty, clear errors and status
            if (!value) {
                this.validationMessages[field] = [];
                this.fieldStatus[field] = null;
            } else {
                // Run all rules
                const errors = this.validationRules(field, value).map(rule => typeof rule === 'function' ? rule(value) : true).filter(msg => msg !== true);
                this.validationMessages[field] = errors;
                this.fieldStatus[field] = errors.length === 0;
            }
            // Async checks (debounced)
            if (['email', 'username'].includes(field) && value) {
                clearTimeout(this.debounceTimers[field]);
                this.debounceTimers[field] = setTimeout(() => {
                    if (field === 'email' && this.existingEmails.includes(value)) {
                        this.validationMessages.email = ['This email is not available.'];
                        this.fieldStatus.email = false;
                    }
                    if (field === 'username' && this.existingUsernames.includes(value)) {
                        this.validationMessages.username = ['This username is not available.'];
                        this.fieldStatus.username = false;
                    }
                }, 400);
            }
            // Age auto-calc
            if (field === 'birthday') {

                this.requiredInfo.personalInformation[4].value = value;

                const age = this.calculateAge(value);
                this.formData.age = isNaN(age) || age < 0 ? '' : age;
                console.log('formdata: ', this.formData);
                this.validateField('age');
            }
            if (field === 'password') {
                this.validateField('confirm_password');
            }
        },
        calculateAge(dob) {
            if (!dob) return '';
            const dobDate = new Date(dob);
            const today = new Date();
            let age = today.getFullYear() - dobDate.getFullYear();
            const m = today.getMonth() - dobDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dobDate.getDate())) {
                age--;
            }
            return age;
        },
        // --- Validation Functions from register.js ---
        wordsCapitalized(value) {
            const words = value.trim().split(/\s+/);
            for (let word of words) {
                if (word.length > 0 && word[0] !== word[0].toUpperCase()) {
                    return false;
                }
            }
            return true;
        },
        allCaps(value) {
            return value && value === value.toUpperCase() && /[A-Z]/.test(value);
        },
        containsNum(value) {
            const numregex = /[0-9]/;
            return numregex.test(value);
        },
        containsSymbol(value) {
            const symbolRegex = /[^a-zA-Z0-9\s]/;
            return symbolRegex.test(value);
        },
        hasThreeConsecutiveSpaces(value) {
            return /\s{3,}/.test(value);
        },
        hasThreeSameConsecutiveLetters(value) {
            return /([a-zA-Z])\1\1/.test(value);
        },
        dotAlphaAllowed(value) {
            const dotAlphaRegex = /^[a-zA-Z.]+$/;
            return value.length > 0 && !dotAlphaRegex.test(value);
        },
        allowsDotNumAlpha(value) {
            const dotAlphaNum = /^[a-zA-Z0-9.]+$/;
            return value.length > 0 && !dotAlphaNum.test(value);
        },
        handleNext(event) {
            event.preventDefault();
            // Validate all fields before submit
            Object.keys(this.formData).forEach(f => this.validateField(f));
            console.log(this.validationMessages);
            console.log('field status: ', this.fieldStatus);
            if (!this.isFormValid) {
                // Collect invalid fields for better feedback
                const invalidFields = Object.keys(this.validationMessages)
                    .filter(key => {
                        // Only require location_access and terms for buyers
                        if ((key === 'location_access' || key === 'terms') && this.userType !== 'buyer') return false;
                        return this.validationMessages[key].length > 0 || this.fieldStatus[key] !== true;
                    })
                    .map(key => {
                        // Convert snake_case to readable label
                        return this.requiredInfo.personalInformation.concat(this.requiredInfo.accountInformation)
                            .find(info => info.name === key)?.label || key.replace(/_/g, ' ');
                    });
                let alertMsg = 'Please fix the errors in the form.';
                if (invalidFields.length) {
                    alertMsg += '\nInvalid or missing: ' + invalidFields.join(', ');
                }
                if (this.userType === 'buyer' && (!this.formData.location_access || !this.formData.terms)) {
                    alertMsg += '\nYou must agree to location access and terms.';
                }
                alert(alertMsg);
                return;
            }
            // Sanitize all fields
            Object.keys(this.formData).forEach(f => {
                if (typeof this.formData[f] === 'string') {
                    this.formData[f] = this.formData[f].trim();
                    if (f === 'email') this.formData[f] = this.formData[f].toLowerCase();
                }
            });
            if (this.userType === 'seller') {
                this.currentStep = 'shop';
            } else {
                this.submitBuyerForm();
            }
        },
        async submitBuyerForm() {
            try{
                this.is_loading = true;

                console.log('Buyer signup data:', this.formData);

                const data = new FormData();
                data.append('data', JSON.stringify(this.formData));
                data.append('image', this.formData.profile_image);
                data.append('role', 'buyer');

                const response = await axios.post('/user/register', data);

                console.log(response.data.message);

                if(response.data.message === 'exist'){
                    window.alert("Email/Username have already used. Please try again.");
                    this.is_loading = false;
                    return;
                }

                this.show_otpContainer = true;


                return;
            }
            catch(error){
                console.log(error);
            }
        },
        changeType(newType) {
            this.userType = newType;
            // Reset all form fields
            Object.keys(this.formData).forEach(key => {
                this.formData[key] = '';
            });
            // Clear all validation messages and statuses
            Object.keys(this.validationMessages).forEach(key => {
                this.validationMessages[key] = [];
            });
            Object.keys(this.fieldStatus).forEach(key => {
                this.fieldStatus[key] = null;
            });
        },
        checkAllCaps() {
            // Check all form fields for all caps (excluding fields like gender, birthday, age, location_access, terms)
            const skipFields = ['gender', 'birthday', 'age', 'contact_no', 'location_access', 'terms' ];
            this.allCapsFields = [];
            for (const key in this.formData) {
                if (skipFields.includes(key)) continue;
                const value = this.formData[key];
                if (value && value === value.toUpperCase() && /[A-Z]/.test(value) && value !== value.toLowerCase()) {
                    this.allCapsFields.push(key.replace('_', ' '));
                }
            }
            this.allCapsError = this.allCapsFields.length > 1;
        },
        triggerFileInput(name) {
            let fileInput = this.$refs['fileInput_' + name];
            // If it's an array (can happen with v-for), get the first element
            if (Array.isArray(fileInput)) {
                fileInput = fileInput[0];
            }
            if (fileInput) {
                fileInput.click();
            } else {
                console.error(`File input with ref 'fileInput_${name}' not found.`);
            }
        },
        handleFileUpload(event, name) {
            const file = event.target.files[0];
            if (file) {
                // Update your formData or perform other actions with the file
                this.formData[name] = file;
            }
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
  min-width: 320px;
  max-width: 600px;
  margin: 0 auto;
  height: auto;
  border: 1px solid #000;
  border-radius: 10px;
  border-radius: 10px;
  box-sizing: border-box;
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
  height: auto;
  padding: 2rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  box-sizing: border-box;
}

.form-header{
    margin-bottom: 2rem;
    text-align: left;
    width: 100%;
    text-align: center;
}

.form-header h1{
    text-transform: uppercase;
    margin-bottom: 1rem;
}

.form-header strong {
    text-transform: uppercase;
}

/* section header */
.section-header {
    margin-bottom: 1rem;
    text-align: left;
    width: 100%;
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
.input-box .label {
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
.input-box .label.floated {
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

.input-box input:focus + .label,
.input-box input.has-content + .label {
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
  .input-box input:focus + .label,
  .input-box input:valid + .label {
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

/* img upload btn */
.img-btn {
  width: 100%;
  padding:  10px;
  font-size: 12px;
  border: 1px solid #9e363a;
  border-radius: 8px;
  background-color: #ffffff;
  color: #000000;
  box-sizing: border-box;
}

.file-name {
  display: block;
  font-size: 12px;
  color: #333;
  margin-top: 4px;
}

/* error message design */
.error-message {
    color: red;
    font-size: 12px;
    width: 100%;
}

.input-box .error-message {
  display: block;
  margin-top: 4px;
  margin-bottom: 2px;
  width: 100%;
}

.input-box.has-error {
  margin-bottom: 28px; /* Adjust as needed */
}

.checkbox input {
    margin-right: 1rem;
}

/* submit/next button */
.form-btn {
  margin-top: 1rem;
  padding: 8px;
  border-radius: 10px;
  background-color: #fc9c70;
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
.checkbox label{
    color: blue;
    text-decoration: underline;
    cursor: pointer;
}
.loading-overlay{
    background-color: rgba(0, 0, 0, 0.596);
    z-index: 300;
    height: 100vh;
    width: 100vw;
    top: 0;
    position: absolute;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
}
.otp-container {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

.otp-box {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  width: 500px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  text-align: center;
  position: relative;
  box-sizing: border-box;
}

.otp-title {
  font-size: 1.5rem;
  margin-bottom: 1rem;
  color: #222;
}

.otp-instructions {
  font-size: 0.95rem;
  color: #555;
  margin-bottom: 1.5rem;
}

.otp-input {
  width: 90%;
  padding: 0.75rem;
  font-size: 1rem;
  text-align: center;
  border: 1px solid #ccc;
  border-radius: 6px;
  margin-bottom: 1.5rem;
}

.otp-submit {
  width: 100%;
  padding: 0.75rem;
  font-size: 1rem;
  background-color: #0069d9;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.otp-submit:hover {
  background-color: #0056b3;
}
</style>

