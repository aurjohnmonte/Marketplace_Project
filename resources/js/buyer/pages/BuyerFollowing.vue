<template>
  <div>
    <h1>FOLLOWING</h1>
  </div>
</template>

<script>
export default {
    mounted(){
        let path = this.$route.path;
        let new_path = path.slice(7);
        this.$emit("changepathtext", new_path);
    }
}
</script>

<style>

</style>