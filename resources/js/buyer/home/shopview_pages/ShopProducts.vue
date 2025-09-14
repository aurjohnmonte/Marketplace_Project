<template>
  <div class="shop-products-container">
    <!-- Category Header -->
    <div class="shop-products-header-wrapper">
      <div class="shop-products-header">
        <label :style="{color: returnColor('all')}" @click="goCategory('all')">ALL</label>
        <label :style="{color: returnColor(c)}" v-for="c in category" :key="c" @click="goCategory(c)">{{ c.toUpperCase() }}</label>
      </div>
    </div>

    <!-- Filter/Search Section -->
    <div class="filter-search">
      <input placeholder="Search product name ..." v-model="search_filter.text"/>
      <select v-model="search_filter.filter">
        <option value="" disabled>Filter</option>
        <option value="latest">Latest</option>
        <option value="popular">Popular</option>
      </select>
    </div>

    <!-- Product List -->
    <div class="buyer-browse-content">
      <div class="item-content" v-for="(product, index) in products" :key="index">
        <div class="item-pic">
          <img :src="'/'+product.photos[0].filename" alt="Product Image">
        </div>
        <div class="item-info" @click="goProduct(product)">
          <div class="item-rate">
            <img src="../../../../images/star.png" class="star-rate" v-for="turn in returnStar('whole',product.overall_rate)" :key="turn">
            <img src="../../../../images/half-star.png" class="star-rate" v-for="turn in returnStar('half',product.overall_rate)" :key="turn">
            <img src="../../../../images/no-star.png" class="star-rate" v-for="turn in returnStar('none',product.overall_rate)" :key="turn">
            <label>({{ product.overall_rate }})</label>
          </div>
          <div class="item-comment">
            <label>{{ product.reviews.length }} Reviews</label>
          </div>
          <div class="item-shopname">
            <label>{{ shop.name }}</label>
          </div>
          <div class="item-name">
            <label>{{ product.name }}</label>
          </div>
          <div class="item-price">
            <label>PHP {{ product.price }}</label>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useDataStore } from '../../../stores/dataStore';
export default {
  watch: {

    'search_filter.filter': function (newval){
      
      this.products = this.store.selected_shop.products;

      if(this.products.length > 0){
        this.haveSearchContent(newval, 'filter');
        return;
      }

    },

    'search_filter.category': function (newval){

      this.search_content = [];
      this.products = [];

      if(newval === 'all'){
        this.products = this.store.selected_shop.products;
        this.search_content = this.store.selected_shop.products;
        this.search_filter.text = '';
        this.search_filter.filter = 'latest';
        return;
      }
      
      let partial = [];
      this.store.selected_shop.products.forEach(product => {

        if(product.category === newval){
          partial.push(product);
        }
      });
      this.products = partial;
      this.search_content = partial;
      this.search_filter.text = '';
      this.search_filter.filter = 'latest';
    },

    'search_filter.text': function (newval){

      if(this.products.length > 0){
        this.haveSearchContent(newval, 'text');
        return;
      }

      if(newval === ''){
        this.products = this.search_content;
        return;
      }

      let partial = [];

      this.store.selected_shop.products.forEach(product => {

        if(product.name.toLowerCase().includes(newval.toLowerCase())){
          partial.unshift(product);
        }

        this.products = partial;
      });
    }
  },
  data() {
    return {
      store: useDataStore(),
      search_content: [],
      rate: {
        start: 3,
        half: true,
        no_star: 1
      },
      search_filter: {
        category: '',
        filter: '',
        text: '',
      },
      category: [],
      shop: null,
      products: null,
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
    haveSearchContent(newval, type){
      console.log('newval: ', newval);
      if(this.products.length > 0){
        console.log('here')
        let partial = [];
        if(type === 'text'){
          this.products.forEach(content => {

            if(content.name.toLowerCase().includes(newval.toLowerCase())){
              partial.unshift(content);
            }
          });
        }
        else if(type === "filter"){

          partial = this.products;

          if(newval === 'latest'){

            console.log('neh agi')

            console.log('products: ', this.products);


            let swapped;

            do {
              swapped = false;
              console.log('partial: ', partial);

              for (let i = 0; i < partial.length - 1; i++) {
                console.log('1')
                if (partial[i].id < partial[i + 1].id) {

                  let temp = partial[i];
                  partial[i] = partial[i + 1];
                  partial[i + 1] = temp;

                  swapped = true;
                }
              }

            } while (swapped);

            this.products = partial;

          }
          else{
            let swapped;

            do {
              swapped = false;
              console.log('partial: ', partial);

              for (let i = 0; i < partial.length - 1; i++) {
                console.log('1')
                if (partial[i].overall_rate < partial[i + 1].overall_rate) {
                  // ✅ Proper swap
                  let temp = partial[i];
                  partial[i] = partial[i + 1];
                  partial[i + 1] = temp;

                  swapped = true;
                }
              }

            } while (swapped);
            this.products = partial;
          }
        }

      }
    },
    returnColor(c){
      if(c === this.search_filter.category){
        return "#d25e27";
      }
    },
    goCategory(category){
      this.search_filter.category = category;
    },
    async goProduct(product){

      console.log('product neh: ', product);
      this.store.setSelectedProduct(product);
      this.$router.push({name: 'BuyerProduct', params: {id: product.id}});
    },
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
  },
  mounted(){
    console.log('shop: ', this.store.selected_shop);
    this.products = this.store.selected_shop.products;
    console.log('products: ', this.products);
    this.shop = this.store.selected_shop;

    this.products.forEach(p => {

      if(!this.category.includes(p.category)){
        this.category.push(p.category);
      }
    });

    console.log('cate: ', this.category);
    this.search_filter.filter = "latest";
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
  width: 100%;
  display: flex;
  gap: 25px;
  padding: 20px;
  background-color: white;
  border-top: 10px solid gray;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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

@media (min-width: 768px){
  .buyer-browse-content {
    grid-template-columns: repeat(7, 10rem);
    gap: 25px;
  }
}
</style>