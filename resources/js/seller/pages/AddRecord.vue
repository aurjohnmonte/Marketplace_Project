<template>
  <div class="product-container">
            <!--FOR SELECT CUSTOMER MODAL-->
        <teleport to="body">
            <SelectCustomer @close="close" @select_customer="select_customer" v-if="show_customer_select"/>
        </teleport>

        <teleport to="body">
            <SelectProduct @close="close" @select_product="select_product" v-if="show_product_select"/>
        </teleport>

        <teleport to="body">
            <div class="overlay" v-if="loading">
                <img src="../../../images/kOnzy.gif" style="width: 100px; height: 100px;">
            </div>
        </teleport>

        <div class="header">
        <h1>Add New Transaction Record</h1>
        <router-link to="/seller/transaction-record">
            <button class="btn btn-secondary">Return to Records</button>
        </router-link>
        </div>

        <!-- SELECT CUSTOMER -->
        <div class="select-section">
        <div class="select-header">
            <label>Select Customer</label>
            <button class="btn btn-primary" @click.stop="show_customer_select = true">Select</button>
        </div>

        <div class="info-list">
            <div class="info-row" v-for="key in Object.keys(user)" :key="key">
            <span>{{ key }}:</span>
            <span>{{ user[key] }}</span>
            </div>
        </div>
        </div>

        <!-- SELECT PRODUCT -->
        <div class="select-section">
        <div class="select-header">
            <label>Select Product</label>
            <button class="btn btn-primary" @click.stop="show_product_select = true;">Select</button>
        </div>

        <div class="info-list">
            <div class="info-row" v-for="key in Object.keys(product)" :key="key">
            <span>{{ key }}:</span>
            <span>{{ product[key] }}</span>
            </div>
        </div>
        </div>

        <div class="action-center">
            <button class="btn btn-success" @click="goSubmit()">Add</button>
        </div>
  </div>
</template>

<script>
import SelectCustomer from '../modal/SelectCustomer.vue';
import SelectProduct from '../modal/SelectProduct.vue';
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore';

export default {
  components: {
    SelectCustomer, SelectProduct
  },
  data() {
    return {
        store: useDataStore(),
      loading: false,
      show_customer_select: false,
      show_product_select: false,
      user: {
        id: null,
        Name: "",
        Age: null,
        Sex: "",
        "Contact Number": "",
      },
      product: {
        id: null,
        Name: "",
        Description: "",
        Category: "",
        Price: null,
        Material: "",
        Dimensions: "",
        Weight: "",
      },
    };
  },
  methods: {
    reset(){
        this.user = {
            id: null,
            Name: "",
            Age: null,
            username: '',
            Sex: "",
            "Contact Number": "",
        };
        this.product = {
            id: null,
            Name: "",
            Description: "",
            Category: "",
            Price: null,
            Material: "",
            Dimensions: "",
            Weight: "",
        };
    },
    async goSubmit(){
        this.loading = true;
        const seller_id = this.store.currentUser_info.id;
        if(!this.product.Name || !this.user.Name){
            window.alert("Please select");
            this.loading = false;
            return;
        }

        const data = {
            product_id: this.product.id,
            user_id: this.user.id,
            name: this.user.Name,
            username: this.user.username,
            description: this.product.Description,
            seller_id: seller_id,
        }

        const obj = new FormData();
        obj.append('data', JSON.stringify(data));

        const res = await axios.post('/seller/new/add-record', obj);

        console.log('here: ',res.data.message);
        window.alert(res.data.message);
        this.reset();
        this.loading = false;
    },
    close(){
        this.show_customer_select = false;
        this.show_product_select = false;
    },
    select_customer(customer){
        this.close();
        console.log('customer: ', customer);
        this.user.id = customer.id;
        this.user.Name = `${customer.firstname} ${customer.m_initial ? customer.m_initial + "." : ""} ${customer.lastname}`;
        this.user['Age'] = customer.age;
        this.user['Sex'] = customer.gender === 'male' ? 'Male' : 'Female';
        this.user['Contact Number'] = customer.contact_no;
        this.user['username'] = customer.name;
    },
    select_product(product){
        this.close();
        this.product.id = product.id;
        this.product['Name'] = product.name;
        this.product['Description'] = product.description;
        this.product['Category'] = product.category;
        this.product['Price'] = product.price;
        this.product['Material'] = product.materials;
        this.product['Dimensions'] = product.dimensions;
        this.product['Weight'] = product.weight;
    }
  }
};
</script>

<style scoped>
.overlay{
  position: fixed;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  background-color: rgba(0, 0, 0, 0.555);
  display: flex;
  align-items: center;
  justify-content: center;
}
/* Reusable buttons */
.btn {
  padding: 0.5em 1.2em;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  font-size: 0.9em;
  transition: all 0.3s ease;
}

.btn-primary {
  background-color: #ca9d73;
  color: #fff;
}

.btn-primary:hover {
  background-color: #b88a5f;
}

.btn-secondary {
  background-color: #ca9d73;
  color: #252525;
  font-weight: 600;
  text-transform: uppercase;
  border-radius: 2em;
  box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.3);
}

.btn-success {
  background-color: green;
  color: white;
  width: 150px;
}

/* Containers */
.product-container {
  padding: 1.5em;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2em;
  padding-bottom: 1em;
  border-bottom: 2px solid #DDD0C8;
}

.header h1 {
  font-size: 1.8em;
  font-weight: 600;
  text-transform: uppercase;
  margin: 0;
  color: #252525;
}

/* Sections */
.select-section {
  margin-bottom: 30px;
  padding-bottom: 10px;
  border-bottom: 2px solid #DDD0C8;
}

.select-header {
  display: flex;
  align-items: center;
  gap: 20px;
}

.select-header label {
  font-size: 20px;
  font-weight: bold;
  color: #ca9d73;
}

.info-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 10px;
}

.info-row {
  display: grid;
  grid-template-columns: 180px auto;
  font-size: 13px;
  color: gray;
}

.quantity-section {
  margin-top: 20px;
}

.quantity-section .section-title {
  color: #ca9d73;
  font-weight: bold;
}

.quantity-input {
  display: grid;
  grid-template-columns: 150px auto;
  align-items: center;
  margin-top: 8px;
}

.quantity-input input {
  padding: 8px;
  width: 100%;
  max-width: 300px;
  border: 1px solid #ddd;
  border-radius: 5px;
}

/* Center Add Button */
.action-center {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

@media (max-width: 480px) {
  .header h1 {
    font-size: 12px;
  }
  .btn {
    font-size: 10px;
    padding: 0.3em 0.8em;
  }
}
</style>
