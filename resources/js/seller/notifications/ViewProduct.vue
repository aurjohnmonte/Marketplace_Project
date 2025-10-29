<template>
    <div class="product-notif-container" v-if="product">
        <teleport to="body">
            <AttachVideo @exit_modal="exit_modal" @exitModal="exitModal" @goExit="goExit" v-if="product_id" :product="product"/>
        </teleport>
        <div class="product-profile">
            <div class="image-container" :class="{ 'center-images': product.photos.length < 3 }">

                <button class="attach-btn"
                        @click="product_id = product.id">
                    Attach video
                </button>

                <button class="nav-btn nav-left"
                    @click="scrollImages('left', product.id)"
                    v-if="product.photos && product.photos.length > 3">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <div
                    v-if="product.photos"
                    class="toggle-img"
                    :class="{
                    'center-1': product.photos.length === 1,
                    'center-2': product.photos.length === 2
                    }"
                >
                    <div v-for="image in product.photos"
                        :key="image.filename"
                        class="image-wrapper"
                    >
                        <img :src="'/' + image.filename" alt="Image" @mouseover="displayIcon" >
                        <div class="zoom-overlay">
                            <i class="fa fa-search-plus zoom-icon" @click.stop="openZoomModal(image.filename)"></i>
                        </div>
                    </div>
                </div>
                <button class="nav-btn nav-right"
                    @click="scrollImages('right', product.id)"
                    v-if="product.photos && product.photos.length > 3">
                    <i class="fa fa-chevron-right"></i>
                </button>
            </div>

            <!-- Modal for zoomed image -->
            <div v-if="showZoom" class="modal">
                <div class="modal-content">
                    <button class="close-modal-btn" @click="closeZoomModal">
                        <i class="fa fa-times" @click.stop="closeZoomModal"></i>
                    </button>
                    <img :src="'/'+showZoom" alt="Zoomed Image" class="zoomed-image" />
                </div>
            </div>

            <div style="width: 100%; margin-top: 20px; display: flex; flex-direction: column; align-items: center; gap: 10px; overflow-x: auto;">
                <div style="width: 100%; text-align: start;">
                    <h5>PRODUCT VIDEO</h5>
                </div>
                <div style="width: 100%; display: flex; flex-direction: row; gap: 15px;">
                    <video :src="'/storage/videos/'+video.path" controls style="max-width: 200px; height: 100px;" v-for="video in product.videos" :key="video" ></video>
                </div>
            </div>


            <div class="product-details">
                <h3>{{ product.name }}</h3>
                <div class="rating">
                    <p>Overall Rating:</p>
                    <span
                        v-for="star in 5"
                        :key="star"
                        class="fa fa-star"
                        :class="{ checked: star <= product.overall_rate }"
                    ></span>
                    <span class="rating-text">({{ product.overall_rate }}/5)</span>
                </div>
            </div>
        </div>

        <div class="about-container">

            <div class="details-container">
                <div class="about-details">
                    <div class="details-header">
                        <h6>Product Details</h6>
                    </div>
                    <div class="full">
                        <div class="details-box">
                            <span>Category:</span>
                            <p>{{ product.category }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="details-box">
                            <span>Status:</span>
                            <p>{{ product.status }}</p>
                        </div>

                        <div class="details-box">
                            <span>Price:</span>
                            <p>{{ product.price }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="details-box">
                            <span>Quantity:</span>
                            <p>{{ product.quantity }}</p>
                        </div>

                        <div class="details-box">
                            <span>Materials:</span>
                            <p>{{ product.materials }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="details-box">
                            <span>Weight:</span>
                            <p>{{ product.weight }}</p>
                        </div>

                        <div class="details-box">
                            <span>Dimensions:</span>
                            <p>{{ product.dimensions }}</p>
                        </div>
                    </div>
                </div>

                <div class="product-desc">
                    <h6>Product Description</h6>
                    <div class="details-box desc">
                        <p>{{ product.description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="product-reviews">
            <h6>Reviews</h6>
            <label>({{ product.reviews.length }}) total of reviews</label><br>
            <div class="reviews-display">
                <div class="review-row" v-for="review in product.reviews" :key="review">
                    <div style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                        <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                            <img style="width: 50px; height: 50px; border-radius: 50px" :src="'/' + review.user.profile">
                            <div>
                                <label>{{ review.user.firstname }} {{ review.user.m_initial }} {{ review.user.lastname }}</label><br>
                                <label style="color: gray;">{{ returnFormatDate(review.created_at) }}</label>
                            </div>
                        </div>
                        <div>
                            <div class="item-rate">
                                <label>( {{ review.rate }} )</label>
                                <img src="../../../images/star.png" class="star-rate" v-for="turn in returnStar('whole',review.rate)" :key="turn">
                                <img src="../../../images/half-star.png" class="star-rate" v-for="turn in returnStar('half',review.rate)" :key="turn">
                                <img src="../../../images/no-star.png" class="star-rate" v-for="turn in returnStar('none',review.rate)" :key="turn">
                            </div>
                        </div>
                    </div>
                    <label style="margin-left: 50px;">{{ review.comment }}</label>
                    <div style="width: 100%; overflow-x: auto; gap: 5px; display: flex;">
                        <img v-for="photo in review.reviewphotos" :key="photo" :src="'/'+photo.path" style="width: 100px; height: 100px; border: 1px solid gray;">
                        <video v-for="video in review.reviewvideos" :key="video" :src="'/'+video.path" style="width: 200px; height: 100px; border: 1px solid gray;" controls></video>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore';
import AttachVideo from '../modal/AttachVideo.vue';

export default {
    components: {
        AttachVideo
    },
    data() {
        return {
            product_id: null,
            show: true,
            product:  null,
            clicked: false,
            showZoom: null,
            notificationData: null,
            store: useDataStore(),
        }
    },
    computed: {
        isActive() {
            return this.active_status?.isActive ?? false;
        }
    },

    methods: {
        goExit(){
            this.exit_modal();
        },
        exitModal(video){
            this.product.videos.push(video);
            this.exit_modal();
        },
        exit_modal(){
            this.product_id = null;
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
        returnFormatDate(date){

            return new Date(date).toLocaleDateString();
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
        displayIcon() {
            this.clicked = true;
        },
        handleOutsideClick(event) {
            // Check if the click was outside the modal
            const modalContent = event.target.closest('.modal-content');
            const toggleDetails = event.target.closest('.toggle-details');
            const actionButton = event.target.closest('.action-btn');

            // Close zoom modal if clicking outside modal content
            if (this.showZoom && !modalContent) {
                this.closeZoomModal();
                return;
            }

            // Only close toggle-details if click is outside both toggle-details and action buttons
            if (!toggleDetails && !actionButton) {
                this.viewedProductId = null;
                this.editedProductId = null;
            }
        },
        openZoomModal(filename) {
            console.log('Opening zoom modal for:', filename);
            this.showZoom = filename;
        },
        closeZoomModal() {
            console.log('Closing zoom modal');
            this.showZoom = null;
        },
        scrollImages(direction, productId) {

            const container = this.$el.querySelector(`[data-product-id="${productId}"] .toggle-img`);
            console.log('Direction:', direction);

            if (container) {
                const scrollAmount = 190;
                if (direction === 'left') {
                    container.scrollLeft -= scrollAmount;
                } else {
                    container.scrollLeft += scrollAmount;
                }
                console.log('New scroll position:', container.scrollLeft);
            } else {
                // Fallback: try to find any visible toggle-img
                const allContainers = this.$el.querySelectorAll('.toggle-img');
                console.log('All containers found:', allContainers.length);
                const visibleContainer = Array.from(allContainers).find(cont =>
                    cont.offsetParent !== null
                );
                if (visibleContainer) {
                    const scrollAmount = 190;
                    if (direction === 'left') {
                        visibleContainer.scrollLeft -= scrollAmount;
                    } else {
                        visibleContainer.scrollLeft += scrollAmount;
                    }
                    console.log('Fallback scroll position:', visibleContainer.scrollLeft);
                } else {
                    console.log('No visible container found');
                }
            }
        },
        updateScrollableClass() {
            document.addEventListener('click', this.handleOutsideClick);
        },
        initializeProductInfo(id){
            console.log(id);

            const products = this.store.selected_shop.products;
            console.log(products);

            console.log('products: ', products);

            //find the product
            const product = products.find(e => e.id === id);
            console.log('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
            console.log('PRODUCT: ', product);
            console.log('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
            this.product = product;


        }
    },
    mounted() {
        this.notificationData = this.store.selectedNotification;

        //THIS FUNCTION WHERE INITIALIZE THE PRODUCT INFO
        //get the id from the param
        let product_id = parseInt(this.$route.params.id);
        //this method will find the product from the store
        this.initializeProductInfo(product_id);

        this.$nextTick(() => {
            const message = this.$route.query.message;
            console.log('messsage', message);
            if(message){
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth' // optional, for smooth animation
                });
            }
        })
    }
}
</script>

<style scoped>
.star-rate{
  width: 15px;
  height: 15px;
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
  margin: 0;
}
.review-row{
    background-color: rgba(255, 255, 255, 0.536);
    padding: 15px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    gap: 15px;
    border-radius: 20px;
}
.reviews-display{
    background-color: #bdbbba98;
    padding: 10px;
    overflow: hidden;
    height: 400px;
    border-radius: 15px;
    box-sizing: border-box;
    padding: 2em;
    display: flex;
    flex-direction: column;
    gap: 20px;
    overflow: auto;
}
.product-reviews{
    padding: 2em;
    border-radius: 1em;
    background-color: #DDD0C8;;
    user-select: none;
    display: flex;
    flex-direction: column;
}

.product-notif-container {
    width: 100%;
    height: auto;
    padding: 1.5em;
    display: flex;
    flex-direction: column;
    gap: 1.5em;
    margin: 0;
}

.product-profile {
    width: 100%;
    height: auto;
    background-color: #DDD0C8;
    padding: 1.5em 1.5em 1em;
    border-radius: 1em;
    user-select: none;
}

.image-container {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
    height: 25vh;
    background-color: #bda79a8c;
    border-radius: 1em;
    padding: .5em;
    user-select: none;
}

.image-container.center-images {
    margin: 0;
    justify-content: center !important;
}

.toggle-img {
    display: flex;
    gap: 2em;
    width: 100%;
    height: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    align-items: center;
    padding: 0;
    margin: 0 4em;
    scroll-margin: 2em;
    scroll-padding: 2em;
}

.toggle-img {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}

.toggle-img.center-1,
.toggle-img.center-2 {
  justify-content: center;
  overflow: hidden;
  margin: 0;
}

.toggle-img.center-1 .image-wrapper {
  flex: 0 0 auto;
}

.toggle-img.center-2 .image-wrapper {
  flex: 0 0 auto;
}

.image-wrapper {
    position: relative;
    cursor: pointer;
    border-radius: 0.5em;
    overflow: hidden;
    flex-shrink: 0;
}

.toggle-img img {
    width: 8em;
    height: 8em;
    object-fit: cover;
    border-radius: 0.5em;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s ease;
    display: block;
    border: 1.5px solid #7575759c;
}

.zoom-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 0.5em;
}

.image-wrapper:hover .zoom-overlay {
    opacity: 1;
}

.zoom-icon {
    color: white;
    font-size: 2em;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none !important;
    outline: none;
    width: 2.5em;
    height: 2.5em;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: all 0.3s ease;
    color: #252525;
    font-size: 1em;
    box-shadow: none;
}

.nav-btn:hover {
    background: rgba(202, 157, 115, 0.438);
    transform: translateY(-50%) scale(1.1);
}

.nav-btn:active {
    transform: translateY(-50%) scale(0.95);
}

.nav-left {
    left: 0.5em;
    border: none !important;
    box-shadow: none;
}

.nav-right {
    right: 0.5em;
    border: none !important;
    box-shadow: none;
}

/* Modal styles for zoomed images */
.modal {
    position: fixed;
    top: 0em;
    left: 0;
    background: rgba(0, 0, 0, 0.623);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    cursor: default;
}

.modal-content {
    position: relative;
    width: 20em;
    height: 20em;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgb(255, 255, 255);
    border-radius: 12px;
    padding: 1em;
}

.zoomed-image {
    width: 15em;
    height: 15em;
    object-fit: contain;
    border-radius: 8px;
    transition: transform 0.3s ease;
    user-select: none;
}

.zoomed-image:hover {
    transform: scale(1.02);
}

.close-modal-btn {
    position: absolute;
    background: transparent;
    top: 3%;
    right: 2%;
    border: none !important;
    outline: none !important;
    width: 1em;
    height: 1em;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 16px;
    color: #333;
    transition: all 0.3s ease;
}

.close-modal-btn:hover {
    color: #7e6556;
    transform: scale(1.1);
}

.product-details {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1em;
    padding: 0 1em;
}

.rating {
    display: flex;
    align-items: center;
    gap: 0.5em;
    background: #ffffff41;
    border-radius: 1em;
    width: fit-content;
    padding: .3em .5em;
}

.rating p {
    margin: 0;
    font-weight: 600;
    color: #252525;
    font-size: 0.9em;
}

.rating .fa-star {
    color: #a1a1a1;
    font-size: 1.2em;
    cursor: pointer;
    transition: color 0.2s ease;
}

.rating .fa-star.checked {
    color: #ff8800;
}

.rating-text {
    margin-left: 0.5em;
    font-weight: 600;
    color: #252525;
    font-size: 0.9em;
}


/* Product Details */
.about-container {
    padding: 2em;
    border-radius: 1em;
    background-color: #DDD0C8;;
    user-select: none;
}

.details-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

h6 {
    font-family: 'Playfair Display', serif;
    font-size: 1.5em;
    font-weight: 800;
    color: #333;
    margin: 0;
}

.details-container {
    display: flex;
    gap: 2em;
}

.about-details {
    flex: 1.50;
    display: flex;
    flex-direction: column;
    gap: 1em;
    height: 100%;
    padding-right: 1em;
}

.row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    column-gap: 1em;
    align-items: center;
    padding: 0 .9em;
}

.full {
    width: 100%;
}

.details-box {
    background-color: #bdbbba98;
    display: grid;
    grid-template-columns: auto 1fr;
    column-gap: 1em;
    row-gap: 0.5em;
    align-items: center;
    padding: 0.75em 1em;
    border-radius: 1em;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.479);
    font-size: .8em;
}

.details-box span {
    font-weight: 600;
    color: #333;
}

.details-box p {
    margin: 0;
    color: #666;
    overflow-wrap: anywhere;
}

.product-desc {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1em;
}

.desc {
    height: 100%;
    align-items: start;
    background-color: #a8a6a591;
    padding: 1em;
}

.desc p{
    color: #3f3c3c !important;
    font-size: larger;
}

.attach-btn{
    position: absolute;
    top: 0;
    right: 0;
    margin: 10px;
    border: none;
    padding: .5em;
    border: 1px solid gray;
    background-color: #FFC107;
    cursor: pointer;
    border-radius: 5px;
    text-transform: capitalize;
    color: #212121;
    font-size: .9em;
}

@media (max-width: 480px) {
    .details-container {
        display: flex;
        flex-direction: column;
    }
    .attach-btn{
        width: 80px;
        font-size: 10px;
        right: -10px;
    }
    .rating {
        display: flex;
        align-items: center;
        gap: 0.5em;
        background: #ffffff41;
        border-radius: 1em;
        width: fit-content;
        padding: .3em .5em;
    }

    .rating p {
        margin: 0;
        font-weight: 600;
        color: #252525;
        font-size: 0.9em;
    }

    .rating .fa-star {
        color: #a1a1a1;
        font-size: 1.2em;
        cursor: pointer;
        transition: color 0.2s ease;
    }
    .product-details {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        margin-top: 1em;
        padding: 0 1em;
    }
}
</style>
