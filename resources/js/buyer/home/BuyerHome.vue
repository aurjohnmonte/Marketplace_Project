<template>
  <div class="buyer-home">
    <div class="buyer-content1">
      <h2>TIMBERSHOPPEE</h2>
    </div>
    <div class="filter-search-container">
      <form @submit.prevent="goSubmitSearch">
        <div class="header-filter-search">
          <label class="filter-header-label">What are you looking for?</label>
          <label class="more-filter" @click="hide_more_filter = !hide_more_filter">More Filter</label>
        </div><br>
        <div class="filter-search">
          <select v-model="search_info.name">
            <option value="Product">Product name</option>
            <option value="Shop">Shop name</option>
          </select>
          <input placeholder="Search ..." v-model="search_text">
          <!-- <img src="../../../images/cancel (1).png" style="width: 10px; height: 10px; padding-left: 20px;" > -->
        </div><br>
            
        <div class="more-filter-option" v-if="hide_more_filter">
          <select v-model="search_info.category">
            <option value="" disabled>Category</option>
            <option value="Any" disabled>Any</option>
            <option value="Furniture">Furniture</option>
            <option value="Kitchenware">Kitchenware</option>
            <option value="Musical Instrument">Musical Instrument</option>
            <option value="Decorative Items">Decorative Items</option>
            <option value="Games">Games</option>
            <option value="Outdoor Decor">Outdoor Decor</option>
            <option value="Home Decor">Home Decor</option>
          </select>
          <select v-model="search_info.filter">
            <option value="" disabled>Filter</option>
            <option value="Popular">Popular</option>
            <option value="New">New</option>
          </select>
        </div>
      </form>
    </div>

    <div class="search-result">
      <div>
        <label style="color: rgb(92, 92, 92); font-size: 15px; font-weight: bolder;">Nearby Shop/s</label>
      </div>
      <template v-if="store.nearbyShops">
        <div class="list-of-nearbyshops" v-for="shop in store.nearbyShops" :key="shop" @click="goShop(shop.id)">
          <div class="nearbyshops-leftside">
            <img :src="'/'+shop.profile_photo">
            <div style="display: flex; flex-direction: column;">
              <label style="font-size: 15px; color: rgb(92, 92, 92);">{{ shop.name }}</label>
              <label style="font-size: 10px; color: rgb(92, 92, 92);">{{ shop.address }}</label>
            </div>
          </div>
          <div class="nearbyshops-leftside">
            <img src="../../../images/location.png" @click.stop="goLocation(parseFloat(shop.latitude), parseFloat(shop.longitude))">
            <img src="../../../images/send.png" @click.stop="goMessage(shop.user_id)">
          </div>
        </div>
      </template>
      <template v-else>
        <label style="color: red; font-size: 12px;">NO NEARBY SHOP/S WITHIN 1 KM</label>
      </template>
    </div>

    <div class="popular-content">
      <div class="popular-content-header">
        <div class="header1">
          <label class="popular">Popular</label>
          <label class="popular-desc">Popular ranked. Buy it before its too late.</label>
        </div>
        <div class="header2" @click="goSearch(search_info={name:'Product',category:'Any',filter:'Popular'})">
          <label class="view-all">VIEW ALL</label>
        </div>
      </div>

      <div class="header1" style="margin-bottom: 10px;">
        <label class="popular" style="font-size: 12px;">All Product</label>
      </div>

      <div class="content">
        <template v-if="!loading_popular">
          <div class="item-content" v-for="(product, index) in popular" :key="index">
            <div class="option-icon">
              <img src="../../../images/location.png" @click="goLocation(parseFloat(product.shop.latitude), parseFloat(product.shop.longitude))">
              <img src="../../../images/send.png" @click="goMessage(product.shop.user_id, product)">
            </div>
            <div class="item-pic">
              <img :src="'/'+product.photos[0].filename" @click="goProduct(product)">
            </div>
            <div class="item-info">
              <div class="item-rate">
                <img src="../../../images/star.png" class="star-rate" v-for="turn in returnStar('whole',product.overall_rate)" :key="turn">
                <img src="../../../images/half-star.png" class="star-rate" v-for="turn in returnStar('half',product.overall_rate)" :key="turn">
                <img src="../../../images/no-star.png" class="star-rate" v-for="turn in returnStar('none',product.overall_rate)" :key="turn">
                <label>{{ product.overall_rate }}</label>
              </div>
              <div class="item-comment">
                <label>{{ product.reviews.length }} Reviews</label>
              </div>
              <div class="item-shopname" @click="goShop(product.shop.id)">
                <label>{{ product.shop.name }}</label>
              </div>
              <div class="item-name">
                <label style="text-decoration: underline;" @click="goProduct(product)">{{ product.name }}</label>
              </div>
              <div class="item-price">
                <label>PHP {{ product.price }}</label>
              </div>
            </div>
          </div>
        </template>
        <template v-else>
          <div class="overlay">
            <img src="../../../images/kOnzy.gif">
          </div>
        </template>
      </div>

      <div class="header1" style="margin-bottom: 10px; margin-top: 20px;">
        <label class="popular" style="font-size: 12px;">Followed Products</label>
      </div>

      <div class="content">
        <template v-if="!loading_popular">
          <div class="item-content" v-for="(product, index) in popular" :key="index">
            <div class="option-icon">
              <img src="../../../images/location.png" @click="goLocation(parseFloat(product.shop.latitude), parseFloat(product.shop.longitude))">
              <img src="../../../images/send.png" @click="goMessage(product.shop.user_id, product)">
            </div>
            <div class="item-pic">
              <img :src="'/'+product.photos[0].filename" @click="goProduct(product)">
            </div>
            <div class="item-info">
              <div class="item-rate">
                <img src="../../../images/star.png" class="star-rate" v-for="turn in returnStar('whole',product.overall_rate)" :key="turn">
                <img src="../../../images/half-star.png" class="star-rate" v-for="turn in returnStar('half',product.overall_rate)" :key="turn">
                <img src="../../../images/no-star.png" class="star-rate" v-for="turn in returnStar('none',product.overall_rate)" :key="turn">
                <label>{{ product.overall_rate }}</label>
              </div>
              <div class="item-comment">
                <label>{{ product.reviews.length }} Reviews</label>
              </div>
              <div class="item-shopname" @click="goShop(product.shop.id)">
                <label>{{ product.shop.name }}</label>
              </div>
              <div class="item-name">
                <label style="text-decoration: underline;" @click="goProduct(product)">{{ product.name }}</label>
              </div>
              <div class="item-price">
                <label>PHP {{ product.price }}</label>
              </div>
            </div>
          </div>
        </template>
        <template v-else>
          <div class="overlay">
            <img src="../../../images/kOnzy.gif">
          </div>
        </template>
      </div>
    </div>

    <div class="popular-content">
      <div class="popular-content-header">
        <div class="header1">
          <label class="popular">New</label>
          <label class="popular-desc">New product have been added. Check it out!</label>
        </div>
        <div class="header2" @click="goSearch(search_info={name:'Product',category:'Any',filter:'New'})">
          <label class="view-all">VIEW ALL</label>
        </div>
      </div>
      <div class="range">
        <label>This 30 Days</label>
      </div>

      <div class="content">
        <div class="content" v-if="!loading_new">
          <div class="item-content" v-for="(product, index) in new_product" :key="index">
            <div class="option-icon">
              <img src="../../../images/location.png" @click="goLocation(parseFloat(product.shop.latitude), parseFloat(product.shop.longitude))">
              <img src="../../../images/send.png" @click="goMessage(product.shop.user_id, product)">
            </div>
            <div class="item-pic">
              <img :src="'/'+product.photos[0].filename" @click="goProduct(product)">
            </div>
            <div class="item-info">
              <div class="item-rate">
                <img src="../../../images/star.png" class="star-rate" v-for="turn in returnStar('whole',product.overall_rate)" :key="turn">
                <img src="../../../images/half-star.png" class="star-rate" v-for="turn in returnStar('half',product.overall_rate)" :key="turn">
                <img src="../../../images/no-star.png" class="star-rate" v-for="turn in returnStar('none',product.overall_rate)" :key="turn">
                <label>{{ product.overall_rate }}</label>
              </div>
              <div class="item-comment">
                <label>{{ product.reviews.length }} Reviews</label>
              </div>
              <div class="item-shopname" @click="goShop(product.shop.id)">
                <label>{{ product.shop.name }}</label>
              </div>
              <div class="item-name">
                <label style="text-decoration: underline;" @click="goProduct(product)">{{ product.name }}</label>
              </div>
              <div class="item-price">
                <label>PHP {{ product.price }}</label>
              </div>
            </div>
          </div>
        </div>
        <div class="overlay" v-else>
          <img src="../../../images/kOnzy.gif">
        </div>
      </div>
    </div><br><br><br>

    <div class="shop-header" style="padding-left: 20px;">
      <label style="font-size: 25px; font-weight: bolder;">SHOPS</label>
    </div>
    <div class="shop-container">

      <div>
        <div class="shop-content">
          <div class="shop-header">
            <label>Popular</label>
          </div>
          <div class="shop-group" v-for="(shop, indx) in shop_popular" :key="indx" @click="goShop(shop.id)">
            <div class="shop">
              <div class="shop-info">
                <label>{{ ++indx }}. </label>
                <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                  <div class="shop-image">
                    <img :src="'/'+shop.profile_photo">
                  </div>
                  <div style="font-size: 10px; display: flex; flex-direction: column; gap: 2px;">
                    <label style="font-size: 14px;">{{ shop.name }}</label>
                    <label style="font-weight: bolder;">{{shop.overall_rate}} ({{ shop.reviews.length }} reviews)</label>
                    <label>{{ shop.address }}</label>
                  </div>
                </div>
              </div>

              <div class="shop-button">
                <img src="../../../images/location.png" @click.stop="goLocation(parseFloat(shop.latitude), parseFloat(shop.longitude))">
                <img src="../../../images/send.png" @click.stop="goMessage(shop.user_id)">
              </div>
              
            </div>
          </div>
        </div><br><br>
      </div>
      
      <div>
        <div class="shop-content">
                  <div class="shop-header">
          <label>Latest</label>
        </div>
          <div class="shop-group" v-for="(shop, indx) in shop_new" :key="indx" @click="goShop(shop.id)">
            <div class="shop">
              <div class="shop-info">
                <label>1. </label>
                <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                  <div class="shop-image">
                    <img :src="'/'+shop.profile_photo">
                  </div>
                  <div style="font-size: 10px; display: flex; flex-direction: column; gap: 2px;">
                    <label style="font-size: 14px;">{{ shop.name }}</label>
                    <label style="font-weight: bolder;">{{ shop.overall_rate }} ({{ shop.reviews.length }} reviews)</label>
                    <label>{{ shop.address }}</label>
                  </div>
                </div>
              </div>

              <div class="shop-button">
                <img src="../../../images/location.png" @click.stop="goLocation(parseFloat(shop.latitude), parseFloat(shop.longitude))">
                <img src="../../../images/send.png" @click.stop="goMessage(shop.user_id)">
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { watch } from 'vue';
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore'

export default {
  data(){
    return{
      shop_popular: [],
      shop_new: [],
      loading_popular: true,
      loading_new: true,
      popular: [],
      new_product: [],
      store: useDataStore(),
      search_info: {
        name: "Product",
        category: "Any",
        filter: "Popular"
      },
      search_text: "",
      hide_more_filter: false,
      rate: {
        start: 3,
        half: true,
        no_star: 1,
      }
    }
  },
  methods: {
    async goProduct(product){

      this.store.setSelectedProduct(product);
      this.$router.push({name: 'BuyerProduct', params: {id: product.id}});
    },
    goLocation(lat, long){
      console.log(lat + " " + long);
      const store = useDataStore();
      store.setSelectedCoordinate(lat, long);
      this.$router.push({name: 'BuyerMap'});
    },
    goMessage(user_id, product = null){
        console.log("go message");
        
        if(product !== null){
          this.$router.push({name: "BuyerConversation",
                          params: {"id": user_id},
                          query: {
                            name: product.name,
                            photo: product.photos[0].filename,
                            product_id: product.id
                          }});
        }else{
          this.$router.push({name: "BuyerConversation", params: {"id": user_id}});
        }
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
    
    async returnShops(){

      const res = await axios.get('/return/shops/popular-new');
      this.shop_new = res.data.new;
      this.shop_popular = res.data.popular;
      console.log('shop new: ', this.shop_new);
      console.log('shop popular: ', this.shop_popular);
    },

    async returnProducts(){
      this.loading_new = true;
      this.loading_popular = true;

      const res = await axios.get('/buyer/return/products');
      console.log(res.data.popular);
      console.log(res.data.new);
      this.popular = res.data.popular;
      this.new_product = res.data.new;

      this.loading_new = false;
      this.loading_popular = false;
    },

    goSubmitSearch(){
      console.log(this.search_info);
      console.log(this.search_text);

      //this navigate to the BuyerBrowse component. This component is for product only. Browsing product only.
      if(this.search_info.name === "Product"){
        this.$router.push({name: "BuyerBrowse",
                         params: {
                          name: this.search_info.name,
                          category: this.search_info.category,
                          filter: this.search_info.filter
                         },
                         query: {
                          "search_text": this.search_text
                         }
                        });
        return;
      }

      //Otherwise browse only Shops.
      this.$router.push({name: "BuyerShops",
                         params: {
                          name: this.search_info.name,
                          category: this.search_info.category,
                          filter: this.search_info.filter
                         },
                         query: {
                          "search_text": this.search_text
                         }
      });
      return;
    },

    goSearch(info){
      console.log("info: ", info);
      if(info.name === "Product"){
        this.$router.push({name: "BuyerBrowse",
                         params: {
                          name: info.name,
                          category: info.category,
                          filter: info.filter
                         },
                         query: {
                          search_text: "",
                         }
                        });
      }
    },
    goShop(id){
      this.$router.push({name: "ShopAbout", params: {id: id}});
    }
  },
  async mounted(){
    let path = this.$route.path;
    let new_path = path.slice(7);
    this.$emit("changepathtext", new_path);
    window.scrollTo(0, 0);
    //return products (popular and new)
    await this.returnProducts();
    await this.returnShops();
    console.log('nearby: ', this.store.nearbyShops);
    
    watch(
      () => this.store.nearbyShops,
      (newshop) => {
        this.store.nearbyShops = newshop;
        console.log('nearby: ', this.store.nearbyShops)
      }
    )
  }
}
</script>

<style scoped>
.range{
  color: rgb(34, 34, 34);
  font-size: 12px;
  margin-bottom: 10px;
}
.shop-group:hover{
  background-color: rgba(0, 0, 0, 0.168);
}
.overlay img{
  width: 100px;
  height: 100px;
}
.overlay{
  padding-top: 20px;
  position: relative;
  width: 100%;
  height: auto;
  display: flex;
  align-items: center;
  justify-content: center;
}
.nearbyshops-leftside{
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
}
.nearbyshops-leftside img{
  width: 30px; 
  height: 30px;
  border-radius: 40px;
}
.list-of-nearbyshops{
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  padding-top: 20px;
}
.more-filter-option{
  display: flex;
  flex-direction: row;
  gap: 5px;
}
.more-filter-option select{
  border: 1px solid #D25E27;
  font-size: 11px;
  padding: 3px;
}
.header-filter-search{
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
}
.more-filter{
  font-size: 12px;
  color: rgb(91, 91, 91);
  text-decoration: underline;
  cursor: pointer;
}
.filter-header-label{
  font-size: 14px;
  font-weight: bolder;
  color: rgb(102, 102, 102);
}
.filter-search-container{
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.search-result{
  padding: 20px;
  border-top: 2px solid rgb(198, 198, 198);
  border-bottom: 2px solid rgb(198, 198, 198);
}
.filter-search {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 5px;
}
.filter-search input{
  width: 100%;
  padding: 4px;
  border: 1px solid #D25E27;
  border-radius: 5px;
}
.filter-search input::placeholder{
  font-size: 12px;
  padding-left: 5px;
}
.filter-search select{
  font-size: 10px;
  padding: 5px;
  height: 25px;
  border: 1px solid #D25E27;
  border-radius: 5px;
}
.shop-button{
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
}
.shop-button img{
  width: 30px;
  height: 30px;
}
.shop{
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  justify-content: space-between;
  width: 100%;
}
.shop-info{
  display: flex;
  flex-direction: row;
  align-items: center;
  color: rgb(92, 92, 92);
  gap: 10px;
}
.shop-image{
  width: 45px;
  height: 45px;
  overflow: hidden;
  background-color: black;
  border-radius: 50px;
}
.shop-image img{
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.shop-group{
  width: 100%;
  height: auto;
  display: flex;
  flex-direction: row;
  align-items: center;
}
.shop-content{
  gap: 20px;
  display: flex;
  flex-direction: column;
}
.shop-header{
  display: flex;
  flex-direction: column;
  color: rgb(92, 92, 92);
}
.shop-container{
  padding: 20px;
  display: flex;
  flex-direction: column;
}
.option-icon img{
  width: 30px;
  height: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  border-radius: 40px;
  background-color: white;
  cursor: pointer;
}
.option-icon{
  position: absolute;
  display: flex;
  flex-direction: row;
  gap: 5px;
}
.item-price{
  font-size: 12px;
  color: #D25E27;
  font-weight: bolder;
}
.item-shopname{
  text-decoration: underline;
  font-size: 12px;
  color: rgb(34, 34, 34);
}
.item-name{
  font-size: 14px;
  color: rgb(92, 92, 92);
  font-weight: bolder;
}
.item-comment{
  color: gray;
  font-size: 12px;
  text-decoration: underline;
}
.item-rate{
  display: flex;
  flex-direction: row;
  gap: 2px;
  align-items: center;
}
.item-rate label{
  font-size: 14px;
  color: gray;
  margin-left: 5px;
}
.star-rate{
  width: 15px;
  height: 15px;
}
.buyer-content1{
  width: 100%;
  height: 200px;
  display: flex;
  background-color: gray;
  align-items: end;
  box-sizing: border-box;
  background-image: url('../../../images/furniture.jpg');
  background-size: cover;
  background-repeat: no-repeat;
  
}
.buyer-content1 h2{
  color: white;
  font-weight: bolder;
  margin-left: 20px;
  margin-bottom: 50px;
}
.buyer-home{
  z-index: 10;
  margin-bottom: 50px;
}
.popular-content{
  padding: 20px;
  padding-top: 10px;
}
.popular-content-header{
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  font-size: 15px;
  margin-bottom: 10px;
}
.popular-content-header .header1{
  display: flex;
  flex-direction: column;
}
.popular-desc{
  font-size: 10px;
  color: gray;
}
.view-all{
  font-size: 10px;
  color: rgb(57, 57, 57);
  text-decoration: underline;
}
.header1 .popular{
  font-size: 20px;
  font-weight: bolder;
  color: rgb(92, 92, 92);
}

.popular-content .content{
  display: flex;
  flex-direction: row;
  align-items: center;
  overflow-x: auto;      /* scroll or auto */
  white-space: nowrap;   /* helps prevent wrapping */
  scroll-behavior: smooth;
  width: 100%;
  gap: 10px;
  height: auto;
}
.item-content{
  flex: 0 0 auto;           /* Prevent shrinking */
  height: auto;
  margin-right: 10px;
  display: flex;
  flex-direction: column;
  border-radius: 10px;
  position: relative;
}
.item-pic{
  width: 200px;
  height: 200px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}
.item-pic img{
  object-fit: cover;
  width: 100%;
  height: 100%;
}
.item-info{
  width: 200px;
  height: auto;
  gap: 5px;
  display: flex;
  flex-direction: column;
  margin-top: 10px;
}
@media screen and (min-width: 1024px){
  .shop-container{
    padding: 20px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 200px;
  }
}
</style>