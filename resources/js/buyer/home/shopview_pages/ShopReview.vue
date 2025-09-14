<template>
  <div class="reviews-container">
    <teleport to="body">
      <ShopRate @cancel="hide = true;" v-if="!hide" @addReview="addReview"/>
    </teleport>
    <div class="reviews-header">
      <div class="reviews-count">{{ reviews.length }} Reviews</div>
      <div style="display: flex; flex-direction: row; gap: 10px;">
        <button class="rate-btn" :class="{is_disabled: !can_rate}" @click="hide = false;" :disabled="!can_rate">Rate this shop</button>
        <select v-model="selectedRating" class="filter-select">
          <option value="">All Ratings</option>
          <option value="5">5 star</option>
          <option value="4">4 star</option>
          <option value="3">3 star</option>
          <option value="2">2 star</option>
          <option value="1">1 star</option>
        </select>
      </div>
    </div>

    <div class="review-list">
      <div
        class="review-card"
        v-for="(review, index) in reviews"
        :key="index"
      >
        <div class="review-header">
          <div class="user-avatar">
            <img :src="'/'+review.user.profile">
          </div>
          <div class="user-info">
            <div class="username">{{ review.user.name }}</div>
            <div class="date">{{ formatDate(review.created_at) }} - {{ formatTime(review.created_at) }}</div>
          </div>
        </div>
        <div class="item-rate">
          <img src="../../../../images/star.png" class="star-rate" v-for="turn in returnStar('whole',review.rate)" :key="turn">
          <img src="../../../../images/half-star.png" class="star-rate" v-for="turn in returnStar('half',review.rate)" :key="turn">
          <img src="../../../../images/no-star.png" class="star-rate" v-for="turn in returnStar('none',review.rate)" :key="turn">
          <label>( {{ review.rate }} )</label>
        </div><br>
        <div class="review-text">
          {{ review.comment }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ShopRate from './ShopRate.vue';
import { useDataStore } from '../../../stores/dataStore';
export default {
  components: {
    ShopRate
  },
  data() {
    return {
      shop: null,
      can_rate: false,
      reviews: [],
      store: useDataStore(),
      hide: true,
      selectedRating: '',
      rate: {
        start: 3,
        half: true,
        no_star: 1,
      },
      reviews: [],
    };
  },
  watch: {
    selectedRating(newval){
      console.log(newval);
      if(newval !== ""){
        let rate = parseInt(newval);

        let partial = [];

        this.store.selected_shop.reviews.forEach(review => {

          if(review.review_type === 'shop'){
            if(review.rate === rate){
              partial.unshift(review);
            }
          }
        });
        this.reviews = partial;
        return;
      }
      this.reviews = this.store.selected_shop.reviews;
      return;
    }
  },
  methods: {
    isFloat(num){
          return (Number(num) === num) && (num % 1 !== 0);
        },

    returnStar(type_star, rate){

          let num = Math.floor(rate);
          let is_float = this.isFloat(rate);

          switch(type_star){

            case 'whole':
              if(rate === 0){
                return 0;
              }
              return num;

            case 'half':
              
              return is_float ? 1 : 0;

            case 'none':

              return is_float ? (5-(num+1)) : 5-num;

          }
        },
    addReview(review){
      this.store.selected_shop.reviews.unshift(review);
      this.reviews = this.store.selected_shop.reviews.filter(e => e.review_type === 'shop');
    },
    formatDate(date){
      return new Date(date).toLocaleDateString();
    },
    formatTime(date){
      return new Date(date).toLocaleTimeString();
    },
    filteredReviews() {
      console.log('reviews: ',this.store.selected_shop.reviews);
        this.reviews = this.store.selected_shop.reviews.filter(e => e.review_type === 'shop');
    },
    check_can_rate(){
      const products = this.store.selected_shop.products;
      const user_id = this.store.currentUser_info.id;

      let can_rate = false;
      for(let product of products){
        
        can_rate = product.records.some(record => record.user_id === user_id && record.status === 'confirmed');

        if(can_rate){
          break;
        }
      }
      this.can_rate = can_rate;
      console.log('can_rate: ', can_rate);
      console.log('PRODUCTS: ', products);
    }
  },
  mounted(){
    this.check_can_rate();
    this.filteredReviews();
    console.log(this.store.currentUser_info);
  }
};
</script>

<style scoped>
.rate-btn{
  font-size: 12px; background-color: #D25E27; color: white; border: gray; border-radius: 5px;
}
.is_disabled{
  background-color: gray;
}
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
  overflow: hidden;
}

.user-avatar img{
  width: 100%;
  height: 100%;
  object-fit: contain;
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
