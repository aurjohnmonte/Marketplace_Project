<template>
  <div class="buyer-product">
    <teleport to="body">
        <MorePhotos v-if="show_morePhotos" @hideMorePicture="hideMorePicture" :photos="photos"/>
    </teleport>
    <div class="back-button" @click="goBack">
        <img src="../../../images/left-arrows.png">
        <label>PRODUCT</label>
    </div>
    <div class="show-pic-overlay" v-if="show_pic" @click="show_pic=false; pic=null;">
        <img :src="'/'+pic" style="background-color: white; width: 250px; height: 250px; border-radius: 20px;">
    </div>
    <div class="border-gray"></div>
    <div class="product-info">
        <div class="product-profile-pic">
            <div class="option-icon">
                <img src="../../../images/location.png" @click="goLocation(parseFloat(product.shop.latitude), parseFloat(product.shop.longitude))">
                <img src="../../../images/send.png" @click="goMessage(product.shop.user_id, product)">
            </div>
            <img :src="'/'+product.photos[0].filename" class="profile-pic-product">
        </div>
        <div class="more-pic">
            <div class="pic-list">
                <img v-for="indx in (product.photos.length > 3 ? 3 : product.photos.length-1)"
                     :key="indx" :src="'/'+product.photos[indx].filename"
                      class="pic" 
                      @click="show_pic=true; pic=product.photos[indx].filename"
                >           
            </div>
            <label style="text-decoration: none;">... </label>
            <label @click="show_morePhotos=true; photos = product.photos;">More pictures</label>
        </div>

        <label style="font-size: 12px; margin-bottom: 10px; margin-top: 10px; color: gray;">View videos</label>
        <div style="width: 100%; display: flex; flex-direction: row; align-items: center; gap: 10px; overflow-x: auto;">
            <template v-for="(video, indx) of videos" :key="indx">
                <video :src="'/storage/videos/' + video.path" style="width: 200px; height: 100px" controls></video>
            </template>
        </div>

        <div class="shop-name">
            <label @click="$router.push({name: 'ShopAbout', params: {id: product.shop_id}});">{{ product.shop.name }}</label>
        </div>
        <div class="product-more-info">
            <label class="product-price">PHP {{ product.price }}</label>
            <label class="product-name">{{ product.name }}</label>
            <label class="text">{{ product.shop.address }}</label>
            <label class="text">{{ product.category }}</label>
        </div>
    </div>
    <div class="border-gray"></div>
    <div class="product-rating-container">
        <div class="product-rating-header">
            <div style="display: flex; flex-direction: row; gap: 5px; align-items: center;">
                <label>{{ product.overall_rate }}</label>
                <img src="../../../images/star.png" style="width: 10px; height: 10px;">
            </div>
            <label>( {{ reviews.length }} ratings)</label>
        </div>
        <div class="product-rating-content">
            <div style="height: auto; max-height: 300px; overflow-y: auto;">
                <div class="user-rate" v-for="review in reviews" :key="review">
                    <div class="profile-info">
                        <img :src="'/'+review.user.profile">
                        <label>{{ review.user.firstname }} {{ review.user.m_initial }} {{ review.user.lastname }}</label>
                    </div>
                    <div class="item-rate">
                        <img src="../../../images/star.png" class="star-rate" v-for="turn in returnStar('whole',review.rate)" :key="turn">
                        <img src="../../../images/half-star.png" class="star-rate" v-for="turn in returnStar('half',review.rate)" :key="turn">
                        <img src="../../../images/no-star.png" class="star-rate" v-for="turn in returnStar('none',review.rate)" :key="turn">
                        <label>{{ review.rate }}</label>
                    </div>
                    <div class="comment-text">
                        <label>{{ review.comment }}</label>
                    </div>
                    <div class="comment-pic">
                        <img :src="'/'+photo.path" v-for="photo in review.reviewphotos" :key="photo" style="border: 1px solid gray; width: 100px; height: 100px;    " @click="show_pic=true; pic=photo.path">
                        <video v-for="video in review.reviewvideos" :key="video" style="border: 1px solid gray; width: 200px; height: 100px;" :src="'/'+video.path" controls></video>
                    </div>
                </div>
            </div>

            <div class="user-comment" v-if="show_rate">
                <form @submit.prevent="goSentReview">
                    <label class="user-comment-text">User Rate</label>
                    <div class="user-rate-star">
                        <select v-model="review_info.product_rate" id="for-product" required>
                            <option disable value=''>Rate for product</option>
                            <option value=1>1 star</option>
                            <option value=2>2 stars</option>
                            <option value=3>3 stars</option>
                            <option value=4>4 stars</option>
                            <option value=5>5 stars</option>
                        </select>
                        <label for="for-product">(How's the product?)</label>
                    </div>
                    <div class="user-rate-star">
                        <select v-model="review_info.shop_rate" id="for-shop" required>
                            <option disable value=''>Rate for shop</option>
                            <option value=1>1 star</option>
                            <option value=2>2 stars</option>
                            <option value=3>3 stars</option>
                            <option value=4>4 stars</option>
                            <option value=5>5 stars</option>
                        </select>
                        <label for="for-shop">(How's the shop?)</label>
                    </div>
                    <textarea placeholder="Leave a comment" required v-model="review_info.comment"></textarea>
                    <div style="display: flex; flex-direction: column; gap: 10px; align-items: center;">
                        <div style="width: 100%; display: flex; flex-direction: row; gap: 10px;">
                            <label for="img"><img src="../../../images/image-gallery.png"></label>
                            <label for="video"><img src="../../../images/video-editing.png"></label>
                        </div>
                        <template v-if="(review_photo_send.length > 0 && review_photo_send) || (review_video_send.length > 0 && review_video_send)">
                            <div style="width: 100%; display: flex; flex-direction: row; align-items: center; gap: 20px; overflow-x: auto;">
                                <div v-for="(photo, indx) in review_photo_send" :key="indx" style="position: relative;">
                                    <img src="../../../images/cancel.png" style="width: 15px; height: 15px; position: absolute; right: 0;" @click="removePhoto(indx)">
                                    <img style="width: 100px; height: 100px; border: 1px solid gray;" :src="photo.preview">
                                </div>
                                <div v-for="(video, indx) in review_video_send" :key="indx" style="position: relative; display: flex; flex-direction: column;">
                                    <div style="width: 100%; justify-content: end; display: flex; flex-direction: row;">
                                        <img src="../../../images/cancel.png" style="width: 15px; height: 15px; " @click="removeVideo(indx)">
                                    </div>
                                    <video style="width: 180px; height: 100px; border: 1px solid gray;" :src="video.preview" controls></video>
                                </div>
                            </div>
                        </template>
                    </div>
                    <input type="file" accept="image/*" id="img" hidden @change="addPhoto">
                    <input type="file" accept="video/*" id="video" hidden @change="addVideo">
                    <button class="submit-btn">Submit</button>
                </form> 
            </div>

            <div class="leave-comment" @click="show_rate = !show_rate" v-if="can_rate">
                <label>{{ returncommentText() }}</label>
            </div>
            <div style="font-size: 10px; color: gray;" v-else>
                <label>Review unavailable — purchase required.</label><br>
                <label>For issues, contact the seller.</label>
            </div>
        </div>
    </div>
    <div class="border-gray"></div>
        <div class="product-description">
            <h5>Product Specification</h5>
            <h6>Description</h6>

            <div class="description-text">
                {{ product.description }}<br><br>
                Materials: {{ product.materials }} <br><br>
                Dimensions: {{ product.dimensions }}<br><br>
                Weight: {{ product.weight }}
            </div>
        </div>
    </div>
</template>

<script>
import MorePhotos from './MorePhotos.vue';
import { useDataStore } from '../../stores/dataStore';
import axios from 'axios';
export default {
    components: {
        MorePhotos
    },
    data(){
        return{
            can_rate: false,
            show_pic: false,
            pic: null,
            show_morePhotos: false,
            review_photo_send: [],
            review_video_send: [],
            review_info: {
                product_rate: '',
                shop_rate: '',
                comment: "",
            },
            photos: [],
            reviews: [],
            product: null,
            show_rate: false,
            rate: {
                start: 3,
                half: true,
                no_star: 1,
            },
            videos: null,
        }
    },
    methods: {
        
        addVideo(e){
            const file = e.target.files[0];

            const reader = new FileReader();

            reader.onload = (e) => {

                this.review_video_send.push({
                    file,
                    preview: e.target.result
                })
                console.log('videos: ', this.review_video_send);
            }

            reader.readAsDataURL(file);

            e.target.value = null;
        },
        async returnVideos(){
            const data = new FormData();
            data.append('id', this.product.id);
            const res = await axios.post('/buyer/return-videos', data);

            this.videos = res.data.videos;
            console.log('videos: ', this.videos);
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
        removePhoto(index){
            console.log(index);
            this.review_photo_send.splice(index, 1);
        },
        removeVideo(index){
            console.log('index: ', index);
            this.review_video_send.splice(index, 1);

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
        addPhoto(e){
            const file = e.target.files[0];
            const reader = new FileReader();
            reader.onload = (e) => {

                this.review_photo_send.push({
                    file,
                    preview: e.target.result
                });
            }
            
            reader.readAsDataURL(file);

            e.target.value = null;
        },
        reset(){
            this.review_photo_send = [];
            this.review_info = {
                product_rate: '',
                shop_rate: '',
                comment: "",
            };
        },
        async goSentReview(){
            if(this.review_info.rate === 0){
                window.alert("Please select your rating");
                return;
            }
            const store = useDataStore();
            const data = new FormData();

            data.append('review_info', JSON.stringify(this.review_info));

            this.review_photo_send.forEach(photo => {

                data.append('photos[]', photo.file);
            });

            this.review_video_send.forEach(video => {

                data.append('videos[]', video.file);    
            })

            data.append('type', 'product');
            data.append('to_id', this.product.id);
            data.append('user_id', this.product.shop.user_id);
            data.append('shop_id', this.product.shop.id);
            data.append('from_id', store.currentUser_info.id);

            console.log('to id: ', this.product.id);
            console.log('shop id: ', this.product.shop.id);

            const res = await axios.post('/buyer/add/review', data);
            console.log(res.data.message);
            this.review_video_send = [];
            this.product.overall_rate = res.data.message;
            this.reset();
            await this.returnReviews();
            this.show_rate = false;
        },
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
        },
        async returnReviews(){
            const data = new FormData();
            data.append('product_id', this.product.id);
            data.append('type', 'product');

            const res = await axios.post('/buyer/return/review', data);
            console.log(res.data.message);
            this.reviews = res.data.message;
        },
        check_if_record_exist(){
            const store = useDataStore();
            const user_id = store.currentUser_info.id;

            const records = this.product.records.some(record => record.user_id === user_id && record.status === 'confirmed');

            records ? this.can_rate = true : this.can_rate = false;
        }
    },
    created(){

        this.$watch(
            () => this.$route.params.id,
            async (newID) => {
                console.log('haaaaa');
                const res = await axios.get('/buyer/return-product/info', {
                    params: {
                        id: newID
                    }
                });

                const store = useDataStore();
                store.setSelectedProduct(res.data.product);
                console.log('PRODUCT: ', res.data.product);
                this.product = store.selectedProduct;
                this.product_id = newID;
                this.check_if_record_exist();
                await this.returnReviews();
            }
        )

        const store = useDataStore();
        this.product = store.selectedProduct;
        console.log('productsss: ', this.product);
        this.check_if_record_exist();
    },
    async mounted(){
        this.$emit("changepathtext", "home");
        window.scrollTo(0, 0);
        await this.returnVideos();
        await this.returnReviews();
    }
}
</script>

<style scoped>
.show-pic-overlay{
    position: fixed;
    background-color: rgba(0, 0, 0, 0.725);
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    z-index: 32;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
}
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
    overflow-x: auto;
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
@media (min-width: 768px){
    .buyer-product{
        position: relative;
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
        margin: 100px auto;
        margin-top: 0;
        max-width: 87%;
        box-sizing: border-box; 
    }
    .back-button{
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 40px;
    }
    .back-button label{
        font-weight: bolder;
        font-size: 25px;
    }
    .back-button img{
        width: 50px;
        height: 50px;
        cursor: pointer;
    }
    .product-profile-pic{
        height: 500px;
        overflow: hidden;
    }
    .option-icon{
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 15px;
        position: absolute;
    }
    .option-icon img{
        width: 80px;
        height: 80px;
        border-radius: 50px;
        background-color: white;
        cursor: pointer;
    }
    .more-pic label{
        font-size:20px;
        text-decoration: underline;
        color: rgb(115, 115, 115);
        cursor: pointer;
    }
    .more-pic{
        display: flex; 
        flex-direction: row;
        align-items: end;
        gap: 20px;
        padding-top: 5px;
    }
    .pic{
        height: 100px;
        width: 100px;
    }
    .shop-name label{
        font-size: 20px;
    }
    .product-price{
        font-size: 25px;
    }
    .product-name{
        font-size: 15px;
    }
    .text{
        font-size: 15px;
    }
    .product-description{
        font-size: 25px;
    }
    .description-text{
        font-size: 16px;
        gap: 20px;
    }
}
</style>