<template>
  <div class="following-shops-container">
    <div class="header">
      <label>FOLLOWING SHOPS</label>
    </div>

    <div class="search-bar">
      <input type="text" placeholder="Search" v-model="searchQuery" />
      <span v-if="searchQuery" class="clear-btn" @click="searchQuery = ''">✕</span>
    </div>
    <div style="margin-bottom: 30px; font-size: 14px;">
      <label>10 Shops followed</label>
    </div>
    <div class="shop-list-following">
      <div class="shop-list">
      <div class="shop-card" v-for="shop in filteredShops" :key="shop.id">
        <div class="shop-info">
          <img src="../../../images/images (1).jpg" alt="shop avatar" class="shop-avatar" />
          <label class="shop-name">{{ shop.name }}</label>
        </div>
        <div class="shop-actions">
          <img src="../../../images/send.png"/>
          <img src="../../../images/location.png"/>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      searchQuery: '',
      shops: [
        {
          id: 1,
          name: 'Shop Value',
          image: 'https://via.placeholder.com/40', // replace with real image later
        },
        {
          id: 2,
          name: 'Other Shop',
          image: 'https://via.placeholder.com/40',
        },
        {
          id: 2,
          name: 'Other Shop',
          image: 'https://via.placeholder.com/40',
        },
        {
          id: 2,
          name: 'Other Shop',
          image: 'https://via.placeholder.com/40',
        },
        {
          id: 2,
          name: 'Other Shop',
          image: 'https://via.placeholder.com/40',
        },
        {
          id: 2,
          name: 'Other Shop',
          image: 'https://via.placeholder.com/40',
        },
        {
          id: 2,
          name: 'Other Shop',
          image: 'https://via.placeholder.com/40',
        },
        {
          id: 2,
          name: 'Other Shop',
          image: 'https://via.placeholder.com/40',
        },
        {
          id: 2,
          name: 'Other Shop',
          image: 'https://via.placeholder.com/40',
        },
      ],
    };
  },
  mounted(){
    let path = this.$route.path;
    let new_path = path.slice(7);
    this.$emit("changepathtext", new_path);
  },
  computed: {
    filteredShops() {
      return this.shops.filter((shop) =>
        shop.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
  },
};
</script>

<style scoped>
.following-shops-container {
  padding: 20px;
  padding-top: 80px;
  font-family: Arial, sans-serif;
  background-color: #fdfdfd;
}

.header label {
  font-weight: bold;
  font-size: 14px;
  color: #111;
}

.search-bar {
  margin: 10px 0 20px;
  display: flex;
  align-items: center;
  position: relative;
}

.search-bar input {
  width: 100%;
  padding: 8px 30px 8px 10px;
  border-radius: 20px;
  border: 1px solid #ccc;
  font-size: 14px;
}

.clear-btn {
  position: absolute;
  right: 10px;
  cursor: pointer;
  font-size: 14px;
  color: #888;
}
.shop-list-following{
  overflow: scroll;
  height: 400px;
}
.shop-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.shop-card {
  background: #fff;
  border-radius: 8px;
  padding: 10px 15px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.shop-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.shop-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #ccc;
}

.shop-name {
  font-size: 14px;
  font-weight: 600;
  color: #222;
}
.shop-actions {
  display: flex;
  flex-direction: row;
  gap: 10px;
}
.shop-actions img {
  width: 20px;
  height: 20px;
}

.material-icons {
  font-family: 'Material Icons';
}
</style>
