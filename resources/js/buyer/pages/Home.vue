<template>
  <div>
    <!-- <BuyerHome v-if="!isbrowse"/>
    <BuyerBrowse v-if="isbrowse"/> -->
    
  </div>
</template>

<script>
import BuyerHome from '../home/BuyerHome.vue';
import BuyerBrowse from '../home/BuyerBrowse.vue';
export default {
  components: {
    BuyerHome,
    BuyerBrowse
  },
  data(){
    return{
      isbrowse: true,
    }
  },
  methods: {
    goSearch(info){
      console.log('info: ', info);
    }
  },
  mounted(){
    this.$router.push({name: "BuyerHome"});
  }
}
</script>

<style>

</style>