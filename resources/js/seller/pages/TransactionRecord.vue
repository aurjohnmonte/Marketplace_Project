<template>
  <div class="record-main-container">
    <!-- Add Product Button -->
    <div class="btn-add-container">
      <router-link to="/seller/add-record">
        <button class="btn-add">Add Record</button>
      </router-link>
    </div>

    <form @submit.prevent>
      <header>
        <div class="search-container">
          <div class="search-bar">
            <input 
              type="search" 
              v-model="searchQuery"
              placeholder="Enter client name..." 
              aria-label="Search client"
            />
            <i class="fa fa-search search-icon"></i>
          </div>
        </div>
      </header>
    </form>
    <div style="width: 100%; margin-top: 20px; border-radius: 20px; overflow: auto;  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);">
        <table class="record-table">
            <thead>
                <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Materials</th>
                <th>Dimensions</th>
                <th>Weight</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
                </tr>
            </thead>
            <tbody v-if="products.length > 0">
                <tr 
                v-for="(product, indx) in filteredProducts" 
                :key="product.id"
                >
                  <td>{{ indx + 1 }}</td>
                  <td>{{ product.name }}</td>
                  <td>{{ product.product_id }}</td>
                  <td>{{ product.product.name }}</td>
                  <td>{{ product.product.description }}</td>
                  <td>{{ product.product.materials }}</td>
                  <td>{{ product.product.dimensions }}</td>
                  <td>{{ product.product.weight }}</td>
                  <td>{{ product.status }}</td>
                  <td>{{ formatDate(product.created_at) }}</td>
                  <td class="action-btn">
                      <button class="btn-delete" @click="goDelete(product.id)">Delete</button>
                  </td>
                  </tr>
                  <tr v-if="filteredProducts.length === 0">
                  <td colspan="10" class="no-data">No results found.</td>
                </tr>
            </tbody>
            <tbody v-else>
              <tr>
                <td colspan="20"> No result</td>
              </tr>
            </tbody>
        </table>
    </div>
  </div>
</template>

<script>
import { useDataStore } from '../../stores/dataStore';
export default {
  data() {
    return {
      searchQuery: "",
      products: [],
    };
  },
  computed: {
    filteredProducts() {
      return this.products.filter(p =>
        p.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    }
  },
  methods: {
    async goDelete(id){
      console.log('id: ', id);
      const data = new FormData();
      data.append('id', id);

      const res = await axios.post('/seller/record/delete', data);

      window.alert(res.data.message);

      console.log(res.data.message);

      if(res.data.message === 'success'){
        this.products = this.products.filter(e => e.id !== id);
      }
    },
    formatDate(datetime){

      const date = new Date(datetime);

      const format_date = `${date.toLocaleDateString()} - ${date.toLocaleTimeString()}`;

      return format_date;
    },
    async returnRecords(){
      const store = useDataStore();

      const id = store.currentUser_info.id;

      const data = new FormData();
      data.append('id', id);

      const res = await axios.post('/seller/return/all-records', data);

      console.log(res.data.records);

      this.products = res.data.records;

    }
  },
  mounted(){
    this.returnRecords();
  }
};
</script>

<style scoped>
/* Container */
.record-main-container {
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  padding: 20px;
}

/* Add Product */
.btn-add-container {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 20px;
}
.btn-add {
  background-color: #ca9d73;
  color: #252525;
  font-weight: 600;
  text-transform: uppercase;
  width: 150px;
  padding: 8px 0;
  border-radius: 2em;
  border: none;
  box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
  cursor: pointer;
  transition: background 0.2s;
}
.btn-add:hover {
  background-color: #b88658;
}

/* Search Bar */
.search-container {
  display: flex;
  align-items: center;
  width: 100%;
}
.search-bar {
  display: flex;
  align-items: center;
  width: 100%;
  max-width: 400px;
  position: relative;
}
.search-bar input {
  border: 1px solid #ccc;
  border-radius: 20px;
  width: 100%;
  padding: 8px 40px 8px 15px;
}
.search-bar i {
  position: absolute;
  right: 12px;
  color: gray;
}

/* Table */
.record-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
  border-radius: 20px;
  overflow: hidden;
}
.record-table tbody tr{
    background-color: #d4edda;
}
.record-table th{
    border: 1px solid gray;
    padding: 10px;
    text-align: center;
}
.record-table td {
  border: 1px solid white;
  padding: 10px;
  text-align: center;
}
.record-table th {
  background: #DDD0C8;
}
.no-data {
  text-align: center;
  font-style: italic;
  color: #777;
  padding: 15px;
  font-size: 25px;
}

/* Action Buttons */
.action-btn {
  display: flex;
  gap: 8px;
  justify-content: center;
}
.action-btn button {
  flex: 1;
  padding: 5px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.btn-edit {
  background: #4caf50;
  color: white;
}
.btn-edit:hover {
  background: #449d48;
}
.btn-delete {
  background: #f44336;
  color: white;
}
.btn-delete:hover {
  background: #d9362c;
}

/* Responsive */
@media (max-width: 480px) {
  .action-btn {
    flex-direction: column;
  }
  .search-bar {
    max-width: 100%;
  }
}
</style>
