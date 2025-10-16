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
                    <input type="search" name="search" v-model="searchtext" id="search" placeholder="Search your product...">
                    <i class="fa-solid fa-search search-icon"></i>
                </div>
                <div class="select">
                    <select name="filter" id="filter" v-model="filter">
                        <option value="">Filter</option>
                        <option v-for="filter in filterBy" :key="filter" :value="filter">{{ filter }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="product-body">
            <div class="row">
                <div class="item-card" v-for="product in products" :key="product.id">
                    <img :src="'/'+product.photos[0].filename" alt="Product Image" @click.stop="$router.push({name: 'ViewProduct', params: {id: product.id}})" style="cursor: pointer;">
                    <div class="item-info" @click.stop="toggleDescription(product.id)" style="cursor: pointer;">
                        <p>{{ product.name }}</p>
                        <i :class="[getStatusIcon(product.status), getStatusClass(product.status)]"></i>
                        <div style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                            <label>₱ {{ product.price }}</label>
                            <label><i class="fa-solid fa-star" style="color: #ec8906;"></i> : {{ product.overall_rate }}/5</label>
                        </div>
                        <div class="product-description" :class="{ 'expanded': expandedItems.includes(product.id) }">
                            <p>{{ product.description }}</p>

                        </div>
                    </div>
                    <label>Created at: {{ returnFormatDate(product.created_at) }}</label>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default{
    props: ['shop'],
    watch: {

        filter(newval) {
            this.haveSearchContent(this.filter);
        },

        searchtext(newval) {
            if (newval === "") {
            this.category_filter();
            return;
            }

            this.products = this.shop.products.filter(p =>
            p.name.toLowerCase().includes(newval.toLowerCase())
            );
            console.log('p: ', this.products);
        },
        productCategory(newval){
            this.filter = 'latest';

            if (this.productCategory === "All") {
                this.products = this.shop.products;
            } else {
                this.products = this.shop.products.filter(product =>
                    product.category.toLowerCase().includes(this.productCategory.toLowerCase())
                );
            }
            console.log('hoy')
        }
    },
    data() {
        return {
            expandedItems: [],
            cat_result: [],
            showModal: false,
            selectedItem: {},
            categories: {
                Any: 'All',
                Furniture: 'Furniture',
                Kitchenware: 'Kitchenware',
                MusicalInstrument: 'Musical Instrument',
                ToysGames: 'Games',
                OfficeSupplies:'Office Supplies',
                HomeDecor: 'Home Decor',
                PersonalAccessories: 'Personal accessories',
                OutdoorEnhancements: 'Outdoor',
            },
            productCategory: 'Any',
            filter: 'latest',
            products: [],
            searchtext: '',
            filterBy: {
                latest: 'latest',
                popular: 'popular',
            }
        }
    },
    computed: {

    },
    methods: {
        returnFormatDate(date){

            return new Date(date).toLocaleDateString();
        },
        haveSearchContent(newval){
            console.log('newval: ', newval);
            if(this.products.length > 0){
                console.log('here')
                let partial = [];

                partial = this.products;

                if(newval === 'latest'){

                    console.log('neh agi')

                    console.log('products: ', this.products);


                    let swapped;

                    do {
                    swapped = false;
                    console.log('partial: ', partial);

                    for (let i = 0; i < partial.length - 1; i++) {
                        console.log('1')
                        if (partial[i].id < partial[i + 1].id) {

                        let temp = partial[i];
                        partial[i] = partial[i + 1];
                        partial[i + 1] = temp;

                        swapped = true;
                        }
                    }

                    } while (swapped);

                    this.products = partial;

                }
                else{
                    let swapped;

                    do {
                    swapped = false;
                    console.log('partial: ', partial);

                    for (let i = 0; i < partial.length - 1; i++) {
                        console.log('1')
                        if (partial[i].overall_rate < partial[i + 1].overall_rate) {
                        // ✅ Proper swap
                        let temp = partial[i];
                        partial[i] = partial[i + 1];
                        partial[i + 1] = temp;

                        swapped = true;
                        }
                    }

                    } while (swapped);
                    this.products = partial;
                }
            }
        },
        category_filter(){
            if(this.productCategory === "Any"){

                this.products = this.shop.products;
            }
            else{

                this.products = this.shop.products.filter(product => product.category.includes(this.productCategory));
            }
            console.log('hey')
        },
        filteredProducts() {

           this.products = this.shop.products;
           this.haveSearchContent(this.filter);
        },
        selectCategory(category) {
            this.productCategory = category;
            console.log('Selected category:', category);
        },
        getStatusIcon(status) {
            return status === 'Out of Stock' ? 'fa-solid fa-circle-xmark' : 'fa-solid fa-circle-check';
        },
        getStatusClass(status) {
            return status === 'Out of Stock' ? 'status-out-of-stock' : 'status-on-stock';
        },
        toggleDescription(itemId) {
            const index = this.expandedItems.indexOf(itemId);
            if (index > -1) {
                this.expandedItems.splice(index, 1);
            } else {
                this.expandedItems.push(itemId);
            }
        }
    },
    mounted(){
        this.filteredProducts();
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
    height: 2.5em;
    display: flex;
    justify-content: space-evenly;
    align-items: center;
    padding: .5em 0;
    background-color: #9b5800a8;
    overflow: hidden
}

.product-category h5 {
    font-size: .8em;
    white-space: nowrap;
    margin: 0 .5em;
    flex-shrink: 0;
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
    text-transform: capitalize;
}

.select option {
    background-color: rgba(240, 209, 174, 0.658);
    text-transform: capitalize;
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
    grid-template-columns: repeat(5, 1fr);
    gap: 1em;
    padding: .5em;
    justify-items: center;
}

.item-card {
    width: 16em;
    max-width: 90%;
    height: 25em;
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

.item-card img {
  width: 100%;
  height: 10em;
  object-fit: cover;
  border-radius: 1em;
  user-select: none;
}

.item-info {
    display: grid;
    grid-template-columns: 2fr 1fr;
    justify-content: center;
    overflow-y: auto;
    overflow-x: hidden;
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


@media screen and (min-width: 1025px) {
    .row {
        width: 98%;
        grid-template-columns: repeat(4, minmax(12em, 1fr));
        padding-right: 1.5em;
    }
}

@media screen and (max-width: 1240px) {
  .row {
    grid-template-columns: repeat(4, 1fr);
  }
  .item-card {
    width: 15em;
    height: 23em;
  }
}

/* Tablets (≤1024px) */
@media screen and (max-width: 1024px) {
  .row {
    grid-template-columns: repeat(4, 1fr);
  }

  .filter-box {
    flex-wrap: wrap;
  }

  .search-bar {
    width: 15em;
  }

  .product-category {
    justify-content: flex-start;
    overflow-x: auto;
    scrollbar-width: thin;
    -ms-overflow-style: none; /* IE/Edge hide scrollbar */
  }

  .product-category::-webkit-scrollbar {
    display: none; /* Chrome/Safari hide scrollbar */
  }

  .item-card {
    width: 13em;
    height: 21em;
    font-size: 0.8em;
  }
}

/* Small tablets (≤768px) */
@media screen and (max-width: 768px) {
  .row {
    grid-template-columns: repeat(2, 1fr);
  }

  .item-card {
    width: 12em;
    height: 20em;
    font-size: 0.75em;
    padding: 0.8em;
  }
}

/* Mobile phones (≤600px) */
@media screen and (max-width: 600px) {

  .search-bar {
    width: 60%;
  }

  .product-category {
    justify-content: flex-start;
    overflow-x: auto;
    scrollbar-width: none;
  }

  .product-category::-webkit-scrollbar {
    display: none;
  }

  .item-card {
    width: 11em;
    height: 18em;
    font-size: 0.7em;
    padding: 0.6em 1em;
  }

  .item-card img {
    width: 50%;
    max-height: 100px;
    border-radius: .6em;
    margin: 0 1em;
  }
}
</style>
