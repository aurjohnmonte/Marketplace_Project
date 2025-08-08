<template>
  <div class="more-main" @click="hideMorePicture">
    <div class="more-photos-container" @click.stop="">
      <div class="more-header">
        <label>More photos</label>
        <img src="../../../images/cancel (1).png" style="width: 15px; height: 15px;" @click.stop="hideMorePicture"/>
      </div>
      <div class="content">
        <img v-for="photo in photos" :key="photo" :src="'/'+photo.filename">
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['photos'],
  methods: {
    hideMorePicture(){
      this.$emit("hideMorePicture");
    },
  }
}
</script>

<style scoped>
.content {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); /* Responsive */
  gap: 1rem;
  padding: 1rem;
  overflow-y: auto;
  flex-grow: 1;
}

.content img {
  width: 100%;
  height: 100px;
  object-fit: cover; /* Keeps aspect ratio and fills container */
  border-radius: 5px;
  cursor: pointer;
}
.more-header {
  width: 100%;
  height: auto;
  border-bottom: 1px solid gray;
  align-items: center;
  display: flex;
  padding: 20px 10px;
  justify-content: space-between;
  box-sizing: border-box;
}

.more-header label {
  font-size: 15px;
}

.more-main {
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

.more-photos-container {
  display: flex;
  flex-direction: column;
  background-color: white;
  border-radius: 10px;
  height: 400px;
  width: 80%;
  left: 10%;
  position: absolute;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
}

</style>