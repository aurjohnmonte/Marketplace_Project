<template>
    <div class="product-container">
        <div class="product-header">
            <div class="search-bar">
                <input type="search" name="search-product" id="search-product" placeholder="Search Product" @input="searchProduct">
                <i class="fa fa-search search-icon"></i>
            </div>
            <div class="filters">
                <select name="product-category" id="product-category" @change="filterByCategory">
                    <option value="">Filter by category</option>
                    <option v-for="category in productCategory" :key="category" :value="category">
                        {{ category }}
                    </option>
                </select>
                <select name="product-status" id="product-status" @change="filterByStatus">
                    <option value="">Filter by status</option>
                    <option v-for="status in productStatus" :key="status" :value="status">
                        {{ status }}
                    </option>
                </select>
            </div>

            <div class="add-btn">
                <router-link to="/seller/product/add">
                    <button>Add Product</button>
                </router-link>
            </div>
        </div>

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

            <tbody>
                <tr v-for="item in filteredProducts" :key="item.id" :class="statusClass(item.status)">
                    <td class="name">{{ item.name }}</td>
                    <td class="qty">{{ item.qty }}</td>
                    <td class="cat">{{ item.cat }}</td>
                    <td class="price">{{ item.price }}</td>
                    <td class="views">{{ item.views }}</td>
                    <td class="status">{{ item.status }}</td>
                    <td class="action-btn">
                        <button class="view-btn" @click="toggleViewProduct(item.id)">View</button>

                        <div v-if="viewedProductId === item.id" class="toggle-details" :data-product-id="item.id">
                            <h3>View Product</h3>
                            <div class="image-container">
                                <button class="nav-btn nav-left"
                                    @click="scrollImages('left', item.id)"
                                    v-if="item.images.length>3">
                                    <i class="fa fa-chevron-left"></i>
                                </button>
                                <div class="toggle-img">
                                    <img v-for="image in item.images" :key="image" :src="image" alt="Image">
                                </div>
                                <button class="nav-btn nav-right"
                                    @click="scrollImages('right', item.id)"
                                    v-if="item.images.length>3">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="rating">
                                <p>Viewer Rating:</p>
                                <span
                                    v-for="star in 5"
                                    :key="star"
                                    class="fa fa-star"
                                    :class="{ checked: star <= item.rating }"
                                ></span>
                                <span class="rating-text">({{ item.rating }}/5)</span>
                            </div>
                            <div class="comments-section">
                                <h4>User Comments</h4>
                                <div v-if="item.comments && item.comments.length">
                                    <div v-for="(comment, idx) in item.comments" :key="idx" :class="comment">
                                        <span class="comment-user">{{ comment.user }}:</span>
                                        <span class="comment-text">{{ comment.text }}</span>
                                    </div>
                                </div>
                                <div v-else>
                                    <span>No comment yet.</span>
                                </div>
                            </div>
                        </div>

                        <button class="edit-btn" @click="editProduct(item.id)">Edit</button>
                        <div v-if="editedProductId === item.id" class="toggle-details">
                            <form method="GET" class="form" @submit.prevent="saveProduct(item.id)">
                                <h3>Edit Product</h3>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="name">Product Name</label>
                                        <input type="text" name="name" id="name" v-model="editingProduct.name">
                                    </div>
                                    <div class="form-group">
                                        <label for="qty">Quantity</label>
                                        <input type="text" name="qty" id="qty" v-model="editingProduct.qty">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="product-category">Category</label>
                                        <select name="product-category" id="product-category" v-model="editingProduct.cat">
                                            <option v-for="category in productCategory" :key="category" :value="category">
                                                {{ category }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="product-status">Status</label>
                                        <select name="product-status" id="product-status" v-model="editingProduct.status">
                                            <option v-for="currentStatus in productStatus" :key="currentStatus" :value="currentStatus">
                                                {{ currentStatus }}
                                            </option>
                                        </select>
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
                                    <button type="submit">Save Changes</button>
                                    <button type="button" @click="cancelEdit" style="margin-left: 10px; background-color: #6c757d;">Cancel</button>
                                </div>
                            </form>
                        </div>
                        <button
                            v-if="canDelete(item.status)"
                            @click="deleteProduct(item.id)"
                            class="delete-btn"
                        >Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Overlay background when toggle-details are displayed -->
        <div v-if="viewedProductId || editedProductId" class="overlay-background"></div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            searchQuery: '',
            selectedCategory: '',
            selectedStatus: '',
            viewedProductId: null,
            editedProductId: null,
            editingProduct: {
                name: '',
                qty: '',
                cat: '',
                price: '',
                status: '',
                description: ''
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
                'Accessory',
                'Decoration',
                'Kitchen',
                'Bathroom',
                'Outdoor'
            ],

            /* Palihog ko ug connect ani sa database bai */
            description: '',
            allProducts:  [
                {id: '1', name: 'Chair', qty: '10', cat: 'Furniture', price: '100', views: '5', status: 'New Arrival', rating: 3, images: ['https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1592078615290-033ee584e267?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1592078615290-033ee584e267?w=400&h=400&fit=crop'],
                    comments: [{user: 'Al', text: 'Very comfortable!'}]
                },
                {id: '2', name: 'Table', qty: '5', cat: 'Furniture', price: '200', views: '3', status: 'Pre Order', rating: 4, images: ['https://images.unsplash.com/photo-1532372320572-cda25653a5d4?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&h=400&fit=crop'],
                    comments: [{user: 'Al', text: 'Very comfortable!'}, {user: 'Jems', text: 'Wide!'}]
                },
                {id: '3', name: 'Lamp', qty: '0', cat: 'Furniture', price: '50', views: '8', status: 'On Stock', rating: 5, images: ['https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1513506003901-1e6a229e2d21?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400&h=400&fit=crop']},
                {id: '4', name: 'Sofa', qty: '2', cat: 'Furniture', price: '500', views: '2', status: 'Limited Stock', rating: 2, images: ['https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1493663284031-f7d311293a2d?w=400&h=400&fit=crop']},
                {id: '5', name: 'Necklace', qty: '2', cat: 'Accessory', price: '500', views: '2', status: 'Out of Stock', rating: 4, images: ['https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1573408301185-9146fe634ad0?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=400&h=400&fit=crop']},
                {id: '7', name: 'Ring', qty: '3', cat: 'Accessory', price: '300', views: '1', status: 'New Arrival', rating: 3, images: ['https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1573408301185-9146fe634ad0?w=400&h=400&fit=crop']},
                {id: '8', name: 'Bracelet', qty: '1', cat: 'Accessory', price: '150', views: '4', status: 'Limited Stock', rating: 5, images: ['https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1573408301185-9146fe634ad0?w=400&h=400&fit=crop']},
                {id: '9', name: 'Earrings', qty: '0', cat: 'Accessory', price: '80', views: '6', status: 'Out of Stock', rating: 1, images: ['https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1573408301185-9146fe634ad0?w=400&h=400&fit=crop']},
                {id: '10', name: 'Watch', qty: '4', cat: 'Accessory', price: '250', views: '3', status: 'On Stock', rating: 4, images: ['https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1547996160-81dfa63595aa?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=400&h=400&fit=crop']},
                {id: '11', name: 'Desk', qty: '2', cat: 'Furniture', price: '350', views: '2', status: 'Limited Stock', rating: 3, images: ['https://images.unsplash.com/photo-1532372320572-cda25653a5d4?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&h=400&fit=crop']},
                {id: '12', name: 'Bookshelf', qty: '1', cat: 'Furniture', price: '180', views: '1', status: 'Out of Stock', rating: 2, images: ['https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1592078615290-033ee584e267?w=400&h=400&fit=crop']},
                {id: '13', name: 'Mirror', qty: '3', cat: 'Furniture', price: '120', views: '5', status: 'New Arrival', rating: 4, images: ['https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1493663284031-f7d311293a2d?w=400&h=400&fit=crop', 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=400&fit=crop']}
            ]
        }
    },
    computed: {
        filteredProducts() {
            let filtered = this.allProducts;

            // Filter by search
            if (this.searchQuery) {
                filtered = filtered.filter(item =>
                    item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    item.cat.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            }

            // Filter by category
            if (this.selectedCategory) {
                filtered = filtered.filter(item =>
                    item.cat.toLowerCase() === this.selectedCategory.toLowerCase()
                );
            }

            // Filter by status
            if (this.selectedStatus) {
                filtered = filtered.filter(item =>
                    item.status === this.selectedStatus
                );
            }

            return filtered;
        }
    },
    methods: {
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
        deleteProduct(id) {
            this.allProducts = this.allProducts.filter(item => item.id !== id);
            this.$nextTick(() => {
                this.updateScrollableClass();
            });
        },
        filterByCategory(event) {
            this.selectedCategory = event.target.value;
        },
        filterByStatus(event) {
            this.selectedStatus = event.target.value;
        },
        searchProduct(event) {
            this.searchQuery = event.target.value;
        },
        updateScrollableClass() {
            this.$nextTick(() => {
                const tbody = this.$el.querySelector('.product-table tbody');
                if (tbody) {
                    const isScrollable = tbody.scrollHeight > tbody.clientHeight;
                    tbody.classList.remove('scrollable', 'not-scrollable');
                    tbody.classList.add(isScrollable ? 'scrollable' : 'not-scrollable');
                }
            });
        },
        toggleViewProduct(productId) {
            this.viewedProductId = this.viewedProductId === productId ? null : productId;
        },
        editProduct(productId) {
            if (this.editedProductId === productId) {
                this.editedProductId = null;
                this.editingProduct = {
                    name: '',
                    qty: '',
                    cat: '',
                    price: '',
                    status: '',
                    description: ''
                };
            } else {
                this.editedProductId = productId;
                // Find the product and populate the editing form
                const product = this.allProducts.find(item => item.id === productId);
                if (product) {
                    this.editingProduct = {
                        name: product.name,
                        qty: product.qty,
                        cat: product.cat,
                        price: product.price,
                        status: product.status,
                        description: product.description || ''
                    };
                }
            }
        },
        saveProduct(productId) {
            // Find the product index
            const productIndex = this.allProducts.findIndex(item => item.id === productId);
            if (productIndex !== -1) {
                // Update the product with the edited data
                this.allProducts[productIndex] = {
                    ...this.allProducts[productIndex],
                    name: this.editingProduct.name,
                    qty: this.editingProduct.qty,
                    cat: this.editingProduct.cat,
                    price: this.editingProduct.price,
                    status: this.editingProduct.status,
                    description: this.editingProduct.description
                };
            }

            // Close the edit form
            this.editedProductId = null;
            this.editingProduct = {
                name: '',
                qty: '',
                cat: '',
                price: '',
                status: '',
                description: ''
            };
        },
        cancelEdit() {
            this.editedProductId = null;
            this.editingProduct = {
                name: '',
                qty: '',
                cat: '',
                price: '',
                status: '',
                description: ''
            };
        },
        handleOutsideClick(event) {
            // Check if the click was outside any toggle-details element
            const toggleDetails = event.target.closest('.toggle-details');
            const actionButton = event.target.closest('.action-btn');

            // Only close if click is outside both toggle-details and action buttons
            if (!toggleDetails && !actionButton) {
                this.viewedProductId = null;
                this.editedProductId = null;
            }
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
    mounted() {
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
.product-container {
    padding: 3em;
    display: flex;
    flex-direction: column;
    gap: 4em;
    overflow: hidden;
}

.product-header {
    display: flex;
    flex-wrap: wrap;
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
    padding: .5em 1.5em;
    border-radius: 2em;
    border: none;
    box-shadow: 2px 2px 2px rgba(0,0,0, 0.585);
}

.product-table {
    max-height: 500px;
    text-align: center;
    font-size: .95em;
    width: 100%;
    border-radius: 1em;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.733);
}

.product-table thead {
    position: sticky;
    top: 0;
    z-index: 1;
    display: table;
    width: 100%;
    table-layout: fixed;
}

.product-table tbody {
    display: block;
    max-height: 450px;
    overflow-y: auto;
    overflow-x: hidden;
}

.product-table tbody tr {
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
    padding: 1.1em 1em;
    border-left: 2px solid #7e6556;
    border-bottom: 1px solid #818181;
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
    padding: .5em 0;
}

/* Action btn */
.product-table td.action-btn {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5em;
    padding: 0.8em;
    flex-wrap: wrap;
    min-height: 40px;
}

.action-btn button {
    padding: 0.15em 0.6em;
    margin: 0;
    min-width: 45px;
    font-size: 0.75em;
    color: #252525;
    font-weight: 700;
    border-radius: 0.5em;
    border: .5px solid #818181;
    outline: none;
    text-transform: uppercase;
    white-space: nowrap;
}
.view-btn {
    background-color: #8ABB6C;
}
.edit-btn {
    background-color: #FFC107;
}
.delete-btn {
    background-color: #E14434;
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
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.342);
    z-index: 999;
}

.toggle-details {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50vw;
    height: 80vh;
    background: #DDD0C8;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    z-index: 1000;
    padding: 2em 4.5em;
    border-radius: 2em;
    overflow: hidden;
}

.toggle-details form {
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100%;
    gap: 1em;

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
.image-container {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
    height: 30vh;
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
    padding: 0 .3em;
}

.toggle-img {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}

.toggle-img img {
    max-width: 11em;
    max-height: 11em;
    object-fit: cover;
    border-radius: 0.5em;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s ease;
    flex-shrink: 0;
}

.toggle-img img:hover {
    transform: scale(1.05);
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
    margin-top: 1em;
    padding: 0.5em 1em;
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
.comments-section h4 {
    margin: 0 0 0.5em 0;
    font-size: 1em;
    color: #ca9d73;
    font-weight: 700;
}
.comment {
    margin-bottom: 0.5em;
    font-size: 0.95em;
}
.comment-user {
    font-weight: 600;
    color: #495057;
    margin-right: 0.3em;
}
.comment-text {
  color: #252525;
}
</style>
