<template>
  <div class="dashboard-container">
    <div class="header">
        <div class="header-left">
            <h3>Welcome {{ user }},</h3>
            <p>Your dashboard overview</p>
            <div class="search-product">
                <div class="search-input-wrapper">
                    <input type="search" name="search" id="search" placeholder="Search your product..." @input="searchProduct">
                    <i class="fa fa-search search-icon"></i>
                </div>
                <select name="filter" id="filter">
                    <option value="">Filter by Category</option>
                    <option value="all">All</option>
                    <option v-for="category in productCategory" :key="category" :value="category">
                        {{ category }}
                    </option>
                </select>
            </div>
        </div>
        <div class="header-right">
            <router-link to="/seller/map">
                <i class="fa-solid fa-location-dot"
                    :class="{ active: $route.name === 'Map' }">
                </i>
            </router-link>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="card-container">
            <div class="cards"></div>
            <div class="cards"></div>
            <div class="cards"></div>
        </div>
        <div class="product-table">
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" style="text-align:center; color:#aaa;">No products found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
  </div>
</template>

<script>
export default {
    data() {
        return {
            productCategory: [
                'Furniture',
                'Kitchenware',
                'Musical Instrument',
                'Toys & Games',
                'Office Supplies',
                'Home Decor',
                'Personal accessories',
                'Outdoor enhancements'
            ]
        }
    }
}
</script>

<style scoped>
.dashboard-container {
    padding: 40px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-left {
    display: flex;
    flex-direction: column;
    width: 50%;
}

.header-right {
    display: flex;
    align-items: center;
}
.header-right i {
    font-size: 24px;
    color: #333;
    cursor: pointer;
    filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.582));
}
.header-right i:hover {
    color: #ff7300;
}

.search-product {
    display: flex;
    align-items: center;
    gap: 2em;
    width: 80%;
    font-size: .9em;
    color: #333;
}

.search-input-wrapper {
  position: relative;
  display: inline-block;
  width: 100%;
}

.search-input-wrapper input[type="search"] {
  padding: 0.5em 2em 0.5em 1.3em;
  border-radius: 15px;
  border: none;
  width: 100%;
  box-sizing: border-box;
  background-color: #f5f5f5;
  outline: none;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.582);
}

.search-icon {
  position: absolute;
  right: 0.7em;
  top: 50%;
  transform: translateY(-50%);
  color: #aaa;
  pointer-events: none;
  font-size: 1em;
}

.search-product select {
    padding: 0.5em 1em;
    border-radius: 15px;
    border: none;
    background-color: #DDD0C8;
    outline: 1px solid #DDD0C8;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.582);
    transition: all 0.3s ease;
}
.search-product select:hover {
    background-color: #ddd0c8bd;
}
.search-product select:focus {
    background-color: #ddd0c8bd;
}
.search-product select:active {
    background-color: #ddd0c8bd;
}

/* contents */
.dashboard-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
}
.cards {
    background-color: #DDD0C8;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.582);
    margin-bottom: 20px;
    flex: 1;
    height: 200px;
}
.card-container {
    display: flex;
    gap: 20px;
    width: 100%;
}

.product-table {
    width: 100%;
    display: flex;
    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.582);
}
</style>
