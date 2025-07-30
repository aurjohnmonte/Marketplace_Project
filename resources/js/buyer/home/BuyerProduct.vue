<template>
  <div class="buyer-product">
    <teleport to="body">
        <MorePhotos v-if="show_morePhotos" @hideMorePicture="hideMorePicture"/>
    </teleport>
    <div class="back-button" @click="goBack">
        <img src="../../../images/left-arrows.png">
        <label>PRODUCT</label>
    </div>
    <div class="border-gray"></div>
    <div class="product-info">
        <div class="product-profile-pic">
            <div class="option-icon">
                <img src="../../../images/location.png">
                <img src="../../../images/send.png">
            </div>
            <img src="../../../images/images.jpg" class="profile-pic-product">
        </div>
        <div class="more-pic">
            <div class="pic-list">
                <img src="../../../images/images.jpg" class="pic">
                <img src="../../../images/images.jpg" class="pic">
                <img src="../../../images/images.jpg" class="pic">
            </div>
            <label @click="show_morePhotos=true">More pictures</label>
        </div>
        <div class="shop-name">
            <label>Shop Name</label>
        </div>
        <div class="product-more-info">
            <label class="product-price">PHP 400.00</label>
            <label class="product-name">Product Name</label>
            <label class="text">Purok 3, Barangay 3 Cabadbaran City ADN</label>
            <label class="text">Games, Woodcraft</label>
        </div>
    </div>
    <div class="border-gray"></div>
    <div class="product-rating-container">
        <div class="product-rating-header">
            <div style="display: flex; flex-direction: row; gap: 5px; align-items: center;">
                <label>4.5</label>
                <img src="../../../images/star.png" style="width: 10px; height: 10px;">
            </div>
            <label>Product Ratings (3.4k)</label>
        </div>
        <div class="product-rating-content">
            <div class="user-rate">
                <div class="profile-info">
                    <img src="../../../images/man.jpg">
                    <label>aurjohnmonte2003</label>
                </div>
                <div class="item-rate">
                    <img src="../../../images/star.png" class="star-rate" v-for="turn in rate.start" :key="turn">
                    <img src="../../../images/half-star.png" class="star-rate" v-if="rate.half">
                    <img src="../../../images/no-star.png" class="star-rate" v-for="turn in rate.no_star" :key="turn">
                    <label>(7.5)</label>
                </div>
                <div class="comment-text">
                    <label>Okay ra ang product. Ginahan kos iyaha design actually ...</label>
                </div>
                <div class="comment-pic">
                    <img src="../../../images/images.jpg">
                    <img src="../../../images/images.jpg">
                    <img src="../../../images/images.jpg">
                    <img src="../../../images/images.jpg">
                </div>
            </div>
            <div class="user-rate">
                <div class="profile-info">
                    <img src="../../../images/man.jpg">
                    <label>aurjohnmonte2003</label>
                </div>
                <div class="item-rate">
                    <img src="../../../images/star.png" class="star-rate" v-for="turn in rate.start" :key="turn">
                    <img src="../../../images/half-star.png" class="star-rate" v-if="rate.half">
                    <img src="../../../images/no-star.png" class="star-rate" v-for="turn in rate.no_star" :key="turn">
                    <label>(7.5)</label>
                </div>
                <div class="comment-text">
                    <label>Okay ra ang product. Ginahan kos iyaha design actually ...</label>
                </div>
                <div class="comment-pic">
                    <img src="../../../images/images.jpg">
                    <img src="../../../images/images.jpg">
                    <img src="../../../images/images.jpg">
                    <img src="../../../images/images.jpg">
                </div>
            </div>

            <div class="user-comment" v-if="show_rate">
                <form>
                    <label class="user-comment-text">User Rate</label>
                    <div class="user-rate-star">
                        <select>
                            <option>Rate</option>
                            <option>1 star</option>
                            <option>1.5 star</option>
                            <option>2 stars</option>
                            <option>2.5 stars</option>
                            <option>3 stars</option>
                            <option>3.5 stars</option>
                            <option>4 stars</option>
                            <option>4.5 stars</option>
                            <option>5 stars</option>
                        </select>
                        <label>(Choose your rating for this product)</label>
                    </div>
                    <textarea placeholder="Leave a comment" required></textarea>
                    <div>
                        <label for="img"><img src="../../../images/image-gallery.png"></label>
                    </div>
                    <input type="file" id="img" hidden>
                    <button class="submit-btn">Submit</button>
                </form>
            </div>

            <div class="leave-comment" @click="show_rate = !show_rate">
                <label>{{ returncommentText() }}</label>
            </div>
        </div>
    </div>
    <div class="border-gray"></div>
    <div class="product-description">
        <h5>Product Specification</h5>
        <h6>Description</h6>

        <div class="description-text">
            Here’s a polished overview of the Acer Nitro V gaming laptop series, focusing on the Nitro V 15 and 16 models. These models offer compelling value for gamers and creators alike:

🧠 Overview & Positioning
The Acer Nitro V series is built for budget-conscious gamers and content creators seeking solid performance with modern specs—including Intel or AMD processors and RTX‑class GPUs. These models start at around $999 (USD) or ₱50K (PHP) and balance portability, refresh rate, and cooling for casual gaming and productivity.
        </div>
    </div>
  </div>
</template>

<script>
import MorePhotos from './MorePhotos.vue';
import { useDataStore } from '../../stores/dataStore';
export default {
    components: {
        MorePhotos
    },
    data(){
        return{
            show_morePhotos: false,
            product: null,
            show_rate: false,
            rate: {
                start: 3,
                half: true,
                no_star: 1,
            }
        }
    },
    methods: {
        hideMorePicture(){
            this.show_morePhotos = false;
        },
        goBack(){
            this.$router.go(-1);
        },
        returncommentText(){
            if(this.show_rate){
                return "Hide";
            }
            return "Leave a comment";
        }
    },
    created(){
        const store = useDataStore();
        this.product = store.selectedProduct;
    },
    mounted(){
        window.scrollTo(0, 0);
    }
}
</script>

<style scoped>
.product-description{
    padding-right: 20px;
}
.description-text{
    font-size: 12px;
    color: rgb(72, 72, 72);
    line-height: 1rem;
}
.product-description h5, h6{
    color: rgb(44, 44, 44);
}
.back-button{
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
}
.back-button label{
    font-weight: bolder;
}
.submit-btn{
    width: 50px;
    font-size: 10px;
    padding: 5px;
    color: white;
    border: none;
    background-color: #D25E27;
}
.user-rate-star label{
    font-size: 11px;
    color: rgb(88, 88, 88);
    margin-left: 10px;
}
.user-rate-star select{
    padding: 5px;
    font-size: 12px;
    color: rgb(74, 74, 74);
    border: 1px solid rgb(156, 156, 156);
}
.user-rate-star{
    display: flex;
    flex-direction: row;
    align-items: center;
}
.user-comment form img{
    width: 20px;
    height: 20px;
}
.user-comment-text{
    font-size: 14px;
    color: gray;
    font-weight: bolder;
}
.user-comment form textarea{
    padding: 10px;
    height: 100px;
    border: 1px solid rgb(188, 188, 188);
}
.user-comment form{
    display: flex;
    flex-direction: column;
    gap: 10px;
    justify-content: start;
}
.leave-comment label{
    font-size: 12px;
    text-decoration: underline;
    color: rgb(83, 83, 83);
}
.product-description{
    height: 300px;
    padding-bottom: 100px;
    padding-left: 20px;
}
.comment-pic img{
    width: 50px;
    height: 50px;
}
.comment-pic{
    display: flex;
    flex-direction: row;
    gap: 10px;
}
.comment-text{
    font-size: 12px;
    color: rgb(65, 65, 65);
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
.star-rate{
  width: 10px;
  height: 10px;
}
.profile-info{
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
}
.profile-info label{
    font-size: 10px;
    color: rgb(69, 69, 69);
}
.profile-info img{
    width: 30px;
    height: 30px;
    border-radius: 30px;
}
.user-rate{
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding-bottom: 20px;
    margin-bottom: 10px;
    border-bottom: 1px solid rgb(216, 216, 216);
}
.product-rating-content{
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 20px;
}
.product-rating-header label{
    font-size: 12px;
}
.product-rating-header{
    padding-left: 20px;
    padding-right: 20px;
    padding-top: 10px;
    padding-bottom: 10px;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid rgb(178, 178, 178);
}
.product-rating-container{
    height: auto;
    display: flex;
    flex-direction: column;
    justify-content: start;
}
.border-gray{
    padding: 5px;
    margin-top: 10px;
    background-color: rgb(211, 211, 211);
}
.text{
    font-size: 11px;
    color: rgb(70, 70, 70);
}
.product-price{
    color: #D25E27;
    font-weight: bolder;
}
.product-name{
    color: rgb(0, 0, 0);
    font-size: 13px;
}
.product-more-info{
    display: flex;
    flex-direction: column;
    gap: 2px;
    justify-content: start;
}
.shop-name{
    display: flex;
    flex-direction: row;
    justify-content: end;
}
.shop-name label{
    font-size: 12px;
    text-decoration: underline;
    color: rgb(102, 102, 102);
}
.back-button{
    display: flex;
    flex-direction:row;
    justify-content: start;
    padding-left: 20px;
    padding-top: 10px;
    padding-right: 20px;
    padding-bottom: 10px;
}
.back-button img{
    width: 30px;
    height: 30px;
}
.option-icon{
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 6px;
    position: absolute;
}
.option-icon img{
    width: 40px;
    height: 40px;
    background-color: white;
    border-radius: 30px;
}
.pic{
    height: 30px;
    width: 30px;
}
.pic-list{
    display: flex;
    flex-direction: row;
    gap: 5px;
}
.more-pic label{
    font-size: 12px;
    text-decoration: underline;
    color: rgb(115, 115, 115);
}
.more-pic{
    display: flex; 
    flex-direction: row;
    align-items: center;
    gap: 10px;
    padding-top: 5px;
}
.product-profile-pic{
    height: 200px;
    overflow: hidden;
}
.profile-pic-product{
    height: 100%;
    width: 100%;
    object-fit: cover;
}
.product-info{
    display: flex;
    flex-direction: column;
    padding-left: 20px;
    padding-right: 20px;
    padding-top: 20px;
    padding-bottom: 5px;
}
.buyer-product{
    height: 100vh;
    width: 100%;
    position: relative;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    padding-top: 70px;
}
</style>