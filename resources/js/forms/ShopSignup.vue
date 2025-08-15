<template>
    <form @submit.prevent="goSubmit" class="form-content">
        <teleport to="body">
            <PermissionInfo v-if="show_permission" @goCancel="goCancel" @goAllow="goAllow"/>
        </teleport>

        <teleport to="body">
            <div class="map-container" v-if="shop_map">
                <div style="height: 500px; width: 1000px; display: flex; overflow: hidden; flex-direction: column; position: relative;">
                    <div style="display: flex; flex-direction: row; align-items: center; z-index: 5000; background-color: gray; height: 50px; gap: 10px; justify-content: end; padding-right: 10px;">
                        <button style="padding: 2px;" @click="getLocation()">Current Location</button>
                        <img src="../../images/cancel.png" style="width: 20px; height: 20px;  cursor: pointer;" @click="shop_map = false">
                    </div>
                    <div style="gap: 10px; position: absolute; top: 10%; right: 0; width: 300px; height: auto; padding: 10px; background-color: white; display: flex; flex-direction: column; z-index: 500; margin-right: 10px;">
                        <div>
                            <label>This is the coordinate you selected</label>
                        </div>
                        <div>
                            <label>Latitude: {{ shopData.latitude }}</label><br>
                            <label>Longitude: {{ shopData.longitude }}</label>
                        </div>
                        <button style="padding: 5px; background-color: green; font-weight: bolder; color: white;" @click="map_save_coordinate">Save</button>
                    </div>
                    <Map_Buyer @returnCoordinate="returnCoordinate" :shopData="shopData"/>
                </div>
            </div>
        </teleport>

        <div class="form-header">
            <h1>Sign up</h1>
            <p>Shop Details</p>
        </div>
        <div class="input-container">
            <div v-for="details in shopInfo" :key="details.id" class="input-box">
                <!-- template for img upload -->
                <template v-if="details.type === 'file'">
                    <div class="shop-btns">
                        <div class="shop-img-btn">
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
                        </div>

                        <div class="location-btn">
                            <label style="font-size: 10px; color: gray;">Please double check</label>
                            <button type="button" @click="shop_map = true;">Share Location</button>
                        </div>
                    </div>
                </template>

                <template v-else-if="details.type === 'textarea'">
                    <label :for="details.id" class="desc-label">{{ details.label }}</label>
                    <textarea
                        :id="details.id"
                        :name="details.name"
                        v-model="shopData[details.name]"
                        class="shop-desc"
                        rows="4"
                        placeholder="Describe your shop, products, and what makes you unique..."
                    ></textarea>
                </template>

                <template v-else-if="details.type === 'checkbox'">
                    <hr>
                    <div class="category-label">
                        <h5>{{ details.label }}</h5>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px; width: 100%;">
                            <template v-for="(option, idx) in details.options" :key="idx">
                                <div style="display: flex; align-items: center; width: 48%; margin-bottom: 4px;">
                                    <input
                                        type="checkbox"
                                        :id="details.id + '_' + option.value"
                                        :value="option.value"
                                        @change="addcategory($event, option.value)"
                                    />
                                    <label :for="details.id + '_' + option.value" style="margin-left: 6px;">{{ option.label }}</label>
                                </div>
                            </template>
                        </div>
                    </div>
                    <hr>
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
                    <label :for="details.id" class="label">{{ details.label }}</label>
                    <span v-if="validationMessages[details.name] && validationMessages[details.name].length" class="error-message">
                        <ul style="padding-left: 1.2em; margin: 0;">
                            <li v-for="(msg, idx) in validationMessages[details.name]" :key="idx">{{ msg }}</li>
                        </ul>
                    </span>
                </template>
            </div>
            <hr>
            <div class="terms">
                <div class="checkbox">
                    <input type="checkbox" name="location_access" id="location_access" v-model="formData.location_access" :true-value="'yes'" :false-value="''">
                    <label @click="show_permission = true">Allow location access to the nearest shop.</label>
                </div>

                <div class="checkbox">
                    <input type="checkbox" name="terms" id="terms" v-model="formData.terms" :true-value="'yes'" :false-value="''">
                    <label @click="show_permission = true">I agree to the <strong>Terms</strong> and <strong>Conditions</strong></label>
                </div>
            </div>
        </div>
        <button class="form-btn style" id="next-btn" type="submit">Sign Up</button>
    </form>
</template>

<script>
import PermissionInfo from './PermissionInfo.vue';
import Map_Buyer from './Map.vue';
export default {
    props: [
        'formData'
    ],
    components: {
        PermissionInfo,
        Map_Buyer
    },
    data() {
        return {
            show_permission: false,
            shop_map: false,
            shopInfo: [
                {label: 'Shop Name', type: 'text', name: 'shop_name', id: 'shop_name' },
                {label: 'Shop Address', type: 'text', name: 'shop_address', id: 'shop_address' },
                {
                    label: 'Shop Category',
                    type: 'checkbox',
                    name: 'shop_category',
                    id: 'shop_category',
                    options: [
                        { value: 'Furniture', label: 'furniture' },
                        { value: 'Home Decor', label: 'home decor' },
                        { value: 'Kitchen Essentials', label: 'kitchen essentials' },
                        { value: 'Toys & games', label: 'toys & games' },
                        { value: 'Personal accessories,', label: 'personal accessories' },
                        { value: 'Outdoor enhancements', label: 'outdoor enhancements' }
                    ]
                },
                {label: 'Shop Description', type: 'textarea', name: 'shop_description', id: 'shop_description' },
                {label: 'Upload Shop Image', type: 'file', name: 'shop_profile', id: 'shop_profile' },
            ],
            shopData: {
                shop_name: '',
                shop_address: '',
                shop_description: '',
                shop_profile: '',
                shop_category: [],
                latitude: "none",
                longitude: "none",
            },
            validationMessages: {},
        }
    },
    methods: {
        map_save_coordinate(){
            if(this.shopData.longitude === "none" || this.shopData.latitude === "none"){
                window.alert("Please select a the coordinate/location of your shop")
                return;
            }
            console.log('coordinate: ', this.shopData);
            this.shop_map = false;
        },
        returnCoordinate(coordinate){
            this.shopData.longitude = parseFloat(coordinate.long);
            this.shopData.latitude = parseFloat(coordinate.lat);
        },
        goAllow(){
            const arr = ['terms','location_access'];

            arr.forEach(element => {
                document.getElementById(element).checked = true;
            })
            this.goCancel();
        },

        addcategory(e,value){
            if(e.target.checked){
                this.shopData.shop_category.push(value);
            }
            else{
                this.shopData.shop_category = this.shopData.shop_category.filter(e => e !== value);
            }
            console.log('category: ', this.shopData.shop_category);
        },
        async goSubmit(){

            if(this.shopData.latitude === null || this.shopData.longitude === null){
                window.alert("Please click the share location to proceed on registration. You can read the terms and condition.")
                return;
            }
            console.log('shopdata: ', this.shopData);

            this.$emit('goloading', true);

            const data = new FormData();
            data.append('data', JSON.stringify(this.formData));
            data.append('shopdata', JSON.stringify(this.shopData));
            data.append('image', this.formData.profile_image);
            data.append('shopimage', this.shopData.shop_profile);
            data.append('role', 'seller');

            const response = await axios.post('/user/register', data);

            console.log(response.data.message);

            if(response.data.message === 'exist'){
                window.alert("Email/Username have already used.");
                this.$emit('goloading', false);
                return;
            }

            this.$emit('shopinfosubmit');
        },
        goCancel(){
            this.show_permission = false;
        },
        handleFileUpload(event, name) {
            const file = event.target.files[0];
            if (file) {
                this.shopData[name] = file; // <-- assign to shopData
                this.formData[name] = file; // (optional, if you need it in formData too)
            }
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
        validateField(name) {

        },
        getLocation() {
            if (!navigator.geolocation) {
                alert("Geolocation is not supported by your browser.")
                return
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                this.shopData.latitude = position.coords.latitude
                this.shopData.longitude = position.coords.longitude
                },
                (error) => {
                alert("Unable to retrieve your location. You may have denied permission.")
                console.error(error)
                }
            )
        },
    },
    mounted(){
        console.log('user: ', this.formData);
    }
}
</script>


<style scoped>
.map-container{
    width: 100vw;
    height: 100vh;
    position: absolute;
    background-color: rgba(0, 0, 0, 0.781);
    left: 0;
    top: 0;
    z-index: 3000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.form-content {
    max-width: 500px;
    width: 100%;
    margin: 0 auto;
    padding: 40px;
    box-sizing: border-box;
    border-radius: 8px;
}

.input-container {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.input-box {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    margin-bottom: 16px;
}

/* Custom style for Shop Category checkbox */
.input-box input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin-right: 8px;
    accent-color: #8d5307;
    vertical-align: middle;
}

.input-box .category-label {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    color: #333;
    margin: 1em 0;
    text-transform: capitalize;
    font-size: .9em;
}

.input-box .category-label h5{
    font-size: 1em;
    margin-bottom: .5em;
}

.shop-btns {
    width: 100%;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.shop-img-btn {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.img-btn {
    width: 100%;
    padding:  10px;
    font-size: 12px;
    border: 1px solid #9e363a;
    border-radius: 8px;
    background-color: #ffffff;
    color: #000000;
    box-sizing: border-box;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}

.img-btn:hover {
    background: #8d5307;
    color: #fff;
}

.location-btn{
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.location-btn button {
    width: 100%;
    padding:  10px;
    font-size: 12px;
    border: 1px solid #9e363a;
    border-radius: 8px;
    background-color: #ffffff;
    color: #000000;
    box-sizing: border-box;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}

.location-btn button:hover {
    background-color: #72542d;
    color: #fff;
}

.desc-label {
    font-size: .9rem;
    font-weight: 600;
    margin-bottom: 6px;
    color: #333333;
}

.shop-desc {
    width: 100%;
    max-width: 350px;
    min-height: 80px;
    padding: 12px;
    font-size: 1rem;
    border: 1px solid #9e363a;
    border-radius: 10px;
    background: #fffbe6;
    color: #222;
    resize: vertical;
    box-sizing: border-box;
    margin-bottom: 8px;
    transition: border-color 0.2s;
}
.shop-desc:focus {
    border-color: #9e363a;
    outline: none;
}
.checkbox{
    margin-top: 10px;
}
.checkbox label{
    color: rgb(57, 57, 218);
    text-decoration: underline;
    cursor: pointer;
}
.loading-overlay{
    background-color: rgba(0, 0, 0, 0.712);
    height: 100%;
    width: 100%;
    position: relative;
}

hr {
    width: 100%;
}
</style>
