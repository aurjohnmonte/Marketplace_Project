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

    <!-- User Signup Form -->
    <form @submit.prevent="handleNext" class="form-content" v-if="currentStep === 'user'">

        <div currentStep = 'user' class="user-type">
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

        <teleport to="body">
            <PermissionInfo v-if="show_permission" @goCancel="goCancel" @goAllow="goAllow"/>
        </teleport>
        <div class="form-header">
            <h1>Sign up</h1>
            <p>Sign up as a <strong>{{ userType }}</strong></p>
        </div>

        <div class="information-container" v-if="accountInfo === false">
            <!-- Personal Information Section -->
            <div class="section-header"><strong>Personal Information</strong></div>
            <div v-for="(pair, idx) in personalInfoPairs" :key="'personal-row-' + idx" class="input-row">
                <div v-for="info in pair" :key="info.id" class="input-box">
                    <!-- template for select type input with options-->
                    <template v-if="info.type === 'select'">
                         <select
                            v-model="formData.gender"
                            id="gender"
                            name="gender"
                            @blur="validateField('gender')"
                        >
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <label :for="info.id" class="label" :class="{ floated: formData[info.name] }">
                            {{ info.label }}
                        </label>
                    </template>

                    <!-- for other input types that is not mentioned above -->
                    <template v-else>
                        <input
                            :type="info.type"
                            :name="info.name"
                            :id="info.id"
                            :maxlength="info.max > 0 ? info.max : null"
                            v-model="formData[info.name]"
                            @focus="fieldStatus.focusedField = info.name"
                            @blur="fieldStatus.focusedField = null"
                            @input="validateField(info.name, (info.name === 'age' ? true : false))"
                            :required="info.name !== 'm_initial'"
                        />
                        <label
                            :for="info.id"
                            class="label"
                            :class="{ floated: formData[info.name] || fieldStatus.focusedField === info.name }"
                        >
                            {{ info.label }}
                        </label>
                    </template>
                </div>
            </div>
        </div>

        <div class="information-container" v-if="accountInfo === true">
            <!-- Account Information Section -->
            <div class="section-header">
                <strong>Account Information</strong>
            </div>
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
                            :class="{ floated: formData[info.name] }"
                            tabindex="0"
                        >
                            {{ info.label }}
                        </label>
                        <button
                            type="button"
                            class="img-btn"
                            @click="triggerFileInput(info.name)"
                        >
                            -
                            <span v-if="formData[info.name]" class="file-name" :title="formData[info.name]?.name">
                                {{ formData[info.name]?.name }}
                            </span>
                        </button>
                    </template>
                    <template v-else>
                        <div  v-if="info.name === 'password' || info.name === 'confirm_password'">
                            <input
                                :type="fieldStatus['show' + (info.name === 'password' ? 'Password' : 'ConfirmPassword')] ? 'text' : 'password'"
                                :name="info.name"
                                :id="info.id"
                                v-model="formData[info.name]"
                                required
                                @input="validateField(info.name)"
                            />
                            <span
                            class="toggle-icon"
                            @click="fieldStatus['show' + (info.name === 'password' ? 'Password' : 'ConfirmPassword')] = !fieldStatus['show' + (info.name === 'password' ? 'Password' : 'ConfirmPassword')]"
                            >
                            <svg v-if="fieldStatus['show' + (info.name === 'password' ? 'Password' : 'ConfirmPassword')]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.961 9.961 0 012.132-3.368m3.61-2.385A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7-.463 1.475-1.28 2.772-2.347 3.772M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3l18 18" />
                            </svg>
                            </span>

                            <label :for="info.id" class="label" :class="{ floated: formData[info.name] }">
                                {{ info.label }}
                            </label>
                        </div>

                        <!-- other normal inputs -->
                        <div v-else>
                            <input
                                :type="info.type"
                                :name="info.name"
                                :id="info.id"
                                v-model="formData[info.name]"
                                required
                                @input="validateField(info.name)"
                            />
                            <label :for="info.id" class="label" :class="{ floated: formData[info.name] }">{{ info.label }}</label>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="terms" v-if="userType === 'buyer' & accountInfo === true">
            <div class="checkbox">
                <input type="checkbox"
                    name="location_access"
                    id="location_access"
                    v-model="formData.location_access"
                    :true-value="'yes'"
                    :false-value="''"
                    @change="validateField('location_access')"
                >
                <label @click="show_permission = true">Allow location access to the nearest shop.</label>
            </div>

            <div class="checkbox">
                <input type="checkbox"
                    name="terms"
                    id="terms"
                    v-model="formData.terms"
                    :true-value="'yes'"
                    :false-value="''"
                    @change="validateField('terms')"
                >
                <label @click="show_permission = true">I agree to the <strong>Terms</strong> and <strong>Conditions</strong></label>
            </div>
        </div>

        <!-- warnings are emitted to App.vue -->
        <div class="btn-container">
            <button v-if="accountInfo === false"
                class="form-btn style"
                @click="validatePersonalInfo">
                Next
            </button>
            <button v-if=" accountInfo === true"
                class="form-btn style"
                @click="accountInfo = false">
                Back
            </button>
            <button v-if="userType==='buyer' & accountInfo === true"
                class="form-btn style" type="submit">
                Sign Up
            </button>
            <button v-if="userType==='seller' & accountInfo === true" class="form-btn style" type="submit">Next</button>
        </div>
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
            accountInfo: false,
            action: 'signup',
            userType: 'buyer',
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
                    { label: 'Gender', type: 'select', name: 'gender', id: 'gender', options: [ { value: 'male', text: 'Male' }, { value: 'female', text: 'Female' } ] },
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
                confirm_password: [],
                location_access: [],
                terms: []
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
                confirm_password: null,
                location_access: null,
                terms: null,
                showPassword: false,
                showConfirmPassword: false,
                focusedField: null,
            },
            debounceTimers: {},
            // Simulated existing data for async checks
            existingEmails: ['test@example.com'],
            existingUsernames: ['user1'],
            commonPasswords: ['password', '12345678', 'qwerty']
        }
    },
    computed: {
        activeErrors() {
            // pick only relevant fields for the current step
            const personalFields = ["firstname", "lastname", "email", "contact_no", "gender", "birthday", "age"];
            const accountFields = ["username", "password", "confirm_password", "terms", "location_access", "profile_image"];


            const currentFields = this.accountInfo ? accountFields : personalFields;

            return Object.entries(this.validationMessages)
            .filter(([field]) => currentFields.includes(field))
            .flatMap(([, messages]) => messages)
            .filter(msg => msg && msg.trim() !== '');
        },
        isFormValid() {
            // List of required fields for each user type
            const requiredFields = this.userType === 'buyer'
                ? [
                    'firstname', 'lastname', 'gender', 'birthday', 'age',
                    'email', 'contact_no', 'username', 'password', 'confirm_password', 'profile_image',
                    'location_access', 'terms'
                ]
                : [
                    'firstname', 'lastname', 'gender', 'birthday', 'age',
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
    watch: {
        activeErrors: {
            handler(newVal) {
                this.$emit('update-signup-errors', newVal);
            },
            immediate: true,
            deep: true
        }
    },
    methods: {
        togglePassword(field) {
            this.passwordVisibility[field] = !this.passwordVisibility[field]
        },
        getInputType(field, defaultType) {
            if (defaultType !== "password") return defaultType
            return this.passwordVisibility[field] ? "text" : "password"
        },
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
                    v => v.length >= 2 || 'Firstname must be at least 2 characters',
                    v => !/^\s|\s$/.test(v) || 'No leading or trailing spaces',
                    v => !/[0-9]/.test(v) || 'No numbers allowed',
                    v => !/[^a-zA-Z\s]/.test(v) || 'No symbols allowed.',
                    v => !/\s{2,}/.test(v) || 'Only single spaces allowed.',
                    v => /^[A-Z][a-z]+(\s[A-Z][a-z]*)*$/.test(v) || 'Each word in firstname must start with a capital letter.'
                ],
                m_initial: [
                    v => v === '' || /^[A-Z]\.?$/.test(v) || 'Middle initial must be an uppercase letter.'
                ],
                lastname: [
                    v => v.length >= 2 || 'Lastname must be at least 2 characters.',
                    v => !/^\s|\s$/.test(v) || 'No leading or trailing spaces.',
                    v => !/[0-9]/.test(v) || 'No numbers allowed.',
                    v => !/[^a-zA-Z0-9\s]/.test(v) || 'No symbols allowed.',
                    v => /^[A-Z][a-z]*(\s[A-Z][a-z]*)*$/.test(v) || 'Each word in lastname must start with a capital letter.'
                ],
                birthday: [
                    v => !!v || 'Birthday is required.',
                    v => new Date(v) <= new Date() || 'Birthday cannot be in the future.'
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
                ],
                terms: [
                    v => v === 'yes' || 'You must accept Terms & Conditions.'
                ],
                location_access: [
                    v => v === 'yes' || 'You must allow location access.'
                ],
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
                if(value < 18){
                    this.requiredInfo.personalInformation[5].value = null;
                    this.requiredInfo.personalInformation[4].value = null;
                    this.formData[field] = null;
                    this.fieldStatus.age = false;
                    this.validationMessages.age = ['Age must be greater than 18.'];
                    return;
                }
                console.log('Valid Age')
                this.requiredInfo.personalInformation[5].value = value;
                this.fieldStatus.age = true;
                this.validationMessages.age = [];
                return;
            }

            // If the field is empty, clear errors and status
            if (!value) {
                this.validationMessages[field] = [];

                if (field === 'm_initial') {
                    this.fieldStatus[field] = true;
                } else {
                    this.fieldStatus[field] = null;
                }
                return;
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

            if (field === 'terms') {
                if (!this.formData.terms) {
                    this.validationMessages.terms = ['You must agree to the Terms and Conditions.'];
                    this.fieldStatus.terms = false;
                } else {
                    this.validationMessages.terms = [];
                    this.fieldStatus.terms = true;
                }
            }

            if (field === 'location_access') {
                if (!this.formData.location_access) {
                    this.validationMessages.location_access = ['You must allow location access.'];
                    this.fieldStatus.location_access = false;
                } else {
                    this.validationMessages.location_access = [];
                    this.fieldStatus.location_access = true;
                }
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
                    if ((key === 'location_access' || key === 'terms') && this.userType !== 'buyer') return false;
                    return this.validationMessages[key].length > 0 || this.fieldStatus[key] !== true;
                })
                .map(key => {
                    return this.requiredInfo.personalInformation.concat(this.requiredInfo.accountInformation)
                        .find(info => info.name === key)?.label || key.replace(/_/g, ' ');
                });

                let warningMsg = 'Please fix the errors in the form.';
                if (invalidFields.length) {
                    warningMsg += '\nInvalid or missing: ' + invalidFields.join(', ');
                }

                this.message = warningMsg;
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

            // Reset all form data
            Object.keys(this.formData).forEach(key => {
                this.formData[key] = '';
            });

            // Reset validation messages
            Object.keys(this.validationMessages).forEach(key => {
                this.validationMessages[key] = [];
            });

            // Reset field status
            Object.keys(this.fieldStatus).forEach(key => {
                this.fieldStatus[key] = null;
            });

            // Reset step and states
            this.accountInfo = false;       // revert back to personal info
            this.currentStep = 'user';      // reset step
            this.show_otpContainer = false; // close otp modal
            this.is_loading = false;        // stop loading spinner

            // Reset file inputs (profile & cover photos if you have them)
            this.$nextTick(() => {
                const profileInput = document.getElementById('profile_image');
                const coverInput = document.getElementById('cover_photo');
                if (profileInput) profileInput.value = '';
                if (coverInput) coverInput.value = '';
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
        },
        validatePersonalInfo() {
            // Validate only personal info fields
            const personalFields = this.requiredInfo.personalInformation.map(f => f.name);
            personalFields.forEach(f => this.validateField(f));

            const hasErrors = personalFields.some(
                key => this.validationMessages[key].length > 0 || this.fieldStatus[key] !== true
            );

            if (!hasErrors) {
                this.accountInfo = true; // move to Account Info only if valid
            } else {
                this.message = "Please complete all personal information correctly before continuing.";
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
    width:  35em;
    max-width: 100%;
    padding: 0 0 1em;
    margin: 0 2em;
    height: 100%;
    border: 1px solid #000;
    border-radius: 1em;
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
  border-radius: 1.1em;
  cursor: pointer;
}
.typebox#buyer{
  border-top-left-radius: 0;
  border-bottom-right-radius: 0;
}
.typebox#seller{
  border-top-right-radius: 0;
  border-bottom-left-radius: 0;
}

/* for active and inactive button designs */
.typebox.active {
  background-color: #49494975 !important;
  border: none;
  font-weight: 500;
  color: #ffffff;
  letter-spacing: 1px;
}
.typebox.inactive {
  background-color: #F5F5DC !important;
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
  border: none;
}

/* div containing the form */
.form-content{
  width: 100%;
  height: auto;
  padding: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  box-sizing: border-box;
}

.form-header{
    text-align: left;
    width: 100%;
    text-align: center;
    margin: .8em 0;
}

.form-header h1{
    text-transform: uppercase;
}

.form-header strong {
    text-transform: uppercase;
}

.information-container {
    margin: 0;
}

/* section header */
.section-header {
    margin-bottom: 1em;
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
  grid-template-columns: repeat(2, 1fr);
  align-items: start;
  justify-content: center;
  gap: 1em;
  margin-bottom: 1rem;
}

/* for the input */
.input-box {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  width: 100%;
  position: relative;
  margin-bottom: 0rem;
}

.input-box input,
.input-box select {
  width: 100%;
  max-width: 17em;
  padding:  7px 8px;
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
  top: .5em;
  left: 5px;
  font-size: 12px;
  color: #575454;
  background-color: #ffffff;
  width: 60%;
  padding: .2em 1em;
  margin: 0 .2em;
  pointer-events: none;
  transition: 0.2s ease all;
}

/* input and label animation */
.input-box .label.floated {
  top: -8px;
  left: 5px;
  width: fit-content;
  font-size: 10px;
  padding: 0 .8em;
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
  max-width: 17em;
  margin: 0;
  padding:  7px 8px;
  font-size: 12px;
  border: 1px solid #9e363a;
  border-radius: 8px;
  background-color: #ffffff;
  color: #000000;
  box-sizing: border-box;
  display: flex;
  justify-content: center;
}

.file-name {
  display: block;
  font-size: 12px;
  color: #333;
  max-width: 150px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  vertical-align: middle;
}


.toggle-icon {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #555;
  width: 1.5rem;
  height: 1.5rem;
}

input[type="password"]::-ms-reveal {
  display: none;
}

.checkbox input {
    margin-right: 1rem;
}

.btn-container {
    display: flex;
    gap: 1em;
    margin: .5em 0;
}

/* submit/next button */
.form-btn {
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
    box-sizing: border-box;
    border-radius: 1.1em;
  }
  .form-content {
    padding: 0;
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
  max-width: 90vw;
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

/* Responsive Design */
@media (max-width: 900px) {

}

@media (max-width: 768px) {
    .signup-container{
        padding: 0;
    }
    .user-type {
        flex-direction: row;
    }
    .typebox {
        width: 100%;
    }
}

@media (max-width: 600px) {

}

@media (max-width: 480px) {

}
</style>

