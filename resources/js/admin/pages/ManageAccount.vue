<template>
  <div class="manage-account-container">

        <!--IS LOADING-->
        <UserSeemore 
        v-if="user_clicked && open_modal === 'view'" 
        :user="user_clicked" @close="user_clicked = null"
         @goEdit="goEdit"
        @save_deactivate="save_deactivate"/>
        <EditPersonalInformation :user_clicked="user_clicked" v-if="user_clicked && open_modal === 'p_info'" :user="user_clicked" @close="closeEdit" @save_close="save_close"/>
        <EditShopInformation :user_clicked="user_clicked" v-if="user_clicked && open_modal === 's_info'" :user="user_clicked" @close="closeEdit" @save_close="save_close"/>

        <div class="pick-user">
                <span :class="{is_clicked : clicked_type === 'seller'}" @click="clicked_type = 'seller'">
                    <label>SELLER</label>
                </span>
                <span  :class="{is_clicked: clicked_type === 'buyer'}" @click="clicked_type = 'buyer';">
                    <label>BUYER</label>
                </span>
        </div>
        <div class="search">
            <label>Search</label>
            <div style="display: flex; flex-direction: row; align-items: center; justify-content: space-between; width: 100%;">
                <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                    <input type="search" placeholder="Search ..." class="search-bar" v-model="search_text">
                    <select v-model="search_type" style="padding: 10px; font-size: 15px;">
                        <option value="">All</option>
                        <option value="username">USERNAME</option>
                        <option value="name">NAME</option>
                    </select>

                    <select v-model="client_status_search" style="padding: 10px; font-size: 15px;">
                        <option value="">All</option>
                        <option value="block">Blocked</option>
                        <option value="active">Actived</option>
                    </select>

                    <button @click="search()" style="padding: 10px; font-size: 15px;">Search</button>
                    <button @click="search_clear()" style="padding: 10px; font-size: 15px;">Clear</button>
                </div>
                <label style="font-weight: bolder;">{{ clicked_type.toUpperCase() }}</label>
            </div>
        </div>

        <label class="count-result"><strong>{{ count_result }}</strong> result/s</label>
        <div class="table-c" v-if="clicked_type === 'seller'" >
            <table border="1">
                <thead>
                    <tr>
                        <th>TYPE</th>
                        <th>IS BLOCKED</th>
                        <th>USERNAME</th>
                        <th>TOTAL REVIEWS</th>
                        <th>FULLNAME</th>
                        <th>SHOP NAME</th>
                        <th>SHOP ADDRESS</th>
                        <th>OVERALL RATE</th>
                    </tr>
                </thead>
                
                <tbody>
                    <tr v-for="(user, index) of search_data" :key="index" @click="user_clicked = user">

                        <td>{{ user.role }}</td>
                        <td>{{ user.is_deactivate ? 'YES' : 'NO' }}</td>
                        <td>{{ user.name }}</td>
                        <td>{{ user.shop.reviews.length }}</td>
                        <td>{{ user.firstname}} {{ user.m_initial ? `${user.m_initial}.` : '' }} {{ user.lastname }}</td>
                        <td>{{ user.shop.name }}</td>
                        <td>{{ user.shop.address }}</td>
                        <td>{{ user.shop.overall_rate }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="table-c" v-else>
            <table border="1">
                <thead>
                    <tr>
                        <th>TYPE</th>
                        <th>IS BLOCKED</th>
                        <th>USERNAME</th>
                        <th>FULLNAME</th>
                        <th>Birthday</th>
                        <th>ADDRESS</th>
                    </tr>
                </thead>
                
                <tbody>
                    <tr v-for="(user, index) of search_data" :key="index"  @click="user_clicked = user;">

                        <td>{{ user.role }}</td>
                        <td>{{ user.is_deactivate ? 'YES' : 'NO' }}</td>
                        <td>{{ user.name }}</td>
                        <td>{{ user.firstname}} {{ user.m_initial ? `${user.m_initial}.` : '' }} {{ user.lastname }}</td>
                        <td>{{ user.birthday }}</td>
                        <td>{{ user.current_address }}</td>
                    </tr>
                </tbody>
            </table>
        </div>  
        <div style="width: 100%; display: flex; flex-direction: row; align-items: center; justify-content: center; margin-top: 30px;" v-if="is_loading">
            <img src="../../../images/kOnzy.gif" style="width: 100px; height: 100px;">
        </div>
  </div>
</template>

<script>
import axios from 'axios';
import UserSeemore from '../modals/UserSeemore.vue';
import EditPersonalInformation from '../modals/EditPersonalInformation.vue';
import EditShopInformation from '../modals/EditShopInformation.vue';

export default {
    components: {
        UserSeemore, 
        EditPersonalInformation, 
        EditShopInformation
    },
    computed: {
        filteredUsers(){

            let return_data;

            if(this.clicked_type === "seller"){

                return_data = this.search_data.filter(user => user.role === "seller");
            }
            else{
                 return_data = this.search_data.filter(user => user.role === "buyer");
            }

            this.count_result = return_data.length;

            return return_data;
        }       
    },
    data() {
        return{
            search_type: '',
            clicked_type: 'seller',
            client_status_search: '',
            open_modal: 'view',
            is_loading: false,
            count_result: 100,
            user_clicked: null,
            buyers: [],
            sellers: [],
            search_text: '',
            search_data: [],
        }
    },
    
    watch: {
        clicked_type(){

            this.search();
        }
    },
    methods: {

        search_clear(){

            this.search_text = '';
            this.client_status_search = '';
            this.search_type = '';

            this.search();
        },

        async save_deactivate(){

            this.user_clicked = null;
            this.open_modal = 'view';

            await this.returnUsers();
        },

        search(){
            let return_data;

            if(this.clicked_type === "seller"){

                return_data = this.sellers.filter(user => user.role === "seller");
            }
            else{ 
                 return_data = this.buyers.filter(user => user.role === "buyer");
            }

            //for search_type
            if(this.search_type === 'username'){

                return_data = return_data.filter(user => user.name.toLowerCase().includes(this.search_text.toLowerCase()));
            }
            else if(this.search_type === 'name'){

                return_data = return_data.filter(user => this.returnFullname(user).toLowerCase().includes(this.search_text.toLowerCase()));
            }

            //for status
            if(this.client_status_search === 'active'){

                return_data = return_data.filter(user => user.is_deactivate === 0);
            }
            else if(this.client_status_search === 'block'){

                return_data = return_data.filter(user => user.is_deactivate === 1);
            }

            this.count_result = return_data.length;

            this.search_data = return_data;
        },

        returnFullname(user){

            return `${user.firstname} ${user.lastname}`;
        },


        async save_close(){

            this.open_modal = 'view';
            this.user_clicked = null;
            await this.returnUsers();
        },

        closeEdit(){

            this.open_modal = 'view';
        },

        goEdit(action){

            console.log('action: ', action);
            this.open_modal = action;
            console.log('user clicked: ', this.user_clicked);

        },

        async returnUsers(){

            this.is_loading = true;

            const res = await axios.get('/admin/users/return-all');

            this.buyers = res.data.buyers;
            this.sellers = res.data.sellers;

            console.log('buyers: ', this.buyers);
            console.log('sellers: ', this.sellers);

            this.search();

            this.is_loading = false;
        }
    },
    async mounted() {

        await this.returnUsers();
    }   
}
</script>

<style scoped>
.manage-account-container {
  width: 100%;
  box-sizing: border-box;
  padding: 20px 10px;
}

/* Pick User */
.pick-user {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 25px;
}

.pick-user span {
  border: 1px solid #32323290;
  padding: 8px;
  min-width: 80px;
  text-align: center;
  border-radius: 8px;
  font-size: 14px;
  transition: 0.3s;
}

.pick-user span:hover {
  background-color: #555;
  color: white;
  cursor: pointer;
}

.is_clicked {
  color: white;
  background-color: #323232;
}

/* Search section */
.search {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 20px;
}

.search label {
  font-size: 18px;
  font-weight: bold;
}

.search-bar {
  padding: 8px;
  border-radius: 6px;
  border: 1px solid gray;
  width: 100%;
  font-size: 14px;
}

.search select,
.search button {
  padding: 8px;
  font-size: 14px;
  border-radius: 6px;
  border: 1px solid #ccc;
  cursor: pointer;
}

.search button:hover {
  background-color: #e5e5e5;
}

/* Result count */
.count-result {
  font-size: 14px;
  margin-bottom: 10px;
  display: block;
}

/* Tables */
.table-c {
  width: 100%;
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  min-width: 700px;
}

table thead tr th {
  font-size: 12px;
  padding: 6px;
  min-width: 100px;
  background-color: #c0673e;
  color: white;
  text-align: left;
}

table tbody tr td {
  font-size: 13px;
  text-align: start;
  padding: 6px;
}

table tbody tr:hover {
  background-color: rgba(128, 128, 128, 0.2);
  cursor: pointer;
}

/* Loading gif */
.loading {
  width: 100%;
  display: flex;
  justify-content: center;
  margin-top: 30px;
}

.loading img {
  width: 80px;
  height: 80px;
}

/* Overlay */
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  background-color: rgba(0, 0, 0, 0.45);
  width: 100%;
  height: 100vh;
}

/* -------------- MEDIA QUERIES -------------- */

/* Medium devices (tablets) */
@media screen and (min-width: 600px) {
  .manage-account-container {
    padding: 40px;
  }

  .search {
    flex-direction: column;
  }

  .search-bar {
    width: 70%;
    font-size: 16px;
  }

  .search select,
  .search button {
    font-size: 16px;
  }

  table thead tr th {
    font-size: 14px;
    padding: 10px;
  }

  table tbody tr td {
    font-size: 14px;
    padding: 8px;
  }

  .count-result {
    font-size: 16px;
  }
}

/* Large devices (desktop) */
@media screen and (min-width: 1024px) {
  .manage-account-container {
    padding: 60px 80px;
  }

  .pick-user span {
    font-size: 18px;
    padding: 10px 15px;
    min-width: 100px;
  }

  .search {
    flex-direction: column;
    gap: 15px;
  }

  .search label {
    font-size: 28px;
  }

  .search-bar {
    width: 400px;
    padding: 10px;
    font-size: 18px;
  }

  table thead tr th {
    font-size: 18px;
    padding: 12px;
  }

  table tbody tr td {
    font-size: 16px;
    padding: 10px;
  }

  .count-result {
    font-size: 18px;
  }
}
</style>
