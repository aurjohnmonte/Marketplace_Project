<template>
  <div class="overlay">
    <div class="modal">
      <div class="header-1">
        <label class="title">FILTER SEARCH</label>
        <span class="material-icons close-icon" @click="$emit('close')">cancel</span>
      </div>

      <div class="filter-body">
        <!-- WHEN -->
        <div class="row-flex">
          <label>When:</label>
          <select v-model="search_data.when">
            <option value="" disabled>Select when</option>
            <option value="1">Today</option>
            <option value="2">Yesterday</option>
            <option value="3">Last 7 Days</option>
            <option value="4">Last 30 Days</option>
            <option value="5">This Month</option>
            <option value="6">Last Month</option>
          </select>
        </div>

        <!-- RATING
        <div class="row-flex">
          <label>Rating:</label>
          <select v-model="search_data.rate">
            <option value="" disabled>Select rate</option>
            <option value="5">5 ⭐⭐⭐⭐⭐</option>
            <option value="4">4 ⭐⭐⭐⭐</option>
            <option value="3">3 ⭐⭐⭐</option>
            <option value="2">2 ⭐⭐</option>
            <option value="1">1 ⭐</option>
          </select>
        </div> -->

        <!-- BUYER VIEW -->
        <div v-if="true">
          <!-- SHOP FILTER -->
          <div class="row-flex toggle-row" @click="show_shop_filter = !show_shop_filter">
            <label>Shop Name: <strong>( {{ search_data.shop_name }} )</strong></label>
            <span class="material-icons">
              {{ show_shop_filter ? 'arrow_drop_up' : 'arrow_drop_down' }}
            </span>
          </div>
          <div class="column-flex" v-if="show_shop_filter">
            <input type="text" placeholder="Search shop name..." v-model="shop_search" />
            <div class="result-customer">
              <label
                v-for="shop in filterShops"
                :key="shop.id"
                @click="selectShop(shop.name)"
              >
                {{ shop.name }}
              </label>
            </div>
          </div>

          <!-- PRODUCT FILTER -->
          <div class="row-flex toggle-row" @click="show_product_filter = !show_product_filter">
            <label>Product Name: <strong>( {{ search_data.product_name }} )</strong></label>
            <span class="material-icons">
              {{ show_product_filter ? 'arrow_drop_up' : 'arrow_drop_down' }}
            </span>
          </div>
          <div class="column-flex" v-if="show_product_filter">
            <input type="text" placeholder="Search product name..." v-model="product_search" />
            <div class="result-customer">
              <label
                v-for="product in filterProducts"
                :key="product.id"
                @click="selectProduct(product.name)"
              >
                {{ product.name }}
              </label>
            </div>
          </div>
        </div>

        <!-- SELLER VIEW -->
        <div v-if="false">
          <div class="row-flex toggle-row" @click="show_buyer_filter = !show_buyer_filter">
            <label>Buyer Name: <strong>( {{ search_data.buyer_name }} )</strong></label>
            <span class="material-icons">
              {{ show_buyer_filter ? 'arrow_drop_up' : 'arrow_drop_down' }}
            </span>
          </div>
          <div class="column-flex" v-if="show_buyer_filter">
            <input type="text" placeholder="Search buyer name..." v-model="buyer_search" />
            <div class="result-customer">
              <label
                v-for="buyer in filterBuyers"
                :key="buyer.id"
                @click="selectBuyer(buyer.name)"
              >
                {{ buyer.name }}
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- BUTTONS -->
      <div class="row-filter">
        <button class="btn cancel" @click="$emit('close')">CANCEL</button>
        <button class="btn save" @click="$emit('save', search_data)">SAVE</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: ["clicked_type"],
  data() {
    return {
      show_shop_filter: false,
      show_product_filter: false,
      show_buyer_filter: false,

      shop_search: "",
      product_search: "",
      buyer_search: "",

      shops: [],
      products: [],
      buyers: [],

      search_data: {
        shop_name: "",
        product_name: "",
        buyer_name: "",
        when: "1",
        rate: "5",
      },
    };
  },

  computed: {
    filterShops() {
      return this.shops.filter((s) =>
        s.name.toLowerCase().includes(this.shop_search.toLowerCase())
      );
    },
    filterProducts() {
      let shop = this.shops.find(shop => shop.name === this.search_data.shop_name);

      return shop.products;
    },
    filterBuyers() {
      return this.buyers.filter((b) =>
        b.name.toLowerCase().includes(this.buyer_search.toLowerCase())
      );
    },
  },

  methods: {
    async returnNames() {
      const data = new FormData();
      data.append("type", this.clicked_type);

      const res = await axios.post("/admin/return-all/names", data);
      const result = res.data.data;

      console.log('result: ', result);

      if (this.clicked_type === "buyer") {
        // buyer can filter shops & products
        this.shops = result;
      } else {
        // seller can filter buyers only
        this.buyers = result;
      }
    },

    selectShop(name) {
      this.search_data.shop_name = name;
      this.show_shop_filter = false;
    },
    selectProduct(name) {
      this.search_data.product_name = name;
      this.show_product_filter = false;
    },
    selectBuyer(name) {
      this.search_data.buyer_name = name;
      this.show_buyer_filter = false;
    },
  },

  async mounted() {
    await this.returnNames();
  },
};
</script>

<style scoped>
/* (Your existing styles – unchanged, still perfect) */
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  background-color: #32323290;
  width: 100%;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}
.modal {
  width: 600px;
  max-width: 95%;
  background: #ffffff;
  border-radius: 14px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
  overflow: hidden;
  padding: 25px;
  box-sizing: border-box;
  animation: pop 0.3s ease-in-out;
}
.header-1 {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 2px solid #ca9d73;
  padding-bottom: 10px;
  margin-bottom: 20px;
}
.title {
  font-weight: bold;
  color: #323232;
  font-size: 18px;
}
.close-icon {
  color: #c0673e;
  cursor: pointer;
  transition: 0.3s;
}
.close-icon:hover {
  transform: rotate(90deg);
}
.filter-body {
  display: flex;
  flex-direction: column;
  gap: 15px;
}
.row-flex {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}
.column-flex {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 10px;
}
select,
input[type="text"] {
  flex: 1;
  padding: 8px 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  outline: none;
  transition: 0.3s;
}
select:focus,
input[type="text"]:focus {
  border-color: #c0673e;
  box-shadow: 0 0 3px #ca9d73;
}
.toggle-row {
  cursor: pointer;
  background: #f9f9f9;
  padding: 8px;
  border-radius: 6px;
  transition: 0.3s;
}
.toggle-row:hover {
  background: #ca9d7320;
}
.result-customer {
  width: 100%;
  border: 1px solid #ccc;
  height: 120px;
  border-radius: 6px;
  background: #fafafa;
  padding: 5px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}
.result-customer label {
  cursor: pointer;
  padding: 3px;
  border-radius: 4px;
}
.result-customer label:hover {
  background-color: rgb(199, 199, 199);
}
.row-filter {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}
.btn {
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
}
.btn.cancel {
  background: #323232;
  color: white;
}
.btn.cancel:hover {
  background: #323232c9;
}
.btn.save {
  background: #c0673e;
  color: white;
}
.btn.save:hover {
  background: #ca9d73;
}
@keyframes pop {
  from {
    transform: scale(0.9);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}
@media screen and (max-width: 768px) {
  .modal {
    width: 90%;
    padding: 20px;
  }
}
</style>
