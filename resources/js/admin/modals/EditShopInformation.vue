<template>
  <div v-if="visible" class="overlay">
    <div class="modal" v-if="form">
      <div class="modal-header">
        <h2>Edit Shop Information</h2>
        <button class="close-btn" @click="close">✕</button>
      </div>

      <form class="modal-body" @submit.prevent="save">
        <div class="row">
          <label>Shop Name</label>
          <input type="text" v-model="form.name"/>
        </div>

        <div class="row">
          <label>Shop Address</label>
          <input type="text" v-model="form.address" />
        </div>

        <div class="row">
          <label>Shop Description</label>
          <input type="text" v-model="form.description" />
        </div>

        <div class="meta">
          <div><strong>Joined:</strong> {{ formatDate(user_clicked.created_at) }}</div>
          <div><strong>Last Logout:</strong> {{ user_clicked.time_logout || 'N/A' }}</div>
          <div><strong>Followers:</strong> {{ user_clicked.followers?.length || 0 }}</div>
        </div>

        <div class="actions">
          <button type="button" class="btn btn-ghost" @click="close">Cancel</button>
          <button type="submit" class="btn btn-primary" :disabled="saving">
            <span v-if="!saving">Save</span>
            <span v-else>Saving...</span>
          </button>
        </div>

        <p v-if="error" class="error">{{ error }}</p>
        <p v-if="success" class="success">Saved successfully ✅</p>
      </form>
    </div>
  </div>
</template>

<script>
import { useDataStore } from '../../stores/dataStore';
export default {
  name: 'EditUserModal',
  props: {
    modelValue: { type: Boolean, default: false },
    user_clicked: { type: Object, default: null }
  },
  emits: ['update:modelValue', 'saved'],
  data() {
    return {
      visible: true,
      saving: false,
      error: '',
      success: false,
      form: null,
    }
  },
  watch: {
    modelValue(val) {
      this.visible = val
    },
    visible(val) {
      this.$emit('update:modelValue', val)
    },
  },
  methods: {
    close() {
      this.visible = false
      this.error = ''
      this.success = false

      this.$emit('close');
    },
    formatDate(dateStr) {
      if (!dateStr) return 'N/A'
      const d = new Date(dateStr)
      return d.toLocaleDateString()
    },
    async save() {
      this.error = ''
      this.success = false
      this.saving = true

      try {
        const data = new FormData();
        data.append('data', JSON.stringify(this.form));
        data.append('user_id', this.user_clicked.id);

        const res = await axios.post('/admin/edit/shop-info', data);

        console.log(res.data.message);
        if(res.data.message === 'success'){
          window.alert("YOU HAVE SUCCESSFULLY EDITED THE INFORMATION OF A SELLER'S SHOP.");

          this.$emit('save_close');
        }
        else{
            window.alert("SOMETHING WENT WRONG! PLEASE RELOAD THE PAGE.");
        }

      } catch (err) {
        this.error = err.message
      } finally {
        this.saving = false
      }
    }
  },
  mounted(){

    console.log('user_clicked: ', this.user_clicked);
    this.form = {
        name: this.user_clicked.shop.name,
        address: this.user_clicked.shop.address,
        description: this.user_clicked.shop.description,
    };
  }
}
</script>

<style scoped>
/* === COLOR PALETTE ===
#32323290 (overlay)
#DDD0C8 (background)
#c0673e (dark orange)
#ca9d73 (light orange)
====================== */

.overlay {
  position: fixed;
  inset: 0;
  background: rgba(50, 50, 50, 0.56);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  z-index: 9999;
}

.modal {
  width: 600px;
  max-width: 95%;
  background: #DDD0C8;
  border-radius: 14px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
  overflow: hidden;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #323232;
  color: #fff;
  padding: 16px 20px;
  font-size: 1.2rem;
}

.close-btn {
  background: transparent;
  border: none;
  color: white;
  font-size: 22px;
  cursor: pointer;
}

.modal-body {
  padding: 24px 28px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  font-size: 1.05rem;
}

.row {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.row label {
  color: #323232;
  font-weight: 700;
  font-size: 1rem;
}

.row input,
.row select {
  padding: 10px 12px;
  border-radius: 10px;
  border: 1px solid rgba(0, 0, 0, 0.2);
  font-size: 1rem;
  outline: none;
}

.meta {
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid rgba(0,0,0,0.1);
  font-size: 0.95rem;
  color: #323232;
  display: flex;
  flex-wrap: wrap;
  gap: 12px 20px;
}

.actions {
  display: flex;
  justify-content: flex-end;
  gap: 14px;
  margin-top: 18px;
}

.btn {
  padding: 10px 18px;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 600;
  font-size: 1rem;
  border: none;
  transition: 0.2s ease;
}

.btn-ghost {
  background: transparent;
  color: #323232;
  border: 1px solid rgba(0, 0, 0, 0.2);
}

.btn-ghost:hover {
  background: rgba(0,0,0,0.05);
}

.btn-primary {
  background: linear-gradient(90deg, #c0673e, #ca9d73);
  color: white;
  box-shadow: 0 4px 14px rgba(192, 99, 62, 0.3);
}

.btn-primary:hover {
  opacity: 0.9;
}

.error {
  color: #b00020;
  margin-top: 8px;
  font-weight: 600;
}

.success {
  color: green;
  margin-top: 8px;
  font-weight: 600;
}
</style>
