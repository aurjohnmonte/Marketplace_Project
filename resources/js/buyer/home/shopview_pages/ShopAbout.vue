<template>
  <div class="shop-about-wrapper">
    <h2 class="section-title">About This Shop</h2>

    <!-- Shop Info Section -->
    <div class="about-section">
      <div class="info-row">
        <span class="label">Shop Name:</span>
        <span class="value">{{ shop.shopName }}</span>
      </div>
      <div class="info-row">
        <span class="label">Seller Name:</span>
        <span class="value">{{ shop.sellerName }}</span>
      </div>
    </div>

    <!-- Contact Info -->
    <div class="about-section">
      <div class="info-row">
        <span class="label">Address:</span>
        <span class="value">{{ shop.address }}</span>
      </div>
      <div class="info-row">
        <span class="label">Contact Number:</span>
        <span class="value">{{ shop.contact }}</span>
      </div>
      <div class="info-row">
        <span class="label">Email:</span>
        <span class="value">{{ shop.email }}</span>
      </div>
    </div>

    <!-- Description -->
    <div class="about-section">
      <div class="info-row">
        <span class="label">Shop Description:</span>
        <span class="value">{{ shop.description }}</span>
      </div>
    </div>

    <!-- Statistics -->
    <div class="about-section">
      <div class="info-row">
        <span class="label">Total Products:</span>
        <span class="value">{{ shop.totalProducts }}</span>
      </div>
      <div class="info-row">
        <span class="label">Categories:</span>
        <span class="value category-list">
          <span v-for="(cat, i) in shop.categories" :key="i" class="category-tag">
            {{ cat }}
          </span>
        </span>
      </div>
      <div class="info-row">
        <span class="label">Followers:</span>
        <span class="value">{{ shop.followers }}</span>
      </div>
      <div class="info-row">
        <span class="label">Shop Rating:</span>
        <span class="value">{{ shop.rating }} / 5</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ShopAbout",
  data() {
    return {
      shop: {
        shopName: "DigiGadget Store",
        sellerName: "John Monte",
        address: "Blk 1 Lot 23, Makati City",
        contact: "09123456789",
        email: "support@digigadget.com",
        description:
          "We offer a wide range of authentic tech gadgets and accessories at affordable prices. Customer satisfaction is our top priority.",
        totalProducts: 245,
        categories: ["Electronics", "Gadgets", "Accessories", "Mobile"],
        followers: 3200,
        rating: 4.8,
      },
    };
  },
};
</script>

<style scoped>
.shop-about-wrapper {
  background-color: #fff;
  padding: 20px;
  font-size: 12px;
  color: #323232;
  margin: auto;
}

.section-title {
  font-size: 16px;
  font-weight: bold;
  margin-bottom: 16px;
  color: #d25e27;
  border-bottom: 1px solid gray;
  padding-bottom: 8px;
}

.about-section {
  margin-bottom: 18px;
  padding-bottom: 12px;
  border-bottom: 1px solid #ccc;
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 4px 0;
}

.label {
  font-weight: 600;
  color: #323232;
  flex: 0 0 140px;
}

.value {
  color: #666;
  flex: 1;
  text-align: right;
}

.description {
  text-align: justify;
  line-height: 1.5;
}

.category-list {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.category-tag {
  background-color: #d25e27;
  color: white;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 11px;
}
</style>
