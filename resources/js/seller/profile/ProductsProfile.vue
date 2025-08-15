<template>
    <div class="product-container">
        <div class="product-header">
            <div class="product-category">
                <h5 v-for="(category, key) in categories" :key="key" @click="selectCategory(category)" :class="{ active: productCategory === category }">
                    {{ category }}
                </h5>
            </div>

            <div class="filter-box">
                <div class="search-bar">
                    <input type="search" name="search" id="search" placeholder="Search your product...">
                    <i class="fa-solid fa-search search-icon"></i>
                </div>
                <div class="select">
                    <select name="filter" id="filter">
                        <option value="">Filter</option>
                        <option v-for="filter in filterBy" :key="filter" value="filter">{{ filter }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="product-body" v-if="productCategory">
            <div class="row">
                <div class="item-card" v-for="item in filteredProducts" :key="item.id" @click="toggleDescription(item.id)">
                    <img :src="item.productImage" alt="Product Image" style="width:11em; object-fit: cover; border-radius: 1em; user-select: none;">
                    <div class="item-info">
                        <p>{{ item.productName }}</p>
                        <i :class="[getStatusIcon(item.status), getStatusClass(item.status)]"></i>
                        <p>₱ {{ item.productPrice }}</p>
                        <p><i class="fa-solid fa-star" style="color: #ec8906;"></i> : {{ item.productRatings }}/5</p>
                        <div class="product-description" :class="{ 'expanded': expandedItems.includes(item.id) }">
                            <p>{{ item.productDescription }}</p>
                            <small class="click-hint" v-if="!expandedItems.includes(item.id)">Click to expand</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default{
    data() {
        return {
            expandedItems: [],
            showModal: false,
            selectedItem: {},
            user: {
                sellerName: 'Bravo',
                shop: 'Budol Seller',
                profilePicture: 'https://tse1.mm.bing.net/th/id/OIP.airZynZaLzvgWLOJFbVF6QHaE8?rs=1&pid=ImgDetMain&o=7&rm=3',
                coverPhoto: 'https://wallpapercave.com/wp/wp1996490.jpg',
                email: 'bravobudol@gmail.com',
                contacts: '090909090909',
                address: 'balay',
                rating: 2.5,
                followers: 10,
                category: 'Furniture',
                description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vel nisi sed felis dictum lobortis. Vivamus ut justo in diam malesuada vestibulum sed in lorem. Praesent imperdiet enim in eros porta, sit amet semper nunc maximus. Phasellus mauris ligula, volutpat pretium dolor ut, mattis pulvinar felis. Duis vehicula massa velit, non sollicitudin leo facilisis et. Aliquam leo nisl, elementum sit amet sapien vitae, mattis suscipit risus. Curabitur sollicitudin fermentum mauris non pellentesque. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Aenean vehicula semper felis id posuere. Aenean finibus enim et fermentum volutpat. Etiam ac finibus sem, eget elementum ante.',
                totalProducts: 10,
                allProducts: [
                    {id: 101, productName: 'Custom Pen', productImage: 'https://i.etsystatic.com/22836928/r/il/e2656f/2264726362/il_1588xN.2264726362_1ps7.jpg', itemCategory: 'Home decor', productPrice: 10, productRatings: 4, productViews: 100, productDescription: 'Suspendisse vel nisi sed felis dictum lobortis.', status: 'on-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 2, productViews: 200, productDescription: 'High quality wooden chair.', status: 'on-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Kawayan Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                    {id: 102, productName: 'Wooden Chair', productImage: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308', itemCategory: 'Furniture', productPrice: 50, productRatings: 5, productViews: 200, productDescription: 'High quality wooden chair.', status: 'out-of-stock' },
                ]
            },
            categories: {
                furniture: 'Furniture',
                homeDecor: 'Home decor',
                kitchenEssentials: 'Kitchen essentials',
                toysandGames: 'Toys & games',
                personalAccessories: 'Personal accessories',
                outdoorEnhancements: 'Outdoor enhancements'
            },
            productCategory: 'Furniture',
            filterBy: {
                latest: 'Latest',
                popular: 'Popular',
                trending: 'Trending'
            }
        }
    },
    computed: {
        filteredProducts() {
            return this.user.allProducts.filter(item => item.itemCategory === this.productCategory);
        }
    },
    methods: {
        selectCategory(category) {
            this.productCategory = category;
            console.log('Selected category:', category);
        },
        getStatusIcon(status) {
            return status === 'on-stock' ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-xmark';
        },
        getStatusClass(status) {
            return status === 'on-stock' ? 'status-on-stock' : 'status-out-of-stock';
        },
        toggleDescription(itemId) {
            const index = this.expandedItems.indexOf(itemId);
            if (index > -1) {
                this.expandedItems.splice(index, 1);
            } else {
                this.expandedItems.push(itemId);
            }
        }
    }
}
</script>


<style scoped>
/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.575);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
.modal-content {
    background: #fff;
    padding: 2em;
    border-radius: 1em;
    max-width: 400px;
    width: 90vw;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    position: relative;
}

.modal-content p {
    font-size: 1em;

}
.modal-close {
    position: absolute;
    top: 0em;
    right: 0.5em;
    background: none;
    border: none;
    font-size: 1.5em;
    cursor: pointer;
    color: #333;
}


.product-container {
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100%;
    flex: 1;
}

.product-header {
    display: flex;
    flex-direction: column;
}

.product-category {
    width: 100%;
    height: 2em;
    display: flex;
    justify-content: space-evenly;
    padding: .5em 0;
    background-color: #9b5800a8;
}

.product-category h5 {
    font-size: .9em;
    text-transform: capitalize;
    user-select: none;
}

.product-category h5.active {
    color: #f3e1d2;
    font-weight: bold;
}

.filter-box {
    display: flex;
    padding: 1em;
    justify-content: flex-end;
    align-items: center;
    gap: 1em;
    user-select: none;
}

.search-bar {
  position: relative;
  display: inline-block;
  width: 20em;
}

.search-bar input[type="search"]{
    border-radius: 1.5em;
    padding: .55em 1em;
    border: .1em solid #111;
    box-sizing: border-box;
    font-size: 0.8em;
    width: 100%;
}

.search-icon {
  position: absolute;
  right: .7em;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
  pointer-events: none;
  font-size: .8em;
  z-index: 1;
}

.select select{
    border-radius: 1em;
    padding: .3em 1em;
    text-align: center;
    font-size: 0.8em;
}

.select option {
    background-color: rgba(240, 209, 174, 0.658);
}

.product-body {
    width: 100%;
    box-sizing: border-box;
    padding-left: 1em;
    display: flex;
    justify-content: center;
}

.row {
    width: 100%;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(12em, 1fr));
    gap: 1em;
    padding: .5em;
}

.item-card {
    width: 14em;
    max-width: 90vh;
    height: 20em;
    border-radius: 1em;
    padding: 1em;
    background-color: #f3e1d2;
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: .8em;
    gap: 1em;
    box-shadow: 0px 1px 1px #000000b0;
}

.item-info {
    display: grid;
    grid-template-columns: 2fr 1fr;
    justify-content: center;
    padding: 0 .5em;
    overflow: hidden;
}

.status-on-stock {
    color: #1e9239;
    font-size: 1.2em;
    justify-self: center;
}

.status-out-of-stock {
    color: #dc3545;
    font-size: 1.2em;
    justify-self: center;
}

.product-description {
    grid-column: 1 / -1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 0.9em;
}

.product-description.expanded {
    overflow: visible;
    text-overflow: unset;
    white-space: normal;
    max-height: none;
}

.click-hint {
    color: #666;
    font-style: italic;
    font-size: 0.7em;
    margin-top: 0.2em;
    display: block;
}

</style>
