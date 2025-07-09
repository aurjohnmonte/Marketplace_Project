<template>
    <div>
        <button @click="showlocation()">Show location</button>
        <div id="map" style="height: 100vh;"> 

        </div>
    </div>
</template>

<script>
import L from "leaflet";
export default {
    data(){
        return{

        }
    },
    methods: {
        getLocation() {
            if (!navigator.geolocation) {
                alert("Geolocation is not supported by your browser.")
                return
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                const lat = position.coords.latitude
                const lng = position.coords.longitude
                alert(`Your location: ${lat}, ${lng}`)
                },
                (error) => {
                alert("Unable to retrieve your location. You may have denied permission.")
                console.error(error)
                }
            )
        },

        mapuserLoc() {
            
        },

        showlocation(){
            this.getLocation();
        }
    },

    mounted(){
        const map = L.map('map').setView([9.0753, 125.5126], 13) // sample: Butuan coordinates
        //https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png'
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map)

        // Optional: Add marker
        L.marker([9.0753, 125.5126])
            .addTo(map)
            .bindPopup('You are here')
            .openPopup()
        
        //8.944239986238491, 125.53202965120367 gaisano
        L.marker([8.944239986238491, 125.53202965120367])
            .addTo(map)
            .bindPopup("Gaisano")
            .openPopup()
    }
}
</script>

<style>

</style>