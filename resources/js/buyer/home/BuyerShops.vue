<template>
  <div class="buyer-browse-maincontainer">
    <div style="padding: 20px;" class="back-header">
        <img src="../../../images/left-arrows.png" style="width: 30px; height: 30px; cursor: pointer" @click="goPrevious">
        <label style="font-weight: bolder;">BROWSE</label>
    </div>
    <label style="font-weight: bolder; padding-left: 20px; padding-bottom: 10px;">{{ $route.params.name}}s</label>
    <div class="filter-search-browse">
        <form @submit.prevent="returnSearchShops">
          <select v-model="search_info.category">
              <option value="" disabled>Category</option>
              <option value="Any">Any</option>
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
          <input placeholder="Search product name ..." v-model="search_text">
        </form>
        <img src="../../../images/cancel (1).png" style="width: 10px; height: 10px; margin-left: 5px;">
    </div>
    <div class="buyer-browse-header">
        <label class="popular-text">{{ $route.params.filter }}</label>
        <label class="category-text">{{ $route.params.category }}</label>
    </div>
    <div v-if="loading" class="loading-content">
        <img src="../../../images/kOnzy.gif"/>
    </div>
    <div class="buyer-browse-content" v-else>
        <div class="item-content" v-for="shop in result" :key="shop">
          <div class="option-icon">
            <img src="../../../images/location.png" @click="goLocation(parseFloat(shop.latitude), parseFloat(shop.longitude))">
            <img src="../../../images/send.png" @click="goMessage(shop.user_id)">
          </div>
          <div class="item-pic">
            <img :src="'/' + shop.profile_photo" @click="goShop(shop.id)">
          </div>
          <div class="item-info" @click="goShop(shop.id)">
            <div class="item-rate">
              <img src="../../../images/star.png" class="star-rate" v-for="turn in returnStar('whole',shop.overall_rate)" :key="turn">
              <img src="../../../images/half-star.png" class="star-rate" v-for="turn in returnStar('half',shop.overall_rate)" :key="turn">
              <img src="../../../images/no-star.png" class="star-rate" v-for="turn in returnStar('none',shop.overall_rate)" :key="turn">
              <label>({{ shop.overall_rate }})</label>
            </div>
            <div class="item-comment">
              <label>99+ Reviews</label>
            </div>
            <div class="item-comment">
              <label>{{ shop.total_followers }} Follower/s</label>
            </div>
            <div class="item-shopname">
              <label>{{ shop.name }}</label>
            </div>
          </div>
        </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore';
export default {
    data(){
        return{
            loading: true,
            product: {
              name: "Wood Bed",
              description: "Just a bed that made of wood",
              rate: {
                star: 3,
                half: true,
                no_star: 1
              }
            },
            search_info: {
                name: "Product",
                category: "Any",
                filter: "Popular"
            },
            search_text: "",
            rate: {
                    start: 3,
                    half: true,
                    no_star: 1,
                },
            result: [],
        }
    },
    methods: {
      goLocation(lat, long){
      console.log(lat + " " + long);
      const store = useDataStore();
      store.setSelectedCoordinate(lat, long);
      this.$router.push({name: 'BuyerMap'});
    },
      goShop(id){
        this.$router.push({name: "ShopAbout", params: {id: id}});
      },
      goMessage(user_id){
        console.log("go message");
        this.$router.push({name: "BuyerConversation", params: {"id": user_id}});
      },
        goProduct(product){
            const store = useDataStore();
            store.setSelectedProduct(product);

            console.log("hello");
            this.$router.push({name: "BuyerShops", 
                            params: {id: 1},
                            query: {}});
        },
        initialize_params(){
            this.search_info.name = this.$route.params.name;
            this.search_info.category = this.$route.params.category;
            this.search_info.filter = this.$route.params.filter;

            this.search_text = this.$route.query.search_text;
        },
        goPrevious(){ 
            this.$router.go(-1);
        },
        isFloat(num){
          return (Number(num) === num) && (num % 1 !== 0);
        },
        async returnSearchShops(){
            this.loading = true;
            const data = new FormData();
            data.append("shop_info", JSON.stringify(this.search_info));
            data.append("search_text", this.search_text);
            const res = await axios.post("/buyer/search-shop", data);

            console.log(res.data.message);

            this.result = res.data.message;

            this.loading = false;
        },
        returnStar(type_star, rate){

          let num = Math.floor(rate);
          let is_float = this.isFloat(rate);

          console.log('num: ', num);

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
        }
    },
    mounted(){
        this.$emit("changepathtext", "home");
        window.scrollTo(0, 0);
        this.initialize_params();
        this.returnSearchShops();
    }
}
</script>

<style scoped>
.loading-content img{
    width: 100px;
    height: 100px;
}
.loading-content{
    width: 100%;
    height: auto;
    display: flex;
    align-items: center;
    justify-content: center;
}
.back-header{
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
}
.item-pic{
  width: 150px;
  height: 150px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}
.item-pic img{
  object-fit: cover;
  width: 100%;
  height: 100%;
}
.item-info{
  height: auto;
  gap: 5px;
  display: flex;
  flex-direction: column;
  margin-top: 10px;
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
.item-shopname{
  font-size: 12px;
  color: rgb(34, 34, 34);
}
.item-comment{
  color: gray;
  font-size: 12px;
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
.buyer-browse-content{
    display: grid;
    grid-template-columns: repeat(2, 8.5rem);
    gap: 30px;
    padding-left: 20px;
}
.filter-search-browse form{
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 5px;
}
.filter-search-browse select{
    padding: 5px;
    font-size: 10px;
    width: 60px;
    border: 1px solid #D25E27;
}
.filter-search-browse input{
    padding: 4px;
    font-size: 12px;
    padding-left: 10px;
    border: 1px solid #D25E27;
    width: 100%;
}
.filter-search-browse{
    display: flex;
    flex-direction: row;
    align-items: center;
    padding-left: 20px; padding-right: 20px; padding-top: 5px; padding-bottom: 5px;
    gap: 5px;
    border-bottom: 1px solid rgb(188, 188, 188);
    padding-bottom: 20px;
}
.popular-text{
    font-size: 15px;
    font-weight: bolder;
    color: rgb(40, 40, 40);
}
.category-text{
    font-size: 10px;
    color: rgb(131, 131, 131);
}
.buyer-browse-maincontainer{
    padding-top: 15%;
    width: 100%;
    height: auto;
    position: relative;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
}
.buyer-browse-header{
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 20px;
    gap: 5px;
}
</style>