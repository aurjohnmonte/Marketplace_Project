<template>
  <div class="reviews-container">
    <div class="reviews-header">
      <div class="reviews-count">{{ reviews.length }}+ Reviews</div>
      <select v-model="selectedRating" class="filter-select">
        <option value="">All Ratings</option>
        <option value="5">5 star</option>
        <option value="4">4 star</option>
        <option value="3">3 star</option>
        <option value="2">2 star</option>
        <option value="1">1 star</option>
      </select>
    </div>

    <div class="review-list">
      <div
        class="review-card"
        v-for="(review, index) in filteredReviews"
        :key="index"
      >
        <div class="review-header">
          <div class="user-avatar"></div>
          <div class="user-info">
            <div class="username">{{ review.username }}</div>
            <div class="date">{{ review.date }}</div>
          </div>
        </div>
        <div class="item-rate">
          <img src="../../../../images/star.png" class="star-rate" v-for="turn in rate.start" :key="turn">
          <img src="../../../../images/half-star.png" class="star-rate" v-if="rate.half">
          <img src="../../../../images/no-star.png" class="star-rate" v-for="turn in rate.no_star" :key="turn">
          <label>(7.5)</label>
        </div><br>
        <div class="review-text">
          {{ review.text }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selectedRating: '',
      rate: {
        start: 3,
        half: true,
        no_star: 1,
      },
      reviews: [
        {
          username: "John Doe",
          date: "3 months ago",
          text: "Great shop! Fast delivery and excellent products.",
          rating: 5,
        },
        {
          username: "Jane Smith",
          date: "2 weeks ago",
          text: "Item arrived broken. Seller was unresponsive.",
          rating: 1,
        },
        {
          username: "Carlos Reyes",
          date: "1 month ago",
          text: "Affordable prices and friendly seller.",
          rating: 4,
        },
        // Add more reviews as needed
      ],
    };
  },
  computed: {
    filteredReviews() {
      if (!this.selectedRating) return this.reviews;
      return this.reviews.filter(
        (review) => review.rating === parseInt(this.selectedRating)
      );
    },
  },
};
</script>

<style scoped>
.star-rate{
  width: 10px;
  height: 10px;
}
.item-rate{
  display: flex;
  flex-direction: row;
  gap: 2px;
  align-items: center;
}
.item-rate label{
  font-size: 10px;
  color: gray;
  margin-left: 5px;
}
.reviews-container {
  padding: 20px;
  background-color: #f8f8f8;
  font-size: 12px;
  font-family: Arial, sans-serif;
  max-width: 600px;
  margin: auto;
}

.reviews-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.reviews-count {
  font-weight: bold;
  color: #333;
}

.filter-select {
  padding: 6px 10px;
  font-size: 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  background-color: white;
}

.review-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.review-card {
  background-color: #fff;
  padding: 15px;
  border: 1px solid #e2e2e2;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.review-header {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.user-avatar {
  width: 35px;
  height: 35px;
  background-color: #d8d8d8;
  border-radius: 50%;
  margin-right: 10px;
}

.user-info {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.username {
  font-weight: bold;
  color: #222;
}

.date {
  font-size: 11px;
  color: #999;
}

.review-text {
  color: #444;
  line-height: 1.4;
}
</style>
