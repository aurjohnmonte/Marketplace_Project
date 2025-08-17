<template>
    <div class="about-container" v-if="shop">
        <div class="desc">
            <h6>Shop Description</h6>
            <p>{{ shop.description }}</p>
        </div>
        <div class="about-details">
            <div class="details-container">
                <span>Seller Name:</span>
                <p>{{ shop.name }}</p>
            </div>
            <div class="details-container">
                <span>Email :</span>
                <p>{{ shop.user.email }}</p>
            </div>
            <div class="details-container">
                <span>Contact Number:</span>
                <p>{{ shop.user.contact_no }}</p>
            </div>
            <div class="details-container">
                <span>Address:</span>
                <p>{{ shop.address }}</p>
            </div>
            <div class="row">
                <div class="details-container">
                    <span>Category:</span><br>
                    <div style="display: flex; flex-direction: column;" class="cat">
                        <label v-for="cat of category" :key="cat">{{ cat }}</label>
                    </div>
                </div>
                <div class="details-container">
                    <span>Total Products:</span>
                    <p>{{ shop.products.length }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['shop'],
    data() {
        return {
            category: [],
        }
    },
    mounted(){
        console.log(this.shop);
        this.category = JSON.parse(this.shop.category);
        console.log(this.category);

        for(let cat of this.category){
            console.log('cat: ', cat);
        }
    }
}
</script>

<style scoped>
.cat label{
    background-color: orange;
    text-align: center;
    width: 160px;
    color: white;
    padding: 5px;
    border-radius: 10px;
}
.about-container {
    padding: 1.5rem 1.5rem 0 1.5rem;
    display: flex;
}

.desc {
    flex: 2;
    padding: 1em;
    border-radius: 0.5em;
}

.desc h6 {
    font-family: 'Playfair Display', serif;
    font-size: 1.5em;
    font-weight: 700;
    color: #333;
    margin-bottom: .4em;
}

.desc p {
    text-indent: 2em;
}

.about-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 1em  1.5em;
    border-radius: 0.5em;
    gap: 1em;
}

.row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    column-gap: 1em;
    row-gap: 0.5em;
    align-items: center;
    padding: 0 1em;
}

.details-container {
    background-color: #bdbbba98;
    display: grid;
    grid-template-columns: auto 1fr;
    column-gap: 1em;
    row-gap: 0.5em;
    align-items: center;
    padding: 0.75em 1em;
    border-radius: 1em;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.479);
    font-size: .8em;
    transition: .3s ease-in-out
}

.details-container:hover {
    transform: scale(1.1);
}

.details-container span {
    font-weight: 600;
    color: #333;
}

.details-container p {
    margin: 0;
    color: #666;
    overflow-wrap: anywhere;
}
</style>
