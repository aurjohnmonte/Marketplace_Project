<template>
  <div class="shop-products-container">
    <!-- Category Header -->
    <div class="shop-products-header-wrapper">
      <div class="shop-products-header">
        <label>KITCHENWARE</label>
        <label>MUSICAL INSTRUMENT</label>
        <label>DECORATIVE ITEMS</label>
        <label>GAMES</label>
        <label>OTHER</label>
        <label>MORE ITEMS</label>
      </div>
    </div>

    <!-- Filter/Search Section -->
    <div class="filter-search">
      <input placeholder="Search product name ..." />
      <select>
        <option>Latest</option>
        <option>Popular</option>
        <option>Trending</option>
      </select>
    </div>

    <!-- Product List -->
    <div class="buyer-browse-content">
      <div class="item-content" v-for="index in 6" :key="index">
        <div class="item-pic">
          <img src="../../../../images/images.jpg" alt="Product Image">
        </div>
        <div class="item-info" @click="goProduct">
          <div class="item-rate">
            <img src="../../../../images/star.png" class="star-rate" v-for="turn in rate.start" :key="turn">
            <img src="../../../../images/half-star.png" class="star-rate" v-if="rate.half">
            <img src="../../../../images/no-star.png" class="star-rate" v-for="turn in rate.no_star" :key="turn">
            <label>(7.5)</label>
          </div>
          <div class="item-comment">
            <label>99+ Comments</label>
          </div>
          <div class="item-shopname">
            <label>Soysoy's Furniture cheap</label>
          </div>
          <div class="item-name">
            <label>Wooden Chair</label>
          </div>
          <div class="item-price">
            <label>PHP 700.00</label>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useDataStore } from '../../../stores/dataStore';
export default {
  data() {
    return {
      rate: {
        start: 3,
        half: true,
        no_star: 1
      },
      product: {
        name: "Wood Bed neh soysoy men",
        description: "Just a bed that made of wood",
        rate: {
          star: 3,
          half: true,
          no_star: 1
        }
      },
    };
  },
  methods: {
    goProduct(){
        const store = useDataStore();
        store.setSelectedProduct(this.product);

        console.log("hello");
        this.$router.push({name: "BuyerProduct", 
                           params: {id: 1},
                           query: {}});
      },
  }
};
</script>

<style scoped>
.shop-products-container {
  width: 100%;
  display: flex;
  flex-direction: column;
}

.shop-products-header-wrapper {
  overflow-x: auto;
  white-space: nowrap;
}

.shop-products-header {
  display: flex;
  gap: 25px;
  padding: 20px;
  background-color: white;
  border-top: 10px solid gray;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  width: max-content;
}

.shop-products-header label {
  font-size: 12px;
  white-space: nowrap;
  text-decoration: underline;
}

.filter-search {
  background-color: white;
  padding: 15px 20px;
  display: flex;
  gap: 12px;
  align-items: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border-bottom: 1px solid gray;
}

.filter-search input,
.filter-search select {
  font-size: 12px;
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  outline: none;
}

.buyer-browse-content {
  display: grid;
  grid-template-columns: repeat(2, 10rem);
  gap: 10px;
  padding: 20px;
  background-color: #f7f7f7;
}

.item-content {
  display: flex;
  flex-direction: column;
  border-radius: 10px;
  background-color: white;
  padding: 5px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: transform 0.2s ease;
}

.item-pic {
  width: 100%;
  height: 150px;
  overflow: hidden;
  border-radius: 8px;
}

.item-pic img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-info {
  display: flex;
  flex-direction: column;
  gap: 5px;
  margin-top: 10px;
  font-size: 12px;
}

.item-rate,
.item-comment,
.item-shopname,
.item-name,
.item-price {
  display: flex;
  align-items: center;
  font-size: 12px;
}

.item-rate label,
.item-comment label,
.item-shopname label,
.item-name label,
.item-price label {
  font-size: 12px;
}

.item-name {
  font-weight: bold;
  color: #5c5c5c;
}

.item-price {
  color: #d25e27;
  font-weight: bold;
}

.star-rate {
  width: 15px;
  height: 15px;
  margin-right: 2px;
}
</style>