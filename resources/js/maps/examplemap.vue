<template>
    <div>
        <!-- <button @click="showlocation()">Show location</button> -->
        <div id="map"> 

        </div>
    </div>
</template>

<script>
import L from "leaflet";

export default {
    data(){
        return{
            map_object: null,
            user_marker: null,
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

                this.mapuserLoc(lat, lng);
                },
                (error) => {
                alert("Unable to retrieve your location. You may have denied permission.")
                console.error(error)
                }
            )
        },

        mapuserLoc(lat, lng) {
            if(!lat || !lng){
                return;
            }

            if(this.user_marker !== null){
                console.log('remove');
                this.map_object.removeLayer(this.user_marker);
                this.user_marker = null;
            }

            // Add a new marker
            this.user_marker = L.marker([lat, lng])
                                .addTo(this.map_object)
                                .bindPopup("<strong>My location </strong>")
                                .openPopup();

            //center the map
            this.map_object.setView([lat, lng], this.map_object.getZoom());
        },

        showlocation(){
            this.getLocation();
        }
    },

    mounted(){

        //to check resposition marker
        L.Marker.prototype._animateZoom = function (opt) {
            if (!this._map) {
            return;
            }
            const pos = this._map._latLngToNewLayerPoint(this._latlng, opt.zoom, opt.center).round();
            this._setPos(pos);
        };

        //to check and reposition popup
        L.Popup.prototype._animateZoom = function (opt) {
        if (!this._map) return;
        const pos = this._map._latLngToNewLayerPoint(this._latlng, opt.zoom, opt.center).round();
        const anchor = this._getAnchor();
        L.DomUtil.setPosition(this._container, pos.add(anchor));
        };

        this.map_object = L.map('map').setView([9.0753, 125.5126], 13) // sample: Butuan coordinates

        //https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png'
        //tiles sa sattelite ver.

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(this.map_object)


        L.marker([9.0753, 125.5126])
            .addTo(this.map_object)
            .bindPopup('You are here')
            .openPopup()
        
        //8.944239986238491, 125.53202965120367 gaisano
        L.marker([8.944239986238491, 125.53202965120367])
            .addTo(this.map_object)
            .bindPopup("Gaisano")
            .openPopup()
        

    }
}
</script>

<style>
#map{
    height: 100vh;
    z-index: 1;
    position: relative;
    top: 60px;
    left: 0;
}
</style>