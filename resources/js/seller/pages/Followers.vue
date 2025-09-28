<template>
  <div class="followers-container">
    <div class="overlay" v-if="loading">
      <img src="../../../images/kOnzy.gif" style="width: 100px; height: 100px;">
    </div>

    <div class="followers-header">
      <h2>Followers</h2>
      <p>Manage and view your shop followers</p>
    </div>

    <div class="followers-content">
      <div class="followers-stats">
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fa-solid fa-users"></i>
          </div>
          <div class="stat-info">
            <h3>Total Followers</h3>
            <p class="stat-number">{{ followers.length }}</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fa-solid fa-user-plus"></i>
          </div>
          <div class="stat-info">
            <h3>New This Month</h3>
            <p class="stat-number">{{ current_month_total }}</p>
          </div>
        </div>
      </div>

      <div class="followers-list">
        <div class="list-header">
          <h3>Recent Followers</h3>
          <button class="refresh-btn" @click="loadFollowers()">
            <i class="fa-solid fa-sync-alt"></i>
            Refresh
          </button>
        </div>

        <div class="filter-search">
          <label style="font-weight: bolder;">Search by name</label><br>
          <input type="text" placeholder="Enter follower name ..." v-model="search.name">
        </div>

        <div class="empty-state" v-if="followers.length < 1">
          <div class="empty-icon">
            <i class="fa-solid fa-users-slash"></i>
          </div>
          <h4>No Followers Yet</h4>
          <p>When customers follow your shop, they'll appear here. Focus on creating great products and excellent service to attract followers!</p>
        </div>

        <div v-else style="margin-top: 20px;">
            <table class="followers-table">
              <thead>
                <tr>
                  <th></th>
                  <th>Name</th>
                  <th>Address</th>
                  <th>Contact No.</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(follower, index) in filteredFollowers" :key="index">
                  <td>
                    <img :src="'/'+follower.followed_by.profile" style="width: 60px; height: 60px; border-radius: 50%;">
                  </td>
                  <td>{{ follower.followed_by.firstname }} {{ follower.followed_by.m_initial }} {{ follower.followed_by.lastname }}</td>
                  <td>{{ follower.followed_by.current_address }}</td>
                  <td>{{ follower.followed_by.contact_no }}</td>
                  <td>
                    <button class="action-btn view" @click="goMessage(follower.followed_by.name)">
                      <i class="fa-regular fa-comment"></i> Message
                    </button>
                    <button class="action-btn remove" @click="goRemoveFollower(follower.followed_by.id)">
                      <i class="fa-solid fa-user-minus"></i> Remove
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore';

export default {
  name: 'Followers',
  data() {
    return {
      followers: [],
      search: {
        name: '',
      },
      loading: false,
      store: useDataStore(),
      current_month_total: 0,
    }
  },
  computed: {
    filteredFollowers(){

      let data = [];

      data = this.followers.filter(e => this.returnSearch(`${e.followed_by.firstname} ${e.followed_by.m_initial} ${e.followed_by.lastname}`));

      return data;
    }
  },
  methods: {
    returnSearch(name){

      if(name.includes(this.search.name)){
        return true;
      }

      return false;
    },
    goMessage(username){
      console.log('hello')
      this.$router.push({name: 'Chats', query: {username: username}});
    },
    async goRemoveFollower(follower_id){
      this.loading = true;
      const id = this.store.currentUser_info.id;
      const data = new FormData();
      data.append('follower_id', follower_id);
      data.append('user_id', id);

      console.log(follower_id + " - " + id);

      const res = await axios.post('/seller/remove-follower', data);

      window.alert(res.data.message);

      this.followers = this.followers.filter(e => e.followed_by.id !== follower_id);
      this.loading = false;
    },
    async loadFollowers() {
      this.loading = true;
      // TODO: Implement API call to load followers

      const id = this.store.currentUser_info.id;

      const data = new FormData();
      data.append('id', id);

      const res = await axios.post('/seller/return-followers', data);

      console.log(res.data.followers);
      console.log(res.data.current_month_total);
      this.followers = res.data.followers;
      this.current_month_total = res.data.current_month_total;
      this.loading = false;
    }
  },
  mounted() {
    this.loadFollowers();
  }
}
</script>

<style scoped>
.followers-container {
    width: 100%;
    padding: 2em;
}
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
.followers-table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  font-size: 0.95rem;
}

.followers-table thead {
  background: #ca9d73;
  color: white;
}

.followers-table th,
.followers-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #f0f0f0;
}

.followers-table tbody tr:hover {
  background: #faf6f3;
}

.followers-table th:first-child,
.followers-table td:first-child {
  border-left: none;
}

.followers-table th:last-child,
.followers-table td:last-child {
  border-right: none;
}

/* Action buttons */
.action-btn {
  border: none;
  padding: 0.5rem 0.9rem;
  border-radius: 8px;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-right: 0.4rem;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
}

.action-btn.view {
  background: #4caf50;
  color: white;
}

.action-btn.view:hover {
  background: #43a047;
}

.action-btn.remove {
  background: #e74c3c;
  color: white;
}

.action-btn.remove:hover {
  background: #c0392b;
}

/* Responsive table for mobile */
@media (max-width: 768px) {
  .followers-table thead {
    display: none;
  }

  .followers-table,
  .followers-table tbody,
  .followers-table tr,
  .followers-table td {
    display: block;
    width: 100%;
  }

  .followers-table tr {
    margin-bottom: 1rem;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    padding: 0.8rem;
  }

  .followers-table td {
    padding: 0.8rem;
    text-align: right;
    position: relative;
  }

  .followers-table td::before {
    content: attr(data-label);
    position: absolute;
    left: 1rem;
    font-weight: bold;
    text-align: left;
    color: #555;
  }
}

.filter-search input{
  width: 300px;
  padding: 5px;
  padding-left: 20px;
  border-radius: 10px;
  border: 1px solid gray;
}
.followers-container {
  max-width: 1200px;
  margin: 0 auto;
}

.followers-header {
  text-align: center;
  margin-bottom: 3rem;
}

.followers-header h2 {
  font-size: 2.5rem;
  color: #333;
  margin-bottom: 0.5rem;
  font-weight: 700;
}

.followers-header p {
  font-size: 1.1rem;
  color: #666;
  margin: 0;
}

.followers-content {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.followers-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.stat-card {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1.5rem;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

.stat-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #ca9d73, #c0673e);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}

.stat-info h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1rem;
  color: #666;
  font-weight: 500;
}

.stat-number {
  margin: 0;
  font-size: 2rem;
  font-weight: 700;
  color: #333;
}

.followers-list {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #f0f0f0;
}

.list-header h3 {
  margin: 0;
  font-size: 1.5rem;
  color: #333;
  font-weight: 600;
}

.refresh-btn {
  background: #ca9d73;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.2s ease;
}

.refresh-btn:hover {
  background: #b88a5f;
}

.empty-state {
  text-align: center;
  padding: 3rem 1rem;
}

.empty-icon {
  width: 80px;
  height: 80px;
  background: #f8f9fa;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
  color: #ccc;
  font-size: 2rem;
}

.empty-state h4 {
  margin: 0 0 1rem 0;
  font-size: 1.3rem;
  color: #666;
  font-weight: 600;
}

.empty-state p {
  margin: 0;
  color: #999;
  line-height: 1.6;
  max-width: 400px;
  margin: 0 auto;
}

/* ===== RESPONSIVE DESIGN ===== */

/* Small mobile devices */
@media (max-width: 480px) {
  .followers-container {
    padding: 1rem;
  }

  .followers-header h2 {
    font-size: 1.8rem;
  }

  .followers-header p {
    font-size: 1rem;
  }

  .followers-stats {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .stat-card {
    padding: 1.5rem;
    gap: 1rem;
  }

  .stat-icon {
    width: 50px;
    height: 50px;
    font-size: 1.2rem;
  }

  .stat-number {
    font-size: 1.5rem;
  }

  .followers-list {
    padding: 1.5rem;
  }

  .list-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .refresh-btn {
    width: 100%;
    justify-content: center;
  }

  .empty-state {
    padding: 2rem 0.5rem;
  }

  .empty-icon {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
  }
    .filter-search input{
    width: 270px;
  }
}

/* Mobile - Medium devices */
@media (max-width: 768px) {

  .followers-header h2 {
    font-size: 2rem;
  }

  .followers-stats {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
  }

  .stat-card {
    padding: 1.5rem;
    gap: 1rem;
  }

  .stat-icon {
    width: 55px;
    height: 55px;
    font-size: 1.3rem;
  }

  .stat-number {
    font-size: 1.8rem;
  }

  .followers-list {
    padding: 1.5rem;
  }

  .list-header {
    margin-bottom: 1.5rem;
  }

  .list-header h3 {
    font-size: 1.3rem;
  }

  .refresh-btn {
    padding: 0.6rem 1.2rem;
    font-size: 0.85rem;
  }
}

/* Tablet devices */
@media (min-width: 769px) and (max-width: 1024px) {
  .followers-container {
    padding: 2rem;
  }

  .followers-header h2 {
    font-size: 2.2rem;
  }

  .followers-stats {
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
  }

  .stat-card {
    padding: 1.8rem;
    gap: 1.3rem;
  }

  .stat-icon {
    width: 58px;
    height: 58px;
    font-size: 1.4rem;
  }

  .stat-number {
    font-size: 1.9rem;
  }

  .followers-list {
    padding: 1.8rem;
  }

  .list-header h3 {
    font-size: 1.4rem;
  }

  .refresh-btn {
    padding: 0.7rem 1.4rem;
    font-size: 0.9rem;
  }
}

/* Desktop devices */
@media (min-width: 1025px) {

  .followers-header h2 {
    font-size: 2.5rem;
  }

  .followers-stats {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
  }

  .stat-card {
    padding: 2rem;
    gap: 1.5rem;
  }

  .stat-icon {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
  }

  .stat-number {
    font-size: 2rem;
  }

  .followers-list {
    padding: 2rem;
  }

  .list-header h3 {
    font-size: 1.5rem;
  }

  .refresh-btn {
    padding: 0.75rem 1.5rem;
    font-size: 0.9rem;
  }
}

/* Large desktop devices */
@media (min-width: 1440px) {

  .followers-header h2 {
    font-size: 3rem;
  }

  .followers-stats {
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2.5rem;
  }

  .stat-card {
    padding: 2.5rem;
    gap: 2rem;
  }

  .stat-icon {
    width: 70px;
    height: 70px;
    font-size: 1.8rem;
  }

  .stat-number {
    font-size: 2.5rem;
  }

  .followers-list {
    padding: 2.5rem;
  }

  .list-header h3 {
    font-size: 1.7rem;
  }

  .refresh-btn {
    padding: 0.9rem 1.8rem;
    font-size: 1rem;
  }
}

/* Landscape orientation for mobile */
@media (max-width: 768px) and (orientation: landscape) {
  .followers-header {
    margin-bottom: 2rem;
  }

  .followers-stats {
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
  }

  .stat-card {
    padding: 1rem;
  }
}

/* High DPI displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  .stat-card {
    border-radius: 16px;
  }

  .followers-list {
    border-radius: 16px;
  }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
  .stat-card,
  .refresh-btn {
    min-height: 44px;
    min-width: 44px;
  }
}

/* Print styles */
@media print {
  .followers-container {
    padding: 0;
  }

  .refresh-btn {
    display: none;
  }

  .stat-card,
  .followers-list {
    break-inside: avoid;
    box-shadow: none;
    border: 1px solid #ddd;
  }
}
</style>
