<template>
  <div class="manage-reviews-page">

    <teleport to="body">
      <div class="overlay" v-if="is_overlay_loading">
          <img src="../../../images/kOnzy.gif">
      </div>
    </teleport>

    <teleport to="body">
        <ReviewSeeMore :review="selected_review" v-if="showModal && selected_review" @close="showModal = false; selected_review = null;"/>
    </teleport>

    <teleport to="body">
      <Question 
        @close="show_question = false; selected_review = null;" 
        @confirm="confirm" v-if="show_question && selected_review"
        :question="question"
        :action_type="action_type"
      />
    </teleport>

    <!-- HEADER -->
    <header class="page-header">
      <h1>📝 Manage Reviews</h1>
      <p class="subtitle">Monitor and evaluate reviews between buyers and sellers</p>
    </header>

    <!-- SUMMARY CARDS -->
    <div class="summary-section">
      <div class="summary-card">
        <h2>Total Reviews</h2>
        <p>{{ top_data.total_reviews }}</p>
      </div>
      <div class="summary-card">
        <h2>Avg Seller Rating</h2>
        <p>{{ top_data.avg_seller_rate.toFixed(1) }} ★</p>
      </div>
      <div class="summary-card">
        <h2>Low Ratings</h2>
        <p>{{ top_data.low_rate }}</p>
      </div>
      <div class="summary-card">
        <h2>Blocked Accounts</h2>
        <p>{{ top_data.blocked_accounts }}</p>
      </div>
      <div class="summary-card">
        <h2>Blocked Products</h2>
        <p>{{ top_data.blocked_products }}</p>
      </div>
    </div>

    <!-- FILTER BAR -->
    <div class="filter-bar">
      <input type="text" v-model="search_data.name" placeholder="🔍 Search reviews based on ..." class="filter-input search-input"/>
      <select class="filter-select" v-model="search_data.type">
        <option value="">All Types</option>
        <option value="shop">Shop</option>
        <option value="seller">Buyer</option>
        <option value="product">Product</option>
      </select>
      <select class="filter-select" v-model.number="search_data.rate">
        <option value="0">All Ratings</option>
        <option value="1">1★</option>
        <option value="2">2★</option>
        <option value="3">3★</option>
        <option value="4">4★</option>
        <option value="5">5★</option>
      </select>
      <button class="filter-btn" @click="applyFilters">Filter</button>
      <button class="filter-btn clear" @click="clearFilters">Clear</button>
    </div>

    <!-- REVIEWS TABLE -->
    <div class="reviews-table-container">
      <table class="reviews-table">
        <thead>
          <tr>
            <th>Buyer</th>
            <th>Seller</th>
            <th>Product name</th>
            <th>Product overall rate</th>
            <th>Shop overall rating</th>
            <th>Rate</th>
            <th>Comment</th>
            <th>Product blocked</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="review in filtered_reviews" :key="review.id">
            <td>{{ review.user.name }}</td>
            <td>{{ review.shop.user.name }}</td>
            <td>{{ review.product.name }}</td>
            <td>{{ review.product.overall_rate }}★</td>
            <td>{{ review.shop.overall_rate }}★</td>
            <td>{{ review.rate }}★</td>
            <td>{{ review.comment }}</td>
            <td>{{ review.product.is_blocked ? 'blocked' : 'pass' }}</td>
            <td>{{ returnDateFormat(review.created_at) }}</td>
            <td>
              <button class="action-btn view" @click="showModal = true; selected_review = review;">View</button>
              <button class="action-btn delete" @click="selected_review = review; show_question = true; action_type = 'delete';">Delete</button>
              <button class="action-btn block" @click="askUnblock(review)" v-if="review.product.is_blocked">Unblock Product</button>
              <button class="action-btn block" @click="askBlock(review)" v-else>Block Product</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import ReviewSeeMore from "../modals/ReviewSeeMore.vue";
import Question from "../modals/Question.vue";

export default {
  components: {ReviewSeeMore, Question},
  data() {
    return {
      is_overlay_loading: false,
      showModal: false,
      show_question: false,
      action_type: '',
      all_reviews: [],
      filtered_reviews: [],
      shops: [],
      search_data: {
        name: "",
        rate: 0,
        type: "",
      },
      top_data: {
        total_reviews: 0,
        avg_seller_rate: 0,
        low_rate: 0,
        blocked_accounts: 0,
        blocked_products: 0,
      },
      selected_review: null,
      question: 'Do you really want to delete this reviews?',
      products: [],
      accounts: [],
    };
  },
  methods: {

    askUnblock(review){

      this.question = "Do you really want to unblock this product?";
      this.selected_review = review;
      this.show_question = true;

      this.action_type = "unblock";
    },

    askBlock(review){

      this.question = "Do you really want to block this product?";
      this.selected_review = review;
      this.show_question = true;

      this.action_type = "block";
    },  

    async confirm(message){

      const data = new FormData();

      this.show_question = false;
      this.is_overlay_loading = true;

      if(this.action_type === 'delete'){

        data.append('review_id', this.selected_review.id);
        const res = await axios.post('/admin/delete-review', data);

        if(res.data.message === 'success'){

          window.alert('YOU HAVE SUCCESSFULLY DELETED THE REVIEW');

          await this.returnAllReviews();

          this.selected_review = null;
        }
      }
      else if(this.action_type === 'block'){

        console.log('neh sulod man');

        data.append('product_id', this.selected_review.product.id);
        data.append('gmail', this.selected_review.shop.user.email);
        data.append('message', message);

        let data_pass = {
          seller_name: this.selected_review.shop.user.name,
          product_id: this.selected_review.product.id,
          product_name: this.selected_review.product.name,
          shop_name: this.selected_review.shop.name,
          message: message,
          gmail: this.selected_review.shop.user.email
        };

        console.log('DATA: ', data_pass);

        data.append('data', JSON.stringify(data_pass));

        const res = await axios.post('/admin/block-product', data);

        if(res.data.message === 'success'){

          window.alert('YOU HAVE SUCCESSFULLY BLOCKED THE PRODUCT');
          await this.returnAllReviews();

          this.selected_review = null;
        }

        console.log(res.data.message);
      }
      else if(this.action_type === 'unblock'){

        data.append('product_id', this.selected_review.product.id);
        data.append('gmail', this.selected_review.shop.user.email);
        data.append('message', message);
        
        let data_pass = {
          seller_name: this.selected_review.shop.user.name,
          product_id: this.selected_review.product.id,
          product_name: this.selected_review.product.name,
          shop_name: this.selected_review.shop.name,
          message: message,
          gmail: this.selected_review.shop.user.email
        };

        console.log('DATA: ', data_pass);

        data.append('data', JSON.stringify(data_pass));

        const res = await axios.post('/admin/unblock-product', data);

        if(res.data.message === 'success'){

          window.alert('YOU HAVE SUCCESSFULLY UNBLOCKED THE PRODUCT');

          await this.returnAllReviews();

          this.selected_review = null;
        }
      }

      this.is_overlay_loading = false;
    },
    applyFilters() {
      let results = [...this.all_reviews];

      // Filter by rating
      if (this.search_data.rate > 0) {
        results = results.filter(
          (r) => parseInt(r.rate) === this.search_data.rate
        );
      }

      // Filter by type (shop or seller)
      if (this.search_data.type === "shop") {
        results = results.filter((r) =>
          r.shop.user.name
            .toLowerCase()
            .includes(this.search_data.name.toLowerCase())
        );
      } else if (this.search_data.type === "seller") {
        results = results.filter((r) =>
          r.user.name
            .toLowerCase()
            .includes(this.search_data.name.toLowerCase())
        );

      } else if (this.search_data.type === 'product') {

        results = results.filter((r) =>
          r.product.name
            .toLowerCase()
            .includes(this.search_data.name.toLowerCase())
        );
      }
      else if (this.search_data.name.trim() !== "") {
        // Search both buyer and seller
        results = results.filter(
          (r) =>
            r.user.name
              .toLowerCase()
              .includes(this.search_data.name.toLowerCase()) ||
            r.shop.user.name
              .toLowerCase()
              .includes(this.search_data.name.toLowerCase())
        );
      }

      this.filtered_reviews = results;
    },

    clearFilters() {
      this.search_data = { name: "", rate: 0, type: "" };
      this.filtered_reviews = this.all_reviews;
    },

    returnDateFormat(d) {
      const date = new Date(d);
      return `${date.toLocaleDateString()} - ${date.toLocaleTimeString()}`;
    },

    async returnAllReviews() {
      try {
        const res = await axios.get("/admin/return-all/reviews");
        this.all_reviews = res.data.reviews || [];
        this.products = res.data.products;
        this.filtered_reviews = [...this.all_reviews];
        this.shops = res.data.shops || [];
        this.processTopData();
      } catch (error) {
        console.error("Error fetching reviews:", error);
      }
    },

    processTopData() {
      this.top_data.total_reviews = this.all_reviews.length;

      // Average seller rating
      const total = this.shops.reduce(
        (acc, shop) => acc + shop.overall_rate,
        0
      );
      this.top_data.avg_seller_rate = this.shops.length
        ? total / this.shops.length
        : 0;

      // Low ratings
      this.top_data.low_rate = this.all_reviews.filter(
        (r) => r.rate < 3
      ).length;

      // blocked products
      this.top_data.blocked_products = this.products.filter(product => product.is_blocked === 1).length;
    },
  },
  async mounted() {
    await this.returnAllReviews();
  },
};
</script>


<style scoped>
.overlay{
    position: fixed;
    left: 0;
    top: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.841);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
}
.overlay img{
    width: 100px;
    height: 100px;
}
.manage-reviews-page {
  font-family: "Poppins", sans-serif;
  color: #323232;
  background: #f9f9f9;
  min-height: 100vh;
  padding: 30px 60px;
}

/* HEADER */
.page-header {
  text-align: center;
  margin-bottom: 40px;
}
.page-header h1 {
  font-size: 2rem;
  color: #c0673e;
  margin-bottom: 8px;
}
.subtitle {
  color: #666;
  font-size: 15px;
}

/* SUMMARY CARDS */
.summary-section {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 20px;
  margin-bottom: 40px;
}
.summary-card {
  background: #ca9d73;
  color: white;
  border-radius: 12px;
  flex: 1 1 180px;
  min-width: 160px;
  max-width: 230px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}
.summary-card h2 {
  font-size: 18px;
  margin-bottom: 8px;
}
.summary-card p {
  font-size: 22px;
  font-weight: bold;
}

/* FILTER BAR */
.filter-bar {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  gap: 10px;
  margin-bottom: 30px;
}
.filter-input.search-input{
  width: 300px;
}
.filter-input,
.filter-select {
  padding: 10px 15px;
  font-size: 14px;
  border: 1px solid #c0673e;
  border-radius: 8px;
  outline: none;
}
.filter-btn {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  background: #c0673e;
  color: white;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}
.filter-btn:hover {
  background: #323232;
}
.filter-btn.clear {
  background: #ca9d73;
}

/* REVIEWS TABLE */
.reviews-table-container {
  background: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
  overflow-x: auto;
}
.reviews-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  min-width: 800px;
}
.reviews-table th {
  background: #323232;
  color: white;
  padding: 12px;
  font-weight: 600;
}
.reviews-table td {
  border-bottom: 1px solid #eee;
  padding: 12px;
  font-size: 14px;
  vertical-align: top;
}
.action-btn {
  margin: 2px;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  color: white;
  font-size: 13px;
  font-weight: 600;
  transition: 0.3s;
}
.action-btn.view {
  background: #28904b;
}
.action-btn.delete {
  background: #c0673e;
}
.action-btn.block {
  background: #323232;
}
.action-btn:hover {
  opacity: 0.85;
}

/* MODAL */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 10px;
}
.modal {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 500px;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  animation: fadeIn 0.3s ease;
}
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #323232;
  color: white;
  padding: 15px 20px;
  border-top-left-radius: 12px;
  border-top-right-radius: 12px;
}
.modal-body {
  padding: 20px;
}
.modal-section {
  margin-bottom: 20px;
}
.modal-section h3 {
  color: #c0673e;
  margin-bottom: 8px;
}
.modal-footer {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-end;
  gap: 10px;
  padding: 15px 20px;
}
.modal-btn {
  padding: 10px 15px;
  border: none;
  border-radius: 8px;
  color: white;
  font-weight: bold;
  cursor: pointer;
}
.modal-btn.delete {
  background: #c0673e;
}
.modal-btn.block {
  background: #323232;
}
.modal-btn.message {
  background: #ca9d73;
}
.close-btn {
  background: transparent;
  color: white;
  border: none;
  font-size: 18px;
  cursor: pointer;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* RESPONSIVE DESIGN */
@media (max-width: 768px) {
  .manage-reviews-page {
    padding: 20px;
  }
  .summary-section {
    gap: 12px;
  }
  .summary-card {
    flex: 1 1 45%;
  }
  .filter-bar {
    flex-direction: column;
  }
  .filter-input,
  .filter-select,
  .filter-btn {
    width: 100%;
  }
}
@media (max-width: 480px) {
  .page-header h1 {
    font-size: 1.5rem;
  }
  .summary-card {
    flex: 1 1 100%;
  }
  .modal {
    width: 100%;
  }
}
</style>
