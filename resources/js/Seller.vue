<template>
    <div>
        <!--THIS IS JUST AN EXAMPLE. YOU CAN REALLY CHANGE THIS IF YOU WANT BAI-->
        <form method="GET" action="/seller/logout">
            <button type="submit">Logout</button>
        </form>
        <h1>HELLO THIS IS THE SELLER SIDE</h1>
        <!--THIS IS THE ANCHOR TAG WHEN USER CLICK TO NAVIGATE-->
        <router-link to="/seller/dashboard">CLICK TO NAVIGATE DASHBOARD</router-link>

        <!--THIS IS WHERE THE COMPONENT APPEAR WHEN CLICKED ROUTER-LINK / OR THE RESULT WHEN CLICKED. -->
        <router-view></router-view>

        <!-- THIS IS THE EXAMPLE OF MAP. FREE TO REMOVE THIS-->
         <h1>example of map</h1>
        <examplemap/>
        <!-- END HERE FOR EXAMPLE OF MAP-->

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

        }
    }
}
</script>
<style scoped>

</style>