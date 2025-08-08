<template>
  <div class="following-shops-container">
    <div class="header">
      <label>FOLLOWING SHOPS</label>
    </div>

    <div class="search-bar">
      <input type="text" placeholder="Search" v-model="searchQuery" />
      <span v-if="searchQuery" class="clear-btn" @click="searchQuery = ''">✕</span>
    </div>
    <select v-model="category" class="category-select">
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
    <div class="overlay" v-if="!follows">
      <img src="../../../images/kOnzy.gif">
    </div>
    <template v-else>
      <div style="margin-bottom: 30px; font-size: 14px;">
        <label>{{ follows.length }} Shops followed</label>
      </div>
      <div class="shop-list-following">
        <div class="shop-list" v-if="follows">
          <div class="shop-card" v-for="follow in follows" :key="follow.id" @click="goShop(follow.follows.shop.id)">
            <div class="shop-info">
              <img :src="'/'+follow.follows.shop.profile_photo" alt="shop avatar" class="shop-avatar" />
              <label class="shop-name">{{ follow.follows.shop.name }}</label>
            </div>
            <div class="shop-actions">
              <img src="../../../images/location.png" @click.stop="goLocation(parseFloat(follow.follows.shop.latitude), parseFloat(follow.follows.shop.longitude))">
              <img src="../../../images/send.png" @click.stop="goMessage(follow.follows.id)">
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import { useDataStore } from '../../stores/dataStore';
import axios from 'axios';
export default {
  data() {
    return {
      searchQuery: '',
      store: useDataStore(),
      follows: null,
      orig_data: [],
      category: "",
      shops: [
        {
          id: 1,
          name: 'Shop Value',
          image: 'https://via.placeholder.com/40'
        },
      ],
    };
  },
  watch: {
    searchQuery(newVal){
      let partial = [];

      this.orig_data.forEach(data => {
        
        if(data.follows.shop.name.includes(newVal)){
          partial.unshift(data);
        }
      })
      this.follows = partial;
    },

    category(newVal){

      if(newVal === "Any"){
        this.follows = this.orig_data;
        return;
      }

      let partial = [];


      this.orig_data.forEach(data => {
        let category = JSON.parse(data.follows.shop.category);
        if(category.includes(newVal)){
          partial.unshift(data);
        }
      })
      this.follows = partial;
    }
  },
  methods: {
    goShop(id){
      this.$router.push({name: "ShopAbout", params: {id: id}});
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

    async returnFollowing(){

      const res = await axios.get('/buyer/return-following', {
        params: {
          id: this.store.currentUser_info.id
        }
      });
      console.log(res.data.message);
      this.follows = res.data.message;
      this.orig_data = res.data.message;
    }
  },
  async mounted(){
    let path = this.$route.path;
    let new_path = path.slice(7);
    this.$emit("changepathtext", new_path);

    await this.returnFollowing();
  },
  computed: {
    filteredShops() {
      return this.shops.filter((shop) =>
        shop.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
  },
};
</script>

<style scoped>
.category-select{
  padding: 5px;
  border: 1px solid rgba(0, 0, 0, 0.457);
  color: black;
  font-size: 10px;
  margin-bottom: 10px;
  border-radius: 5px;
}
.overlay img{
    width: 100px;
    height: 100px;
}
.overlay{
  padding-top: 50px;
    width: 100%;
    height: auto;
    display: flex;
    align-items: center;
    justify-content: center;
}
.following-shops-container {
  padding: 20px;
  padding-top: 80px;
  font-family: Arial, sans-serif;
  background-color: #fdfdfd;
}

.header label {
  font-weight: bold;
  font-size: 14px;
  color: #111;
}

.search-bar {
  margin: 10px 0 10px;
  display: flex;
  align-items: center;
  position: relative;
}

.search-bar input {
  width: 100%;
  padding: 8px 30px 8px 10px;
  border-radius: 20px;
  border: 1px solid #ccc;
  font-size: 14px;
}

.clear-btn {
  position: absolute;
  right: 10px;
  cursor: pointer;
  font-size: 14px;
  color: #888;
}
.shop-list-following{
  overflow: scroll;
  height: 400px;
}
.shop-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.shop-card {
  background: #fff;
  border-radius: 8px;
  padding: 10px 15px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.shop-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.shop-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #ccc;
}

.shop-name {
  font-size: 14px;
  font-weight: 600;
  color: #222;
}
.shop-actions {
  display: flex;
  flex-direction: row;
  gap: 10px;
}
.shop-actions img {
  width: 20px;
  height: 20px;
}

.material-icons {
  font-family: 'Material Icons';
}
</style>
