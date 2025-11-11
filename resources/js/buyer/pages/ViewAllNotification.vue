<template>
  <div class="main-container">

    <teleport to="body">
        <ConfirmRecord :product="product" :seller="seller" :notify_data="notify_data" v-if="product && seller" @go_exit="go_exit"/>
    </teleport>

    <header>
        <label>Notifications</label>
    </header>
    <main>
        <div class="top-action">
            <div class="group-action">
                <input type="checkbox" @change="selectAll">
                <label>Select All</label>
            </div>
            <div class="group-action">
                <button class="btn-action" :disabled="!is_selectAll" @click="markAsRead">Mark as read</button>
                <button class="btn-action" :disabled="!is_selectAll" @click="goDelete">Delete</button>
            </div>
        </div>

        <div class="list-notify">
            <div class="group-action list" v-for="(notify, index) in filterNotifications" :key="index" :class="{is_clicked: check_select(index), unread: check_unread(notify)}" @click="goNavigate(notify)">
                <div class="list-text">
                    <label class="custom-checkbox" @click.stop>
                        <input type="checkbox" @change="clicked_list($event, index)" :checked="check_select(index)">
                        <span class="checkmark"></span>
                    </label>
                    <img :src="'/'+notify.users.profile" style="width: 20px; height: 20px; border: 1px solid gray; border-radius: 50%;">
                    <label>{{ notify.text }}</label>
                </div>
                <div style="font-size: 10px;">
                    <label>{{ returnFormatTime(notify.created_at) }}</label>
                </div>
            </div>
        </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore';
import ConfirmRecord from '../modals/ConfirmRecord.vue';

export default {
    components: {ConfirmRecord},
    data(){
        return{
            product: null,
            seller: null,
            notify_data: {},
            store: useDataStore(),
            is_selectAll: false,
            is_clicked: [],
            list_id: [],
            notifications: [],
            all_indices: [],
        }
    },
    computed: {

        filterNotifications(){

            for( let [index, notify] of this.notifications.entries()){
                this.all_indices.push(parseInt(index));
            }

            console.log('indices: ',this.all_indices);

            return this.notifications.filter(notif => notif.users.shop.user.is_deactivate === 0);
        }
    },
    methods: {
        go_exit(id){
            this.product =null;
            this.seller = null;
            this.notify_data = {};

            if(id){
                let notification = this.notifications.find(notif => notif.id === id );
                notification.status = 'answered';
            }
        },
        async goDelete(){
            
            const data = new FormData();
            data.append('data', JSON.stringify(this.list_id));

            const res = await axios.post('/delete-notif', data);
            
            console.log(res.data.message);

            if(res.data.message === 'successful'){

                this.notifications = this.notifications.filter(e => !this.list_id.includes(e.id));

                this.store.setNotifications(this.notifications);

                this.is_clicked = [];
                this.list_id = [];
            }
        },
        check_unread(notify){

            if(notify.seen){
                return false;
            }
            else{
                return true;
            }
        },
        async markAsRead(){

            const data = new FormData();
            data.append('list_id', JSON.stringify(this.list_id));

            const res = await axios.post('/buyer/mark-as-read/notification', data);

            console.log(res.data.message);

            if(res.data.message === 'successful'){

                for(let notif of this.notifications){
                    
                    if(this.list_id.includes(notif.id)){
                        notif.seen = 1;
                    }
                }

                this.store.setNotifications(this.notifications);
                this.is_clicked = [];
            }
        },
        async goNavigate(notify){


            if(notify.seen === 0){
                const res = await axios.get('/seen-notify', {
                    params: {
                        id: notify.id
                    }
                });

                console.log(res.data.message);

                for(let notif of this.notifications){

                    console.log('notif: ', notif);

                    if(notify.id === notif.id){

                        console.log('nakita raaaaa: ', notif);
                        notif.seen = 1;
                        break;
                    }
                }

                this.store.setNotifications(this.notifications);
            }

            console.log(notify);

            switch(notify.type){

                case 'message':
                    this.$router.push({name: 'BuyerConversation',
                                       params: {
                                        id: notify.from_id
                                       }
                                    })
                    break;
                case 'product':
                    this.store.setSelectedProduct(notify.products);
                    this.$router.push({name: 'BuyerProduct',
                                       params: {
                                        id: notify.products.id
                                       }
                    })
                    break;
                case 'customer record':
                    console.log('product id: ', notify.products.id);
                    console.log('NOTIFY: ',notify);
                    this.product = notify.products;
                    this.seller = notify.users;
                    this.notify_data['datetime'] = notify.updated_at;
                    this.notify_data['status'] = notify.status;
                    this.notify_data['record_id'] = notify.record_id;
                    this.notify_data['notify_id'] = notify.id;
                    this.notify_data['user_id'] = this.store.currentUser_info.id;
                    this.notify_data['product_id'] = notify.products.id;
                    break;
                
            }
        },
        check_select(index){

            if(this.is_clicked.includes(index)){
                return true;
            }
            else{
                return false;
            }
        },
        clicked_list(e, index){
            
            if(e.target.checked){
                this.is_clicked.push(index);
                this.is_selectAll = true;
                this.list_id.push(this.notifications[index].id);
            }
            else{
                console.log('unchecked')
                this.is_clicked = this.is_clicked.filter(e => e !== index);

                if(this.is_clicked.length < 1){
                    this.is_selectAll = false;
                }

                this.list_id = this.list_id.filter(e => e !== this.notifications[index].id);
            }

            console.log('list: ', this.list_id);
        },
        selectAll(e){

            if(e.target.checked){
                this.is_selectAll = true;
                for(let index in this.all_indices){

                    console.log(index);

                    if(!this.all_indices.includes(index)){
                        this.is_clicked.push(parseInt(index));
                    }

                    this.list_id.push(this.notifications[index].id);

                    console.log('all indices: ', this.is_clicked);
                }
                
                console.log('list id: ', this.list_id);
            }
            else{

                this.is_clicked = [];
                this.list_id = [];
                this.is_selectAll = false;
            }
        },
        returnFormatTime(date){
            return new Date(date).toLocaleDateString();
        }
    },
    watch: {
        'store.notifications'(newval) {
            console.log('newval view all notif: ', newval);
            this.notifications = newval;
        }
    },
    mounted(){
        this.notifications = this.store.notifications;
        console.log('notifications: ', this.store.notifications);
        let new_path = 'home';
        this.$emit("changepathtext", new_path);
    }
}
</script>

<style scoped>
.group-action{
    border-bottom: 1px solid rgb(213, 213, 213);
}
.unread{
    background-color: rgba(255, 68, 0, 0.293);
}
.is_clicked{
    border: 1px solid rgb(224, 224, 224);
    background-color: rgb(224, 224, 224);
}
.list-text{
    display: flex; flex-direction: row; align-items: center; gap: 5px;
    font-size: 10px;
}

.list{
    padding-top: 5px;
    padding-bottom: 5px;
    justify-content: space-between;
}
.list-notify{
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.btn-action{
    font-size: 10px;
}
.main-container{
    margin-top: 22%;
}
header{
    padding-left: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #ccc;
}
main{
    padding-top: 10px;
    padding-bottom: 10px;
    padding-left: 20px;
    padding-right: 20px;
}
.top-action{
    width: 100%;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: end;
    font-size: 12px;
    gap: 15px;
    margin-bottom: 10px;
}
.group-action{
    display: flex;
    flex-direction: row;
    align-items: center;
    
}
.custom-checkbox {
  position: relative;
  display: flex;
  align-items: center;
  cursor: pointer;
  font-size: 12px;
  user-select: none;
}

.custom-checkbox input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.custom-checkbox .checkmark {
  height: 16px;
  width: 16px;
  background-color: white;
  border: 1px solid #aaa;
  border-radius: 4px;
  margin-right: 6px;
  transition: background-color 0.2s, border-color 0.2s;
}

.custom-checkbox:hover .checkmark {
  border-color: #777;
}

.custom-checkbox input:checked + .checkmark {
  background-color: #4caf50;
  border-color: #4caf50;
}

.custom-checkbox .checkmark::after {
  content: "";
  position: absolute;
  display: none;
  left: 5px;
  top: 2px;
  width: 4px;
  height: 8px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.custom-checkbox input:checked + .checkmark::after {
  display: block;
}

@media (min-width: 768px){
    .main-container{
        margin-top: 5%;
    }
    header{
        font-size: 25px;
        box-sizing: border-box;
        margin-left: 100px;
        margin-right: 100px;
    }
    .top-action{
        font-size: 17px;
        gap: 25px;
        box-sizing: border-box;
        padding-right: 80px;
        padding-left: 80px;
    }
    .group-action{
        gap: 10px;
    }
    .btn-action{
        font-size: 17px;
    }
    .list-text{
        display: flex; flex-direction: row; align-items: center; gap: 5px;
        font-size: 17px;
    }
    .list{
        margin-right: 80px;
        margin-left: 80px;
    }
    .list-notify{
        gap: 20px;
    }
}

</style>