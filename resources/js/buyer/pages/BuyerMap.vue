<template>
  <div>
    <div class="search-filter">
        <div style="display: flex; flex-direction: row; gap: 2px;">
          <input placeholder="Search ...">
          <select>
            <option>Shop</option>
            <option>Seller</option>
          </select>
        </div>
        <div class="map-result-container" @click="hide_result = false;">
          <div class="search-map-result" v-if="!hide_result">
          </div>
          <div style="border-bottom: 3px solid gray; width: 97%;"></div>
        </div>
    </div>
    <examplemap @click="goHideResult" :shops="shops" v-if="show"/>
  </div>
</template>

<script>
import examplemap from '../../maps/examplemap.vue';
export default {
    components: {
      examplemap
    },
    data(){
      return{
        show: false,
        shops: null,
        hide_result: true,
        search_result: [1],
      }
    },
    methods: {
      goHideResult(){
        this.hide_result = true;
      },
      async returnShops(){
        
        const res = await axios.get("/return/shop");

        console.log(res.data.shops);
        this.shops = res.data.shops;
      }
    },
    async mounted(){
        window.scrollTo(0, 0);
        let path = this.$route.path;
        let new_path = path.slice(7);
        this.$emit("changepathtext", new_path);
        await this.returnShops();
        this.show = true;
    }
}
</script>

<style scoped>
.map-result-container{
  background-color: white;
  padding: 5px;
  width: 97%;
}
.search-map-result{
  width: 97%;
  height: 200px;
  background-color: white;
  border-radius: 5px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}
.search-filter{
  position: fixed;
  top: 70px;
  z-index: 500;
  right: 0;
  gap: 2px;
  display: flex;
  flex-direction: column;
}
.search-filter select{
  margin-right: 5px;
  padding: 5px;
  font-size: 12px;
  width: 70px;
}
.search-filter input{
  padding-left: 10px;
  width: 150px;
}
</style>