<template>
    <div class="about-container">
        <div class="desc">
            <h6>Shop Description</h6>
            <p>{{ user.description }}</p>
        </div>
        <div class="about-details">
            <div class="details-container">
                <span>Seller Name:</span>
                <p>{{ user.sellerName }}</p>
            </div>
            <div class="details-container">
                <span>Email :</span>
                <p>{{ user.email }}</p>
            </div>
            <div class="details-container">
                <span>Contact Number:</span>
                <p>{{ user.contacts }}</p>
            </div>
            <div class="details-container">
                <span>Address:</span>
                <p>{{ user.address }}</p>
            </div>
            <div class="row">
                <div class="details-container">
                    <span>Category:</span>
                    <p>{{ user.category }}</p>
                </div>
                <div class="details-container">
                    <span>Total Products:</span>
                    <p>{{ user.totalProducts }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            user: {
                sellerName: 'Bravo',
                shop: 'Budol Seller',
                profilePicture: 'https://tse1.mm.bing.net/th/id/OIP.airZynZaLzvgWLOJFbVF6QHaE8?rs=1&pid=ImgDetMain&o=7&rm=3',
                coverPhoto: 'https://wallpapercave.com/wp/wp1996490.jpg',
                email: 'bravobudol@gmail.com',
                contacts: '090909090909',
                address: 'balay',
                rating: 2.5,
                followers: 10,
                category: 'Furniture',
                description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vel nisi sed felis dictum lobortis. Vivamus ut justo in diam malesuada vestibulum sed in lorem. Praesent imperdiet enim in eros porta, sit amet semper nunc maximus. Phasellus mauris ligula, volutpat pretium dolor ut, mattis pulvinar felis. Duis vehicula massa velit, non sollicitudin leo facilisis et. Aliquam leo nisl, elementum sit amet sapien vitae, mattis suscipit risus. Curabitur sollicitudin fermentum mauris non pellentesque. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Aenean vehicula semper felis id posuere. Aenean finibus enim et fermentum volutpat. Etiam ac finibus sem, eget elementum ante.',
                totalProducts: 10
            }
        }
    }
}
</script>

<style scoped>
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
