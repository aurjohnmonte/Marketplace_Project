<template>
  <div class="profile-container">
    <div class="profile-box" :style="{ backgroundImage: `url(${user.coverPhoto})` }">
        <div class="profilebox-top">
            <div class="profile-picture">
                <img :src="user.profilePicture" alt="Profile Picture">
                <h3>{{ user.username }}</h3>
            </div>
            <div class="settings">
                <i class="fa-solid fa-gear"></i>
            </div>
        </div>

        <div class="profilebox-bottom">
            <div class="rating">
                <p>Overall Rating</p>
                <div class="stars">
                    <img src="../../../images/star.png" class="star-rate" v-for="turn in calculateStar.start" :key="turn">
                    <img src="../../../images/half-star.png" class="star-rate" v-if="calculateStar.half">
                    <img src="../../../images/no-star.png" class="star-rate" v-for="turn in calculateStar.no_star" :key="turn">
                </div>
            </div>

            <div class="followers">
                <p>Number of Followers</p>
                <p>{{ user.followers }}</p>
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
                username: 'Budol Seller',
                profilePicture: 'https://tse1.mm.bing.net/th/id/OIP.airZynZaLzvgWLOJFbVF6QHaE8?rs=1&pid=ImgDetMain&o=7&rm=3',
                coverPhoto: 'https://wallpapercave.com/wp/wp1996490.jpg',
                rating: 2.5,
                followers: 10
            },
            rate: {
                start: 0,
                half: true,
                no_star: 2,
            }
        }
    },
    computed: {
        calculateStar() {
            let rating = this.user.rating;
            let fullStars = Math.floor(rating);
            let hasHalfStar = rating % 1 !== 0;
            let emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

            return {
                start: fullStars,
                no_star: emptyStars,
                half: hasHalfStar
            };
        }
    }
}
</script>

<style scoped>
.profile-container {
    padding: 1em;
    width: 100%;
    height: 100%;
}

.profile-box {
    width: 100%;
    height: 20em;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1em;
}

.profile-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(32, 32, 32, 0.616);
    z-index: 1;
}

.profilebox-top{
    position: relative;
    z-index: 2;
    display: flex;
    width: 100%;
    justify-content: space-between;
    padding: 0 2em;
}

.profilebox-top .profile-picture img {
    width: 10em;
    height: 10em;
    border-radius: 50%;
    background: none !important;
    border: 4px solid white;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
    margin-bottom: 0.8em;
    object-fit: cover;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profilebox-top .profile-picture img:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.5);
}

.profile-picture h3 {
    color: #fff;
    text-transform: capitalize;
    font-size: 1.5em;
    font-weight: 600;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    letter-spacing: 0.5px;
}

.profilebox-bottom {
    position: relative;
    z-index: 2;
    width: 100%;
    padding: 0 1em;
}

.profilebox-top .profile-picture {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.settings i {
    display: flex;
    justify-content: flex-end;
    background-color: rgb(255, 255, 255);
    padding: 0.5em;
    border-radius: 50%;
    cursor: pointer;
}


.profilebox-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.rating {
    display: flex;
    gap: 0.5em;
}

.rating p {
    color: #fff;
    margin: 0;
    font-size: 1.2em;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.stars {
    display: flex;
    gap: 0.2em;
    align-items: center;
}

.star-rate {
    width: 1.2em;
    height: 1.2em;
}

.followers{
    display: flex;
    align-items: center;
    gap: 0.5em;
}

.followers p {
    color: #fff;
    margin: 0;
    text-align: center;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}
</style>
