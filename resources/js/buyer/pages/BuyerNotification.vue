<template>
    <teleport to="body">
      <!-- <div class="overlay" @click="goexit()"></div> -->
      <div class="notify-container">
        <div class="notify-header">
            <label style="font-weight: bolder;">Notification</label>
            <label style="font-size: 12px; text-decoration: underline;">View all</label>
        </div><br>
        <div class="notify-content">
            <div class="notify-item">
                <label class="notify-text">Aurjohnmonte has sent a message.</label>
                <label class="notify-time">15 mins ago.</label>
            </div>
        </div>
      </div>
    </teleport>
</template>

<script>
export default {
    methods: {
      goexit(){
        this.$emit("goexit");
      }
    },
    mounted(){
        let path = this.$route.path;
        let new_path = path.slice(7);
        this.$emit("changepathtext", new_path);
    }
}
</script>

<style scoped>
.notify-time{
    font-size: 10px;
    color: #D25E27;
    font-weight: bolder;
}
.notify-item{
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
}
.notify-text{
    font-size: 10px;
}
.notify-content{
    display: flex;
    flex-direction: column;
}
.notify-header{
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    color: rgb(44, 44, 44);
    align-items: center;
}
.overlay{
  position: fixed;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.568);
  left: 0;
  top: 0;
  z-index: 999; 
}
.notify-container{
  position: fixed;
  width: 300px;
  height: 300px;
  background-color: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  top: 10%;
  right: 2%;
  border-radius: 5px;
  overflow-y: auto;
  padding: 20px;
  z-index: 1000; 
}
</style>