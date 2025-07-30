<template>
  <div class="more-main" @click="hideMorePicture">
    <div class="more-photos-container" @click.stop="">
      <div class="more-header">
        <label>More photos</label>
        <img src="../../../images/cancel (1).png" style="width: 15px; height: 15px;" @click.stop="hideMorePicture"/>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    hideMorePicture(){
      this.$emit("hideMorePicture");
    },
  }
}
</script>

<style scoped>
.more-header{
  width: 100%;
  height: 20px;
  border-bottom: 1px solid gray;
  align-items: center;
  display: flex;
  padding: 10px;
  justify-content: space-between;
  padding-right: 10px;
}
.more-header label{
  font-size: 15px;
}
.more-main{
  background-color: rgba(10, 10, 10, 0.493);
  width: 100vw;
  height: 100vh;
  position: fixed;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;  
  top: 0;
}
.more-photos-container{
    background-color: white;
    border-radius: 10px;
    height: 400px;
    position: absolute;
    left: 10%;
    width: 80%;
    display: flex;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
}
</style>