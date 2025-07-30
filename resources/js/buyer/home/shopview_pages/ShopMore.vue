<template>
  <div class="more_container">
    <label class="more-option-text">MORE OPTION</label>
    <div class="options">
        <img src="../../../../images/add-friend.png">
        <label>Follow</label>
    </div>
  </div>
</template>

<script>
export default {

}
</script>

<style>
.more-option-text{
    font-size: 12px;
    color: rgb(57, 57, 57);
}
.options{
    display: grid;
    grid-template-columns: repeat(2, 2rem);
    align-items: center;
}
.options label{
    font-size: 12px;
}
.options img{
    width: 12px;
    height: 12px;
}
.more_container{
    position: absolute;
    background-color: white;
    border-radius: 5px;
    padding: 10px;
    top: 15%;
    right: 20px;
    width: 200px;
    height: 100px;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
    display: flex;
    flex-direction: column;
    gap: 20px;
};
</style>