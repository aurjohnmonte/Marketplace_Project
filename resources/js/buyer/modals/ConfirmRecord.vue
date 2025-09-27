<template>
  <div class="record-confirm-container">
    <div class="modal">
      <!-- Header -->
      <div class="header_one">
        <h3>Confirm Record</h3>
        <img 
          src="../../../images/cancel.png" 
          class="close-icon" 
          @click="$emit('go_exit', null)"
        />
      </div>

      <!-- Body -->
      <main>
        <section>
          <h5>Product Information</h5>
          <div class="info-grid">
            <label>Product name:</label><span>{{ product.name }}</span>
            <label>Price:</label><span>PHP {{ product.price }}</span>
            <label>Dimension:</label><span>{{ product.dimensions }}</span>
            <label>Materials:</label><span>{{ product.materials }}</span>
            <label>Weight:</label><span>{{ product.weight }}</span>
            <label>Description:</label><span>{{ product.description }}</span>
          </div>
        </section>

        <section>
          <h5>Shop Information</h5>
          <div class="info-grid">
            <label>Shop name:</label><span>{{ seller.shop.name }}</span>
            <label>Address:</label><span>{{ seller.shop.address }}</span>
            <label>Seller name:</label><span>{{ seller.firstname }} {{ seller.m_initial }} {{ seller.lastname }}</span>
            <label>Email:</label><span>{{ seller.email }}</span>
            <label>Contact number:</label><span>{{ seller.contact_no }}</span>
          </div>
        </section>

        <p class="date-added">
          Added to the record on {{ returnDateformat }}
        </p>
      </main>

      <!-- Footer -->
      <!-- <div class="btn-choose" v-if="notify_data.status === 'pending'">
        <button class="btn-secondary" @click="goAction('ignore')">Ignore</button>
        <button class="btn-primary" @click="goAction('confirm')">Confirm</button>
      </div> -->
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: ['product', 'seller', 'notify_data'],
  computed: {
    returnDateformat() {
      const date = new Date(this.notify_data.datetime);
      return `${date.toLocaleDateString()} - ${date.toLocaleTimeString()}`;
    }
  },
  methods: {
    async goAction(action){

        console.log('notify data: ', this.notify_data);
        const data = new FormData();
        data.append('seller_username', this.seller.name);
        data.append('seller_id', this.seller.id);
        data.append('record_id', this.notify_data.record_id);
        data.append('action', action);
        data.append('notify_id', this.notify_data.notify_id);
        data.append('user_id', this.notify_data.user_id);
        data.append('product_id', this.notify_data.product_id);

        const res = await axios.post('/buyer/respond/to-record/from-seller', data);

        window.alert(res.data.message);

        this.$emit('go_exit', this.notify_data.notify_id);
    }
  }
};
</script>

<style scoped>
/* Overlay */
.record-confirm-container {
  position: fixed;
  inset: 0;
  z-index: 1000;
  background-color: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Modal */
.modal {
  width: 90%;
  max-width: 600px;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0px 6px 20px rgba(0,0,0,0.2);
}

/* Header */
.header_one {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 15px 20px;
  border-bottom: 1px solid #ddd;
}

.header_one h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}

.close-icon {
  width: 16px;
  height: 16px;
  cursor: pointer;
}

/* Body */
main {
  padding: 20px;
  max-height: 400px;
  overflow-y: auto;
  overflow-x: hidden;
}

h5 {
  font-size: 14px;
  margin: 15px 0 8px;
  font-weight: 600;
  color: #333;
}

.info-grid {
  display: grid;
  grid-template-columns: 35% 65%;
  gap: 6px 12px;
  font-size: 13px;
}

.info-grid label {
  font-weight: 600;
  color: #444;
}

.info-grid span {
  color: #555;
}

/* Footer */
.btn-choose {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 15px 20px;
  border-top: 1px solid #ddd;
}

.btn-primary {
  background: #2563eb;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.2s;
}

.btn-primary:hover {
  background: #1e4fd1;
}

.btn-secondary {
  background: #f3f4f6;
  color: #333;
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.2s;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

/* Date text */
.date-added {
  font-size: 12px;
  margin-top: 15px;
  color: #666;
}
</style>
