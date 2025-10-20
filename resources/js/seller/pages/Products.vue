<template>
    <div class="product-container">

        <teleport to="body">
            <div class="overlay" v-if="is_overlay_loading">
                <img src="../../../images/kOnzy.gif">
            </div>
        </teleport>

        <div class="product-header">
            <form class="product-header" @submit.prevent="goSearch">
                <div class="search-bar">
                    <input type="search" name="search-product" id="search-product" placeholder="Search Product" v-model="search.text">
                    <i class="fa fa-search search-icon"></i>
                </div>
                <div class="filters">
                    <select name="product-category" id="product-category" v-model="search.category">
                        <option value="">Filter by category</option>
                        <option v-for="category in productCategory" :key="category" :value="category">
                            {{ category }}
                        </option>
                    </select>
                    <select name="product-status" id="product-status" v-model="search.status">
                        <option value="">Filter by status</option>
                        <option v-for="status in productStatus" :key="status" :value="status">
                            {{ status }}
                        </option>
                    </select>
                </div>
            </form>

            <div class="add-btn">
                <router-link to="/seller/product/add">
                    <button>Add Product</button>
                </router-link>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Views</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

            <tbody class="tbody-scroll" v-if="!is_loading">
                <tr v-if="filteredProducts.length === 0">
                    <td>NO PRODUCTS HAVE ADDED</td>
                </tr>
                <tr v-for="item in filteredProducts" :key="item.id" :class="statusClass(item.status)">
                    <td class="name">{{ item.name }}</td>
                    <td class="qty">{{ item.quantity }}</td>
                    <td class="cat">{{ item.category }}</td>
                    <td class="price">{{ item.price }}</td>
                    <td class="views">{{ item.total_views }}</td>
                    <td class="status">{{ item.status }}</td>
                    <td class="action-btn">
                        <div>
                            <!-- View button/form -->
                            <svg @click="toggleViewProduct(item.id)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.3" stroke="currentColor" class="size-6 view-btn">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                            <div v-if="viewedProductId === item.id" class="toggle-details" :data-product-id="item.id">
                                <div style="width: 100%; display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                                    <h3>View Product</h3>
                                    <button @click="$router.push({name: 'ViewProduct', params: {id: item.id}})">Learn more</button>
                                </div>
                                <div class="image-container">
                                    <button class="nav-btn nav-left"
                                        @click="scrollImages('left', item.id)"
                                        v-if="item.photos.length>3">
                                        <i class="fa fa-chevron-left"></i>
                                    </button>
                                    <div class="toggle-img">
                                        <div v-for="image in item.photos"
                                            :key="image"
                                            class="image-wrapper"
                                        >
                                            <img :src="'/'+image.filename" alt="Image" @mouseover="displayIcon" >
                                            <div class="zoom-overlay">
                                                <i class="fa fa-search-plus zoom-icon" @click.stop="openZoomModal(image.filename)"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="nav-btn nav-right"
                                        @click="scrollImages('right', item.id)"
                                        v-if="item.photos.length>3">
                                        <i class="fa fa-chevron-right"></i>
                                    </button>
                                </div>

                                    <!-- Modal for zoomed image -->
                                    <div v-if="showZoom" class="modal">
                                        <div class="modal-content">
                                            <button class="close-modal-btn" @click="closeZoomModal">
                                                <i class="fa fa-times" @click.stop="closeZoomModal"></i>
                                            </button>
                                            <img :src="'/'+showZoom" alt="Zoomed Image" class="zoomed-image" />
                                        </div>
                                    </div>

                                    <div class="view-row">
                                        <div class="rating">
                                            <p>Viewer Rating:</p>
                                            <span
                                                v-for="star in 5"
                                                :key="star"
                                                class="fa fa-star"
                                                :class="{ checked: star <= item.overall_rate }"
                                            ></span>
                                            <span class="rating-text">({{ item.overall_rate }}/5)</span>
                                        </div>

                                    <div class="rating">
                                        <p>Status:</p>
                                        <span>{{ item.status }}</span>
                                    </div>
                                </div>
                                <div class="comments-section">
                                    <h4>User Comments - ({{ item.reviews.length }} reviews)</h4>
                                    <div class="comments-list">
                                        <div v-if="item.reviews && item.reviews.length">
                                            <div v-for="(comment, idx) in item.reviews" :key="idx" class="comment">
                                                <div style="display: flex; flex-direction: row; gap: 10px; align-items: center;">
                                                    <img :src="'/'+comment.user.profile" style="width: 30px; height: 30px; border-radius: 50%; border: 1px solid gray; padding: 2px;">
                                                    <span class="comment-user">{{ comment.user.firstname }} {{ comment.user.lastname }}</span>
                                                </div>
                                                <span class="comment-text">
                                                    <label>
                                                        {{ comment.comment }}
                                                    </label>

                                                    <div v-if="comment.reviewphotos && comment.reviewphotos.length > 1" style="margin-left: 50px; margin-bottom: 10px;">
                                                        <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                                                            <img :src="'/'+photo.path" style="width: 40px; height: 40px; cursor: pointer;" v-for="photo in comment.reviewphotos" :key="photo">
                                                        </div>
                                                    </div>
                                                    <label>
                                                        {{ returnformatTime(comment.created_at) }}
                                                    </label>
                                                </span>
                                                <div class="rating" style="margin-left: 20px;">
                                                    <span
                                                        v-for="star in 5"
                                                        :key="star"
                                                        class="fa fa-star"
                                                        :class="{ checked: star <= comment.rate }"
                                                    ></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <span>No comment yet.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- edit button/form -->
                            <i class="fa-solid fa-pen-to-square edit-btn"  @click="editProduct(item.id)"></i>
                            <div v-if="editedProductId === item.id" class="toggle-details">
                                <form method="GET" class="form" @submit.prevent="saveProduct(item.id)">
                                    <h3>Edit Product</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="name">Product Name</label>
                                            <input type="text" name="name" id="name" v-model="editingProduct.name">
                                        </div>
                                        <div class="form-group">
                                            <label for="price">Price</label>
                                            <input type="number" name="price" id="price" v-model="editingProduct.price">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="materials">Product Material</label>
                                            <input type="text" name="materials" id="materials" v-model="editingProduct.materials">
                                        </div>
                                        <div class="form-group">
                                            <label for="quantity">Quantity</label>
                                            <input type="number" name="quantity" id="quantity" v-model="editingProduct.quantity">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="category">Category</label>
                                            <select name="category" id="category" v-model="editingProduct.category">
                                                <option v-for="category in productCategory" :key="category" :value="category">
                                                    {{ category }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" v-model="editingProduct.status">
                                                <option v-for="currentStatus in productStatus" :key="currentStatus" :value="currentStatus">
                                                    {{ currentStatus }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="dimensions">Product Dimensions</label>
                                            <input type="text" name="dimensions" id="dimensions" v-model="editingProduct.dimensions">
                                        </div>
                                        <div class="form-group">
                                            <label for="weight">Product Weight</label>
                                            <input type="text" name="weight" id="weight" v-model="editingProduct.weight">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="product-description">Description</label>
                                        <textarea
                                            id="product-description"
                                            rows="5"
                                            style="resize: none;"
                                            v-model="editingProduct.description"
                                            placeholder="Enter product description..."
                                        ></textarea>
                                    </div>

                                    <div class="form-group">
                                        <div style="width: 100%; display: flex; flex-direction: row; justify-content: space-between; align-items: center;">
                                            <label for="product-description">Photos</label>
                                            <label style="text-transform: capitalize; cursor: pointer;" for="addphoto">Add photo</label>
                                        </div>
                                        <div class="photo-container">
                                            <div v-for="(img, index) in editingProduct.photos" :key="index" style="position: relative; cursor: pointer;" >
                                                <img src="../../../images/cancel.png" style="border: none; position: absolute; width: .5em; height: .5em; top: -.3em; right: -.2em;" @click.stop="cancelNotBlob(index, img.id)">
                                                <img :src="'/'+img.filename" alt="product image">
                                            </div>
                                            <div v-for="(img, indx) in new_photo" :key="indx" style="position: relative; cursor: pointer;" >
                                                <img src="../../../images/cancel.png" style="border: none; position: absolute; width: .5em; height: .5em; top: -.3em; right: -.2em;" @click.stop="cancelBlob(indx)">
                                                <img :src="img.preview" alt="product image">
                                            </div>
                                            <input type="file" hidden id="addphoto" @change="handleFileUpload" accept="image/*">
                                        </div>
                                    </div>

                                    <div v-if="message" :class="['message', messageType]">
                                        {{ message }}
                                    </div>

                                    <div class="form-group" style="padding-bottom: 30px;">
                                        <button type="submit" class="btn" style=" padding: .5em; background-color: aliceblue; color: #6c757d;">Save Changes</button>
                                        <button type="button" @click="cancelEdit"  class="btn" style="margin-left: 10px; background-color: #6c757d; padding: .5em; color: white;">Cancel</button>
                                    </div>
                                </form>
                            </div>

                            <i class="fa-solid fa-trash delete-btn"
                                v-if="canDelete(item.status)"
                                @click="deleteProduct(item.id)">
                            </i>
                        </div>
                    </td>
                </tr>
            </tbody>
            <h3 style="color: red; font-size: 1.3em; margin: 1em;" v-if="allProducts === null">No results</h3>
            <div class="loading-container" v-if="is_loading">
                <img src="../../../images/kOnzy.gif" style="width: 200px; height: 200px;">
            </div>
        </table>

        <!-- Overlay background when toggle-details are displayed -->
        <div v-if="viewedProductId || editedProductId" class="overlay-background"></div>
    </div>
</div>
</template>

<script>
import { useDataStore } from '../../stores/dataStore';
import axios from 'axios';
import AttachVideo from '../modal/AttachVideo.vue';


export default {
    components: {AttachVideo},
    data() {
        return {
            search: {
                text: "",
                status: "",
                category: "",
            },
            is_overlay_loading: false,
            is_loading: true,
            store: useDataStore(),
            searchQuery: '',
            selectedCategory: '',
            selectedStatus: '',
            viewedProductId: null,
            editedProductId: null,
            deleted_photo_edit: [],
            new_photo: [],
            editingProduct: {
                name: '',
                quantity: '',
                category: '',
                price: '',
                status: '',
                description: '',
                materials: '',
                dimensions: '',
                weight: ''
            },
            productStatus: [
                'New Arrival',
                'Pre Order',
                'On Stock',
                'Limited Stock',
                'Out of Stock'
            ],

            productCategory: [
                'Furniture',
                'Kitchenware',
                'Musical Instrument',
                'Toys & Games',
                'Office Supplies',
                'Home Decor',
                'Personal accessories',
                'Outdoor enhancements'
            ],


            allProducts:  null,
            clicked: false,
            showZoom: null,
            message: '',
            messageType: 'success'
        }
    },
    computed: {
        filteredProducts() {
            let filtered = this.allProducts || [];

            // Filter by search
            if (this.search.text) {
                filtered = filtered.filter(item =>
                    item.name.toLowerCase().includes(this.search.text.toLowerCase()) ||
                    item.category.toLowerCase().includes(this.search.text.toLowerCase())
                );
            }
            // Filter by category
            if (this.search.category) {
                filtered = filtered.filter(item =>
                    item.category.toLowerCase() === this.search.category.toLowerCase()
                );
            }
            // Filter by status
            if (this.search.status) {
                filtered = filtered.filter(item =>
                    item.status === this.search.status
                );
            }

            return filtered;
        }
    },
    methods: {
        returnformatTime(date){
            return new Date(date).toLocaleDateString();
        },
        async goSearch(){
            this.is_loading = true;
            const data = new FormData();
            data.append('data', JSON.stringify(this.search));

            const res = await axios.post('/seller/search/product', data);
            console.log(res.data.message);
            this.allProducts = res.data.message;
            this.is_loading = false;
        },
        cancelBlob(index){
            console.log('index: ', index);
            this.new_photo.splice(index, 1);
        },
        cancelNotBlob(index, id){
            console.log('id: ', id);
            this.deleted_photo_edit.push(id);
            this.editingProduct.photos.splice(index,1);
        },
        handleFileUpload(event){
            const files = event.target.files;

            for(let file of files){
                const reader = new FileReader();

                reader.onload = (e) => {

                    this.new_photo.push({
                        file,
                        preview: e.target.result
                    })
                };

                reader.readAsDataURL(file);
            }

            event.target.value = null;
        },
        async returnProducts(){

            try{
                this.is_loading = true;
                const data = new FormData();
                data.append('id', this.store.currentUser_info.id);

                const res = await axios.post('/seller/return/products', data);
                console.log(res.data.message);
                this.allProducts = res.data.message;
                this.is_loading = false;
            }
            catch(error){
                this.is_loading = false;
                console.log(error);
            }
        },

        statusClass(status) {
            switch(status) {
                case 'New Arrival': return 'status-new';
                case 'Pre Order': return 'status-preorder';
                case 'On Stock': return 'status-onstock';
                case 'Limited Stock': return 'status-limited';
                case 'Out of Stock' : return 'status-outstock'
            }
        },
        canDelete(status) {
            return status === 'Out of Stock' || status === 'Limited Stock';
        },
        async deleteProduct(id) {

            this.is_overlay_loading = true;
            console.log('id: ', id);
            const data = new FormData();
            data.append('id', id);

            const res = await axios.post('/seller/delete/product', data);

            console.log(res.data.message);
            if(res.data.message === "successful"){
                this.allProducts = this.allProducts.filter(item => item.id !== id);
                this.$nextTick(() => {
                    this.updateScrollableClass();
                });

            }
            this.is_overlay_loading = false;
        },
        updateScrollableClass() {
            this.$nextTick(() => {
                const tbody = this.$el.querySelector('.product-table .tbody-scroll');
                if (tbody) {
                    const isScrollable = tbody.scrollHeight > tbody.clientHeight;
                    tbody.classList.remove('scrollable', 'not-scrollable');
                    tbody.classList.add(isScrollable ? 'scrollable' : 'not-scrollable');
                }
            });
        },
        displayIcon() {
            this.clicked = true;
        },
        toggleViewProduct(productId) {
            this.viewedProductId = this.viewedProductId === productId ? null : productId;
        },
        editProduct(productId) {
            // Clear any existing messages when opening edit form
            this.message = '';

            if (this.editedProductId === productId) {
                this.editedProductId = null;
                this.editingProduct = {
                    name: '',
                    quantity: '',
                    category: '',
                    price: '',
                    status: '',
                    description: '',
                    photos: null,
                    materials: '',
                    dimensions: '',
                    weight: ''
                };
            } else {
                this.editedProductId = productId;
                // Find the product and populate the editing form
                const product = this.allProducts.find(item => item.id === productId);
                if (product) {
                    this.editingProduct = {
                        name: product.name,
                        quantity: product.quantity,
                        category: product.category,
                        price: product.price,
                        status: product.status,
                        description: product.description || '',
                        photos: product.photos,
                        materials: product.materials || '',
                        dimensions: product.dimensions || '',
                        weight: product.weight || ''
                    };
                }
            }

        },
        async saveProduct(productId) {

            this.is_overlay_loading = true;
            this.message = ''; // Clear any previous messages

            try {
                const data = new FormData();

                const images = [];

                console.log('new photo: ', this.new_photo);

                if(this.new_photo){
                    console.log("here");
                    //this store all the photos
                    this.new_photo.forEach(photo => {

                        images.push(photo.file);
                    });

                    images.forEach(img => {
                        data.append('images[]', img);
                    })
                }
                else{
                    data.append('images', null);
                }

                data.append('product_info', JSON.stringify(this.editingProduct));
                data.append('list_id', JSON.stringify(this.deleted_photo_edit));
                data.append('id', productId);

                const res = await axios.post('/seller/edit/product', data);

                console.log(res.data.message);

                console.log('product id: ', productId);
                console.log('edited product: ', this.editingProduct);
                console.log('total images: ', images);

                console.log('paths: ', res.data.paths);
                this.new_photo = [];

                if(res.data.message === 'successful'){
                    const productIndex = this.allProducts.findIndex(item => item.id === productId);
                    if (productIndex !== -1) {
                        // Update the product with the edited data
                        this.allProducts[productIndex] = {
                            ...this.allProducts[productIndex],
                            name: this.editingProduct.name,
                            quantity: this.editingProduct.quantity,
                            category: this.editingProduct.category,
                            price: this.editingProduct.price,
                            status: this.editingProduct.status,
                            description: this.editingProduct.description,
                            photos: res.data.photos,
                            materials: this.editingProduct.materials,
                            dimensions: this.editingProduct.dimensions,
                            weight: this.editingProduct.weight
                        };
                    }

                    // Show success message
                    this.message = 'Product updated successfully!';
                    this.messageType = 'success';

                    // Close the edit form after a short delay to show the message
                    setTimeout(() => {
                        this.editedProductId = null;
                        this.editingProduct = {
                            name: '',
                            quantity: '',
                            category: '',
                            price: '',
                            status: '',
                            description: '',
                            photos: [],
                            materials: '',
                            dimensions: '',
                            weight: ''
                        };
                        this.message = ''; // Clear the message
                    }, 2000);

                } else {
                    // Show error message for unsuccessful update
                    this.message = 'Failed to update product. Please try again.';
                    this.messageType = 'error';
                }

            } catch (error) {
                console.error('Error updating product:', error);
                this.message = 'An error occurred while updating the product. Please try again.';
                this.messageType = 'error';
            }

            console.log('updated products: ', this.allProducts);

            this.is_overlay_loading = false;

        },
        cancelEdit() {
            // Show cancel message
            this.message = 'Update cancelled!';
            this.messageType = 'error';

            setTimeout(() => {
                this.editedProductId = null;
                this.editingProduct = {
                    name: '',
                    quantity: '',
                    category: '',
                    price: '',
                    status: '',
                    description: '',
                    photos: null,
                    materials: '',
                    dimensions: '',
                    weight: ''
                };

                this.message = '';
            }, 1000);
        },
        handleOutsideClick(event) {
            // Check if the click was outside any toggle-details element
            const toggleDetails = event.target.closest('.toggle-details');
            const actionButton = event.target.closest('.action-btn');
            const modalContent = event.target.closest('.modal-content');

            // Close zoom modal if clicking outside modal content
            if (this.showZoom && !modalContent) {
                this.closeZoomModal();
                return;
            }

            // Only close toggle-details if click is outside both toggle-details and action buttons
            if (!toggleDetails && !actionButton) {
                this.viewedProductId = null;
                this.editedProductId = null;
            }
        },
        openZoomModal(filename) {
            console.log('Opening zoom modal for:', filename);
            this.showZoom = filename;
        },
        closeZoomModal() {
            console.log('Closing zoom modal');
            this.showZoom = null;
        },
        scrollImages(direction, productId) {
            // Use querySelector to find the container for this specific product
            const container = this.$el.querySelector(`[data-product-id="${productId}"] .toggle-img`);
            console.log('Container found:', container);
            console.log('Product ID:', productId);
            console.log('Direction:', direction);

            if (container) {
                const scrollAmount = 300; // Adjust scroll amount as needed
                if (direction === 'left') {
                    container.scrollLeft -= scrollAmount;
                } else {
                    container.scrollLeft += scrollAmount;
                }
                console.log('New scroll position:', container.scrollLeft);
            } else {
                // Fallback: try to find any visible toggle-img
                const allContainers = this.$el.querySelectorAll('.toggle-img');
                console.log('All containers found:', allContainers.length);
                const visibleContainer = Array.from(allContainers).find(cont =>
                    cont.offsetParent !== null
                );
                if (visibleContainer) {
                    const scrollAmount = 300;
                    if (direction === 'left') {
                        visibleContainer.scrollLeft -= scrollAmount;
                    } else {
                        visibleContainer.scrollLeft += scrollAmount;
                    }
                    console.log('Fallback scroll position:', visibleContainer.scrollLeft);
                } else {
                    console.log('No visible container found');
                }
            }
        }
    },
    async mounted() {
        await this.returnProducts();

        this.updateScrollableClass();
        // Add click event listener to document
        document.addEventListener('click', this.handleOutsideClick);
    },
    updated() {
        this.updateScrollableClass();
    },
    beforeUnmount() {
        // Remove event listener when component is destroyed
        document.removeEventListener('click', this.handleOutsideClick);
    }
}
</script>

<style scoped>
.overlay{
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.841);
    z-index: 3000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.overlay img{
    width: 100px;
    height: 100px;
}
.loading-container{
    width: 100%;
    height: 300px;
    display: flex;
    flex-direction: row;
    justify-content: center;
    padding-top: 50px;
}
.photo-container img{
    width: 100px;
    height: 100px;
    border: none;
}
.photo-container{
    width: 100%;
    height: auto;
    padding: 1em 1em .8em 1em;
    background-color: rgb(237, 237, 237);
    border-style: dotted;
    border-radius: 1em;
    border-color: rgb(135, 135, 135);
    display: flex;
    flex-direction: row;
    gap: 20px;
    overflow-x: auto;
    position: relative;
}

.photo-container::-webkit-scrollbar {
    height: 8px;
}

.photo-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
    margin: 0 1em;
}

.photo-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.photo-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.product-container {
    position: relative;
    padding: 1.5em ;
    display: flex;
    flex-direction: column;
    gap: 4em;
}

.product-header {
    display: flex;
    flex-direction: row;
    gap: 1.5em;
    width: 100%;
    font-size: .9em;
    align-items: center;
}

.search-bar {
    flex: 1;
    position: relative;
    display: inline-block;
}

.search-bar input[type='search'] {
    width: 100%;
    padding: 0.5em 2em 0.5em 1.3em;
    border-radius: 1em;
    outline: none;
    border: none;
    box-shadow: 0px 2px 4px rgba(0,0, 0, 0.585);
}

.search-icon {
  position: absolute;
  right: 1em;
  top: 50%;
  transform: translateY(-50%);
  color: #aaa;
  pointer-events: none;
  font-size: 1em;
}

.filters {
    flex: 2;
    display:flex;
    gap: 1em;
}

.filters select {
    background-color: #DDD0C8;
    padding: .5em .8em;
    border-radius: 1em;
    border: none;
    box-shadow: 0px 2px 5px rgba(0,0,0, 0.585);
}

.filters select option {
    outline: none;
    border: none;
}

.add-btn button {
    background-color: #ca9d73;
    text-transform: uppercase;
    color: #252525;
    font-weight: 600;
    width: 150px;
    padding-top: 5px;
    padding-bottom: 5px;
    border-radius: 2em;
    border: none;
    box-shadow: 2px 2px 2px rgba(0,0,0, 0.585);
}

    .product-table {
    /* Keep proper table layout so th and td line up */
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
    width: 100%;
    max-height: 500px;
    text-align: center;
    font-size: .95em;
    border-radius: 1em;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.733);
}

.product-table thead {
    position: sticky;
    top: 0;
    z-index: 1;
    background-clip: padding-box;
}

/* Keep tbody as block for scrolling but keep rows as table-row to preserve alignment */
.product-table .tbody-scroll {
    display: block;
    max-height: 450px;
    overflow-y: auto;
    overflow-x: hidden;
}

.product-table tbody tr,
.product-table thead tr {
    display: table;
    width: 100%;
    table-layout: fixed;
}

/* Design for when tbody is scrollable (content exceeds 450px) */
.product-table tbody.scrollable {
    background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
    border-radius: 0 0 1em 1em;
}

.product-table tbody.scrollable::-webkit-scrollbar {
    width: 8px;
}

.product-table tbody.scrollable::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 0 1em 1em 0;
}

.product-table tbody.scrollable::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.product-table tbody.scrollable::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Design for when tbody is not scrollable (content below 450px) */
.product-table tbody.not-scrollable {
    background: #ffffff;
    border-radius: 0 0 1em 1em;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.product-table tbody.not-scrollable tr:last-child td {
    border-bottom: none;
}

.product-table tbody.not-scrollable tr:last-child td:first-child {
    border-bottom-left-radius: 1em;
}

.product-table tbody.not-scrollable tr:last-child td:last-child {
    border-bottom-right-radius: 1em;
}

.product-table th {
    background-color: #DDD0C8;
    padding: 0.9em 1em; /* consistent padding with td */
    border-left: 2px solid #7e6556;
    border-bottom: 1px solid #818181;
    vertical-align: middle; /* center header content vertically */
    text-align: center; /* center header text as requested */
}

.product-table th:first-child {
    border-top-left-radius: 1em;
    border-left: none;
}

.product-table th:last-child {
    border-top-right-radius: 1em;
    width: 14.8%;
}
.product-table td {
    border: 1px solid #ffffffc7;
    outline: none;
    padding: 0.9em 1em; /* match th padding */
    vertical-align: middle; /* center cell content vertically */
    text-align: center; /* default left alignment for text cells */
}

/* Numeric or small-column values center-aligned */
.product-table td.qty,
.product-table td.price,
.product-table td.views {
    text-align: center;
}

/* Product name should truncate if long */
.product-table td.name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Action btn */
.product-table td.action-btn {
    text-align: center;
    vertical-align: middle; /* Keep alignment consistent with others */
    padding: 0.9em 1em;     /* match th/td padding */
}

.product-table td.action-btn > div {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1.5em;
}

.view-btn {
    width: 1.2em;
    color: #5e7e4b;
}
.edit-btn {
    color: #aa8f18;
}
.delete-btn {
    color: #812016;
}

/* status color for the table td background */
.status-new {
    background-color: #d4edda !important;
}
.status-preorder {
    background-color: #fff3cd    !important;
}
.status-onstock {
    background-color: #d1ecf1 !important;
}
.status-limited {
    background-color: #e2d6f0 !important;
}
.status-outstock {
    background-color: #e2e3e5 !important;
}

/* pop up design */
.overlay-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.342);
    z-index: 999;
}

.toggle-details {
    position: absolute;
    top: 320px;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50vw;
    max-width: 50em;
    height: 90vh;
    max-height: 600px;
    background: #DDD0C8;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 1.5em 4.5em;
    border-radius: 1.5em;
    overflow: auto;
    scrollbar-width: none;
  -ms-overflow-style: none;
}

.toggle-details::-webkit-scrollbar {
  display: none;
  width: 0;
  height: 0;
}

.toggle-details form {
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100%;
    gap: 1em;
    padding: 1em;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: none;
  -ms-overflow-style: none;

}

.toggle-details h3 {
    text-transform: uppercase;
}

.form-group {
    margin-bottom: 1em;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2em;
}

.form-group label {
    display: block;
    margin-bottom: 0.5em;
    font-weight: 600;
    color: #495057;
    justify-self: start;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75em;
    border: 1px solid #ced4da;
    border-radius: 0.5em;
    font-size: 0.9em;
    transition: border-color 0.3s ease;
    box-shadow: 0px 2px 2px rgba(0,0,0,0.585);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #ca9d73;
    box-shadow: 0 0 0 2px rgba(202, 157, 115, 0.2);
}

/* view toggle box design */
.view-row {
    display: flex;
    justify-content: space-between;
    gap: 1em ;
    width: 100%;
}
.image-container {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
    height: 25vh;
    min-height: 200px;
    margin-top: 1em;
}

.toggle-img {
    display: flex;
    gap: 1.5em;
    width: 100%;
    height: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    align-items: center;
    padding: 0 .3em 1em .3em;
    scroll-margin: 2em;
    scroll-padding: 2em;
}

.toggle-img {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}

.image-wrapper {
    position: relative;
    cursor: pointer;
    border-radius: 0.5em;
    overflow: hidden;
    flex-shrink: 0;
}

.toggle-img img {
    max-width: 11em;
    max-height: 11em;
    object-fit: cover;
    border-radius: 0.5em;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s ease;
    display: block;
}

.zoom-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 0.5em;
}

.image-wrapper:hover .zoom-overlay {
    opacity: 1;
}

.zoom-icon {
    color: white;
    font-size: 2em;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}


.nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none !important;
    outline: none;
    width: 2.5em;
    height: 2.5em;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: all 0.3s ease;
    color: #252525;
    font-size: 1em;
    box-shadow: none;
}

.nav-btn:hover {
    background: rgba(202, 157, 115, 0.438);
    transform: translateY(-50%) scale(1.1);
}

.nav-btn:active {
    transform: translateY(-50%) scale(0.95);
}

.nav-left {
    left: 0.5em;
    border: none !important;
    box-shadow: none;
}

.nav-right {
    right: 0.5em;
    border: none !important;
    box-shadow: none;
}

/* Rating display styles */
.rating {
    display: flex;
    align-items: center;
    gap: 0.5em;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 0.5em;
    width: fit-content;
}

.rating p {
    margin: 0;
    font-weight: 600;
    color: #252525;
    font-size: 0.9em;
}

.rating .fa-star {
    color: #a1a1a1;
    font-size: 1.2em;
    cursor: pointer;
    transition: color 0.2s ease;
}

.rating .fa-star.checked {
    color: #ff8800;
}

.rating-text {
    margin-left: 0.5em;
    font-weight: 600;
    color: #252525;
    font-size: 0.9em;
}

/* comments */
.comments-section {
    margin-top: 1.2em;
    width: 100%;
    background: #fff8f0;
    border-radius: 0.5em;
    padding: 1em;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
}

.comments-list {
    max-height: 150px;
    overflow-y: auto;
    margin-top: 0.5em;
    display: flex;

}
.comments-section h4 {
    margin: 0 0 0.5em 0;
    font-size: 1em;
    color: #ca9d73;
    font-weight: 700;
}
.comment {
    margin-bottom: 0.5em;
    font-size: 0.95em;
    margin-bottom: 30px;
    position: relative;

}
.comment-user {
    font-weight: 600;
    color: #495057;
    margin-right: 0.3em;
}
.comment-text {
    color: #252525;
    left: 20px;
    margin-top: 10px;
    display: flex;
    flex-direction: column;
}

/* Modal styles for zoomed images */
.modal {
    position: fixed;
    top: 0em;
    left: 0;
    background: rgba(0, 0, 0, 0.623);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    cursor: default;
}

.modal-content {
    position: relative;
    max-width: 30em;
    max-height: 30em;
    width: auto;
    height: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgb(255, 255, 255);
    border-radius: 12px;
    padding: 1em;
}

.zoomed-image {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.zoomed-image:hover {
    transform: scale(1.02);
}

.close-modal-btn {
    position: absolute;
    background: transparent;
    top: 1%;
    right: -3%;
    border: none !important;
    outline: none !important;
    width: 1em;
    height: 1em;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 16px;
    color: #333;
    transition: all 0.3s ease;
}

.close-modal-btn:hover {
    color: #7e6556;
    transform: scale(1.1);
}

/* Custom scrollbar for comments list */
.comments-list::-webkit-scrollbar {
    width: 6px;
}

.comments-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.comments-list::-webkit-scrollbar-thumb {
    background: #ca9d73;
    border-radius: 3px;
}

.comments-list::-webkit-scrollbar-thumb:hover {
    background: #a87d5a;
}

.message {
    padding: 1em;
    border-radius: 0.5em;
    margin-top: 1em;
    font-weight: 600;
}

.message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Responsive Design */
@media (max-width: 768px) {
    .product-container {
        padding: 1.5em;
        gap: 2em;
    }

    .product-header {
        flex-direction: column;
        gap: 1em;
        align-items: stretch;
    }

    .search-bar {
        flex: none;
        width: 100%;
    }

    .filters {
        flex: none;
        width: 100%;
        flex-direction: column;
        gap: 0.5em;
    }

    .add-btn {
        align-self: center;
    }

    .toggle-details {
        width: 95vw;
        height: 95vh;
        padding: 1em;
        border-radius: 1em;
    }

    .toggle-details h3 {
        font-size: 1.2em;
        margin-bottom: 0.5em;
    }

    .image-container {
        height: 20vh;
        min-height: 150px;
    }

    .toggle-img img {
        max-width: 8em;
        max-height: 8em;
    }

    .zoom-icon {
        font-size: 1.5em;
    }

    .nav-btn {
        width: 2em;
        height: 2em;
        font-size: 0.8em;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 1em;
    }

    .comments-section {
        margin-top: 1em;
        padding: 0.8em;
    }

    .comments-list {
        max-height: 120px;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 1em;
    }

    .product-table {
        min-width: 1300px;
        width: auto;
    }

    .product-table th,
    .product-table td {
        white-space: nowrap;
    }

    .product-container {
        padding: 1.5em;
        gap: 2em;
    }

    .product-table {
        font-size: 0.9em;
        overflow-x: auto;
    }

    .product-table th,
    .product-table td {
        padding: 0.8em 0.5em;
    }

    .action-btn {
        width: 15em;
    }

    .action-btn button {
        padding: 0.3em 0.6em;
        font-size: 0.8em;
        min-width: 50px;
    }

    .toggle-details {
        width: 95vw;
        height: 95vh;
        padding: 1em;
    }

    .toggle-details h3 {
        font-size: 1.2em;
    }

    .image-container {
        height: 20vh;
        min-height: 150px;
    }

    .toggle-img img {
        max-width: 8em;
        max-height: 8em;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 0.8em;
        font-size: 0.9em;
    }

    .comments-section {
        padding: 0.8em;
    }

    .comments-list {
        max-height: 120px;
    }

    .comment {
        font-size: 0.9em;
    }
}

@media (max-width: 480px) {
    .product-container {
        padding: 1em;
        gap: 1.5em;
    }

    .product-table {
        font-size: 0.8em;
    }

    .action-btn button {
        padding: 0em 0.4em;
        font-size: 0.7em;
        min-width: 20px;
    }

    .toggle-details {
        width: 98vw;
        height: 98vh;
        padding: 0.8em;
    }

    .toggle-details h3 {
        font-size: 1em;
    }

    .image-container {
        height: 18vh;
        min-height: 120px;
    }

    .toggle-img img {
        max-width: 6em;
        max-height: 6em;
    }

    .zoom-icon {
        font-size: 1.2em;
    }

    .nav-btn {
        width: 1.8em;
        height: 1.8em;
        font-size: 0.7em;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 0.6em;
        font-size: 0.85em;
    }

    .comments-section {
        padding: 0.6em;
    }

    .comments-list {
        max-height: 100px;
    }

    .comment {
        font-size: 0.85em;
    }
}

@media (max-width: 320px) {
    .product-container {
        padding: 0.5em;
    }

    .toggle-details {
        width: 99vw;
        height: 99vh;
        padding: 0.5em;
    }

    .image-container {
        height: 15vh;
        min-height: 100px;
    }

    .toggle-img img {
        max-width: 5em;
        max-height: 5em;
    }

    .zoom-icon {
        font-size: 1em;
    }

    .nav-btn {
        width: 1.5em;
        height: 1.5em;
        font-size: 0.6em;
    }
}

/* Tablet devices */
@media (min-width: 769px) and (max-width: 900px) {
    .product-container {
        padding: 2em;
        gap: 2.5em;
    }

    .product-table {
        font-size: 0.95em;
    }

    .product-table th,
    .product-table td {
        padding: 1em 0.8em;
    }

    .action-btn button {
        padding: 0.4em 0.8em;
        font-size: 0.85em;
        min-width: 60px;
    }

    .toggle-details {
        width: 90vw;
        height: 90vh;
        padding: 1.5em;
    }

    .toggle-details h3 {
        font-size: 1.4em;
    }

    .image-container {
        height: 25vh;
        min-height: 180px;
    }

    .toggle-img img {
        max-width: 10em;
        max-height: 10em;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 1em;
        font-size: 0.95em;
    }

    .comments-section {
        padding: 1em;
    }

    .comments-list {
        max-height: 150px;
    }

    .comment {
        font-size: 0.95em;
    }
}

/* Laptop devices */
@media (min-width: 901px) and (max-width: 1380px) {
    .product-container {
        padding: 1.5em 2em;
        gap: 2em;
    }

    .product-table {
        font-size: .8em;
    }

    .product-table th{
        padding: 1em;
    }

    .product-table tbody {
        background-color: #000;
        max-height: 28em;
    }

    .toggle-details {
        top: 50%;
        width: 85vw;
        height: 75vh;
        padding: 2em;
    }

    .toggle-details h3 {
        font-size: 1.6em;
    }

    .image-container {
        height: 30vh;
        min-height: 220px;
    }

    .toggle-img img {
        max-width: 13em;
        max-height: 13em;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 1.2em;
        font-size: 1em;
    }

    .comments-section {
        padding: 1.2em;
    }

    .comments-list {
        max-height: 170px;
    }

    .comment {
        font-size: 1em;
    }
}

/* Landscape orientation for mobile */
@media (max-width: 768px) and (orientation: landscape) {
    .toggle-details {
        width: 98vw;
        height: 98vh;
        padding: 1.5em;
    }

    .image-container {
        height: 25vh;
        min-height: 120px;
    }

    .toggle-img img {
        max-width: 10em;
        max-height: 10em;
    }
}

/* High DPI displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .toggle-img img {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    .action-btn button {
        min-height: 44px;
        min-width: 44px;
    }

    .nav-btn {
        min-height: 44px;
        min-width: 44px;
    }

    .zoom-icon {
        min-height: 44px;
        min-width: 44px;
    }
}

/* Print styles */
@media print {
    .product-container {
        padding: 0;
    }

    .action-btn,
    .toggle-details {
        display: none;
    }

    .product-table {
        border: 1px solid #000;
    }
}
</style>
