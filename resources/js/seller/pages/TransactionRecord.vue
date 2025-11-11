<template>
    <!-- Popup Message -->
    <div class="warning-container" v-if="showWarning">
        <div class="warning-box" :class="warningType">
            <div class="warning-header">
                <strong>{{ warningType === 'success' ? 'Success' : 'Error' }}</strong>
                <button class="warning-close" @click="showWarning = false">×</button>
            </div>
            <div class="warning-body">{{ warningMessage }}</div>
        </div>
    </div>

    <!-- Confirmation Popup -->
    <div v-if="showConfirm" class="confirm-overlay">
        <div class="confirm-box">
            <h3>Confirm Delete</h3>
            <p>Are you sure you want to delete this record?</p>
            <div class="confirm-actions">
            <button class="btn-yes" @click="goDelete(deleteId)">Yes, Delete</button>
            <button class="btn-no" @click="showConfirm = false">Cancel</button>
            </div>
        </div>
    </div>

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
            <tbody v-if="products ? products.length > 0 : false">
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
                      <button class="btn-delete" @click="confirmDelete(product.id)">Delete</button>
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
  name: "TransactionRecord",
  props: ['active_status'],
  emits: ['checknotif', 'returnActiveStatus'],
  data() {
    return {
      searchQuery: "",
      products: [],
      showWarning: false,
      warningType: "", // 'success' or 'error'
      warningMessage: "",
      showConfirm: false,
      deleteId: null,
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
    confirmDelete(id) {
        this.deleteId = id;
        this.showConfirm = true;
    },
    async goDelete(id){
        try {
            const data = new FormData();
            data.append('id', id);

            const res = await axios.post('/seller/record/delete', data);

            const message = (res.data.message || '').toLowerCase();

            if (message.includes('success')) {
            this.warningType = 'success';
            this.warningMessage = 'Record deleted successfully!';
            this.products = this.products.filter(e => e.id !== id);
            } else {
            this.warningType = 'error';
            this.warningMessage = res.data.message || 'Failed to delete record.';
            }
        } catch (err) {
            console.error(err);
            this.warningType = 'error';
            this.warningMessage = 'An error occurred while deleting the record.';
        } finally {
            // CLOSE THE CONFIRMATION POPUP
            this.showConfirm = false;
            this.showWarning = true;
            setTimeout(() => {
            this.showWarning = false;
            }, 2500);
        }
    },
    closePopup() {
        this.showPopup = false;
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
      console.log(res.data.message);

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

.warning-container {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 4000;
  width: calc(100% - 2rem);
  max-width: 560px;
  display: flex;
  justify-content: center;
  pointer-events: none;
}

.warning-box {
  pointer-events: auto;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.12);
  width: 100%;
  transition: all 0.3s ease;
}

/* Error Theme */
.warning-box.error {
  background: #fff6f6;
  border: 1px solid #ffcccc;
  color: #7a1f1f;
}

/* Success Theme */
.warning-box.success {
  background: #f6fff8;
  border: 1px solid #b3ffcc;
  color: #1f7a3d;
}

.warning-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.4rem;
}

.warning-close {
  background: transparent;
  border: none;
  font-size: 1.2rem;
  line-height: 1;
  cursor: pointer;
  color: inherit;
}

.warning-body {
  font-size: 0.95rem;
}

/* Background overlay covering entire page */
.confirm-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5); /* dark overlay */
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-top: 80px;
  z-index: 5000;
}

/* Confirmation box itself */
.confirm-box {
  background: white;
  border-radius: 10px;
  padding: 20px 25px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
  width: 90%;
  max-width: 400px;
  text-align: center;
  color: #252525;
  animation: slideDown 0.25s ease-out;
}

/* Title and buttons */
.confirm-box h3 {
  margin-bottom: 8px;
}

.confirm-actions {
  margin-top: 15px;
  display: flex;
  justify-content: center;
  gap: 12px;
}

/* Buttons */
.btn-yes {
  background-color: #f44336;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 5px;
  cursor: pointer;
}
.btn-no {
  background-color: #ccc;
  border: none;
  padding: 8px 16px;
  border-radius: 5px;
  cursor: pointer;
}
.btn-yes:hover { background-color: #d9362c; }
.btn-no:hover { background-color: #bbb; }

/* Smooth slide-down animation */
@keyframes slideDown {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
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
