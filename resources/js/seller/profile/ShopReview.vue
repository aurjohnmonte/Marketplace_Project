<template>
  <div class="shop-review-page">
    <h4>Shop Reviews</h4>
    <div class="average-rating">
      <span>Average Rating: </span>
      <span v-if="reviews.length">{{ this.store.selected_shop.overall_rate }} / 5</span>
      <span v-else>No reviews yet</span>
    </div>
    <div class="reviews-list">
      <h5>All Reviews</h5>
      <div v-if="reviews.length">
        <div v-for="review in paginatedReviews" :key="review.id" class="review-item">
          <div class="review-header">
            <span class="review-rating">Rating: {{ review.rate }} / 5</span>
            <span class="review-date">{{ formatDate(review.created_at) }}</span>
          </div>
          <div class="review-comment">{{ review.comment }}</div>
        </div>
        <div class="pagination">
          <button @click="prevPage" :disabled="page === 1">Prev</button>
          <span>Page {{ page }} of {{ totalPages }}</span>
          <button @click="nextPage" :disabled="page === totalPages">Next</button>
        </div>
      </div>
      <div v-else>
        <p>No reviews yet.</p>
      </div>
    </div>
  </div>
</template>

<script>
import { useDataStore } from '../../stores/dataStore';
import axios from 'axios';
export default {
  name: 'ShopReview',
  data() {
    return {
      reviews: [],
      store: useDataStore(),
      page: 1,
      perPage: 5
    };
  },
  computed: {
    averageRating() {
      if (!this.reviews.length) return 0;
      const sum = this.reviews.reduce((acc, r) => acc + r.rating, 0);
      return sum / this.reviews.length;
    },
    sortedReviews() {
      // Sort reviews by date descending
      return [...this.reviews].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    },
    totalPages() {
      return Math.ceil(this.sortedReviews.length / this.perPage) || 1;
    },
    paginatedReviews() {
      const start = (this.page - 1) * this.perPage;
      return this.sortedReviews.slice(start, start + this.perPage);
    }
  },
  methods: {
    formatDate(dateStr) {
      const date = new Date(dateStr);
      return date.toLocaleDateString();
    },
    prevPage() {
      if (this.page > 1) this.page--;
    },
    nextPage() {
      if (this.page < this.totalPages) this.page++;
    },

    async return_shopReviews(){
      const data = new FormData();
      data.append('shop_id', this.store.selected_shop.id);

      const res = await axios.post('/seller/return-reviews/shop', data);

      console.log(res.data.message);

      this.reviews = res.data.message;
    }
  },
  mounted() {
    // Fetch reviews from API in production
    // Example static data for demo (unique IDs)
    this.reviews = [
      { id: 1, rating: 5, comment: 'Excellent shop!', created_at: '2025-08-10T12:00:00Z' },
      { id: 2, rating: 4, comment: 'Good service.', created_at: '2025-08-12T15:30:00Z' },
      { id: 3, rating: 3, comment: 'Average experience.', created_at: '2025-08-13T09:45:00Z' },
      { id: 4, rating: 5, comment: 'Eaxcellent shop!', created_at: '2025-08-10T12:00:00Z' },
      { id: 5, rating: 4, comment: 'Gaood service.', created_at: '2025-08-12T15:30:00Z' },
      { id: 6, rating: 3, comment: 'Avaerage experience.', created_at: '2025-08-13T09:45:00Z' },
      { id: 7, rating: 5, comment: 'Exacellent shop!', created_at: '2025-08-10T12:00:00Z' },
      { id: 8, rating: 4, comment: 'Goaod service.', created_at: '2025-08-12T15:30:00Z' },
      { id: 9, rating: 3, comment: 'Avearage experience.', created_at: '2025-08-13T09:45:00Z' },
      { id: 10, rating: 5, comment: 'Exceallent shop!', created_at: '2025-08-10T12:00:00Z' },
      { id: 11, rating: 4, comment: 'Good aservice.', created_at: '2025-08-12T15:30:00Z' },
      { id: 12, rating: 3, comment: 'Averagae experience.', created_at: '2025-08-13T09:45:00Z' },
      { id: 13, rating: 5, comment: 'Excelleant shop!', created_at: '2025-08-10T12:00:00Z' },
      { id: 14, rating: 4, comment: 'Good seravice.', created_at: '2025-08-12T15:30:00Z' },
    ];

    this.return_shopReviews();
  }
};
</script>

<style scoped>
.shop-review-page {
  width: 100%;
  margin: 0 auto;
  padding: 2em;
}

.shop-review-page h4 {
    font-family: 'Playfair Display', serif;
    font-weight: 600;
    font-size: 2em;
}

.average-rating {
  font-size: .9em;
  margin-bottom: 1rem;
}
.reviews-list {
  margin-top: 2rem;
}
.review-item {
  padding: 1em;
  background-color: #e4c8b8bb;
  margin-bottom: 1em;
  border-radius: 1em;
  box-shadow:    1px 1px 1px rgba(0, 0, 0, 0.404);
}
.review-header {
  display: flex;
  justify-content: space-between;
  font-size: 0.85rem;
  margin-bottom: 0.5rem;
}
.review-rating {
  font-weight: bold;
  color: #dd8d0d;
}
.review-date {
  color: #888;
}
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  margin-top: 1rem;
}
.pagination button {
  background: #ca9d73;
  color: #fafafa;
  border: none;
  font-size: .9em;
  padding: 0.5em 0.8em;
  border-radius: 1em;
  box-shadow: 0 1px 1px rgba(0,0,0,0.585);
  cursor: pointer;
}
.pagination button:disabled {
  background: #ccc;
  cursor: not-allowed;
}

/* -------- Responsive Design -------- */
@media (max-width: 1024px) { /* Tablet */
  .shop-review-page {
    padding: 1.5em;
  }
  .shop-review-page h4 {
    font-size: 1.8em;
  }
  .review-header {
    font-size: 0.85rem;
  }
}

@media (max-width: 768px) { /* Large phones & small tablets */
  .shop-review-page {
    padding: 1.2em;
  }
  .shop-review-page h4 {
    font-size: 1.5em;
  }
  .review-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.3rem;
    font-size: 0.8rem;
  }
  .review-comment {
    font-size: 0.9rem;
    line-height: 1.4;
  }
}

@media (max-width: 480px) { /* Mobile phones */
  .shop-review-page {
    padding: 1em;
  }
  .shop-review-page h4 {
    font-size: 1.3em;
  }
  .average-rating {
    font-size: 0.85rem;
  }
  .review-item {
    padding: 0.8em;
  }
  .review-header {
    font-size: 0.75rem;
  }
  .pagination {
    flex-direction: column;
    gap: 0.5rem;
  }
  .pagination span {
    font-size: 0.8rem;
  }
  .pagination button {
    width: 100%;
    max-width: 150px;
    text-align: center;
  }
}
</style>
