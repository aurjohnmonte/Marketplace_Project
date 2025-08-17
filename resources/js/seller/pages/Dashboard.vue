<template>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="dashboard-header-left">
                <h3>Welcome {{ user.name }},</h3>
                <p>Your dashboard overview</p>
                <div class="search-product">
                    <div class="search-input-wrapper">
                        <input type="search" name="search" id="search" placeholder="Search your product..." @input="searchProduct">
                        <i class="fa fa-search search-icon"></i>
                    </div>
                    <select name="filter" id="filter">
                        <option value="">Filter by Category</option>
                        <option value="all">All</option>
                        <option v-for="category in productCategory" :key="category" :value="category">
                            {{ category }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="header-right">
                <router-link to="/seller/map">
                    <i class="fa-solid fa-location-dot"
                        :class="{ active: $route.name === 'Map' }">
                    </i>
                </router-link>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="card-container">
                <div class="card-group-1 cards">
                    <div class="card-header">
                        <h6>Total Shop Reviews</h6>
                        <i class="fi fi-rr-thumbs-up-trust"></i>
                        <p><span>{{ user.reviews }}</span> Reviews</p>
                    </div>
                    <div class="card-content">
                        <canvas id="genderViewsChart" class="gender-chart"></canvas>
                        <div class="info-container">
                            <h6>Top Reviews from follower</h6>
                            <img :src=" follower.profilepic" alt="Follower">
                            <div class="details">
                                <p>{{ follower.username }}</p>
                                <p>{{ follower.reviews }} reviews</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-group-2 cards">
                    <div class="card-header">
                        <h6>Total Products</h6>
                        <i class="fi fi-rr-box-open-full"></i>
                        <p><span>{{ user.totalProducts }}</span> Products</p>
                    </div>
                    <div class="product-container">
                        <div class="review-section">
                            <div class="section-header">
                                <h6>Top product with high reviews</h6>
                                <a href="#" class="view-all">view all</a>
                            </div>
                            <div class="product-list">
                                <div class="product-item" v-for="(product, index) in highReviewProducts" :key="index">
                                    <div class="product-icon">
                                        <i class="fi fi-rr-box-open"></i>
                                    </div>
                                    <div class="product-details">
                                        <p class="product-name">{{ product.name }}</p>
                                        <p class="review-count" v-if="product.reviewCount">{{ product.reviewCount }} reviews</p>
                                    </div>
                                    <div class="rating-info">
                                        <span class="rating-score">{{ product.rating }}</span>
                                        <i class="fi fi-sr-star filled"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="review-section">
                            <div class="section-header">
                                <h6>Top product with low reviews</h6>
                                <a href="#" class="view-all">view all</a>
                            </div>
                            <div class="product-list">
                                <div class="product-item" v-for="(product, index) in lowReviewProducts" :key="index">
                                    <div class="product-icon">
                                        <i class="fi fi-rr-box-open"></i>
                                    </div>
                                    <div class="product-details">
                                        <p class="product-name">{{ product.name }}</p>
                                        <p class="review-count" v-if="product.reviewCount">{{ product.reviewCount }} reviews</p>
                                    </div>
                                    <div class="rating-info">
                                        <span class="rating-score">{{ product.rating }}</span>
                                        <i class="fi fi-sr-star filled"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="graph cards">
                </div>
                <div class="chart cards">
                    <div class="chart-header">
                        <div class="header-left">
                            <h4>Top Categories</h4>
                        </div>
                        <a href="#" class="view-all">view all</a>
                    </div>
                    <div class="category-content">

                    </div>
                </div>
            </div>
            <div class="card-container-2">
                <div class="follower-card cards">
                    <div class="follower-stats">
                        <h4>Follower Statistics</h4>
                        <p class="total-followers"><span>{{ user.totalFollowers }}</span> total followers</p>
                    </div>
                    <div class="stats-content">
                        <div class="stat-item">
                            <span class="stat-label">gender</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">age</span>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="search-section">
                        <div class="search-bar">
                            <div class="search-input-wrapper">
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    placeholder="Search products..."
                                    @input="handleSearch"
                                    class="search-input"
                                >
                                <i class="fi fi-sr-search search-icon"></i>
                            </div>
                            <button @click="performSearch" class="search-button">
                                <i class="fi fi-sr-search"></i>
                                Search
                            </button>
                        </div>
                    </div>
                    <div class="table-section">
                        <div v-if="searchResults.length > 0" class="search-results">
                            <h4>Search Results ({{ searchResults.length }})</h4>
                            <div class="table-wrapper">
                                <table class="products-table">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th>Total Products Sold</th>
                                            <th>Rating</th>
                                            <th>Total Reviews</th>
                                            <th>Followers</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(result, index) in searchResults" :key="index" class="table-row">
                                            <td class="product-name-cell">{{ result.name }}</td>
                                            <td class="category-cell">{{ result.category }}</td>
                                            <td class="sales-cell">{{ result.totalSold || 0 }}</td>
                                            <td class="rating-cell">
                                                <div class="rating-display">
                                                    <span class="rating-score">{{ result.rating }}</span>
                                                    <i class="fi fi-sr-star filled"></i>
                                                </div>
                                            </td>
                                            <td class="reviews-cell">{{ result.reviewCount || 0 }}</td>
                                            <td class="followers-cell">{{ result.followers || 0 }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div v-else-if="searchQuery && !isSearching" class="no-results">
                            <i class="fi fi-sr-search"></i>
                            <p>No products found for "{{ searchQuery }}"</p>
                        </div>
                        <div v-else-if="isSearching" class="searching">
                            <i class="fi fi-sr-loading"></i>
                            <p>Searching...</p>
                        </div>
                        <div v-else class="default-content">
                            <div class="table-wrapper">
                                <table class="products-table">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th>Items Sold</th>
                                            <th>Rating</th>
                                            <th>Reviews</th>
                                            <th>Followers</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(product, index) in allProducts" :key="index" class="table-row">
                                            <td class="product-name-cell">{{ product.name }}</td>
                                            <td class="category-cell">{{ product.category }}</td>
                                            <td class="sales-cell">{{ product.totalSold || 0 }}</td>
                                            <td class="rating-cell">
                                                <div class="rating-display">
                                                    <span class="rating-score">{{ product.rating }}</span>
                                                    <i class="fi fi-sr-star filled"></i>
                                                </div>
                                            </td>
                                            <td class="reviews-cell">{{ product.reviewCount || 0 }}</td>
                                            <td class="followers-cell">{{ product.followers || 0 }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="chart cards notif-card">
                    <div class="chart-header">
                        <div class="header-left">
                            <h4>Recent Notification</h4>
                            <span v-if="unreadNotificationsCount > 0" class="unread-badge">{{ unreadNotificationsCount }}</span>
                        </div>
                        <a href="#" class="view-all">view all</a>
                    </div>
                    <div class="notification-content">
                        <div
                            class="notification-item"
                            :class="notification.status"
                            v-for="(notification, index) in recentNotifications"
                            :key="index"
                            @click="markAsRead(index)"
                            :style="{ cursor: notification.status === 'unread' ? 'pointer' : 'default' }"
                        >
                            <div class="notification-icon">
                                <i :class="notification.icon"></i>
                            </div>
                            <div class="notification-details">
                                <p class="notification-title">{{ notification.title }}</p>
                                <p class="notification-message">{{ notification.message }}</p>
                                <span class="notification-time">{{ notification.time }}</span>
                            </div>
                            <div class="notification-status" :class="notification.status">
                                <i class="fi fi-sr-circle"></i>
                            </div>
                        </div>
                        <div v-if="recentNotifications.length === 0" class="no-notifications">
                            <i class="fi fi-sr-bell"></i>
                            <p>No new notifications</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            searchQuery: '',
            searchResults: [],
            isSearching: false,
            searchTimeout: null,
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


            user: {
                name: 'Borlolot',
                reviews: 32,
                totalProducts: 200,
                totalFollowers: 100
            },
            recentNotifications: [
                {
                    icon: 'fi fi-sr-shopping-cart',
                    title: 'New Order Received',
                    message: 'Order #12345 for Wooden Chair has been placed',
                    time: '2 hours ago',
                    status: 'unread'
                },
                {
                    icon: 'fi fi-sr-star',
                    title: 'Product Review',
                    message: 'New 5-star review for Kitchen Knife Set',
                    time: '4 hours ago',
                    status: 'read'
                },
                {
                    icon: 'fi fi-sr-user-add',
                    title: 'New Follower',
                    message: 'John Doe started following your shop',
                    time: '6 hours ago',
                    status: 'unread'
                },
                {
                    icon: 'fi fi-sr-box',
                    title: 'Low Stock Alert',
                    message: 'Desk Organizer is running low on stock',
                    time: '1 day ago',
                    status: 'unread'
                },
                {
                    icon: 'fi fi-sr-chart-line-up',
                    title: 'Sales Milestone',
                    message: 'Congratulations! You\'ve reached 200 total sales',
                    time: '2 days ago',
                    status: 'read'
                }
            ],
            allProducts: [
                {
                    name: 'Wooden Chair',
                    category: 'Furniture',
                    rating: 4.5,
                    totalSold: 45,
                    reviewCount: 23,
                    followers: 156
                },
                {
                    name: 'Kitchen Knife Set',
                    category: 'Kitchenware',
                    rating: 4.8,
                    totalSold: 78,
                    reviewCount: 34,
                    followers: 89
                },
                {
                    name: 'Acoustic Guitar',
                    category: 'Musical Instrument',
                    rating: 4.6,
                    totalSold: 12,
                    reviewCount: 8,
                    followers: 234
                },
                {
                    name: 'Board Game Collection',
                    category: 'Toys & Games',
                    rating: 4.3,
                    totalSold: 67,
                    reviewCount: 19,
                    followers: 123
                },
                {
                    name: 'Desk Organizer',
                    category: 'Office Supplies',
                    rating: 4.1,
                    totalSold: 134,
                    reviewCount: 45,
                    followers: 67
                },
                {
                    name: 'Wall Art Canvas',
                    category: 'Home Decor',
                    rating: 4.7,
                    totalSold: 29,
                    reviewCount: 12,
                    followers: 189
                },
                {
                    name: 'Leather Wallet',
                    category: 'Personal accessories',
                    rating: 4.4,
                    totalSold: 89,
                    reviewCount: 31,
                    followers: 145
                },
                {
                    name: 'Garden Bench',
                    category: 'Outdoor enhancements',
                    rating: 4.2,
                    totalSold: 23,
                    reviewCount: 7,
                    followers: 98
                },{
                    name: 'Wooden Chair',
                    category: 'Furniture',
                    rating: 4.5,
                    totalSold: 45,
                    reviewCount: 23,
                    followers: 156
                },
                {
                    name: 'Kitchen Knife Set',
                    category: 'Kitchenware',
                    rating: 4.8,
                    totalSold: 78,
                    reviewCount: 34,
                    followers: 89
                },
                {
                    name: 'Acoustic Guitar',
                    category: 'Musical Instrument',
                    rating: 4.6,
                    totalSold: 12,
                    reviewCount: 8,
                    followers: 234
                },
                {
                    name: 'Board Game Collection',
                    category: 'Toys & Games',
                    rating: 4.3,
                    totalSold: 67,
                    reviewCount: 19,
                    followers: 123
                },
                {
                    name: 'Desk Organizer',
                    category: 'Office Supplies',
                    rating: 4.1,
                    totalSold: 134,
                    reviewCount: 45,
                    followers: 67
                },
                {
                    name: 'Wall Art Canvas',
                    category: 'Home Decor',
                    rating: 4.7,
                    totalSold: 29,
                    reviewCount: 12,
                    followers: 189
                },
                {
                    name: 'Leather Wallet',
                    category: 'Personal accessories',
                    rating: 4.4,
                    totalSold: 89,
                    reviewCount: 31,
                    followers: 145
                },
                {
                    name: 'Garden Bench',
                    category: 'Outdoor enhancements',
                    rating: 4.2,
                    totalSold: 23,
                    reviewCount: 7,
                    followers: 98
                }
            ],
            follower: {
                username: 'loloy',
                reviews: 20,
                profilepic: 'https://imgcdn.stablediffusionweb.com/2024/4/17/6d71579f-ecef-42de-b83e-c0cb8179130c.jpg'
            },
            genderViews: {
                male: 120,
                female: 180
            }
        }
    },
    computed: {
        // Add computed properties for better data handling
        totalViews() {
            return this.genderViews.male + this.genderViews.female;
        },
        malePercentage() {
            return this.totalViews > 0 ? ((this.genderViews.male / this.totalViews) * 100).toFixed(1) : 0;
        },
        femalePercentage() {
            return this.totalViews > 0 ? ((this.genderViews.female / this.totalViews) * 100).toFixed(1) : 0;
        },
        unreadNotificationsCount() {
            return this.recentNotifications.filter(notification => notification.status === 'unread').length;
        },
        highReviewProducts() {
            // Get products with highest ratings and review counts
            return [...this.allProducts]
                .sort((a, b) => {
                    // Sort by rating first, then by review count
                    if (b.rating !== a.rating) {
                        return b.rating - a.rating;
                    }
                    return b.reviewCount - a.reviewCount;
                })
                .slice(0, 2)
        },
        lowReviewProducts() {
            return [...this.allProducts]
                .sort((a, b) => {
                    // Sort by rating first, then by review count
                    if (a.rating !== b.rating) {
                        return a.rating - b.rating;
                    }
                    return a.reviewCount - b.reviewCount;
                })
                .slice(0, 1);
        }
    },
    mounted() {
        // Load Chart.js from CDN if not already loaded
        if (!window.Chart) {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = this.renderGenderChart;
            document.head.appendChild(script);
        } else {
            this.renderGenderChart();
        }
    },
    methods: {
        // Add error handling for images
        handleImageError(event) {
            event.target.src = '/path/to/fallback-image.jpg'; // Add a fallback image
            event.target.alt = 'Product image not available';
        },

        // Search functionality
        handleSearch() {
            // Clear results when query is empty
            if (!this.searchQuery.trim()) {
                this.searchResults = [];
                return;
            }

            // Debounce search for better performance
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.performSearch();
            }, 300);
        },

        performSearch() {
            if (!this.searchQuery.trim()) {
                this.searchResults = [];
                return;
            }

            this.isSearching = true;

            // Simulate API call delay
            setTimeout(() => {
                const query = this.searchQuery.toLowerCase().trim();

                this.searchResults = this.allProducts.filter(product =>
                    product.name.toLowerCase().includes(query) ||
                    product.category.toLowerCase().includes(query) ||
                    product.totalSold.toString().includes(query) ||
                    product.rating.toString().includes(query) ||
                    product.reviewCount.toString().includes(query) ||
                    product.followers.toString().includes(query)
                );

                this.isSearching = false;
            }, 500);
        },

        clearSearch() {
            this.searchQuery = '';
            this.searchResults = [];
        },

        markAsRead(index) {
            if (this.recentNotifications[index].status === 'unread') {
                this.recentNotifications[index].status = 'read';
            }
        },

        getUnreadCount() {
            return this.recentNotifications.filter(notification => notification.status === 'unread').length;
        },

        // Improve the chart rendering method
        renderGenderChart() {
            const ctx = document.getElementById('genderViewsChart');
            if (!ctx) return;

            const context = ctx.getContext('2d');
            if (!context) return;

            new window.Chart(context, {
                type: 'bar',
                data: {
                    labels: [
                        `Male (${this.malePercentage}%)`,
                        `Female (${this.femalePercentage}%)`
                    ],
                    datasets: [{
                        label: 'Views by Gender',
                        data: [this.genderViews.male, this.genderViews.female],
                        backgroundColor: ['#4e79a7', '#f19393'],
                        barThickness: 6,
                        categoryPercentage: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            titleFont: { size: 12 },
                            bodyFont: { size: 12 }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                font: { size: 10 }
                            }
                        },
                        y: {
                            position: 'right',
                            ticks: {
                                font: { size: 10 }
                            }
                        }
                    }
                }
            });
        }
    }

}
</script>

<style scoped>
.dashboard-container {
    padding: 2em;
    display: flex;
    flex-direction: column;
    gap: 2em;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dashboard-header-left {
    display: flex;
    flex-direction: column;
    width: 50%;
}

.header-right {
    display: flex;
    align-items: center;
}
.header-right i {
    font-size: 24px;
    color: #333;
    cursor: pointer;
    filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.582));
}
.header-right i:hover {
    color: #ff7300;
}

.search-product {
    display: flex;
    align-items: center;
    gap: 2em;
    width: 80%;
    font-size: .9em;
    color: #333;
}

.search-input-wrapper {
  position: relative;
  display: inline-block;
  width: 100%;
}

.search-input-wrapper input[type="search"] {
  padding: 0.5em 2em 0.5em 1.3em;
  border-radius: 15px;
  border: none;
  width: 100%;
  box-sizing: border-box;
  background-color: #f5f5f5;
  outline: none;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.582);
}

.search-icon {
  position: absolute;
  right: 0.7em;
  top: 50%;
  transform: translateY(-50%);
  color: #aaa;
  pointer-events: none;
  font-size: 1em;
}

.search-product select {
    padding: 0.5em 1em;
    border-radius: 15px;
    border: none;
    background-color: #DDD0C8;
    outline: 1px solid #DDD0C8;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.582);
    transition: all 0.3s ease;
}
.search-product select:hover {
    background-color: #ddd0c8bd;
}
.search-product select:focus {
    background-color: #ddd0c8bd;
}
.search-product select:active {
    background-color: #ddd0c8bd;
}

/* contents */
.dashboard-content {
    display: grid;
    grid-template-areas:
    'card-container'
    'card-container-2';
    gap: 3.5em;
    height: 100%;
    padding: 0;
}
.cards {
    border-radius: 1em;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.582);
    height: 100%;   /* fill vertical */
    width: 100%;
}
.card-container {
    display: grid;
    width: 100%;
    height: 100%;
    gap: 2em;
    grid-template-columns: 1fr 1fr 1fr;
    grid-template-rows: 16em 13em;
    grid-template-areas:
        'card-group-1 card-group-2  chart'
        'graph graph chart';
}

.card-group-1 {
    grid-area: card-group-1;
    background-color: #f1c5ab;
    padding: 1em;
}

.card-header {
    display: grid;
    grid-template-areas:
        'title icon'
        'content .';
    align-content: center;
    height: 3.8em;
    padding-top: 2em;
    margin: 0;
    border-radius: 1em;
    background-color: #d8bdad;
}
.card-header h6 {
    grid-area: title;
    text-transform: uppercase;
    font-weight: 700;
}

.card-header i {
    grid-area: icon;
    justify-self: flex-end;
}

.card-header span {
    font-weight: 600;
}

.card-header p {
    grid-area: content;
    font-size: .8em;
}

.card-content {
    display: flex;
    flex-direction: column;
    height: 10em;
    align-items: center;
    width: 100%;
    border-radius: 1em;
    padding: .5em;
    gap: .5em;
}

.gender-chart {
    height: 80px !important;   /* ✅ small height */
    width: 100% !important;    /* fills container */
    font-size: .7em;
}

.info-container img {
    flex: 1;
    width: 2em;
    border-radius: 50%;
}

.details {
    display: flex;
    justify-content: space-between;
    font-size: .8em;
}

.card-group-2 {
    grid-area: card-group-2;
    background-color: #f1c5ab;
    padding: 1em;
}

.graph {
    grid-area: graph;
    background-color: #8f7c6d;
}

.chart {
    grid-area: chart;
    background: rgb(219, 205, 184);
    padding: 1.5em;
    display: flex;
    flex-direction: column;
    gap: 1em;
}

.category-content {
    flex: 1;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 0.8em;
}

.category-item {
    display: flex;
    align-items: center;
    gap: 0.8em;
    padding: 0.8em;
}


.card-container-2 {
    display: grid;
    height: 84vh;
    gap: 2em;
    grid-template-columns: 1fr 1fr 1fr;
    grid-template-rows: 10em 2fr;
    grid-template-areas:
        'card card chart'
        'box  box  chart';
}

.follower-card {
    grid-area: card;
    background-color: #b3997b;
    display: flex;
    gap: 1em;
    padding: 1em;
}

.notif-card {
    height: 41em;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 1em;
    border-bottom: 2px solid #f0f0f0;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 0.8em;
    user-select: none;
}

.unread-badge {
    background: #d86900;
    color: white;
    font-size: 0.8em;
    font-weight: 600;
    padding: 0.2em 0.6em;
    border-radius: 50%;
    min-width: 1.5em;
    text-align: center;
    animation: pulse 2s infinite;
    user-select: none;
}

.chart-header h4 {
    font-size: 1.1em;
    font-weight: 700;
    color: #333;
    margin: 0;
}

.chart-header .view-all {
    font-size: .9em;
    color: #d86900;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.chart-header .view-all:hover {
    color: #c0673e;
    text-decoration: underline;
}

.notification-content {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
    gap: 1em;
    padding-right: 1em;
    font-size: smaller;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    gap: 1em;
    padding: 1em;
    background: #dcdddf;
    border-radius: 0.8em;
    border-left: 4px solid #d86900;
    transition: all 0.2s ease;
    user-select: none;
}

.notification-item:hover {
    background: #ccced1;
    transform: translateX(5px);
}

.notification-item:active {
    transform: scale(0.98);
    transition: transform 0.1s ease;
}

.notification-item.unread {
    background: #fff3cd;
    border-left-color: #ffc107;
}

.notification-icon {
    width: 2.5em;
    height: 2.5em;
    background: #d86900;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notification-icon i {
    color: white;
    font-size: 1.2em;
}

.notification-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.3em;
}

.notification-title {
    font-size: 0.95em;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.notification-message {
    font-size: 0.85em;
    color: #666;
    margin: 0;
    line-height: 1.4;
}

.notification-time {
    font-size: 0.8em;
    color: #999;
    margin: 0;
}

.notification-status {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1.5em;
    height: 1.5em;
    flex-shrink: 0;
}

.notification-status.unread i {
    color: #d86900;
    font-size: 0.8em;
}

.notification-status.read i {
    color: #ccc;
    font-size: 0.8em;
    transition: color 0.3s ease;
}

.notification-status.unread i {
    color: #d86900;
    font-size: 0.8em;
    transition: color 0.3s ease;
}

.no-notifications {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1em;
    padding: 3em 1em;
    color: #999;
    text-align: center;
}

.no-notifications i {
    font-size: 3em;
    opacity: 0.5;
}

.no-notifications p {
    font-size: 1em;
    margin: 0;
    color: #666;
}

/* Custom scrollbar for notifications */
.notification-content::-webkit-scrollbar {
    width: 6px;
}

.notification-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.notification-content::-webkit-scrollbar-thumb {
    background: #d86900;
    border-radius: 3px;
}

.notification-content::-webkit-scrollbar-thumb:hover {
    background: #c0673e;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.product-group {
    display: flex;
    flex-direction: column;
    gap: 0.8em;
    padding: 1em;
    border-radius: 0.8em;
}

.rating-badge {
    background: rgba(255, 115, 0, 0.9);
    color: white;
    padding: 0.3em 0.6em;
    border-radius: 1em;
    font-size: 0.8em;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.3em;
}

.product-info {
    text-align: center;
}

.product-name {
    font-size: .8em;
    font-weight: 600;
    color: #333;
}

.rating-display {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3em;
}

.stars {
    display: flex;
    gap: 0.2em;
}

.stars .fi {
    font-size: 1em;
}

.stars .filled {
    color: #833d03;
}

.stars .empty {
    color: #ddd;
}

.rating-text {
    font-size: 0.9em;
    font-weight: 600;
    color: #666;
}

.no-product {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5em;
    padding: 2em;
    color: #999;
}

/* product-container layout */
.product-container {
    display: flex;
    flex-direction: column;
    gap: .5em;
    padding: .5em;
    border-radius: 0.8em;
    height: 100%;
    font-size: .85em;
}

.review-section {
    display: flex;
    flex-direction: column;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-header h6 {
    font-size: 0.8em;
    font-weight: 700;
    color: #333;
    text-transform: capitalize;
    margin: 0;
}

.view-all {
    font-size: 0.7em;
    color: #ff7300;
    text-decoration: none;
    font-weight: 500;
}

.view-all:hover {
    text-decoration: underline;
}

.product-list {
    display: flex;
    flex-direction: column;
}

.product-item {
    display: flex;
    align-items: center;
    gap: 0.8em;
    padding: 0.2em;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 0.5em;
}

.product-icon {
    width: 2.5em;
    height: 2.5em;
    background: #ff7300;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.product-icon i {
    color: white;
    font-size: 1.2em;
}

.product-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.2em;
}

.product-details .product-name {
    font-size: 0.8em;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.product-details .review-count {
    font-size: 0.7em;
    color: #666;
    margin: 0;
}

.rating-info {
    display: flex;
    align-items: center;
    gap: 0.3em;
    flex-shrink: 0;
}

.rating-score {
    font-size: 0.9em;
    font-weight: 600;
    color: #333;
}

.rating-info .fi.filled {
    color: #ce6e00;
    font-size: 1em;
}

/* Follower Statistics styles */
.follower-stats {
    flex: 1;
    padding: 1em 0 1em 1em;
    display: flex;
    flex-direction: column;
    background-color: #d8bdad;
    border-radius: 1em;
}

.follower-stats h4 {
    font-size: 1.1em;
    font-weight: 700;
    color: #333;
    margin: 0 0 1em 0;
}

.stats-content {
    flex: 2;
    display: flex;
    flex-direction: column;
    padding: 1em;
    gap: 1.5em;
    background-color: #d8bdad;
    border-radius: 1em;
}

.total-followers {
    font-size: 0.9em;
    color: #333;
    margin: 0;
}

.total-followers span {
    font-weight: 600;
}

.stat-item {
    display: flex;
    flex-direction: column;
}

.stat-label {
    font-size: 0.9em;
    color: #333;
    text-transform: lowercase;
}

/* Box layout styles */
.box {
    grid-area: box;
    display: flex;
    flex-direction: column;
    padding: 0;
}

.search-section {
    border: none;
}

.search-bar {
    min-height: 3em;
    display: flex;
    align-items: center;
    gap: 1em;
}

.search-input-wrapper {
    position: relative;
    flex: 1;
    display: flex;
    align-items: center;
}

.search-input {
    width: 100%;
    padding: 0.8em 2.5em 0.8em 1em;
    border: 2px solid #e0e0e0;
    border-radius: 0.8em;
    font-size: 0.9em;
    outline: none;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.search-input:focus {
    border-color: #c0673e;
    background-color: white;
    box-shadow: 0 0 0 3px rgba(255, 115, 0, 0.1);
}

.search-icon {
    position: absolute;
    right: 1em;
    color: #999;
    font-size: 1em;
    pointer-events: none;
}

.search-button {
    padding: 0.8em 1.5em;
    background: #d86900;
    color: white;
    border: none;
    border-radius: 0.8em;
    font-size: 0.9em;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5em;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(255, 115, 0, 0.3);
}

.search-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 115, 0, 0.4);
}

.search-button:active {
    transform: translateY(0);
}

.search-button i {
    font-size: 0.9em;
}

.table-section {
    flex: 1;
    display: flex;
    justify-content: center;
    overflow: hidden;
}

/* Table styles */
.table-wrapper {
    width: 100%;
    border-radius: 0.8em;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Responsive table */
@media (max-width: 768px) {
    .products-table {
        font-size: 0.75em;
        max-height: 400px;
    }

    .products-table tbody {
        max-height: 350px;
    }

    .products-table th,
    .products-table td {
        padding: 0.6em 0.4em;
    }
}

.products-table {
    text-align: center;
    font-size: .95em;
    width: 46em;
    border-radius: 1em;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.733);
    background: white;
}

.products-table thead {
    position: sticky;
    top: 0;
    z-index: 1;
    display: table;
    width: 100%;
    table-layout: fixed;
    background: linear-gradient(135deg, #d86900, #c0673e);
    color: white;
    border-top-right-radius: 1em;
    border-top-left-radius: 1em;
}

.products-table th {
    padding: 1em 0.8em;
    text-align: left;
    font-size: 0.8em;
    font-weight: 600;
    text-transform: uppercase;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
}

.products-table tbody {
    display: block;
    max-height: 22.4em;
    overflow-y: auto;
    overflow-x: hidden;
}

.products-table tbody tr {
    display: table;
    width: 100%;
    table-layout: fixed;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f0f0f0;
}

.products-table tbody tr:hover {
    background-color: rgba(255, 115, 0, 0.05);
    transform: scale(1.01);
}

.products-table tbody tr:last-child {
    border-bottom: none;
}

/* Custom scrollbar styling */
.products-table tbody::-webkit-scrollbar {
    width: 8px;
}

.products-table tbody::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 0 1em 1em 0;
}

.products-table tbody::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.products-table tbody::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.products-table td {
    padding: 1em 0.8em;
    font-size: 0.85em;
    color: #333;
    vertical-align: middle;
}

/* Table cell specific styles */
.product-name-cell {
    font-weight: 600;
    color: #2c3e50;
}

.category-cell {
    color: #666;
}

.sales-cell {
    font-weight: 600;
    color: #27ae60;
    text-align: center;
}

.rating-cell {
    text-align: center;
}

.rating-display {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.3em;
}

.rating-score {
    font-weight: 600;
    color: #f39c12;
}

.rating-display .fi.filled {
    color: #f39c12;
    font-size: 0.9em;
}

.reviews-cell {
    text-align: center;
    color: #666;
}

.followers-cell {
    text-align: center;
    font-weight: 600;
    color: #3498db;
}

/* Search results styles */
.search-results {
    width: 100%;
    text-align: left;
}

.search-results h4 {
    font-size: 1.1em;
    font-weight: 700;
    color: #333;
    margin: 0 0 1em 0;
    padding-bottom: 0.5em;
    border-bottom: 2px solid #d86900;
}

.no-results, .searching, .default-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1em;
    padding: 2em;
    text-align: center;
}

.no-results i, .searching i {
    font-size: 2.5em;
    color: #999;
    opacity: 0.7;
}

.no-results p, .searching p {
    font-size: 0.9em;
    color: #666;
    margin: 0;
}

.searching i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

