<template>
  <div class="seller-profile">
    <div class="seller-profile-header">
        <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
          <img src="../../../images/left-arrows.png" @click="goPrevious" class="left-arrow">
          <label style="font-weight: bolder; font-size: 18px;">SHOPS</label>
        </div>
        <!-- <img src="../../../images/more (2).png" style=" cursor: pointer; width: 20px; height: 20px; border: 1px solid gray; padding-left: 10px; padding-right: 10px; border-radius: 10px;"> -->
    </div>
    <div class="seller-info">
        <img src="../../../images/images.jpg">
        <div class="cover-section">
            <div class="settings-icon"></div>
                <div class="profile-info">
                    <div class="option-icon">
                        <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                          <img src="../../../images/location.png" class="option-img">
                          <img src="../../../images/send.png" class="option-img">
                        </div>
                        <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                          <img src="../../../images/star (2).png" class="option-rate">
                          <img src="../../../images/user (4).png" class="option-img">
                        </div>
                    </div>
                    <div class="profile-circle">
                        <img src="../../../images/images (1).jpg">
                    </div>
                    <h2>SHOP NAME</h2>
                    <div class="stats">
                    <span>4.5 (overall rate)</span>
                    <span>100+ followers</span>
                </div>
            </div>
        </div>
    </div>

    <div class="tabs">
      <router-link :to="{name: 'ShopAbout', params: {shopname: $route.params.shopname}}" class="tab" active-class="active">ABOUT</router-link>
      <router-link :to="{name: 'ShopProducts', params: {shopname: $route.params.shopname}}" class="tab" active-class="active">PRODUCTS</router-link>
      <router-link :to="{name: 'ShopReview', params: {shopname: $route.params.shopname}}" class="tab" active-class="active">REVIEW</router-link>
    </div>

    <div class="content">
      <router-view />
    </div>
  </div>
</template>
<script>

export default{
    methods: {
        goPrevious(){
            this.$router.go(-1);
        }
    },
    mounted(){
        window.scrollTo(0,0);
        this.$router.push({name: 'ShopAbout', params: {shopname: this.$route.params.shopname}})
    }
}
</script>

<style scoped>
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
  background-color: #c62828;
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
  font-size: 10px;
  text-decoration: underline;
}

.tabs {
  display: flex;
  justify-content: space-between;
  background-color: #ffffff;
  padding-left: 20px;
  padding-right: 20px;
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
</style>
