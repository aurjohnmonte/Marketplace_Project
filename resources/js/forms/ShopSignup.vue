<template>
    <form method="POST" class="shop-form">
        <div class="form-header">
            <h1>Sign up</h1>
            <p>Shop Details</p>
        </div>

        <div>
            <div v-for="details in shopInfo" :key="details.id" class="input-row">
                <div class="input-box">
                    <template v-if="details.type === 'file'">
                        <input
                            :type="details.type"
                            :name="details.name"
                            :id="details.id"
                            :accept="details.accept"
                            @change="handleFileUpload($event, details.name)"
                            :ref="'fileInput_' + details.name"
                            style="display: none;"
                            required
                        />
                        <label
                            :for="details.id"
                            class="custom-file-label"
                            :class="{ floated: shopData[details.name] }"
                            tabindex="0"
                        >
                            {{ details.label }}
                        </label>
                        <button
                            type="button"
                            class="img-btn"
                            @click="triggerFileInput(details.name)"
                            style="margin-top: 5px;"
                        >
                            Upload Image
                        </button>
                        <span v-if="shopData[details.name]" class="file-name">
                            {{ shopData[details.name]?.name }}
                        </span>
                    </template>
                    <template v-else>
                        <input
                            :type="details.type"
                            :name="details.name"
                            :id="details.id"
                            v-model="shopData[details.name]"
                            @input="validateField(details.name)"
                            required
                        />
                        <label :for="details.id" :class="{ floated: shopData[details.name] }">{{ details.label }}</label>
                        <span v-if="validationMessages[details.name] && validationMessages[details.name].length" class="error-message">
                            <ul style="padding-left: 1.2em; margin: 0;">
                                <li v-for="(msg, idx) in validationMessages[details.name]" :key="idx">{{ msg }}</li>
                            </ul>
                        </span>
                    </template>
                </div>

            </div>
        </div>
        <button class="form-btn style" id="next-btn">Sign Up</button>
    </form>
</template>

<script>
export default {
    data() {
        return {
            shopInfo: [
                {label: 'Shop Name', type: 'text', name: 'shop_name', id: 'shop_name' },
                {label: 'Shop Address', type: 'text', name: 'shop_address', id: 'shop_address' },
                {label: 'Shop Description', type: 'text', name: 'shop_description', id: 'shop_description' },
                {label: 'Upload Shop Image', type: 'file', name: 'shop_profile', id: 'shop_profile' },
                {label: 'Shop Category', type: 'checkbox', name: 'shop_category', id: 'shop_category' },
            ],
            shopData: {
                shop_name: '',
                shop_address: '',
                shop_description: '',
                shop_profile: '',
                shop_category: ''
            },
            validationMessages: {},
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
            }
        }
    },
    methods: {
        handleFileUpload(event, name) {
            // Implement file upload logic here
        },
        triggerFileInput(name) {
            // Implement file input trigger logic here
        },
        validateField(name) {
            // Implement validation logic here
        }
    }
}
</script>


<style>
.shop-form {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
</style>
