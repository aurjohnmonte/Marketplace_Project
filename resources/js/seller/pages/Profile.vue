<template>
    <div class="profile-container" @click="handleClickOutside" v-if="shop">
        <div class="profile-box">
            <div class="profile-header" :style="{ backgroundImage: `url(${user.coverPhoto})` }">
                <div class="profile-img">
                    <img :src="'/'+shop.profile_photo" alt="">
                </div>

                <div class="settings-menu" @click.stop>
                    <i class="fa-solid fa-gear" @click="toggleMenu"></i>
                    <div class="menu-dropdown" v-if="showMenu">
                        <div class="menu-item" @click="changeCoverPhoto">
                            <span>Change Cover Photo</span>
                            <span class="change-link">Change</span>
                        </div>
                        <div class="menu-item" @click="changeProfilePhoto">
                            <span>Change Profile Photo</span>
                            <span class="change-link">Change</span>
                        </div>
                        <div class="menu-item" @click="changeShopDescription">
                            <span>Shop Details</span>
                            <span class="change-link">Change</span>
                        </div>
                    </div>
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
                <div class="divider-line"></div>
                <h5 @click="details = 'review'" :class="{ active: details === 'review' }">SHOP'S REVIEW</h5>
            </div>
            <div class="body-details" v-if="details === 'about'">
                <About :shop="shop"/>
            </div>
            <div class="body-details" v-else-if="details === 'product'">
                <ProductsProfile :shop="shop"/>
            </div>
            <div class="body-details" v-else-if="details === 'review'">
                <ShopReview/>
            </div>
        </div>

        <!-- Shop Name Edit Modal -->
        <div class="modal-overlay" v-if="showShopNameModal" @click="closeShopNameModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>Change Shop Name</h3>
                    <button class="close-btn" @click="closeShopNameModal">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="shopName">Shop Name:</label>
                        <input
                            type="text"
                            id="shopName"
                            v-model="newShopName"
                            placeholder="Enter new shop name"
                            @keyup.enter="saveShopName"
                        >
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-cancel" @click="closeShopNameModal">Cancel</button>
                    <button class="btn-save" @click="saveShopName">Save</button>
                </div>
            </div>
        </div>

        <!-- Shop Description Edit Modal -->
        <div class="modal-overlay" v-if="showDescriptionModal" @click="closeDescriptionModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>Change Shop Description</h3>
                    <button class="close-btn" @click="closeDescriptionModal">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
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
                    <button class="btn-cancel" @click="closeDescriptionModal">Cancel</button>
                    <button class="btn-save" @click="saveDescription">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ProductsProfile from '../profile/ProductsProfile.vue';
import About from '../profile/About.vue';
import ShopReview from '../profile/ShopReview.vue';
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore';

export default {
    components: {
        About,
        ProductsProfile,
        ShopReview
    },
    data() {
        return {
            store: useDataStore(),
            user: {
                sellerName: 'Bravo',
                shop: 'Budol Seller',
                profilePicture: 'https://tse1.mm.bing.net/th/id/OIP.airZynZaLzvgWLOJFbVF6QHaE8?rs=1&pid=ImgDetMain&o=7&rm=3',
                coverPhoto: 'https://wallpapercave.com/wp/wp1996490.jpg',
                email: 'bravobudol@gmail.com',
                contacts: '090909090909',
                address: 'balay',
                rating: 2.5,
                followers: 10,
                category: 'Furniture',
                description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vel nisi sed felis dictum lobortis. Vivamus ut justo in diam malesuada vestibulum sed in lorem. Praesent imperdiet enim in eros porta, sit amet semper nunc maximus. Phasellus mauris ligula, volutpat pretium dolor ut, mattis pulvinar felis. Duis vehicula massa velit, non sollicitudin leo facilisis et. Aliquam leo nisl, elementum sit amet sapien vitae, mattis suscipit risus. Curabitur sollicitudin fermentum mauris non pellentesque. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Aenean vehicula semper felis id posuere. Aenean finibus enim et fermentum volutpat. Etiam ac finibus sem, eget elementum ante.',
                totalProducts: 10
            },
            rate: {
                start: 0,
                half: true,
                no_star: 2,
            },
            shop: null,
            details: 'about',
            showMenu: false,
            showShopNameModal: false,
            showDescriptionModal: false,
            newShopName: '',
            newDescription: '',
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
        }
    },
    methods: {
        isFloat(rate){

            return (rate % 1) !== 0 ? true : false;
        },
        toggleMenu() {
            this.showMenu = !this.showMenu;
        },
        closeMenu() {
            this.showMenu = false;
        },
        changeCoverPhoto() {
            // Create a file input element
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = (event) => {
                const file = event.target.files[0];
                if (file) {
                    // Create a URL for the selected image
                    const imageUrl = URL.createObjectURL(file);
                    this.user.coverPhoto = imageUrl;
                    this.closeMenu();
                }
            };
            input.click();
        },
        changeProfilePhoto() {
            // Create a file input element for profile photo
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = (event) => {
                const file = event.target.files[0];
                if (file) {
                    // Create a URL for the selected image
                    const imageUrl = URL.createObjectURL(file);
                    this.user.profilePicture = imageUrl;
                    this.closeMenu();
                }
            };
            input.click();
        },
        changeShopName() {
            this.newShopName = this.user.shop;
            this.showShopNameModal = true;
            this.closeMenu();
        },
        changeShopDescription() {
            this.newDescription = this.user.description;
            this.showDescriptionModal = true;
            this.closeMenu();
        },
        closeShopNameModal() {
            this.showShopNameModal = false;
            this.newShopName = '';
        },
        closeDescriptionModal() {
            this.showDescriptionModal = false;
            this.newDescription = '';
        },
        saveShopName() {
            if (this.newShopName && this.newShopName.trim() !== '') {
                this.user.shop = this.newShopName.trim();
                this.closeShopNameModal();
            }
        },
        saveDescription() {
            if (this.newDescription && this.newDescription.trim() !== '') {
                this.user.description = this.newDescription.trim();
                this.closeDescriptionModal();
            }
        },
        handleClickOutside(event) {
            // Close menu if clicking outside the settings menu
            if (!event.target.closest('.settings-menu')) {
                this.closeMenu();
            }
        }
    },
    mounted(){
        this.shop = this.store.selected_shop;
        console.log('this shop: ', this.shop);
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap');

.profile-container {
    padding: 1.5em;
    display: flex;
    flex-direction: column;
    gap: 1.5em;
    box-sizing: border-box;
    min-height: 100%;
    overflow-y: auto;
}

.profile-box {
    width: 100%;
    background: #ffece1;
    box-shadow: 0 4px 4px rgba(0, 0, 0, 0.199);
    border-radius: 1em;
    user-select: none;
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
    width: 100%;
    flex: 1;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 4px rgba(0, 0, 0, 0.527);
    border-radius: 1em;
    margin-bottom: .5em;
}

.body-header {
    background-color: #534741;
    display: flex;
    justify-content: space-evenly;
    align-items: center;
    padding: 0.75em 2em;
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
    cursor: pointer;
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
    max-width: 500px;
    max-height: 80vh;
    overflow-y: auto;
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
    padding: 1.5em;
}

.form-group {
    margin-bottom: 1em;
}

.form-group label {
    display: block;
    margin-bottom: 0.5em;
    color: #333;
    font-weight: 500;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.8em;
    border: 1px solid #a7a7a7;
    border-radius: 0.5em;
    font-size: 1em;
    background-color: white;
    box-sizing: border-box;
    resize: none;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #ff7300;
    box-shadow: 0 0 0 2px rgba(255, 94, 0, 0.25);
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1em;
    padding: 1.5em;
}

.btn-cancel,
.btn-save {
    padding: 0.8em 1.5em;
    border: none;
    border-radius: 0.5em;
    cursor: pointer;
    font-size: .9em;
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

</style>
