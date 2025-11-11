<template>
    <div class="profile-container" @click="handleClickOutside" v-if="shop">
        <div class="profile-box">
            <div class="profile-header">
                <img :src="'/'+shop.cover_photo" alt="Enter Cover Photo" class="cover-photo">
                <div class="profile-img">
                    <img :src="'/'+shop.profile_photo" alt="Profile Picture">
                </div>
            </div>

            <div class="profile-details">
                <h4>{{ shop.name }}</h4>
                <div class="profile-info">
                    <div class="rating">
                        <h6 class="deta">Overall Rating: </h6>
                        <p>({{ shop.overall_rate }}/5)</p>
                        <div class="stars">
                            <i class="fa-solid fa-star filled star-rate" v-for="turn in (Math.floor(shop.overall_rate))" :key="turn"></i>
                            <span v-if="isFloat(shop.overall_rate)" class="half-star star-rate">
                                <i class="fa-solid fa-star grey"></i>
                                <span class="clip"><i class="fa-solid fa-star filled"></i></span>
                            </span>
                            <i class="fa-solid fa-star no-star star-rate" v-for="turn in (5 - Math.floor(shop.overall_rate))" :key="turn"></i>
                        </div>
                    </div>

                    <div class="followers">
                        <h6>Followers: </h6>
                        <p>{{ shop.user.followers.length }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="body">
            <div class="body-header">
                <h5 @click="details = 'about'" :class="{ active: details === 'about' }">ABOUT</h5>
                <div class="divider-line"></div>
                <h5 @click="details = 'product'" :class="{ active: details === 'product' }">PRODUCT</h5>
                <!-- <div class="divider-line"></div>
                <h5 @click="details = 'review'" :class="{ active: details === 'review' }">SHOP'S REVIEW</h5> -->
            </div>
            <div class="body-details" v-if="details === 'about'">
                <About :shop="shop" @edit-seller-info="changeSellerInformation"/>
            </div>
            <div class="body-details" v-else-if="details === 'product'">
                <Products :shop="shop"/>
            </div>
            <div class="body-details" v-else-if="details === 'review'">
                <ShopReview/>
            </div>
        </div>

        <!-- Shop Details Edit Modal -->
        <div class="modal-overlay" v-if="showDetailsModal" @click="closeDetailsModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>Change Shop Details</h3>
                    <button class="close-btn" @click="closeDetailsModal">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="shopName">Shop Name:</label>
                        <input
                            type="text"
                            id="shopName"
                            v-model="shop.name"
                            placeholder="Enter shop name"
                            :class="{ 'error': shopNameError }"
                        >
                    </div>

                    <div class="form-group">
                        <label for="shopName">Shop Categories:</label>
                        <div class="category-label" style="margin-left:1em;">
                            <template v-for="(option, idx) in categories.options" :key="idx">
                                <input
                                    type="checkbox"
                                    :id="categories.id + '_' + option.value"
                                    :value="option.value"
                                    v-model="selectedCategories"
                                />
                                <label :for="categories.id + '_' + option.value">
                                    {{ option.label }}
                                </label>
                            </template>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="shopDescription">Shop Description:</label>
                        <textarea
                            id="shopDescription"
                            v-model="newDescription"
                            placeholder="Enter new shop description"
                            rows="4"
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-cancel" @click="closeDetailsModal">Cancel</button>
                    <button class="btn-save" @click="saveDetails">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Products from './Products.vue';
import About from '../../seller/profile/About.vue';
import ShopReview from '../../seller/profile/ShopReview.vue';
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore';

export default {
    components: {
        About,
        Products,
        ShopReview
    },
    data() {
        return {
            store: useDataStore(),
            user: {
                coverPhoto: 'https://wallpapercave.com/wp/wp1996490.jpg'
            },
            rate: {
                start: 0,
                half: true,
                no_star: 2,
            },
            shop_categories: [],
            shop: null,
            details: 'about',
            showMenu: false,
            categories: {
                label: 'Shop Category',
                type: 'checkbox',
                name: 'shop_category',
                id: 'shop_category',
                options: [
                    { value: 'Kitchenware', label: 'Kitchenware' },
                    { value: 'Musical Instrument', label: 'Musical Instrument' },
                    { value: 'Decorative Items', label: 'Decorative Items' },
                    { value: 'Games', label: 'Games' },
                    { value: 'Outdoor Enhancements', label: 'Outdoor Enhancements' },
                    { value: 'Home Decor', label: 'Home Decor' },
                    { value: 'Furniture', label: 'Furniture' },
                    { value: 'Personal accessories', label: 'Personal accessories' },
                ]
            }
        }
    },
    computed: {
        calculateStar() {
            let rating = this.user.rating;
            let fullStars = Math.floor(rating);
            let hasHalfStar = rating % 1 !== 0;
            let emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

            return {
                start: fullStars,
                no_star: emptyStars,
                half: hasHalfStar
            };
        },

    },
    methods: {

        async returnSellerInfo(){
          const seller_id = this.$route.params.id;

          const data = new FormData();
          data.append('seller_id', seller_id);

          const res = await axios.post('/admin/return/seller-info', data);

          this.shop = res.data.shop;

          this.store.setSelectedShop(this.shop);
        },

        isFloat(rate){

            return (rate % 1) !== 0 ? true : false;
        },
        toggleMenu() {
            this.showMenu = !this.showMenu;
        },
        closeMenu() {
            this.showMenu = false;
        },
        handleClickOutside(event) {
            // Close menu if clicking outside the settings menu
            if (!event.target.closest('.settings-menu')) {
                this.closeMenu();
            }
        },
        initiateCategories(categories){
            this.selectedCategories = [];

            const result = JSON.parse(categories);

            for(let category of result){

                console.log('category: ', category);
                this.selectedCategories.push(category);
            }
        }

    },
    watch: {
        'store.selected_shop': {
            handler(newShop) {
                if (newShop) {
                    this.shop = newShop;
                    // Initialize modal data with shop data
                    this.newShopNameForSeller = this.shop.name;
                    this.newDescription = this.shop.description || '';
                }
            },
            immediate: true
        }
    },
    async mounted(){

        await this.returnSellerInfo();

        this.initiateCategories(this.shop.category);

        console.log('ID: ', this.shop.id);
        console.log('SHOP', this.shop);

        // Initialize modal data with shop data
        if (this.shop) {
            this.newShopNameForSeller = this.shop.name;
            this.newDescription = this.shop.description || '';
        }
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

.category-label {
    display: grid;
    grid-template-columns: auto 1fr auto 1fr;
    gap: 8px 16px;
    align-items: center;
}

.profile-container {
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 1em;
    box-sizing: border-box;
    min-height: 100%;
    overflow-y: auto;
}

.profile-box {
    width: 100%;
    background: #ffece1;
    box-shadow: 0 4px 4px rgba(36, 36, 36, 0.199);
    user-select: none;
    position: relative;
    overflow: hidden;
}
.profile-box .overlay{
    width: 100%;
    height: 100%;
    object-fit: contain;
    position: absolute;
    left: 0;
    top: 0;
}
.profile-box .cover-photo{
    width: 100%;
    height: 100%;
    position: absolute;
    object-fit: none;
    left: 0;
    top: 0;
    opacity: 0.5;
    z-index: 1;
}

.profile-header {
    position: relative;
    height: 10em;
    background-size: cover;
    background-position: center;
    border-top-left-radius: 1em;
    border-top-right-radius: 1em;
    overflow: visible;
}

.profile-header i {
    position: absolute;
    top: 1em;
    right: 1em;
    font-size: 1.2em;
    color: white;
    cursor: pointer;
    z-index: 3;
}

.profile-img {
    position: absolute;
    bottom: -5em;
    left: 20px;
    width: 10em;
    height: 10em;
    border-radius: 50%;
    border: 4px solid white;
    background-color: white;
    overflow: hidden;
    z-index: 2;
}

.profile-img img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.profile-details {
    padding: 1em 12em;
    display: flex;
    flex-direction: column;
    gap: .35em;
    background-color: #e5cfc3;
}

.profile-info {
    flex: 1;
    display: flex;
    justify-content: space-between;
}

.profile-info h4 {
    margin: 0;
    font-size: 20px;
}

.profile-info h6 {
    font-size: .98em;
    margin: 0;
    font-weight: 500;
}

.rating {
    display: flex;
    gap: 0.8em;
}

.rating p {
    color: #111;
    margin: 0;
    font-size: .8em;
}

.stars {
    display: flex;
    gap: 0.2em;
    align-items: center;
}

.star-rate {
    width: 1.2em;
    height: 1.2em;
}

.filled {
    color: rgb(236, 137, 6);
}

.half {
    color: rgb(236, 137, 6);
}

.no-star {
    color: #bdbbba;
}

.half-star {
    position: relative;
    display: inline-block;
    width: 1.2em;
    height: 1.2em;
}

.half-star .grey {
    color: #bdbbba;
}

.half-star .clip {
    position: absolute;
    left: 0;
    top: 0;
    width: 50%;
    height: 100%;
    overflow: hidden;
    z-index: 2;
}

.half-star i {
    position: absolute;
    left: 0;
    top: 0;
    font-size: 1em;
    line-height: 1;
}

.followers{
    display: flex;
    align-items: center;
    gap: 0.5em;
}

.followers p {
    color: #111;
    margin: 0;
    text-align: center;
}

.body {
    width: 97%;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.body-header {
    background-color: #5c473c;
    display: flex;
    justify-content: space-evenly;
    align-items: center;
    border-top-left-radius: 1em;
    border-top-right-radius: 1em;
}

.body-header h5 {
    margin: 0;
    font-size: 0.9em;
    cursor: pointer;
    color: #e7e4e3;
    transition: color 0.3s ease;
    user-select: none;
    padding-top: 20px;
    padding-bottom: 20px;
}

.body-header h5.active {
    color: #fcba5f;
    font-weight: bold;
    text-decoration: underline;
}

.divider-line {
    width: 2px;
    height: 20px;
    background-color: #666;
    border-radius: 1px;
    user-select: none;
}

.body-details {
    background-color: #DDD0C8;
    flex: 1;
    padding-bottom: 1em;
    border-bottom-left-radius: 1em;
    border-bottom-right-radius: 1em;
    min-height: 0;
    display: block;
    overflow: hidden;
}

.settings-menu {
    position: absolute;
    top: 0em;
    right: 0em;
    z-index: 3;
}

.settings-menu i {
    font-size: 1.2em;
    color: white;
    padding: .5em;
    background-color: #6145388a;
    border-radius: 50%;
    cursor: pointer;
}
.settings-menu i:hover {
    background-color: #967f70;
}

.menu-dropdown {
    position: absolute;
    top: calc(100% + 2.5em);
    right: 1.3em;
    background-color: #f8f1eb;
    border: 1px solid #ccc;
    border-radius: 1em;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    padding: 0.5em;
    z-index: 10;
    display: flex;
    flex-direction: column;
    gap: 0.3em;
    width: 15em;
}

.menu-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.8em 1em;
    cursor: pointer;
    color: #333;
    font-size: 0.9em;
    transition: background-color 0.2s ease;
    border-bottom: 1px solid #f0f0f0;
    border-radius: 1em;
}

.menu-item:last-child {
    border-bottom: none;
}

.menu-item:hover {
    background-color: #f7d6ab;
    border-radius: 1em;
}

.menu-item span:first-child {
    flex-grow: 1;
}

.change-link {
    color: #007bff;
    text-decoration: underline;
    font-weight: 500;
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background-color: #f8f1eb;
    border-radius: 1em;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 40em;
    max-height: 80vh;
    overflow-y: hidden;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5em;
}

.modal-header h3 {
    margin: 0;
    color: #333;
    font-size: 1.2em;
    font-family: 'Poppins', sans-serif;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.2em;
    cursor: pointer;
    color: #666;
    padding: 0.5em;
    border-radius: 50%;
    transition: background-color 0.2s ease;
}

.close-btn:hover {
    background-color: #f0f0f0;
}

.modal-body {
    padding: .5em 1.5em;
    overflow-y: auto;
}

.form-group {
    margin-bottom: 1em;
    overflow-y: auto;
}

.form-group label {
    display: block;
    margin-bottom: 0.5em;
    color: #333;
    font-weight: 500;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 0.6em;
    border: 1px solid #a7a7a7;
    border-radius: 0.5em;
    font-size: 0.9em;
    background-color: white;
    box-sizing: border-box;
    resize: none;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #ff7300;
    box-shadow: 0 0 0 2px rgba(255, 94, 0, 0.25);
}

/* Ensure modal inputs are properly styled */
.modal-content .form-group input,
.modal-content .form-group textarea,
.modal-content .form-group select {
    font-size: 0.9em !important;
    padding: 0.6em !important;
}

.form-group input.error,
.form-group textarea.error,
.form-group select.error {
    border-color: #dc3545;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25);
}

/* Additional styling for select dropdown */
.form-group select {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.7em center;
    background-size: 1em;
    padding-right: 2.5em;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1em;
    padding: 1.5em;
}

.btn-cancel,
.btn-save {
    padding: 1em;
    border: none;
    border-radius: 0.5em;
    cursor: pointer;
    font-size: 1em;
    transition: background-color 0.2s ease;
}

.btn-cancel {
    background-color: #6c757d;
    color: white;
}

.btn-cancel:hover {
    background-color: #5a6268;
}

.btn-save {
    background-color: #e65b0b;
    color: white;
}

.btn-save:hover {
    background-color: #b34200;
}

/* Error message styles */
.error-message {
    color: #dc3545;
    font-size: 0.8em;
    margin-top: 0.5em;
    display: block;
}

/* ===== RESPONSIVE DESIGN ===== */

/* Small mobile devices */
@media (max-width: 480px) {
    .profile-img {
        position: absolute;
        bottom: -5em;
        left: 20px;
        top: 30px;
        width: 10em;
        height: 10em;
        border-radius: 50%;
        border: 4px solid white;
        background-color: white;
        overflow: hidden;
        z-index: 2;
    }
    .body {
        padding: 0;
    }

    .profile-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
        padding: 1rem 0.5rem;
    }

    .profile-header h2 {
        font-size: 1.2rem;
    }

    .profile-header p {
        font-size: 0.9rem;
    }

    .profile-content {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .profile-img {
        width: 5em;
        height: 5em;
        bottom: -2.5em;
        left: 20%;
        transform: translateX(-50%);
    }

    .profile-card {
        padding: 1rem;
    }

    .profile-card h3 {
        font-size: 1.1rem;
    }

    .profile-card p {
        font-size: 0.9rem;
    }

    .profile-actions {
        flex-direction: column;
        gap: 0.5rem;
    }

    .profile-actions button {
        width: 100%;
        padding: 0.8rem;
        font-size: 0.9rem;
    }

    .profile-details {
        padding: 1.5em 0 1em 1em;
        font-size: .85em;
    }

    .profile-info {
        flex-direction: column;
        align-items: right;
        gap: 0.5em;
        width: 100%;
    }

    .profile-actions {
        justify-content: center;
    }

    .modal {
        width: 95vw;
        height: 95vh;
        margin: 2.5vh auto;
    }

    .modal-header {
        padding: 1rem;
    }

    .modal-header h3 {
        font-size: 1.1rem;
    }

    .modal-body {
        padding: 1rem;
    }

    .form-group input,
    .form-group textarea {
        padding: 0.6rem;
        font-size: 0.9rem;
    }

    .modal-footer {
        padding: 1rem;
        flex-direction: column;
    }

    .btn-cancel,
    .btn-save {
        width: 100%;
        padding: 0.8rem;
        font-size: 0.9rem;
    }
}

/* Mobile - Medium devices */
@media (max-width: 768px) {
    .body {
        padding: 1rem;
    }

    .profile-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
        padding: 1.5rem 1rem;
    }

    .profile-header h2 {
        font-size: 1.4rem;
    }

    .profile-header p {
        font-size: 1rem;
    }

    .profile-content {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .profile-card {
        padding: 1.5rem;
    }

    .profile-card h3 {
        font-size: 1.3rem;
    }

    .profile-card p {
        font-size: 1rem;
    }

    .profile-img {
        width: 7em;
        height: 7em;
        bottom: -3.5em;
    }

    .profile-actions {
        flex-direction: column;
        gap: 1rem;
    }

    .profile-actions button {
        width: 100%;
        padding: 1rem;
        font-size: 1rem;
    }

    .profile-details {
        padding: 2em 10em;
    }

    .modal {
        width: 90vw;
        height: 90vh;
        margin: 5vh auto;
    }

    .modal-header {
        padding: 1.5rem;
    }

    .modal-header h3 {
        font-size: 1.3rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-group input,
    .form-group textarea {
        padding: 0.8rem;
        font-size: 1rem;
    }

    .modal-footer {
        padding: 1.5rem;
        flex-direction: column;
        gap: 1rem;
    }

    .btn-cancel,
    .btn-save {
        width: 100%;
        padding: 1rem;
        font-size: 1rem;
    }
}

/* Tablet devices */
@media (max-width: 1024px) {
    .body {
        padding: 3px;
    }

    .profile-details {
        padding: 2em 3em;
    }

    .profile-info {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 1em;
    }
}

/* Desktop devices */
@media (min-width: 1025px) {
    .body {
        padding: 1.5rem;
    }

    .profile-header {
        padding: 2.5rem 2rem;
    }

    .profile-header h2 {
        font-size: 1.8rem;
    }

    .profile-header p {
        font-size: 1.2rem;
    }

    .profile-content {
        grid-template-columns: repeat(3, 1fr);
        gap: 2.5rem;
    }

    .profile-card {
        padding: 2.5rem;
    }

    .profile-card h3 {
        font-size: 1.7rem;
    }

    .profile-card p {
        font-size: 1.2rem;
    }

    .profile-actions {
        gap: 1.5rem;
    }

    .profile-actions button {
        padding: 1.2rem 2rem;
        font-size: 1.2rem;
    }

    .modal {
        width: 70vw;
        height: 70vh;
        margin: 15vh auto;
    }

    .modal-header {
        padding: 2.5rem;
    }

    .modal-header h3 {
        font-size: 1.7rem;
    }

    .modal-body {
        padding: 2.5rem;
    }

    .form-group input,
    .form-group textarea {
        padding: 1.2rem;
        font-size: 1.1rem;
    }

    .modal-footer {
        padding: 2.5rem;
        gap: 1.5rem;
    }

    .btn-cancel,
    .btn-save {
        padding: .5rem 1rem;
        font-size: 1rem;
    }
}

/* Large desktop devices */
@media (min-width: 1440px) {
    .profile-body {
        padding: 2.5rem;
    }

    .profile-header {
        padding: 3rem 2.5rem;
    }

    .profile-header h2 {
        font-size: 2rem;
    }

    .profile-header p {
        font-size: 1.3rem;
    }

    .profile-content {
        grid-template-columns: repeat(3, 1fr);
        gap: 3rem;
    }

    .profile-card {
        padding: 3rem;
    }

    .profile-card h3 {
        font-size: 1.9rem;
    }

    .profile-card p {
        font-size: 1.3rem;
    }

    .profile-actions {
        gap: 2rem;
    }

    .profile-actions button {
        padding: 1.5rem 3rem;
        font-size: 1.3rem;
    }

    .modal {
        width: 60vw;
        height: 60vh;
        margin: 20vh auto;
    }

    .modal-header {
        padding: 3rem;
    }

    .modal-header h3 {
        font-size: 1.9rem;
    }

    .modal-body {
        padding: 3rem;
    }

    .form-group input,
    .form-group textarea {
        padding: 1.5rem;
        font-size: 1.2rem;
    }

    .modal-footer {
        padding: 3rem;
        gap: 2rem;
    }

}

/* Landscape orientation for mobile */
@media (max-width: 768px) and (orientation: landscape) {
    .profile-header {
        flex-direction: row;
        text-align: left;
        padding: 1rem;
    }

    .profile-content {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .modal {
        width: 95vw;
        height: 95vh;
        margin: 2.5vh auto;
    }
}

/* High DPI displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .profile-card {
        border-radius: 12px;
    }

    .modal {
        border-radius: 12px;
    }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    .profile-actions button,
    .btn-cancel,
    .btn-save {
        min-height: 44px;
        min-width: 44px;
    }

    .close-btn {
        min-height: 44px;
        min-width: 44px;
    }
}

/* Print styles */
@media print {
    .body {
        padding: 0;
    }

    .profile-actions,
    .modal {
        display: none;
    }

    .profile-card {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>