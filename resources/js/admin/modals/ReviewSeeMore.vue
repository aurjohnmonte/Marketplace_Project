<template>
  <div class="modal-overlay">
    <div class="modal-container">
      <div class="modal-header">
        <h2>🧾 Review Details</h2>
        <button class="close-btn" @click="$emit('close')">✕</button>
      </div>

      <div class="modal-body">
        <!-- Buyer Info -->
        <section class="modal-section">
          <h3>👤 Buyer Information</h3>
          <p><strong>Name:</strong> {{ review?.user?.name }}</p>
          <p><strong>Email:</strong> {{ review?.user?.email }}</p>
          <p><strong>Status:</strong> {{ review.user.is_active ? 'active' : 'not active' }}</p>
        </section>

        <!-- Seller Info -->
        <section class="modal-section">
          <h3>🏪 Seller Information</h3>
          <p><strong>Name:</strong> {{ review?.shop?.user?.name }}</p>
          <p><strong>Email:</strong> {{ review?.shop?.user?.email }}</p>
          <p><strong>Status:</strong> {{ review.shop.user.is_active ? 'active' : 'not active' }}</p>
        </section>

        <!-- Shop Info -->
        <section class="modal-section">
          <h3>🛍️ Shop Information</h3>
          <p><strong>Shop Name:</strong> {{ review.shop.name }}</p>
          <p><strong>Overall Rating:</strong> {{ review?.shop?.overall_rate }} ★</p>
          <p><strong>Total Reviews:</strong> {{ countAllReviews }}</p>
        </section>

        <section class="modal-section">
          <h3>Product Information</h3>
          <p><strong>Product Name:</strong> {{ review.product.name }}</p>
          <p><strong>Overall Rate:</strong> {{ review.product.overall_rate }} ★</p>
          <p><strong>Status:</strong> {{ review.product.status }}</p>
        </section>

        <!-- Review Details -->
        <section class="modal-section">
          <h3>⭐ Review</h3>
          <p>
            <strong>Rating:</strong>
            <span :class="['rating-badge', isLowRating ? 'low' : 'high']">
              {{ review?.rate }} ★
            </span>
          </p>
          <p><strong>Comment:</strong> "{{ review?.comment }}"</p>
          <p><strong>Date:</strong> {{ formatDate(review?.created_at) }}</p>
        </section>
      </div>

      <div class="modal-footer">
        <!-- <button class="modal-btn delete" @click="$emit('delete', review)">🗑 Delete</button> -->
        <!-- <button class="modal-btn message" @click="$emit('message', review)">✉️ Message Seller</button> -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    visible: { type: Boolean, default: false },
    review: { type: Object, default: () => ({}) }
  },
  computed: {
    isLowRating() {
      return this.review?.rate < 3;
    },

    countAllReviews(){

      return this.review.shop.reviews.filter(review => review.review_type === 'shop').length;
    }
  },
  methods: {
    formatDate(dateStr) {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      return `${date.toLocaleDateString()} - ${date.toLocaleTimeString()}`;
    }
  },
  mounted(){

    console.log("reviews: ", this.review);
  }
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background-color: rgba(50, 50, 50, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal-container {
  background: linear-gradient(to bottom right, #ca9d73, #c0673e);
  color: #fff;
  border-radius: 12px;
  width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 5px 20px rgba(0,0,0,0.4);
  animation: slideIn 0.3s ease;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #323232;
  padding: 15px 20px;
  border-radius: 12px 12px 0 0;
}

.modal-header h2 {
  margin: 0;
  font-size: 22px;
  color: #fff;
}

.close-btn {
  background: transparent;
  border: none;
  color: #fff;
  font-size: 22px;
  cursor: pointer;
}

.modal-body {
  padding: 20px;
  background-color: #fff;
  color: #323232;
}

.modal-section {
  margin-bottom: 20px;
}

.modal-section h3 {
  color: #c0673e;
  border-bottom: 2px solid #ca9d73;
  padding-bottom: 5px;
  margin-bottom: 10px;
}

.rating-badge {
  padding: 4px 10px;
  border-radius: 6px;
  color: #fff;
  font-weight: bold;
}

.rating-badge.low {
  background-color: #c0392b;
}

.rating-badge.high {
  background-color: #1e703c;
}

.modal-footer {
  display: flex;
  justify-content: space-around;
  padding: 15px;
  background-color: #f9f9f9;
  border-top: 2px solid #ca9d73;
  border-radius: 0 0 12px 12px;
}

.modal-btn {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  color: #fff;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}

.modal-btn.delete {
  background-color: #c0392b;
}

.modal-btn.block {
  background-color: #e67e22;
}

.modal-btn.message {
  background-color: #1e703c;
}

.modal-btn:hover {
  opacity: 0.85;
}

@keyframes slideIn {
  from { transform: translateY(-20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
</style>
