<template>
  <div class="seller-profile">

    <teleport to="body">
      <Notify :message="message" v-if="message !== '' || message"/>
    </teleport>

    <template v-if="is_loading">
      <div class="overlay">
        <img src="../../../images/kOnzy.gif">
      </div>
    </template>
    <template v-else>
      <div class="seller-profile-header">
          <div class="back-btn">
            <img src="../../../images/left-arrows.png" @click="goPrevious" class="left-arrow">
            <label class="shop-title">SHOPS</label>
          </div>
          <!-- <img src="../../../images/more (2).png" style=" cursor: pointer; width: 20px; height: 20px; border: 1px solid gray; padding-left: 10px; padding-right: 10px; border-radius: 10px;"> -->
      </div>
      
      <div class="seller-info">
          <img :src="returncoverphoto" alt="Cover Photo" class="cover_photo-img">
          <div class="cover-section">
              <div class="settings-icon"></div>
                  <div class="profile-info">
                      <div class="option-icon">
                          <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                            <img src="../../../images/location.png" class="option-img" @click="goLocation(parseFloat(shop.latitude), parseFloat(shop.longitude))">
                            <img src="../../../images/send.png" class="option-img" @click="goMessage(shop.user_id, product)">
                          </div>
                          <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                            <!-- <img src="../../../images/star (2).png" class="option-rate" @click="$router.push({name: 'ShopReview', params: {id: shop.id}})"> -->
                            <img :src="returnPath" class="option-img" @click="followShop(shop.user_id)">
                          </div>
                      </div>
                      <div class="profile-circle">
                          <img :src="'/'+shop.profile_photo">
                      </div>
                      <h2>{{ shop.name }}</h2>
                      <div class="stats">
                      <span @click="$router.push({name: 'ShopReview', params: {id: shop.id}})">
                        <div style="display: flex; flex-direction: row; align-items: center; gap: 2px; position: relative;">
                          <label>{{ shop.overall_rate }}</label>
                          <img src="../../../images/star (2).png" style="width: 15px; height: 15px; position: relative;">
                          <label style="font-size: 12px;">({{ countShopReviews(shop.reviews) }} review/s)</label>
                        </div>
                      </span>
                      <span>{{ shop.user.followers.length }} followers</span>
                  </div>
              </div>
          </div>
      </div>
      

      <div class="tabs">
        <router-link :to="{name: 'ShopAbout', params: {shopname: $route.params.shopname}}" class="tab" active-class="active">ABOUT</router-link>
        <router-link :to="{name: 'ShopProducts', params: {shopname: $route.params.shopname}}" class="tab" active-class="active">PRODUCTS</router-link>
        <!-- <router-link :to="{name: 'ShopReview', params: {shopname: $route.params.shopname}}" class="tab" active-class="active">REVIEW</router-link> -->
      </div>

      <div class="content">
        <router-view />
      </div>
    </template>
  </div>
</template>
<script>
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore';
import Notify from '../notify-modal/Notify.vue';

export default{
    components: {
      Notify
    },
    data(){
      return{
        is_loading: true,
        is_friend: false,
        message: '',
        shop: null,
      }
    },
    computed: {
      returncoverphoto(){

        return this.shop.cover_photo ? `/${this.shop.cover_photo}` : '';
      },
      returnPath(){
        let followers = this.shop.user.followers;
        if(followers.length > 0){
          const store = useDataStore();
          console.log('followers: ', followers);
          console.log('current user: ', store.currentUser_info);
          for(let follower of followers){
            console.log('f: ', follower);
            if(follower.follower_id === store.currentUser_info.id){
              this.is_friend = true;
              return "/images/user2.png";
            }
          }
        }
        this.is_friend = false;
        return "/images/user3.png";
      }
    },
    methods: {
      countShopReviews(reviews){

        if(reviews.length > 0){
          let shop_reviews = reviews.filter(r => r.review_type === 'shop');

          return shop_reviews.length;
        }

        return 0;
      },
      async followShop(id){

        if(this.is_friend){
          return;
        }

        const store = useDataStore();
        console.log('id: ', id);
        const data = new FormData();
        data.append('user_id', id);
        data.append('id', store.currentUser_info.id);

        const res = await axios.post('/buyer/follow-shop', data);

        this.message = "You have successfully followed this shop";

        setTimeout(() => {
          this.message = "";
        }, 3000);
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
        goPrevious(){
            this.$router.go(-1);
        },
        async returnShopInfo(id){
          try{
            this.is_loading = true;
            const data = new FormData();
            data.append('id', id);

            const res = await axios.post('/return/shop-info', data);
            console.log('shop: ',res.data.shop);
            
            if(res.data.shop.length <= 0){
              window.alert("No shop have found");
            }
            else{
              const store = useDataStore();
              this.shop = res.data.shop[0];
              store.setSelectedShop(this.shop);
            }
            
            this.is_loading = false;
          }
          catch(error){
            window.alert('something went wrong please reload the page');
          }
        }
    },
    async mounted(){
        window.scrollTo(0,0);
        this.$emit("changepathtext", "home");
        this.$router.push({name: 'ShopAbout', params: {shopname: this.$route.params.shopname}})
        let id = this.$route.params.id;
        await this.returnShopInfo(id);
    }
}
</script>

<style scoped>
.shop-title{
  font-weight: bolder; font-size: 18px;
}
.back-btn{
  display: flex; flex-direction: row; align-items: center; gap: 10px;
}
.cover_photo-img{
  background-color: rgb(192, 192, 192);
}
.overlay{
  position: fixed;
  width: 100%;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.48);
  top: 0;
  left: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}
.overlay img{
  width: 50px;
  height: 50px;
}
.follow-action{
  font-size: 12px;
  color: rgb(255, 255, 255);
}
.left-arrow{
    width: 25px;
    height: 25px;
}
.seller-profile-header{
    position: relative;
    padding-left: 20px;
    padding-right: 20px;
    padding-top: 10px;
    padding-bottom: 10px;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
    border-bottom: 10px solid gray;
    justify-content: space-between
}
.option-icon{

    display: flex;
    flex-direction: row;
    align-items: center;
    position: relative;
    top: 0;
    left: 0;
    justify-content: space-between;
}
.option-icon .option-img{
    width: 30px;
    height: 30px;
    background-color: white;
    border-radius: 50%;
    position: relative;
}
.option-icon .option-rate{
    width: 20px;
    height: 20px;
    background-color: white;
    border-radius: 50%;
    padding: 5px;
    position: relative;
    border: 1px solid gray;
}
.seller-info{
    position: relative;
}
.seller-profile {
  font-family: 'Segoe UI', sans-serif;
  color: #323232;
  background-color: #f5f5f5;
  min-height: 100vh;
  padding-top: 60px;
  position: relative;
}

.cover-section {
  color: white;
  padding: 20px;
  padding-bottom: 10px;
  position: relative;
  text-align: center;
  z-index: 20;
}

.seller-info img{
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 10;
}

.settings-icon {
  position: absolute;
  top: 20px;
  right: 20px;
  font-size: 24px;
  cursor: pointer;
}

.profile-circle {
  width: 120px;
  height: 120px;
  background-color: white;
  border: 1px solid white;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto;
  font-weight: bold;
  text-align: center;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  overflow: hidden;
  position: relative;
}
.profile-circle img{
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
}

.profile-info{
    position: relative;
}

.profile-info h2 {
  margin-top: 10px;
  font-size: 15px;
  font-weight: bold;
}

.stats {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  padding: 5px;
  font-weight: bolder;
  font-size: 14px;
  background-color: rgba(0, 0, 0, 0.218);
  border-radius: 20px;
  padding-left: 20px;
  padding-right: 20px;
}

.tabs {
  display: flex;
  justify-content: space-between;
  background-color: #ffffff;
  padding-left: 25%;
  padding-right: 25%;
  padding-top: 5px;
  padding-bottom: 5px;
  font-weight: bold;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  position: relative;
  top: 0;
}

.tab {
  color: #323232;
  text-decoration: none;
  padding: 10px;
  border-bottom: 3px solid transparent;
  font-size: 10px;
}

.tab:hover,
.tab.active {
  border-bottom: 3px solid #D25E27;
}

.content {
  background-color: rgb(235, 235, 235);
  min-height: 300px;
}

@media (min-width: 768px){
  .seller-profile {
    font-family: 'Segoe UI', sans-serif;
    color: #323232;
    background-color: #f5f5f5;
    min-height: 100vh;
    padding-top: 60px;
    position: relative;
    margin: 100px;
    margin-top: 10px;
    box-sizing: border-box;
  }
  .shop-title{
    font-weight: bolder; font-size: 25px;
  }
  .back-btn{
    display: flex; flex-direction: row; align-items: center; gap: 20px;
  }
  .back-btn img{
    width: 50px;
    height: 50px;
    cursor: pointer;
  }
  .profile-circle {
    width: 220px;
    height: 220px;
  }
  .option-icon .option-img{
    width: 50px;
    height: 50px;
  }
  .option-icon .option-rate{
    width: 30px;
    height: 30px;
  }
  .tabs {
    justify-content: center;
    gap: 200px;
  }
  .tabs a{
    font-size: 15px;
  }
}
</style>
