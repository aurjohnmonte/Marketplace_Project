<template>
  <div class="modal-overlay" @click.self="$emit('close')">

    <!--ACTION MODAL-->
    
    <teleport to="body">
      <ActionUserAccount v-if="show_action" :user="user" @close="show_action = false" @save="save" @delete="deleteSeller"/>
    </teleport>
    <div class="modal" v-if="user.role === 'seller'">
      <!-- Header -->
      <div class="modal-header">
        <h3>{{ user.role.toUpperCase() }} INFORMATION</h3>
        <span class="material-icons close-icon" @click="$emit('close')">close</span>
      </div>

      <!-- Profile -->
      <div class="profile-section" style="position: relative;">
        <div style="display: flex; flex-direction: row; align-items: center; gap: 10px; position: absolute; right: 0; top: 0; margin: 10px;">
          <span class="material-icons header-action" @click.stop="goLocation(parseFloat(user.shop.latitude), parseFloat(user.shop.longitude))">
            map
          </span>
        </div>
        <img
          :src="user.profile ? `/${user.profile}` : '/default-profile.png'"
          alt="Profile"
          class="profile-img"
        />
        <div class="profile-info">
          <h2>{{ user.firstname }} {{ user.m_initial ? user.m_initial + '.' : '' }} {{ user.lastname }}</h2>
          <p class="username">@{{ user.name }}</p>
          <p class="email">{{ user.email }}</p>
        </div>
      </div>

      <div style="display: flex; flex-direction: row; align-items: center; justify-content: space-between; gap: 10px; margin-top: 10px;">
        <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
          <button class="visit-profile-btn" @click="this.$router.push({name: 'SellerProfile', params: {'id': user.id}})">
            Visit Profile
          </button>
        </div>
        <span class="material-icons header-action" @click="show_action = true">
          settings
        </span>
      </div>

      <hr />

      <!-- User Info -->
      <div class="info-section">
        <div style="display: flex; flex-direction: row; align-items: center; gap: 30px;">
          <h4>👤 Personal Information</h4>
          <span class="material-icons close-icon" style="padding: 5px; border: 1px solid #c0673e; border-radius: 50%;" @click="this.$emit('goEdit', 'p_info')">edit</span>
        </div>
        <div class="info-grid">
          <div><strong>Age:</strong> {{ user.age }}</div>
          <div><strong>Gender:</strong> {{ user.gender }}</div>
          <div><strong>Birthday:</strong> {{ user.birthday }}</div>
          <div><strong>Contact No:</strong> {{ user.contact_no }}</div>
          <div><strong>User Address:</strong> {{ user.current_address }}</div>
          <div><strong>Joined:</strong> {{ formatDate(user.created_at) }}</div>
          <div><strong>Last Logout:</strong> {{ user.time_logout || 'N/A' }}</div>
          <div><strong>Followers:</strong> {{ user.followers.length }}</div>
        </div>
      </div>

      <!-- Shop Info -->
      <div v-if="user.shop" class="shop-section">
        <div style="display: flex; flex-direction: row; align-items: center; gap: 30px;">
          <h4>🛍️ Shop Information</h4>
          <span class="material-icons close-icon" style="padding: 5px; border: 1px solid #c0673e; border-radius: 50%;" @click="this.$emit('goEdit', 's_info')">edit</span>
        </div>
        <div class="shop-info">
          <img
            :src="`/${user.shop.profile_photo}` || '/default-shop.png'"
            alt="Shop photo"
            class="shop-photo"
          />
          <div class="shop-details">
            <p><strong>Name:</strong> {{ user.shop.name }}</p>
            <p><strong>Address:</strong> {{ user.shop.address }}</p>
            <p><strong>Category:</strong> {{ parseCategories(user.shop.category) }}</p>
            <p><strong>Description:</strong> {{ user.shop.description }}</p>
            <p><strong>Overall Rate:</strong> ⭐ {{ user.shop.overall_rate }}</p>
            <p><strong>Total Views:</strong> {{ user.shop.total_views }}</p>
          </div>
        </div>
      </div>

      <!-- PRODUCT Reviews -->
      <div v-if="user.shop && user.shop.reviews && user.shop.reviews.length" class="reviews-section">
        <h4>⭐ Customer Product Reviews ({{ filterShopReviews.length }})</h4>
        <div
          v-for="(review, i) in filterProductReviews"
          :key="i"
          class="review-card"
        >
          <p class="review-comment">"{{ review.comment }}"</p>
          <p class="review-meta">- {{ review.reviewer_name }} ({{ review.rate }}★)</p>
        </div>
      </div>

      <!-- SHOP Reviews -->
      <div v-if="user.shop && user.shop.reviews && user.shop.reviews.length" class="reviews-section">
        <h4>⭐ Customer Shop Reviews ({{ filterShopReviews.length }})</h4>
        <div
          v-for="(review, i) in filterShopReviews"
          :key="i"
          class="review-card"
        >
          <p class="review-meta">- {{ review.reviewer_name }} ({{ review.rate }}★)</p>
        </div>
      </div>
    </div>


    <!--!!!!!!!!!!!!!!!!!!!!!!!!!!                   BUYER SIDE                   !!!!!!!!!!!!!!!!!!!!!!!!!!-->
    <div class="modal" v-else>
      <!-- Header -->
      <div class="modal-header">
        <h3>{{ user.role }} INFORMATION</h3>
        <span class="material-icons close-icon" @click="$emit('close')">close</span>
      </div>

      <!-- Profile -->
      <div class="profile-section">
        <img
          :src="user.profile ? `/${user.profile}` : '/default-profile.png'"
          alt="Profile"
          class="profile-img"
        />
        <div class="profile-info">
          <h2>{{ user.firstname }} {{ user.m_initial ? user.m_initial + '.' : '' }} {{ user.lastname }}</h2>
          <p class="username">@{{ user.name }}</p>
          <p class="email">{{ user.email }}</p>
        </div>
      </div>
      <div style="display: flex; flex-direction: row; align-items: center; gap: 10px; justify-content: space-between; margin-top: 10px;">
        <span class="material-icons header-action" @click="show_action = true">
          settings
        </span>
      </div>
      <hr />

      <!-- User Info -->
      <div class="info-section">
        <div style="display: flex; flex-direction: row; align-items: center; gap: 30px;">
          <h4>👤 Personal Information</h4>
          <span class="material-icons close-icon" style="padding: 5px; border: 1px solid #c0673e; border-radius: 50%;" @click="this.$emit('goEdit', 'p_info')">edit</span>
        </div>
        <div class="info-grid">
          <div><strong>Age:</strong> {{ user.age }}</div>
          <div><strong>Gender:</strong> {{ user.gender }}</div>
          <div><strong>Birthday:</strong> {{ user.birthday }}</div>
          <div><strong>Contact No:</strong> {{ user.contact_no }}</div>
          <div><strong>Address:</strong> {{ user.current_address }}</div>
          <div><strong>Joined:</strong> {{ formatDate(user.created_at) }}</div>
          <div><strong>Last Logout:</strong> {{ user.time_logout || 'N/A' }}</div>
          <div><strong>Followers:</strong> {{ user.total_followers }}</div>
        </div>
      </div>
      <div v-if="user.reviews && user.reviews.length > 0" class="reviews-section">
        <h4>⭐ Product Reviews ({{ filterBuyer_productReviews.length }})</h4>
        <div
          v-for="(review, i) in filterBuyer_productReviews"
          :key="i"
          class="review-card"
        >
          <p class="review-comment">"{{ review.comment }}"</p>
          <p class="review-meta">- {{ review.reviewer_name }} ({{ review.rate }}★)</p>
        </div>
      </div>
      <!-- SHOP Reviews -->
      <div v-if="user.reviews && user.reviews.length > 0" class="reviews-section">
        <h4>⭐ Shop Reviews ({{ filterBuyer_shopReviews.length }})</h4>
        <div
          v-for="(review, i) in filterBuyer_shopReviews"
          :key="i"
          class="review-card"
        >
          <p class="review-meta">- {{ review.reviewer_name }} ({{ review.rate }}★)</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ActionUserAccount from './ActionUserAccount.vue';
import { useDataStore } from '../../stores/dataStore';
export default {
  components: {ActionUserAccount},
  props: ['user'],
  data(){
    return{
      show_action: false,
    }
  },
  computed: {
    filterProductReviews(){

        return this.user.shop.reviews.filter(review => review.review_type === 'product');
    },
    filterShopReviews(){

        return this.user.shop.reviews.filter(review => review.review_type === 'shop');
    },
    filterBuyer_productReviews(){

        return this.user.reviews.filter(review => review.review_type === 'product');
    },
    filterBuyer_shopReviews(){

        return this.user.reviews.filter(review => review.review_type === 'shop');
    }
  },
  methods: {

    goLocation(lat, long){
      console.log(lat + " " + long);
      const store = useDataStore();
      store.setSelectedCoordinate(lat, long);
      this.$router.push({name: 'AdminMap'});
    },

    save(){

      this.$emit('save_deactivate');
    },

    formatDate(dateString) {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });
    },
    parseCategories(str) {
      try {
        const categories = JSON.parse(str);
        return Array.isArray(categories) ? categories.join(', ') : str;
      } catch {
        return str;
      }
    },
  },
};
</script>

<style scoped>
.filter-search{
  width: 100%;
  display: flex; flex-direction: column; align-items: start;
  gap: 10px;
  margin-bottom: 15px;
}
.filter-search input{
  padding: 10px;
  width: 60%;
  border-radius: 10px;
  border: 1px solid gray;
}
.filter-search select{
  padding: 5px;
  width: 40%;
  border-radius: 5px;
  border: 1px solid gray;
}
.header-action{
  color: white;
  cursor: pointer;
  padding: 5px;
  border: 1px solid #c0673e;
  background-color: #c0673e;
  border-radius: 50%;
}
.message-shop{
  background-color: #c0673e;
  color: white;
  font-size: 15px;
  padding: 5px;
  cursor: pointer;
  border-radius: 10px;
  border: 1px solid gray;

}
.visit-profile-btn{
    background-color:#323232;
    color: white;
    font-size: 15px;
    padding: 5px;
    cursor: pointer;
    border-radius: 10px;
}
/* Background overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: #32323290;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10000;
}

/* Modal box */
.modal {
  background-color: #fff;
  border-radius: 16px;
  width: 100%;
  max-width: 750px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
  padding: 25px;
  box-sizing: border-box;
  border-top: 8px solid #c0673e;
  font-family: 'Poppins', sans-serif;
}

/* Header */
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  color: #323232;
}

.modal-header h3 {
  margin: 0;
  font-size: 22px;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.close-icon {
  cursor: pointer;
  font-size: 28px;
  color: #c0673e;
  transition: 0.3s;
}

.close-icon:hover {
  color: #ca9d73;
}

/* Profile */
.profile-section {
  display: flex;
  align-items: center;
  gap: 20px;
  background-color: #f6f4f2;
  border-radius: 12px;
  padding: 15px;
}

.profile-img {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  border: 3px solid #ca9d73;
  object-fit: cover;
}

.profile-info h2 {
  margin: 0;
  color: #323232;
}

.username {
  color: #c0673e;
  font-weight: 500;
}

.email {
  color: #555;
}

/* Info grid */
.info-section {
  margin-top: 20px;
}

.info-section h4 {
  color: #c0673e;
}

.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  color: #323232;
}

.info-grid div {
  background-color: #f6f4f2;
  padding: 10px;
  border-radius: 10px;
}

/* Shop section */
.shop-section {
  margin-top: 25px;
}

.shop-section h4 {
  color: #c0673e;
}

.shop-info {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  background-color: #f6f4f2;
  border-radius: 12px;
  padding: 15px;
}

.shop-photo {
  width: 130px;
  height: 130px;
  border-radius: 12px;
  border: 3px solid #ca9d73;
  object-fit: cover;
}
.shop-details{
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.shop-details p {
  margin: 3px 0;
  color: #323232;
}

/* Reviews section */
.reviews-section {
  margin-top: 25px;
}

.reviews-section h4 {
  color: #c0673e;
}

.review-card {
  background-color: #f6f4f2;
  border-left: 5px solid #ca9d73;
  padding: 10px 15px;
  border-radius: 8px;
  margin-bottom: 8px;
}

.review-comment {
  font-style: italic;
  color: #323232;
  margin: 0 0 5px 0;
}

.review-meta {
  color: #555;
  font-size: 14px;
}

/* Responsive */
@media screen and (max-width: 768px) {
  .modal {
    max-width: 95%;
    padding: 20px;
  }

  .profile-section {
    flex-direction: column;
    align-items: flex-start;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }
}
</style>
