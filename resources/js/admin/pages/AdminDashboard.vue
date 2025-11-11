<template>
  <div class="dashboard-container">
    <h1>WELCOME ADMIN</h1>

    <div class="dashboard-content">

      <!-- LEFT SIDE -->
      <div class="left-side">
        <!-- STATS SECTION -->
        <div class="stats-container">

          <!-- USERS CARD -->
          <div class="card users-card">
            <div class="main">
              <div class="icon"><img src="../../../images/team.png" /></div>
              <div class="label">{{ data.users.total }} TOTAL USERS</div>
            </div>
            <div class="sub">
              <div class="sub-item">
                <div class="icon"><img src="../../../images/store.png" /></div>
                <span>{{ data.users.sellers }} SELLERS</span>
              </div>
              <div class="sub-item">
                <div class="icon"><img src="../../../images/buyer.png" /></div>
                <span>{{ data.users.buyers }} BUYERS</span>
              </div>
            </div>
          </div>

          <!-- DEACTIVATED CARD -->
          <div class="card deactivated-card">
            <div class="icon"><img src="../../../images/locked-card.png" /></div>
            <h2>{{ data.users.deactivate }}</h2>
            <p>DEACTIVATED ACCOUNTS</p>
          </div>

          <!-- PRODUCTS CARD -->
          <div class="card product-card">
            <div class="product-item">
              <div class="icon"><img src="../../../images/products.png" /></div>
              <span>{{ data.products.total }} TOTAL PRODUCTS</span>
            </div>
            <div class="product-item">
              <div class="icon"><img src="../../../images/block.png" /></div>
              <span>{{ data.products.blocked }} BLOCKED PRODUCTS</span>
            </div>
          </div>
        </div>

        <!-- TABLE SECTION -->
        <div class="table-container">
          <div class="table-header">
            <input type="search" placeholder="🔍 Search user by username" v-model="search_text_user"/>
          </div>
          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>USERNAME</th>
                  <th>CLIENT TYPE</th>
                  <th>FULLNAME</th>
                  <th>STATUS</th>
                  <th>CREATED AT</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user of filteredUsers" :key="user">
                  <td>{{ user.name }}</td>
                  <td>{{ user.role }}</td>
                  <td>{{ user.firstname }} {{ user.lastname }}</td>
                  <td>{{ user.is_deactivate === 0 ? 'Active' : 'Blocked' }}</td>
                  <td>{{ returnFormatDate(user.created_at) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- RIGHT SIDE -->
      <div class="right-side">
        <div class="notif-header">
          <h3>NOTIFICATIONS</h3>
          <label class="see-all" @click="$router.push({name: 'AdminNotificationPage'});">See all</label>
        </div>

        <div class="notif-content" v-if="notifs_list.length > 0">

          <div style="width: 100%; display: flex; flex-direction: column; gap: 10px;">
            <span 
            v-for="notif of notifs_list" 
            :key="notif" 
            style="width: 100%; padding: 5px; border-radius: 5px; display: flex; flex-direction: row; justify-content: space-between;"
            :class="{is_seen: notif.seen === 0}"
            >
              <label>{{ notif.text }} - <strong>{{ notif.users.name }}</strong></label>
              <label>{{ returnFormatDate(notif.created_at) }}</label>
            </span>
          </div>
        </div>

        <div class="notif-content" v-else>
          <p>No new notifications.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {

  data(){
    return{
      users_list: [],
      notifs_list: [],
      search_text_user: '',
      data: {
        'users': {
          'sellers': 0,
          'buyers': 0,
          'total': 0,
          'deactivate': 0,
        },
        'products': {
          'total': 0,
          'blocked': 0
        },
        'notif': []
      },
    }
  },
  computed: {

    filteredUsers(){

      if(this.users_list && this.users_list.length > 0){

        return this.users_list.filter(user => user.name.includes(this.search_text_user));
      }
    }
  },

  methods: {

    returnFormatDate(d){

      const date = new Date(d);

      return `${date.toLocaleDateString()} - ${date.toLocaleTimeString()}`;
    },

    process_data(data){

        console.log('data: ', data);

        if(!data){
          console.log('no data');
          return;
        }

        this.notifs_list = data.notif;

        //for users
        data.users.forEach(user => {

            this.users_list.push(user);

            this.data.users.total++;

            if(user.role === 'seller'){
              this.data.users.sellers++;
            }
            else{
              this.data.users.buyers++;
            }

            if(user.is_deactivate === 1){
              this.data.users.deactivate++;
            }
        });

        //for products
        data.products.forEach(product => {

            this.data.products.total++;
            if(product.is_blocked === 1){
              this.data.products.blocked++;
            }
        });

        console.log('this data: ', this.data);
    },

    async returnData(){

      const res = await axios.get('/admin/return-data/dashboard');

      console.log(res.data.message);

      this.process_data(res.data.data);
    }
  },

  async mounted(){

    await this.returnData();
  }
};
</script>

<style scoped>
.is_seen{
  background-color: #007bff67;
}
/* LAYOUT */
.dashboard-container {
  width: 100%;
  padding: 20px 70px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  gap: 20px;
  background-color: #f6f7fb;
}

h1 {
  font-size: 28px;
  font-weight: bold;
  color: #333;
}

.dashboard-content {
  display: flex;
  flex-direction: row;
  gap: 20px;
}

/* LEFT SIDE */
.left-side {
  flex: 0 0 60%;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* RIGHT SIDE */
.right-side {
  flex: 0 0 35%;
  background-color: #ffffff;
  border-radius: 10px;
  border: 1px solid #ddd;
  padding: 20px;
  box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.05);
}

.notif-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.see-all {
  color: #007bff;
  cursor: pointer;
  font-size: 14px;
}

.notif-content {
  border-top: 1px solid #ddd;
  padding-top: 10px;
  max-height: 500px;
  overflow-y: scroll;
  overflow-x: hidden;
  font-size: 14px;
  color: #555;
}

/* STATS */
.stats-container {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.card {
  background-color: #ffffff;
  border: 1px solid #ddd;
  border-radius: 12px;
  padding: 15px;
  flex: 1;
  box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.05);
  position: relative;
  transition: all 0.2s ease-in-out;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0px 5px 12px rgba(0, 0, 0, 0.1);
}

/* USERS CARD */
.users-card {
  display: flex;
  flex-direction: column;
}

.users-card .main {
  display: flex;
  align-items: center;
  border-bottom: 1px solid #ddd;
  padding-bottom: 8px;
  gap: 10px;
  font-weight: bold;
  font-size: 16px;
}

.users-card .sub {
  display: flex;
  flex-direction: column;
  margin-top: 8px;
}

.sub-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 5px 0;
}

/* DEACTIVATED */
.deactivated-card {
  text-align: center;
  font-weight: bold;
  color: #d9534f;
}

.deactivated-card h2 {
  font-size: 30px;
  margin: 10px 0 0 0;
}

/* PRODUCTS */
.product-card {
  display: flex;
  flex-direction: column;
}

.product-item {
  border-bottom: 1px solid #ddd;
  padding: 8px 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* ICONS */
.icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
}

.icon img {
  width: 40px;
  height: 40px;
}

/* TABLE SECTION */
.table-container {
  background-color: #ffffff;
  border-radius: 12px;
  border: 1px solid #ddd;
  box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.table-header {
  padding: 15px;
  border-bottom: 1px solid #ddd;
}

.table-header input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
}

.table-wrapper {
  max-height: 400px;
  overflow-y: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

th,
td {
  padding: 12px;
  border-bottom: 1px solid #eee;
}

th {
  background-color: #f9f9f9;
  font-weight: bold;
  color: #333;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .dashboard-content {
    flex-direction: column;
  }
  .left-side,
  .right-side {
    width: 100%;
  }
}

@media (max-width: 700px) {
  .stats-container {
    flex-direction: column;
  }

  table {
    font-size: 14px;
  }

  .icon img {
    width: 30px;
    height: 30px;
  }

  .table-header input {
    font-size: 14px;
  }
}
</style>
