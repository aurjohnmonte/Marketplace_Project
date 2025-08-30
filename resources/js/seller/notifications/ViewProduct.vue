<template>
    <div class="product-notif-container">
        <div class="product-profile">
            <div class="image-container">
                <button class="nav-btn nav-left"
                    @click="scrollImages('left', product.id)"
                    v-if="product.photos && product.photos.length > 3">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <div v-if="product.photos" class="toggle-img">
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

            <div class="product-details">
                <h3>{{ product.name }}</h3>
                <div class="rating">
                    <h6 class="deta">Overall Rating: </h6>
                    <div class="star-container" v-if="notificationData && notificationData.reviews">
                        <img src="../../../images/star.png" class="star-rating" v-for="turn in returnStar('whole', notificationData.reviews.rate)" :key="turn">
                        <img src="../../../images/half-star.png" class="star-rating" v-for="turn in returnStar('half', notificationData.reviews.rate)" :key="turn">
                        <img src="../../../images/no-star.png" class="star-rating" v-for="turn in returnStar('none', notificationData.reviews.rate)" :key="turn">
                    </div>
                    <p>({{ notificationData.reviews.rate }}/5)</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore';

export default {
    components: {
    },
    data() {
        return {
            store: useDataStore(),
            product: {
                name: 'Chair',
                id: 1,
                photos: [
                    { filename: 'images/bell (1).png' },
                    { filename: 'images/user2.png' },
                    { filename: 'images/bell (1).png' },
                    { filename: 'images/user3.png' },
                    { filename: 'images/bell (1).png' },
                    { filename: 'images/user2.png' },
                    { filename: 'images/bell (1).png' },
                    { filename: 'images/bell (1).png' },
                    { filename: 'images/bell (1).png' },
                ]
            },
            clicked: false,
            showZoom: null,
            notificationData: null,
        }
    },
    methods: {
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
            // Check if the click was outside any toggle-details element
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
        }
    },
    mounted() {
        this.notificationData = this.store.selectedNotification;
    }
}
</script>

<style scoped>
.star-container {
    height: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
}
.star-rating{
  width: 1em;
  height: 1em;
}

.product-notif-container {
    width: 100%;
    height: 100%;
    padding: 2em;
}

.product-profile {
    width: 100%;
    height: 18em;
    background-color: #DDD0C8;
    padding: 1em;
}

.img-container {
    display: flex;
    justify-content: space-between;
}

.image-container {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
    height: 25vh;
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

.image-wrapper {
    position: relative;
    cursor: pointer;
    border-radius: 0.5em;
    overflow: hidden;
    flex-shrink: 0;
}

.toggle-img img {
    width: 10em;
    height: 10em;
    object-fit: cover;
    border-radius: 0.5em;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s ease;
    display: block;
    border: 1px solid #000;
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
    background-color: #f8843d;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rating {
    width: 15em;
    display: flex;
    gap: .5em;
    background-color: #7e6556;
    font-size: smaller;
    justify-content: center;
    align-items: center;
}

.rating h6 {
    font-size: .9em;
    display: inline-block;
}

.rating p {

}
</style>
