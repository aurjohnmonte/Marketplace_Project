<template>
  <div class="overlay" @click="$emit('cancel')">
    <div class="modal" @click.stop>
      <h2>Leave a Review</h2>
      <div class="form-group">
        <label for="rating">Rating:</label>
        <select v-model="review_info.rate" id="rating">
          <option disabled value="">Select a rating</option>
          <option v-for="n in 5" :key="n" :value="n">{{ n }} Star{{ n > 1 ? 's' : '' }}</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="comment">Comment:</label>
        <textarea v-model="review_info.comment" id="comment" placeholder="Write your comment here..."></textarea>
      </div>

      <div class="buttons">
        <button class="cancel" @click="$emit('cancel')">Cancel</button>
        <button class="submit" @click="submitReview">Submit</button>
      </div>
    </div>
  </div>
</template>

<script>
import { useDataStore } from '../../../stores/dataStore';
import axios from 'axios';
export default {
  data() {
    return {
      review_info: {
            rate: "",
            comment: "",
        },
    };
  },
  methods: {
    async submitReview() {
      if (!this.review_info.rate || !this.review_info.comment.trim()) {
        alert('Please provide a rating and comment.');
        return;
      }

      const data = new FormData();
      const store = useDataStore();
      let user_id = store.currentUser_info.id;
      let shop_id = store.selected_shop.id;
      data.append('from_id', user_id);
      data.append('to_id', shop_id);
      data.append('type', 'shop');
      data.append('review_info', JSON.stringify(this.review_info));

      const res = await axios.post('/buyer/add/review', data);

      const review = {
        created_at: new Date(),
        comment: this.review_info.comment,
        rate: this.review_info.rate,
        user: {
          name: store.currentUser_info.name,
          profile: store.currentUser_info.profile
        }
      }

      store.selected_shop.overall_rate = res.data.message;
      
      console.log(res.data.message);
      this.$emit('cancel');
      this.$emit('addReview', review);
    },

  },
};
</script>

<style scoped>
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 3000;
}

.modal {
  background-color: white;
  padding: 1rem;
  border-radius: 12px;
  width: 80%;
  max-width: 400px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
}

h2 {
  margin-bottom: 1rem;
  text-align: center;
  color: #D25E27;
}

.form-group {
  margin-bottom: 1.2rem;
  display: flex;
  flex-direction: column;
}

select,
textarea {
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 12px;
}

textarea {
  resize: vertical;
  min-height: 80px;
}

.buttons {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
}

button {
  padding: 0.6rem 1.2rem;
  font-size: 1rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

button.cancel {
  background-color: #ddd;
  color: #333;
}

button.submit {
  background-color: #D25E27;
  color: white;
}

button:hover {
  opacity: 0.9;
}
</style>
