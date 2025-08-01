<template>
    <div class="product-container">
        <teleport to="body">
            <div class="overlay" v-if="is_overlay_loading">
                <img src="../../../images/kOnzy.gif">
            </div>
        </teleport>
        <div class="header">
            <h1>Add New Product</h1>
            <router-link to="/seller/products">
                <button class="return-btn">Return to Products</button>
            </router-link>
        </div>

        <form @submit.prevent="submitProduct" class="product-form">
            <div class="form-sections">
                <!-- Basic Information Section -->
                <div class="form-section">
                    <h2>Basic Information</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-name">Product Name *</label>
                            <input
                                type="text"
                                id="product-name"
                                v-model="productData.name"
                                required
                                placeholder="Enter product name"
                            />
                        </div>
                        <div class="form-group">
                            <label for="product-category">Category *</label>
                            <select id="product-category" v-model="productData.category" required>
                                <option value="">Select Category</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Kitchenware">Kitchenware</option>
                                <option value="Musical Instrument">Musical Instrument</option>
                                <option value="Decorative Items">Decorative Items</option>
                                <option value="Games">Games</option>
                                <option value="Outdoor Decor">Outdoor Decor</option>
                                <option value="Home Decor">Home Decor</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="product-status">Status *</label>
                            <select id="product-status" v-model="productData.status" required>
                                <option value="">Select Status</option>
                                <option value="New Arrival">New Arrival</option>
                                <option value="Pre Order">Pre Order</option>
                                <option value="On Stock">On Stock</option>
                                <option value="Limited Stock">Limited Stock</option>
                                <option value="Out of Stock">Out of Stock</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="product-description">Description *</label>
                        <textarea
                            id="product-description"
                            v-model="productData.description"
                            required
                            rows="4"
                            placeholder="Describe your product..."
                        ></textarea>
                    </div>
                </div>

                <!-- Pricing & Inventory Section -->
                <div class="form-section">
                    <h2>Pricing & Inventory</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-price">Price (PHP) *</label>
                            <input
                                type="number"
                                id="product-price"
                                v-model="productData.price"
                                required
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                            />
                        </div>

                        <div class="form-group">
                            <label for="product-quantity">Quantity *</label>
                            <input
                                type="number"
                                id="product-quantity"
                                v-model="productData.quantity"
                                required
                                min="0"
                                placeholder="0"
                            />
                        </div>
                    </div>
                </div>

                <!-- Images Section -->
                <div class="form-section">
                    <h2>Product Images</h2>
                    <div class="image-upload-area">
                        <div class="upload-zone" @click="triggerImageUpload" @drop="handleImageDrop" @dragover.prevent>
                            <div v-if="!productData.images.length" class="upload-placeholder">
                                <i class="fa fa-cloud-upload"></i>
                                <p>Click to upload or drag images here</p>
                                <p class="upload-hint">Supports: JPG, PNG, GIF (Max 5MB each)</p>
                            </div>
                            <div v-else class="image-preview">
                                <div v-for="(image, index) in productData.images" :key="index" class="image-item">
                                    <img :src="image.preview" :alt="`Product image ${index + 1}`" />
                                    <button type="button" @click="removeImage(index)" class="remove-image">×</button>
                                </div>
                            </div>
                        </div>
                        <input
                            type="file"
                            ref="imageInput"
                            multiple
                            accept="image/*"
                            @change="handleImageSelect"
                            style="display: none;"
                        />
                    </div>
                </div>

                <!-- Additional Details Section -->
                <div class="form-section">
                    <h2>Additional Details</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-materials">Materials (Optional)</label>
                            <input
                                type="text"
                                id="product-materials"
                                v-model="productData.materials"
                                placeholder="e.g., Solid wood, Metal, Fabric"
                            />
                        </div>

                        <div class="form-group">
                            <label for="product-dimensions">Dimensions (Optional)</label>
                            <input
                                type="text"
                                id="product-dimensions"
                                v-model="productData.dimensions"
                                placeholder="e.g., 120cm x 60cm x 75cm"
                            />
                        </div>

                        <div class="form-group">
                            <label for="product-weight">Weight (Optional)</label>
                            <input
                                type="text"
                                id="product-weight"
                                v-model="productData.weight"
                                placeholder="e.g., 15kg"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="submit-btn" :disabled="isSubmitting">
                    {{ isSubmitting ? 'Adding Product...' : 'Add Product' }}
                </button>
            </div>
        </form>

        <!-- Success/Error Messages -->
        <div v-if="message" :class="['message', messageType]">
            {{ message }}
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore';

export default {
    data() {
        return {
            is_overlay_loading: false,
            store: useDataStore(),
            productData: {
                name: '',
                description: '',
                category: '',
                status: '',
                price: '',
                quantity: '',
                images: [],
                materials: '',
                dimensions: '',
                weight: ''
            },
            isSubmitting: false,
            message: '',
            messageType: 'success'
        }
    },
    methods: {
        triggerImageUpload() {
            this.$refs.imageInput.click();
        },

        handleImageSelect(event) {
            const files = Array.from(event.target.files);
            this.processImages(files);
        },

        handleImageDrop(event) {
            event.preventDefault();
            const files = Array.from(event.dataTransfer.files);
            this.processImages(files);
        },

        processImages(files) {
            files.forEach(file => {
                if (file.type.startsWith('image/') && file.size <= 5 * 1024 * 1024) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.productData.images.push({
                            file: file,
                            preview: e.target.result
                        });
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.showMessage('Please select valid image files (max 5MB each)', 'error');
                }
            });
        },

        removeImage(index) {
            this.productData.images.splice(index, 1);
        },

        async submitProduct() {

            this.is_overlay_loading = true;

            console.log('user info: ', this.store.currentUser_info);
            console.log('images', this.productData.images);

            if (!this.validateForm()) {
                return;
            }

            this.isSubmitting = true;

            try {
                //this 'data' object will be the container for sending the productData to the server. Using append() method to store the productData
                const data = new FormData();
                data.append('product', JSON.stringify(this.productData));
                this.productData.images.forEach(img => {
                    data.append('images[]', img.file);
                });
                data.append('id', this.store.currentUser_info.id);

                //axios is a library that will help to communicate client and server.
                const res = await axios.post('/seller/add/product', data);

                console.log(res.data.message);

                if(res.data.message === "successful"){
                    this.$router.push({name: 'Products'});
                }

            } catch (error) {
                this.showMessage('Failed to add product. Please try again.', 'error');
            } finally {
                this.is_overlay_loading = false;
                this.isSubmitting = false;
            }
        },

        validateForm() {
            if (!this.productData.name.trim()) {
                this.showMessage('Product name is required', 'error');
                return false;
            }

            if (!this.productData.description.trim()) {
                this.showMessage('Product description is required', 'error');
                return false;
            }

            if (!this.productData.category) {
                this.showMessage('Please select a category', 'error');
                return false;
            }

            if (!this.productData.status) {
                this.showMessage('Please select a status', 'error');
                return false;
            }

            if (!this.productData.price || this.productData.price <= 0) {
                this.showMessage('Please enter a valid price', 'error');
                return false;
            }

            if (!this.productData.quantity || this.productData.quantity < 0) {
                this.showMessage('Please enter a valid quantity', 'error');
                return false;
            }

            return true;
        },

        showMessage(text, type) {
            this.message = text;
            this.messageType = type;

            setTimeout(() => {
                this.message = '';
            }, 5000);
        }
    }
}
</script>

<style scoped>
.overlay{
    position: fixed;
    left: 0;
    top: 0;
    width: 100vw;
    height: 100vh;
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
.product-container {
    padding: 2em;
    margin: 0 auto;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2em;
    padding-bottom: 1em;
    border-bottom: 2px solid #DDD0C8;
}

.header h1 {
    color: #252525;
    font-size: 1.8em;
    font-weight: 600;
    text-transform: uppercase;
    margin: 0;
}

.return-btn {
    padding: 0.5em 1.5em;
    color: #252525;
    font-weight: 600;
    text-transform: uppercase;
    background-color: #ca9d73;
    border-radius: 2em;
    border: none;
    box-shadow: 2px 2px 2px rgba(0,0,0,0.585);
    cursor: pointer;
    transition: all 0.3s ease;
}

.return-btn:hover {
    background-color: #b88a5f;
    transform: translateY(-1px);
}

.product-form {
    display: flex;
    flex-direction: column;
    gap: 2em;
    width: 90%;
    justify-self: center;
}

.form-sections {
    display: flex;
    flex-direction: column;
    gap: 2em;
}

.form-section {
    background: #f8f9fa;
    padding: 2.5em;
    border-radius: 1em;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.585);
    border: none;
}

.form-section h2 {
    color: #252525;
    font-size: 1.2em;
    font-weight: 600;
    margin-bottom: 1em;
    text-transform: uppercase;
}

.form-group {
    margin-bottom: 1em;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1em;
}

.form-group label {
    display: block;
    margin-bottom: 0.5em;
    font-weight: 600;
    color: #495057;
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
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #ca9d73;
    box-shadow: 0 0 0 2px rgba(202, 157, 115, 0.2);
}

.image-upload-area {
    margin-top: 1em;
}

.upload-zone {
    border: 2px dashed #ced4da;
    border-radius: 0.5em;
    padding: 2em;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-zone:hover {
    border-color: #ca9d73;
    background-color: rgba(202, 157, 115, 0.05);
}

.upload-placeholder {
    color: #6c757d;
}

.upload-placeholder i {
    font-size: 2em;
    margin-bottom: 0.5em;
    color: #ca9d73;
}

.upload-hint {
    font-size: 0.8em;
    color: #adb5bd;
    margin-top: 0.5em;
}

.image-preview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1em;
}

.image-item {
    position: relative;
    border-radius: 0.5em;
    overflow: hidden;
}

.image-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
}

.remove-image {
    position: absolute;
    top: 0.25em;
    right: 0.25em;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    cursor: pointer;
    font-size: 1em;
    line-height: 1;
}

.form-actions {
    display: flex;
    gap: 1em;
    justify-content: center;
    padding-top: 1em;
    border-top: 1px solid #e9ecef;
}

.draft-btn,
.submit-btn {
    padding: 0.75em 2em;
    border-radius: 2em;
    font-weight: 600;
    text-transform: uppercase;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.draft-btn {
    background-color: #6c757d;
    color: white;
}

.draft-btn:hover {
    background-color: #5a6268;
}

.submit-btn {
    background-color: #ca9d73;
    color: #252525;
}

.submit-btn:hover:not(:disabled) {
    background-color: #b88a5f;
    transform: translateY(-1px);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
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
        padding: 1em;
    }

    .header {
        flex-direction: column;
        gap: 1em;
        text-align: center;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .image-preview {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
}
</style>
