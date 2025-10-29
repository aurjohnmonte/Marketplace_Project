<template>
        <div class="about-details" v-if="shop">
            <div class="row">
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
            <div class="details-container category">
                <span>Category:</span>
                <div class="user-category">
                    <p v-for="cat of category" :key="cat">{{ cat }}</p>
                </div>
            </div>
            <div class="details-container">
                <span>Total Products:</span>
                <p>{{ shop.products.length }}</p>
            </div>
        </div>
</template>

<script>
import { useDataStore } from '../../stores/dataStore';
export default {
    props: ['shop'],
    data() {
        return {
            category: [],
            store: useDataStore(),
        }
    },
    watch: {
        'store.selected_shop.category'(){
            this.category = JSON.parse(this.store.selected_shop.category);
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
.about-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding-bottom: 1em;
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

.category {
    display: flex;
    align-items: flex-start;
}

.user-category {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    place-content: center;
    place-items: center;
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
