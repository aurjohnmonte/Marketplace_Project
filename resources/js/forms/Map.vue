<template>
        <!-- <button @click="showlocation()">Show location</button> -->
    <!-- Inline warning container -->
    <div class="warning-container" v-if="showWarning">
        <div class="warning-box">
            <div class="warning-header">
                <strong>Error</strong>
                <button class="warning-close" @click="showWarning = false">×</button>
            </div>
            <div class="warning-body">{{ warningMessage }}</div>
        </div>
    </div>

    <div id="map">

    </div>
</template>

<script>
import L from "leaflet";

export default {
    props: ['shopData'],
    data(){
        return{
            map_object: null,
            user_marker: null,
            coordinate: {
                lat: null,
                long: null,
            }
            ,showWarning: false,
            warningMessage: ''
        }
    },
    methods: {
        goSave(){
            console.log('save')
        },
        onMapClick(e) {
            const popup = L.popup()
                .setLatLng(e.latlng)
                .setContent("You clicked the map at: <br><strong>" + e.latlng.lat.toFixed(8) + ", " + e.latlng.lng.toFixed(8) + "</strong>.")
                .openOn(this.map_object);

            console.log("Clicked coordinates:", e.latlng); // Optional: log in console
            this.coordinate.lat = e.latlng.lat.toFixed(8);
            this.coordinate.long = e.latlng.lng.toFixed(8)

            this.$emit("returnCoordinate", this.coordinate);
        },
        getLocation() {
            if (!navigator.geolocation) {
                this.warningMessage = "Geolocation is not supported by your browser.";
                this.showWarning = true;
                return
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                const lat = position.coords.latitude
                const lng = position.coords.longitude

                this.mapuserLoc(lat, lng);
                },
                (error) => {
                this.warningMessage = "Unable to retrieve your location. You may have denied permission.";
                this.showWarning = true;
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

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri — Sources: Esri, Garmin, USGS, etc.',
            maxZoom: 19,
            minZoom: 3,
        }).addTo(this.map_object);


        if(this.shopData.latitude !== "none" && this.shopData.longitude !== "none"){
            L.marker([this.shopData.latitude, this.shopData.longitude])
            .addTo(this.map_object)
            .bindPopup('Selected Coordinate')
            .openPopup()
        }

        this.map_object.on('click', this.onMapClick);


    }
}
</script>

<style>
#map{
    width: 100%;
    height: 100%;
    position: relative;
    object-fit: contain;
    display: flex;
    top: 0;
}

/* Warning container styles similar to the windows.alert */
.warning-container {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 4000;
    width: calc(100% - 2rem);
    max-width: 560px;
    display: flex;
    justify-content: center;
    pointer-events: none;
}
.warning-box {
    pointer-events: auto;
    background: #fff6f6;
    border: 1px solid #ffcccc;
    color: #7a1f1f;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
}
.warning-header{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.4rem;
}
.warning-close{
    background: transparent;
    border: none;
    font-size: 1.2rem;
    line-height: 1;
    cursor: pointer;
    color: #7a1f1f;
}
.warning-body{
    font-size: 0.95rem;
}
</style>
