<template>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-thin-rounded/css/uicons-thin-rounded.css'>

    <div class="loading-container" v-if="is_loading">
        <img src="../../../images/kOnzy.gif" style="width: 100px; height: 100px;">
    </div>

    <div class="dashboard-container" v-else>
        <div class="dashboard-header">
            <div class="dashboard-header-left">
                <h3>Welcome {{ user.name }},</h3>
                <p>Your dashboard overview</p>
            </div>

            <div class="dashboard-header-right">
                <router-link :to="{ name: 'Map' }">
                    <i class="fi fi-tr-land-location"></i>
                </router-link>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="card-container">
                <div class="left-container">
                    <div class="top-section">
                        <div class="card-group-1 cards">
                            <div class="card-header">
                                <h6>Total Shop Reviews</h6>
                                <i class="fi fi-rr-thumbs-up-trust"></i>
                                <p><span>{{ shop_reviews.total }}</span> Reviews</p>
                            </div>
                            <div class="card-content">
                                <canvas id="genderViewsChart" class="gender-chart"></canvas>
                            </div>
                        </div>
                        <div class="card-group-2 cards">
                            <div class="card-header">
                                <h6>Total Products</h6>
                                <i class="fi fi-rr-box-open-full"></i>
                                <p><span>{{ total_products.total }}</span> Products</p>
                            </div>
                            <div class="product-container">
                                <div class="review-section">
                                    <div class="section-header">
                                        <h6>Top product with highest reviews</h6>
                                        <a href="#" class="view-all">view all</a>
                                    </div>
                                    <div class="product-list">
                                        <div class="product-item" v-for="(product, index) in total_products.high_reviews" :key="index">
                                            <div class="product-icon" v-if="product">
                                            <img :src="'/'+product.photos[0].filename" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <div class="product-details" v-if="product">
                                                <p class="product-name">{{ product.name }}</p>
                                                <p class="review-count">{{ product.reviews.length }} reviews</p>
                                            </div>
                                            <div class="rating-info" v-if="product">
                                                <span class="rating-score">{{ product.overall_rate }}</span>
                                                <i class="fi fi-sr-star filled"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="review-section">
                                    <div class="section-header">
                                        <h6>Top product with lowest reviews</h6>
                                        <a href="#" class="view-all">view all</a>
                                    </div>
                                    <div class="product-list" v-if="total_products.low_reviews.length > 0">
                                        <div class="product-item" v-for="(product, index) in total_products.low_reviews" :key="index">
                                            <div class="product-icon">
                                                <img :src="'/'+product.photos[0].filename" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <div class="product-details">
                                                <p class="product-name">{{ product.name }}</p>
                                                <p class="review-count">{{ product.reviews.length }} reviews</p>
                                            </div>
                                            <div class="rating-info">
                                                <span class="rating-score">{{ product.overall_rate }}</span>
                                                <i class="fi fi-sr-star filled"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="graph cards" style="overflow: hidden;">
                        <LineGraph/>
                    </div>
                </div>
                <div class="chart cards">
                    <div class="chart-header">
                        <div class="header-left">
                            <h4>All Categories Reviews</h4>
                        </div>
                    </div>
                    <div class="category-content">
                        <RadialGraph :products_data_radial="products_data_radial" v-if="show"/>
                    </div>
                </div>
            </div>
            <div class="card-container-2">
                <div class="follower-card cards" style="">
                    <div class="follower-stats">
                        <h4>Follower Statistics</h4>
                        <p class="total-followers"><span>{{ follower_statistics.total }}</span> total followers</p>
                    </div>
                    <div class="stats-content">
                        <div class="stat-item">
                            <span class="stat-label" style="font-weight: bolder;">Gender</span>

                            <div style="display: flex; flex-direction: column; margin-top: 10px; gap: .5em; font-size: smaller;">
                                <div style="display: flex; flex-direction: row; align-items: center; gap: 20px;">
                                    <div style="width: 300px; background-color: #CACACA; height: 20px; position: relative;">
                                        <div class="follower-gender-male" :style="{width: `${computemalepercent}%`}"></div>
                                    </div>
                                    <label style="margin: 0;">Male - ({{ computemalepercent }}%)</label>
                                </div>
                                <div style="display: flex; flex-direction: row; align-items: center; gap: 20px;">
                                    <div style="width: 300px; background-color: #CACACA; height: 20px; position: relative;">
                                        <div class="follower-gender-female" :style="{width: `${computefemalepercent}%`}"></div>
                                    </div>
                                    <label style="margin: 0;">Female - ({{ computefemalepercent }}%)</label>
                                </div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label" style="font-weight: bolder;">Age</span>
                            <div style="display: grid; grid-template-columns: 100px 60px 10px; font-size: .7em; padding-left: .5em;">
                                <label>Below 18</label><label>-</label><label>{{ follower_statistics.below_18 }}</label>
                                <label>18 - 25</label><label>-</label><label>{{ follower_statistics.middle }}</label>
                                <label>Above 25</label><label>-</label><label>{{ follower_statistics.above_25 }}</label>
                            </div>
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
                                    @keyup.enter="performSearch"
                                    @input="handleSearchInput"
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
                                            <th>Description</th>
                                            <th>Overall Rate</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(result, index) in searchResults" :key="index" class="table-row">
                                            <td class="product-name-cell">{{ result.name }}</td>
                                            <td class="category-cell">{{ result.category }}</td>
                                            <td class="sales-cell">{{ result.description }}</td>
                                            <td class="rating-cell">
                                                <div class="rating-display">
                                                    <span class="rating-score">{{ result.overall_rate }}</span>
                                                    <i class="fi fi-sr-star filled"></i>
                                                </div>
                                            </td>
                                            <td class="reviews-cell">{{ result.price || 0 }}</td>
                                            <td class="followers-cell">{{ result.status || 0 }}</td>
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
                <div class="cards notif-card">
                    <div class="chart-header">
                        <div class="header-left">
                            <h4>Recent Notification</h4>
                            <span v-if="unread > 0" class="unread-badge">{{ unread }}</span>
                        </div>
                        <a href="#" class="view-all" @click="$router.push({name: 'Notification'})">view all</a>
                    </div>
                    <div class="notification-content">
                        <div
                            class="notification-item"
                            :class="notification.status"
                            v-for="(notification, index) in notifications"
                            :key="index"
                            @click="markAsRead(index)"
                            :style="{ cursor: notification.status === 'unread' ? 'pointer' : 'default', backgroundColor: returnColor(notification) }"
                        >
                            <div class="notification-details">
                                <p class="notification-title">{{ notification.type }}</p>
                                <p class="notification-message">{{ notification.text }}</p>
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
import RadialGraph from '../graph/RadialGraph.vue';
import LineGraph from '../graph/LineGraph.vue';
import { useDataStore } from '../../stores/dataStore';
import axios from 'axios';
import router from '../../router';

export default {
    components: {
        RadialGraph,
        LineGraph
    },
    data() {
        return {
            is_loading: true,
            orig_products: [],
            notifications: [],
            unread: 0,
            show: false,
            products_data_radial: {
                labels: [],
                data: {},
            },
            shop_reviews: {
                total: 0,
                unique_total: 0,
                male: 0,
                female: 0,
            },
            total_products: {
                total: 0,
                high_reviews: [],
                low_reviews: [],
            },
            follower_statistics: {
                total: 0,
                male: 0,
                female: 0,
                below_18: 0,
                middle: 0,
                above_25: 0,
            },
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

            user: [],
            recentNotifications: [],
            allProducts: [],
            followers: [
                {
                    username: 'loloy',
                    reviews: 20,
                    profilepic: 'https://imgcdn.stablediffusionweb.com/2024/4/17/6d71579f-ecef-42de-b83e-c0cb8179130c.jpg'
                },
                {
                    username: 'yolol',
                    reviews: 20,
                    profilepic: 'https://imgcdn.stablediffusionweb.com/2024/4/17/6d71579f-ecef-42de-b83e-c0cb8179130c.jpg'
                }
            ],
            genderViews: {
                male: 120,
                female: 180
            }
        }
    },
    computed: {
        computefemalepercent(){
            if(this.follower_statistics.female < 1){
                return 0;
            }
            const result = (this.follower_statistics.female.length / this.follower_statistics.total) * 100;
            console.log(this.follower_statistics);
            return result.toFixed(2);
        },
        computemalepercent(){
            if(this.follower_statistics.male < 1){
                return 0;
            }
            const result = (this.follower_statistics.male.length / this.follower_statistics.total) * 100;
            console.log(this.follower_statistics);
            return result.toFixed(2);
        },

    },
    async mounted() {

        this.is_loading = true;

        await this.returnProducts();
        await this.returnUserInfo();
        await this.returnNotifications();

        const store = useDataStore();
        const shop = store.selected_shop;
        this.user = store.currentUser_info;
        console.log('SHOP: ',shop);

        //compute total shop reviews
        this.totalShopReviews();
        //compute total of products
        this.totalOfProducts(shop);
        //compute follower statistics;
        this.followerStatistics();
        //return products category
        await this.returnCategoriesProducts();

        

        // Load Chart.js from CDN if not already loaded
        if (!window.Chart) {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = this.renderGenderChart;
            document.head.appendChild(script);
        } else {
            this.renderGenderChart();
        }

        this.renderGenderChart();

        this.is_loading = false;

    },

    methods: {

        async returnUserInfo(){
            const res = await axios.post("/return/user-seller/info");

            const store = useDataStore();

            console.log('user: ',res.data.message);
            console.log('shop: ', res.data.shop);
            this.user = res.data.message;
            store.setUserInfo(res.data.message);
            store.setSelectedShop(res.data.shop);
            console.log('id: ', store.currentUser_info.id);
        },

        returnColor(notif){

            if(!notif.seen){
                return '#F0AE73';
            }
        },

        async returnProducts(){

            try{
                const store = useDataStore();
                const data = new FormData();
                data.append('id', store.currentUser_info.id);

                const res = await axios.post('/seller/return/products', data);
                console.log('PRODUCTSSSS: ',res.data.message);
                this.searchResults = res.data.message;
                this.orig_products = res.data.message;
            }
            catch(error){
                console.log(error);
            }
        },

        async returnNotifications(){
            const store = useDataStore();
            const res = await axios.get('/return/notifications', {
                params: {
                    id: store.currentUser_info.id,
                    type: 'seller',
                }
            });
            console.log(res.data.message);
            let count = 0;
            for(let notif of res.data.message){
                if(count === 10){
                    break;
                }

                if(!notif.seen){
                    this.unread++;
                }
                this.notifications.push(notif);
                count++;
            }
            console.log('notif: ', this.notifications);
        },

        async returnCategoriesProducts(){
            this.show = false;
            const store = useDataStore();
            const shop_id = store.selected_shop.id;

            const res = await axios.get('/seller/dashboard/return-products', {
                params: {
                    shop_id: shop_id
                }
            });

            console.log(res.data.message);

            for(let product of res.data.message){

                if(this.products_data_radial.labels.length < 1){
                    this.products_data_radial.data[product.category] = 0;
                    this.products_data_radial.labels.push(product.category);
                }
                else{

                    if(!this.products_data_radial.labels.includes(product.category)){
                        this.products_data_radial.labels.push(product.category);
                        this.products_data_radial.data[product.category] = 0;
                    }
                }
            }

            for(let product of res.data.message){
                this.products_data_radial.data[product.category] = this.products_data_radial.data[product.category] + product.reviews.length;
            }

            console.log('category: ', this.products_data_radial);


            this.show = true;
        },

        followerStatistics(){

            const store = useDataStore();

            console.log('!!!!!!!!!!!!!!!!!!!!!!!', store.selected_shop);

            const followers = store.selected_shop.user.followers;
            console.log('followers: ', followers);
            if(followers.length < 1){
                console.log('way sulod');
                return;
            }
            console.log('followers: ', followers);

            this.follower_statistics.total = followers.length;
            this.follower_statistics.male = followers.filter(f => f.followed_by.gender === 'male');
            this.follower_statistics.female = followers.filter(f => f.followed_by.gender === 'female');

            for(let user of followers){
                console.log('user: ', user);
                if(user.followed_by.age < 18){
                    this.follower_statistics.below_18 = this.follower_statistics.below_18 + 1;
                }
                else if(user.followed_by.age >= 18 && user.follower_statistics <= 25){
                    this.follower_statistics.middle = this.follower_statistics.middle + 1;
                }
                else{
                    this.follower_statistics.above_25 = this.follower_statistics.above_25 + 1;
                }
            }


            console.log('follower statistics: ', this.follower_statistics);
        },

        totalOfProducts(shop){

            //PRODUCTS ARRAY IS SORTED ASCENDING BASED ON THE OVERALL_RATE

            if(shop.products.length < 1){
                console.log('return')
                return;
            }

            console.log('SHOP DETAILS: ',shop);

            this.total_products.total = shop.products.length;

            console.log('tota: ', this.total_products);

            let products = shop.products;

            let is_continue;

            do{
                is_continue = false;
                let dummy1, dummy2;
                for(let i = 1; i < products.length; i++){

                    dummy1= products[i];
                    dummy2 = products[i-1];

                    if(dummy2.overall_rate < dummy1.overall_rate){
                        products[i-1] = dummy1;
                        products[i] = dummy2;
                        is_continue = true;
                        break;
                    }
                }
            }
            while(is_continue);

            console.log('products: ', products);

            for(let i = 0; i < 3; i++){
                this.total_products.high_reviews.push(products[i]);
            }

            console.log(this.total_products);

            this.total_products.low_reviews.unshift(products[products.length - 1]);

        },

        totalShopReviews() {

            const store = useDataStore();
            const shop = store.selected_shop;

            const shopReviews = shop.reviews.filter(r => r.review_type === 'shop');

            const arr_review = [];
            shopReviews.forEach(r => {
                const exists = arr_review.some(a => a.from_id === r.from_id);
                if (!exists) arr_review.push(r);
            });

            this.shop_reviews.total = shopReviews.length;
            this.shop_reviews.unique_total = arr_review.length;
            this.shop_reviews.male = arr_review.filter(r => r.user.gender === 'male').length;
            this.shop_reviews.female = arr_review.filter(r => r.user.gender === 'female').length;

            console.log('shop_reviews: ', this.shop_reviews);
        },

        clearSearch() {
            this.searchQuery = '';
            this.searchResults = [];
        },

        performSearch() {
            if (this.searchQuery.trim() === '') {
                // If search query is empty, show all products
                this.searchResults = this.orig_products;
                return;
            }

            // Filter products based on search query
            this.searchResults = this.orig_products.filter(product =>
                product.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                product.category.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                product.description.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },

        handleSearchInput() {
            // Clear search results when input is cleared
            if (this.searchQuery.trim() === '') {
                this.searchResults = this.orig_products;
            }
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
            console.log('NEW AGI');
            const ctx = document.getElementById('genderViewsChart');
            if (!ctx) return;
            
            const context = ctx.getContext('2d');
            if (!context) return;

            console.log(this.shop_reviews.male)

            const malePercentage = this.shop_reviews.male > 0 ? (this.shop_reviews.male / this.shop_reviews.unique_total) * 100 : 0;
            const femalePercentage = this.shop_reviews.female > 0 ? (this.shop_reviews.female / this.shop_reviews.unique_total) * 100 : 0;

            console.log(`${malePercentage} - ${femalePercentage}`);

            new window.Chart(context, {
                type: 'bar',
                data: {
                    labels: [
                        `Male (${malePercentage.toFixed(2)}%)`,
                        `Female (${femalePercentage.toFixed(2)}%)`
                    ],
                    datasets: [{
                        label: 'Views by Gender',
                        data: [malePercentage, femalePercentage],
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
.fill_unread{
    background-color: red;
}
.follower-gender-male{
    height: 100%;
    background-color: blue;
}
.follower-gender-female{
    height: 100%;
    background-color: #DE18F2;
}
.graph.cards {
    width: 100%;
    height: 100%;
    padding: 1em;
}

.loading-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 80vh;

}

.dashboard-container {
    padding: 1em;
    display: flex;
    flex-direction: column;
    gap: 1em;
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

.dashboard-header-right {
    display: flex;
    align-items: center;
    padding-right: 2em;
}
.dashboard-header-right i {
    font-size: 1.8em;
    color: #333;
    cursor: pointer;
}
.dashboard-header-right i:hover {
    color: #ff7300;
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
    height: 100%;
    width: 100%;
}
.card-container {
    display: flex;
    width: 100%;
    height: 100%;
    gap: 2em;
    flex-wrap: wrap;
}

.left-container {
    flex: 2;
    display: flex;
    flex-direction: column;
    gap: 2em;
    height: 42em;
}

.top-section {
    display: flex;
    gap: 1em;
    width: 100%;
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
    height: 130px !important;   /* ✅ small height */
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
    flex: 1;
    background: rgb(219, 205, 184);
    padding: 1.5em;
    display: flex;
    flex-direction: column;
    gap: 1em;
    height: 42em;
    width: 3em;
}

.category-content {
    flex: 1;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 0.5em;
    font-size: .8em;
}

.category-item {
    display: flex;
    align-items: center;
    gap: 0.8em;
    padding: 0.8em;
}


.card-container-2 {
    display: grid;
    width: 100%;
    gap: 2em;
    height: 100%;
    grid-template-columns: 2fr 1fr;
    grid-template-rows: 15em 25em;
    grid-template-areas:
        'card chart'
        'box chart';
}

.follower-card {
    grid-area: card;
    background-color: #b3997b;
    display: flex;
    gap: 1em;
    padding: 1em;
}

.notif-card {
    grid-area: 'chart';
    background: rgb(219, 205, 184);
    padding: 1.5em;
    display: flex;
    flex-direction: column;
    gap: 1em;
    height: 42em;
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
    padding-right: .5em;
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
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.product-icon img{
    position: relative;
    border-radius: 50%;
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
    padding: 1em 1em 1em 1em;
    display: flex;
    flex-direction: column;
    background-color: #d8bdad;
    border-radius: 1em;
}

.follower-stats h4 {
    font-size: .9em;
    font-weight: 700;
    color: #333;
    margin: 0 0 1em 0;
}

.stats-content {
    display: flex;
    flex-direction: column;
    padding: 1em;
    gap:1em;
    background-color: #d8bdad;
    border-radius: 1em;
}

.total-followers {
    font-size: 0.7em;
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
}

/* Box layout styles */
.box {
    grid-area: box;
    display: flex;
    flex-direction: column;
    padding: 1em .2em .2em .2em;
    gap: 1.5em;
}

.search-section {
    border: none;
}

.search-bar {
    max-height: 1em;
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
    padding: 0.5em 2.5em 0.5em 1em;
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
    padding: 0.7em 1.5em;
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
    user-select: none;
}

/* Table styles */
.table-wrapper {
    width: 100%;
    border-radius: 0.8em;
    padding: .1em;
}

/* Responsive table */
@media (max-width: 768px) {
    .products-table {
        font-size: 0.75em;
        max-height: 400px;
    }

    .products-table th,
    .products-table td {
        padding: 0.6em 0.4em;
    }
}

.products-table {
    text-align: center;
    font-size: .95em;
    width: 100%;
    border-radius: 1em;
    box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.733);
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
    height: 16em;
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
    background-color: rgba(255, 115, 0, 0.288);
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

/* ===== RESPONSIVE DESIGN ===== */

/* Ensure proper flex behavior on all screen sizes */
.card-container {
    min-height: 0;
}

.left-container {
    min-height: 0;
}

/* Mobile First - Small devices */
@media (max-width: 480px) {
    .dashboard-container {
        padding: 0.5rem;
    }

    .card-container {
        flex-direction: column;
        gap: 1rem;
    }

    .left-container {
        grid-template-columns: 1fr;
        grid-template-rows: auto auto auto;
        grid-template-areas:
            'card-group-1'
            'card-group-2'
            'graph';
        gap: 1rem;
    }

    .graph.cards {
        min-height: 300px;
        max-height: 400px;
    }

    .chart.cards {
        min-height: 300px;
        max-height: 400px;
    }

    .dashboard-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
        padding: 1rem 0.5rem;
    }

    .dashboard-header-left h3 {
        font-size: 1.2rem;
    }

    .dashboard-header-left p {
        font-size: 0.9rem;
    }

    .card-container-2 {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .cards {
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .card-header h6 {
        font-size: 0.9rem;
    }

    .card-header p {
        font-size: 0.8rem;
    }

    .product-container {
        flex-direction: column;
        gap: 1rem;
    }

    .product-item {
        padding: 0.5rem;
        gap: 0.5rem;
    }

    .product-icon {
        width: 40px;
        height: 40px;
    }

    .product-name {
        font-size: 0.8rem;
    }

    .review-count {
        font-size: 0.7rem;
    }

    .rating-score {
        font-size: 0.8rem;
    }

    .follower-stats {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .follower-count {
        font-size: 1.5rem;
    }

    .follower-label {
        font-size: 0.8rem;
    }

    .search-container {
        flex-direction: column;
        gap: 1rem;
    }

    .search-input {
        width: 100%;
        font-size: 0.9rem;
    }

    .search-btn {
        width: 100%;
        padding: 0.75rem;
    }

    .table-container {
        overflow-x: auto;
    }

    .dashboard-table {
        font-size: 0.8rem;
    }

    .dashboard-table th,
    .dashboard-table td {
        padding: 0.5rem 0.25rem;
    }
}

/* Mobile - Medium devices */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }

    .card-container {
        flex-direction: column;
        gap: 1.5rem;
    }

    .left-container {
        grid-template-columns: 1fr;
        grid-template-rows: auto auto auto;
        grid-template-areas:
            'card-group-1'
            'card-group-2'
            'graph';
        gap: 1.5rem;
    }

    .dashboard-header {
        padding: 1.5rem 1rem;
    }

    .card-container-2 {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .cards {
        padding: 1.5rem;
    }

    .product-container {
        flex-direction: column;
        gap: 1.5rem;
    }

    .follower-stats {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .search-container {
        flex-direction: column;
        gap: 1rem;
    }

    .search-input {
        width: 100%;
    }

    .search-btn {
        width: 100%;
    }

    .table-container {
        overflow-x: auto;
        margin: 0 -1rem;
        padding: 0 1rem;
    }

    .dashboard-table {
        min-width: 600px;
    }
}

/* Tablet devices */
@media (min-width: 769px) and (max-width: 991px) {
    .dashboard-container {
        padding: 1.5rem;
    }

    .card-container {
        flex-direction: column;
        gap: 1.5rem;
    }

    .left-container {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto auto;
        grid-template-areas:
            'card-group-1 card-group-2'
            'graph graph';
        gap: 1.5rem;
    }

    .card-container-2 {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .graph.cards {
        grid-column: span 2;
    }

    .chart.cards {
        grid-column: span 2;
    }

    .follower-card {
        grid-column: span 2;
    }

    .search-container {
        gap: 1rem;
    }

    .search-input {
        flex: 1;
    }

    .search-btn {
        min-width: 120px;
    }
}

/* Laptop devices - Specific breakpoint for laptop screens */
@media (min-width: 992px) and (max-width: 1366px) {
    .dashboard-container {
        padding: 1.5rem;
    }

    .left-container {
        height: 38em;
    }

    .card-container {
        gap: 1.5rem;
    }

    .card-group-1,
    .card-group-2 {
        padding: 0.8em;
    }

    .chart {
        height: 38em;
        padding: 1.2em;
    }

    .card-container-2 {
        gap: 1.5rem;
    }

    .follower-card {
        padding: 0.8em;
    }

    .notif-card {
        height: 41em;
        padding: 1.2em;
    }

    /* Adjust gender bars for laptop screens */
    .gender-stats-container div[style*="width: 300px"] {
        width: 250px !important;
    }

    /* Adjust age stats for laptop screens */
    .age-stats-container {
        font-size: 0.65em;
    }

    /* Optimize card headers for laptop screens */
    .card-header h6 {
        font-size: 0.85em;
    }

    .card-header p {
        font-size: 0.75em;
    }

    /* Adjust product container for laptop screens */
    .product-container {
        font-size: 0.8em;
    }

    .section-header h6 {
        font-size: 0.75em;
    }

    .product-name {
        font-size: 0.75em;
    }

    .review-count {
        font-size: 0.65em;
    }

    /* Optimize table for laptop screens */
    .products-table {
        font-size: 0.9em;
    }

    .products-table th {
        padding: 1.5em 0.6em;
    }

    .products-table td {
        padding: 0.9em 0.6em;
    }
}



/* Large desktop devices */
@media (min-width: 1440px) {
    .dashboard-container {
        padding: 2.5rem;
    }

    .follower-card {
        grid-column: span 1;
    }
}

/* Landscape orientation for mobile */
@media (max-width: 768px) and (orientation: landscape) {
    .dashboard-header {
        flex-direction: row;
        text-align: left;
        padding: 1rem;
    }

    .card-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .card-container-2 {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
}

/* High DPI displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .cards {
        border-radius: 12px;
    }

    .product-icon img {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    .cards {
        min-height: 44px;
    }

    .search-btn,
    .view-all {
        min-height: 44px;
        min-width: 44px;
    }

    .product-item {
        min-height: 44px;
    }
}

</style>

