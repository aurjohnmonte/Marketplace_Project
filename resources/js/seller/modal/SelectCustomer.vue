<template>
  <div class="modal-overlay">
    <div class="modal-container">
      <!-- Close Button -->
        <button class="modal-close" @click="$emit('close')">×</button>

      <!-- Search Bar -->
      <div class="modal-header" style="margin-top: 20px;">
        <label style="font-size: 20px; font-weight: bolder;">Select Customer</label>
        <div style="display: flex; flex-direction: column; gap: 10px;">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search..." 
            class="search-input"
          />
          <div style="width: 100%; display: flex; justify-content: end;">
            <select style="width: 100px;" v-model="filter">
              <option value="name">Name</option>
              <option value="username">Username</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="modal-body">
        <table class="result-table">
          <thead>
            <tr>
              <th style="width: 60%;">Name</th>
              <th>Username</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <!-- Loading -->
            <tr v-if="is_loading">
              <td colspan="3" style="text-align: center; color: red;">
                <img src="../../../images/kOnzy.gif" style="width: 30px; height: 30px;">
              </td>
            </tr>

            <!-- Data Rows -->
            <tr v-else-if="filteredData.length > 0" v-for="(item, idx) in filteredData" :key="idx">
              <td>{{ formatName(item) }}</td>
              <td>{{ item.name }}</td>
              <td style="text-align: center;">
                <button 
                  class="btn-select"
                  @click="$emit('select_customer', item)"
                >
                  Select
                </button>
              </td>
            </tr>

            <!-- No Result -->
            <tr v-else>
              <td colspan="3" style="text-align: center; color: red;">No result</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>    
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      searchQuery: "",
      customers: [],
      is_loading: true,
      filter: 'name',
    };
  },
  computed: {
    filteredData() {
      
      if (!this.searchQuery) return this.customers;


      const query = this.searchQuery.toLowerCase();

      if(this.filter === 'name'){
        //if search based on name
        return this.customers.filter(c =>
          this.formatName(c).toLowerCase().includes(query)
        )
      }
      else{
        //if search based on username
        return this.customers.filter(c =>
          c.name.toLowerCase().includes(query)
        )
      }
    },
  },
  methods: {
    formatName(customer) {
      return `${customer.firstname} ${customer.m_initial ? customer.m_initial + "." : ""} ${customer.lastname}`;
    },
    async returnUsers() {
      try {
        this.is_loading = true;
        const res = await axios.get("/seller/return-users");
        this.customers = res.data.users;
      } catch (err) {
        console.error("Error fetching users:", err);
      } finally {
        this.is_loading = false;
      }
    },
  },
  mounted() {
    this.returnUsers();
  },
};
</script>

<style scoped>
.modal-container {
  width: 70%;
  height: 70%;
  background-color: white;
  border-radius: 10px;
  padding: 1em;
  position: relative;
  display: flex;
  flex-direction: column;
}

.modal-overlay {
  background-color: rgba(0, 0, 0, 0.75);
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 9000;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 22px;
  font-weight: bold;
  background: none;
  border: none;
  cursor: pointer;
}

.modal-header {
  display: flex;
  flex-direction: row;
  align-items: center;
  width: 100%;
  justify-content: space-between;
  margin-bottom: 10px;

}

.search-input {
  padding: 0.5em;
  border: 1px solid #ccc;
  border-radius: 6px;
  width: 300px;
}

.modal-body {
  flex: 1;
  overflow-y: auto;
}

.result-table {
  width: 100%;
  border-collapse: collapse;
}

.result-table th,
.result-table td {
  padding: 8px 12px;
  border: 1px solid #ddd;
  font-size: 14px;
  text-align: left;
}

.result-table th {
  background-color: #f5f5f5;
}

.btn-select {
  width: 80px;
  background-color: green;
  color: white;
  border: none;
  padding: 5px;
  border-radius: 5px;
  cursor: pointer;
}

.btn-select:hover {
  background-color: darkgreen;
}
</style>
