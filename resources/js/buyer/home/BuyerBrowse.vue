<template>
  <div class="buyer-browse-maincontainer">
    <div style="padding: 20px;">
        <img src="../../../images/left-arrows.png" style="width: 30px; height: 30px; cursor: pointer" @click="goPrevious">
    </div>
    <label style="padding-left: 20px; padding-right: 20px;  padding-bottom: 10px; font-weight: bolder;">{{ $route.params.name.toUpperCase() }}</label>
    <div class="filter-search-browse">
        <select>
            <option>Category</option>
        </select>
        <select>
            <option>Popular</option>
        </select>
        <input placeholder="Search product name ...">
        <img src="../../../images/cancel (1).png" style="width: 10px; height: 10px; margin-left: 5px;">
    </div>
    <div class="buyer-browse-header">
        <label class="popular-text">{{ $route.params.filter }}</label>
        <label class="category-text">{{ $route.params.category }}</label>
    </div>
    <div class="buyer-browse-content">
        <div class="item-content">
          <div class="option-icon">
            <img src="../../../images/location.png">
            <img src="../../../images/send.png">
          </div>
          <div class="item-pic">
            <img src="../../../images/images.jpg">
          </div>
          <div class="item-info" @click="goProduct">
            <div class="item-rate">
              <img src="../../../images/star.png" class="star-rate" v-for="turn in rate.start" :key="turn">
              <img src="../../../images/half-star.png" class="star-rate" v-if="rate.half">
              <img src="../../../images/no-star.png" class="star-rate" v-for="turn in rate.no_star" :key="turn">
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
        <div class="item-content">
          <div class="option-icon">
            <img src="../../../images/location.png">
            <img src="../../../images/send.png">
          </div>
          <div class="item-pic">
            <img src="../../../images/images.jpg">
          </div>
          <div class="item-info">
            <div class="item-rate">
              <img src="../../../images/star.png" class="star-rate" v-for="turn in rate.start" :key="turn">
              <img src="../../../images/half-star.png" class="star-rate" v-if="rate.half">
              <img src="../../../images/no-star.png" class="star-rate" v-for="turn in rate.no_star" :key="turn">
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
        <div class="item-content">
          <div class="option-icon">
            <img src="../../../images/location.png">
            <img src="../../../images/send.png">
          </div>
          <div class="item-pic">
            <img src="../../../images/images.jpg">
          </div>
          <div class="item-info">
            <div class="item-rate">
              <img src="../../../images/star.png" class="star-rate" v-for="turn in rate.start" :key="turn">
              <img src="../../../images/half-star.png" class="star-rate" v-if="rate.half">
              <img src="../../../images/no-star.png" class="star-rate" v-for="turn in rate.no_star" :key="turn">
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
        <div class="item-content">
          <div class="option-icon">
            <img src="../../../images/location.png">
            <img src="../../../images/send.png">
          </div>
          <div class="item-pic">
            <img src="../../../images/images.jpg">
          </div>
          <div class="item-info">
            <div class="item-rate">
              <img src="../../../images/star.png" class="star-rate" v-for="turn in rate.start" :key="turn">
              <img src="../../../images/half-star.png" class="star-rate" v-if="rate.half">
              <img src="../../../images/no-star.png" class="star-rate" v-for="turn in rate.no_star" :key="turn">
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
        <div class="item-content">
          <div class="option-icon">
            <img src="../../../images/location.png">
            <img src="../../../images/send.png">
          </div>
          <div class="item-pic">
            <img src="../../../images/images.jpg">
          </div>
          <div class="item-info">
            <div class="item-rate">
              <img src="../../../images/star.png" class="star-rate" v-for="turn in rate.start" :key="turn">
              <img src="../../../images/half-star.png" class="star-rate" v-if="rate.half">
              <img src="../../../images/no-star.png" class="star-rate" v-for="turn in rate.no_star" :key="turn">
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
        <div class="item-content">
          <div class="option-icon">
            <img src="../../../images/location.png">
            <img src="../../../images/send.png">
          </div>
          <div class="item-pic">
            <img src="../../../images/images.jpg">
          </div>
          <div class="item-info">
            <div class="item-rate">
              <img src="../../../images/star.png" class="star-rate" v-for="turn in rate.start" :key="turn">
              <img src="../../../images/half-star.png" class="star-rate" v-if="rate.half">
              <img src="../../../images/no-star.png" class="star-rate" v-for="turn in rate.no_star" :key="turn">
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
import { useDataStore } from '../../stores/dataStore';
export default {
    data(){
        return{
            product: {
              name: "Wood Bed",
              description: "Just a bed that made of wood",
              rate: {
                star: 3,
                half: true,
                no_star: 1
              }
            },
            rate: {
                    start: 3,
                    half: true,
                    no_star: 1,
                }
        }
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
        goPrevious(){ 
            this.$router.go(-1);
        }
    }
}
</script>

<style scoped>
.item-pic{
  width: 160px;
  height: 160px;
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
.item-price{
  font-size: 12px;
  color: #D25E27;
  font-weight: bolder;
}
.item-shopname{
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
.buyer-browse-content{
    display: grid;
    grid-template-columns: repeat(2, 10rem);
    gap: 30px;
    padding-left: 20px;
}
.filter-search-browse select{
    padding: 5px;
    font-size: 10px;
    border: 1px solid #D25E27;
}
.filter-search-browse input{
    padding: 4px;
    font-size: 12px;
    padding-left: 10px;
    border: 1px solid #D25E27;
    width: 150px;
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