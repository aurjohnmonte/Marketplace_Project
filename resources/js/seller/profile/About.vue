<template>
    <div class="about-container" v-if="shop">
        <div class="desc">
            <h6>Shop Description</h6>
            <p>{{ shop.description }}</p>
        </div>
        <div class="about-details">
            <div class="details-header">
                <h6>Shop & Seller Information</h6>
                <button class="edit-btn" @click="$emit('edit-seller-info')">
                    <i class="fa-solid fa-edit"></i>
                </button>
            </div>
            <div class="row" style="grid-template-columns: auto auto;">
                <div class="details-container">
                    <span>Seller Name:</span>
                    <p>{{shop.user.firstname}} {{ shop.user.m_initial }}. {{ shop.user.lastname }}</p>
                </div>
                <div class="details-container">
                    <span>Seller Age:</span>
                    <p>{{shop.user.age}}</p>
                </div>
            </div>

            <div class="row">
                <div class="details-container">
                    <span>Gender :</span>
                    <p>{{ shop.user.gender }}</p>
                </div>
                <div class="details-container">
                    <span>Birthday:</span>
                    <p>{{ shop.user.birthday }}</p>
                </div>
            </div>

            <div class="row">
                <div class="details-container">
                    <span>Email :</span>
                    <p>{{ shop.user.email }}</p>
                </div>
                <div class="details-container">
                    <span>Contact Number:</span>
                    <p>{{ shop.user.contact_no }}</p>
                </div>
            </div>
            <div class="details-container">
                <span>Address:</span>
                <p>{{ shop.address }}</p>
            </div>
            <div class="row">
                <div class="details-container category">
                    <span>Category:</span>
                    <p v-for="cat of category" :key="cat">{{ cat }}</p>
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
.about-container {
    padding: 0 1.5rem 0 1.5rem;
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.desc {
    flex: 1.2;
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
    padding: 1em 1em 1em 0em;
    border-radius: 0.5em;
    gap: 1em;
}

.details-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5em;
}

.details-header h6 {
    font-family: 'Playfair Display', serif;
    font-size: 1.2em;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.edit-btn {
    background: none;
    border: none;
    color: #5c473c;
    cursor: pointer;
    padding: 0.4em 0.5em 0.4em .7em;
    border-radius: 50%;
    transition: background-color 0.2s ease;
    font-size: 1em;
}

.edit-btn:hover {
    color:  #fef8f4;
    background-color: #967f70;
}

.row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    column-gap: 1em;
    align-items: start;
    padding: 0 .9em;
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
    transition: .3s ease-in-out;
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

@media screen and (max-width: 767px) {
    .about-container {
        flex-direction: column;
        padding: 0 1rem;
    }

    .row {
        grid-template-columns: 1fr;
        gap: 1em;
    }

    .details-container {
        font-size: 0.9em;
        grid-template-columns: 1fr;
        text-align: left;
    }

    .details-container span {
        display: block;
        margin-bottom: 0.3em;
    }
}

@media screen and (min-width: 768px) and (max-width: 1024px) {
    .about-container {
        flex-direction: column;
    }

    .row {
        grid-template-columns: 1fr 1fr;
    }
}

@media screen and (min-width: 1025px) {
    .about-container {
        flex-direction: row;
    }
}
</style>
