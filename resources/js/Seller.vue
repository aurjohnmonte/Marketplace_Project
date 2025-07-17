<template>
    <div class="seller-container">
        <nav class="navbar navbar-light bg-light justify-content-between">
            <img src="../images/Minimal_Logo.svg" alt="Logo" class="logo">
            <a class="navbar-brand">Navbar</a>
            <div class="form-inline">
                <i class="fa-solid fa-bell"></i>
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </div>
        </nav>


        <!--  <router-link to="/seller/dashboard">CLICK TO NAVIGATE DASHBOARD</router-link>

        THIS IS WHERE THE COMPONENT APPEAR WHEN CLICKED ROUTER-LINK / OR THE RESULT WHEN CLICKED.
        <router-view></router-view>

         THIS IS THE EXAMPLE OF MAP. FREE TO REMOVE THIS
         <h1>example of map</h1>
        <examplemap/>
        END HERE FOR EXAMPLE OF MAP-->

    </div>
</template>
<script>
import examplemap from './maps/examplemap.vue';
export default{
    components: {
        examplemap
    },
    data(){
        return{
            page: 'Dashboard'
        }
    },
    methods: {
        changePage(switchPage) {
            this.page = switchPage;
        }
    }
}
</script>
<style scoped>
.logo {
    width: 50px;
}
</style>
