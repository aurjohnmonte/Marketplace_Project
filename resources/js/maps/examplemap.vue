<template>
    <div>
        <!-- <button @click="showlocation()">Show location</button> -->
        <div id="map"> 

        </div>
    </div>
</template>

<script defer>
import L from "leaflet";
import { watch } from "vue";
import { useDataStore } from "../stores/dataStore";

export default {
    props: ['shops'],
    data(){
        return{
            map_object: null,
            user_marker: null,
            user_circle: null,
            completed_load: false,
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
            
            if(this.user_circle !== null){
                this.map_object.removeLayer(this.user_circle);
                this.user_circle = null;
            }

            if(this.user_marker !== null){
                this.map_object.removeLayer(this.user_marker);
                this.user_marker = null;
            }

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
                                .bindPopup("<strong>My location</strong><br><br><label>1km radius</label>")

            this.user_circle = L.circle([lat, lng], {
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.2,
                    weight: 1,
                    radius: 1000
                })
                .addTo(this.map_object);

            //center the map
            if(!this.completed_load){
                console.log('not completed load');
                this.map_object.setView([lat, lng], this.map_object.getZoom());
            }

            console.log('here3')
        },

        showlocation(){
            this.getLocation();
        },

        viewShopLocation(){
            this.shops.forEach(shop => {


                L.marker([shop.latitude, shop.longitude])
                    .addTo(this.map_object)
                    .bindPopup(`<strong style='color: #D25E27;'>${shop.name}</strong><br><label style='font-size: 12px;'>${shop.address}</label><br><br><button id="go-btn">Visit this shop</button>`)
                    .on('popupopen', ()=> {

                        const btn = document.getElementById("go-btn");
                        if (btn) {
                            btn.addEventListener("click", () => {
                                console.log('clicked');
                                this.$router.push({name: 'ShopAbout', params: {id: shop.id}});
                            });
                        }
                    });
                    
            })
        }
    },

    mounted(){

        console.log('shops: ', this.shops);

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
        
        this.viewShopLocation();

        const store = useDataStore();

        // Immediately use current location if available
        const { latitude, longitude } = store.user_location;
        if (latitude !== null && longitude !== null) {
            this.mapuserLoc(latitude, longitude);
        }

        // Watch for future changes
        watch(
            () => store.user_location,
            (newLoc) => {
                if (newLoc.latitude !== null && newLoc.longitude !== null) {
                    this.mapuserLoc(newLoc.latitude, newLoc.longitude);
                    this.completed_load = true;
                }
            },
            { deep: true, immediate: false } // already handled manually above
        );
        
    },

    unmounted(){
        console.log('onmounted');
        const store = useDataStore();
        store.user_location = {
            latitude: null,
            longitude: null,
        };
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