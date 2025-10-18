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

import 'leaflet/dist/leaflet.css'
import 'leaflet-routing-machine'
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css'

export default {
    props: ['shops'],
    data(){
        return{
            map_object: null,
            user_marker: null,
            user_circle: null,
            watchID: null,
            completed_load: false,
            store: useDataStore(),
        }
    },
    methods: {
        getLocation(lat, long) {

            console.log(`lat: ${lat}, long: ${long}`);

            if (!navigator.geolocation) {
                alert("Geolocation is not supported by your browser.")
                return
            }

            //i need to clearWatch because it need to stop the previous watchPosition before starting again to avoid error.
            navigator.geolocation.clearWatch(this.watchID);

            let routingControl;

            this.watchID = navigator.geolocation.watchPosition(
                (position) => {
                    console.log('hello');
                    
                    this.store.setUser_location(position.coords.latitude, position.coords.longitude);

                    if (routingControl) {

                        routingControl.setWaypoints([userLatLng, destination]);
                    } 
                    else {
                        
                        routingControl = L.Routing.control({
                            waypoints: [userLatLng, destination],
                            routeWhileDragging: false,
                            addWaypoints: false,
                            show: false,
                        }).addTo(map);
                    }

                    console.log('user location: ',this.store.user_location);
                },
                (error) => {

                    alert("Unable to retrieve your location. You may have denied permission.")
                    console.error(error)
                },
                {

                    enableHighAccuracy: true,
                    maximumAge: 1000,     // Accept cached position max 1 second old
                    timeout: 10000        // Timeout for getting position
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
                    radius: this.store.currentUser_info.nearby_km * 1000
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

        goNavigateShopLocation(lat, long){

            console.log('go navigate');
            this.getLocation(lat, long);
        },

        viewShopLocation(){
            this.shops.forEach(shop => {


                L.marker([shop.latitude, shop.longitude])
                    .addTo(this.map_object)
                    .bindPopup(`
                    <strong style='color: #D25E27;'>${shop.name}</strong><br>
                    <label style='font-size: 12px;'>${shop.address}</label>
                    <br><br>
                    <button id="go-btn" style="margin-bottom: 5px;">Visit this shop</button><br>
                    <button id="navigate-btn">Navigate</button>
                    `)
                    .on('popupopen', () => {

                        const btn = document.getElementById("go-btn");
                        const navigate_btn = document.getElementById("navigate-btn");

                        if (btn) {
                            btn.addEventListener("click", () => {
                                console.log('clicked');
                                this.$router.push({name: 'ShopAbout', params: {id: shop.id}});
                            });
                        }

                        //for navigation
                        if(navigate_btn){
                            navigate_btn.addEventListener('click', () => {

                                console.log('shop: ',shop);
                                this.goNavigateShopLocation(shop.latitude, shop.longitude);
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

        // this.map_object = L.map('map').setView([9.0753, 125.5126], 13) // sample: Butuan coordinates

        this.map_object = L.map('map', {
            center: [9.0753, 125.5126],
            zoom: 13,
            maxZoom: 19,
            minZoom: 3,
        })

        //https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png'
        //tiles sa sattelite ver.

        // L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
        //     attribution: 'Tiles &copy; Esri — Sources: Esri, Garmin, USGS, etc.',
        //     maxZoom: 19,
        //     minZoom: 3,
        // }).addTo(this.map_object);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Tiles &copy; Esri — Sources: Esri, Garmin, USGS, etc.',
            maxZoom: 19,
            minZoom: 3,
        }).addTo(this.map_object);

        
        this.viewShopLocation();

        const store = useDataStore();

        // Immediately use current location if available
        const { latitude, longitude } = store.user_location;
        if (latitude !== null && longitude !== null) {
            this.mapuserLoc(latitude, longitude);
        }

        //THIS LOCATE THE SHOP
        let lat = store.selectedCoordinate.latitude;
        let long = store.selectedCoordinate.longitude;
        if (lat !== null && long !== null) {
            this.map_object.setView([lat, long], 15)
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
        store.resetSelectedCoordinate();
        this.watchID = null;
    }
}
</script>

<style>
#map {
    height: 100vh;
    z-index: 1;
    position: relative;
    top: 60px;
    left: 0;
    background-color: #f0f0f0; /* Prevent brown background */
}

</style>