<template>
    <div style="top: 0; bottom: 0;">
        <!-- <button @click="showlocation()">Show location</button> -->
        <div id="map"> 

        </div>
        <div class="map-navbar" v-if="show_nav">
            <div style="border-bottom: 1px solid gray; display: flex; flex-direction: row; align-items: center;">
                <input type="text" placeholder="Search Shop" style="margin: 10px;" v-model="search_text">
                <button @click="searchLocation">Search</button>
                <label style="font-size: 12px; margin-left: 10px; color: white;">{{ search_msg }}</label>
            </div>

            <div v-if="selected_shop" style="width: 100%; height: 100%; padding: 10px; box-sizing: border-box; color: white;">
                <span style="display: flex; flex-direction: column;">
                    <span style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                        <label style="text-decoration: underline; color: orange;">{{ selected_shop.name.toUpperCase() }}</label>
                        <button style="font-size: 9px;" @click="cancelNavigation()">Cancel Navigation</button>
                    </span>
                    <label style="font-size: 10px;">{{ selected_shop.address }}</label>
                    <span style="width: 100%; height: 20px; display: flex; flex-direction: row; align-items: center;justify-content: space-between; gap: 10px; margin-top: 15px;">

                        <label style="font-size: 12px; color: orange; text-decoration: underline;">My Location</label>

                        <div style="display: flex; flex-direction: row; align-items: center; gap: 10px; margin-top: 15px;">
                            <span style="display: flex; flex-direction: column; align-items: center; gap: 5px;" @click="goChangeMovement('car')">
                                <img src="../../images/car.png" class="move-icon" :style="{backgroundColor: returnColor('car')}">
                                <label style="font-size: 10px;">Drive</label>
                            </span>
                            <span style="display: flex; flex-direction: column; align-items: center; gap: 5px;" @click="goChangeMovement('bike')">
                                <img src="../../images/bike.png" class="move-icon" :style="{backgroundColor: returnColor('bike')}">
                                <label style="font-size: 10px;">Bike</label>
                            </span>
                            <span style="display: flex; flex-direction: column; align-items: center; gap: 5px;" @click="goChangeMovement('walk')">
                                <img src="../../images/walk.png" class="move-icon" :style="{backgroundColor: returnColor('walk')}">
                                <label style="font-size: 10px;">Walk</label>
                            </span>
                        </div>
                    </span>
                </span>

                <span style="display: flex; flex-direction: column; margin-top: 25px; font-size: 12px;">
                    <div>
                        <label>Distance: {{ distance }} km</label>
                    </div>
                    <div>
                        <label>Time: {{ duration }} mins</label>
                    </div>
                </span>
                
            </div>
            <div v-else style="width: 100%; height: 100%; padding: 10px; box-sizing: border-box; color: white;">
                <label>NO SHOP IS NAVIGATED</label>
            </div>
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
    props: ['shops', 'watch_id'],
    data(){
        return{
            duration: 0,
            distance: 0,
            selected_shop: null,
            selected_movement: {
                link: 'https://routing.openstreetmap.de/routed-car/route/v1',
                name: 'car',
            },
            is_navigate: false,
            show_nav: true,
            search_text: '',
            search_msg: '',
            map_object: null,
            user_marker: null,
            user_circle: null,
            watchID: null,
            completed_load: false,
            routingControl: null,
            store: useDataStore(),
        }
    },
    methods: {
        cancelNavigation(){

            if(this.routingControl){
                this.routingControl.remove();
                this.routingControl = null;
                navigator.geolocation.clearWatch(this.watchID);
                this.selected_movement = {
                    link: 'https://routing.openstreetmap.de/routed-car/route/v1',
                    name: 'car',
                };
                this.selected_shop = null;
                this.is_navigate = false;
            }
        },
        goChangeMovement(name){

            console.log('name: ', name);
            this.routingControl.remove();
            this.routingControl = null;

            switch(name){

                case 'car':
                    this.selected_movement.link = 'https://routing.openstreetmap.de/routed-car/route/v1';
                    this.selected_movement.name = 'car';
                    return;
                case 'bike':
                    this.selected_movement.link = 'https://routing.openstreetmap.de/routed-bike/route/v1';
                    this.selected_movement.name = 'bike';
                    return;
                case 'walk':
                    this.selected_movement.link = 'https://routing.openstreetmap.de/routed-foot/route/v1';
                    this.selected_movement.name = 'walk';
                    return;
            }

        },
        returnColor(name){

            switch(name){

                case this.selected_movement.name:
                    return "orange";
            }
        },  
        getLocation(lat, long) {

            console.log(`lat: ${lat}, long: ${long}`);

            if (!navigator.geolocation) {
                alert("Geolocation is not supported by your browser.")
                return
            }

            //i need to clearWatch because it need to stop the previous watchPosition before starting again to avoid error.
            navigator.geolocation.clearWatch(this.watchID);

            if(this.routingControl){
                this.routingControl.remove();
                this.routingControl = null;
            }

            
            const destination = L.latLng([lat, long]);

            //the variables are used to check the previous distance so that we can avoid the repetation of requesting in api
            let prev_coords;
            let distance;

            this.watchID = navigator.geolocation.watchPosition(
                (position) => {
                    console.log('hello');
                    
                    this.store.setUser_location(position.coords.latitude, position.coords.longitude);

                    const userLatLng = L.latLng([this.store.user_location.latitude, this.store.user_location.longitude]);

                    //we check and calculate the distance from prev and current coords of user location
                    if(prev_coords && userLatLng){
                        distance = this.map_object.distance(userLatLng, prev_coords);
                        console.log('distance: ', distance);
                    }

                    //we store the prev coords
                    prev_coords = userLatLng;

                    if (this.routingControl) {

                        //we check here if user move less than 10 meters. If it true it cancel the update of the map.
                        if(distance < 10){

                            console.log('cancel update');
                        }
                    } 
                    else {          
                        
                        this.routingControl = L.Routing.control({
                            waypoints: [userLatLng, destination],
                            router: L.Routing.osrmv1({
                                serviceUrl: this.selected_movement.link,
                            }),
                            routeWhileDragging: false,
                            addWaypoints: false,
                            draggableWaypoints: false,
                            show: false,
                            createMarker: function(i, waypoint, n) {
                                // i = index (0 = start, 1 = end, etc.)
                                if (i === 0) {
                                // ✅ Create a marker only for the user's location
                                return L.marker(waypoint.latLng).bindPopup("<strong>My location</strong>");
                                }
                                // 🚫 Return null to skip the destination marker
                                return null;
                            }
                        }).addTo(this.map_object);

                        this.map_object.flyTo(userLatLng, 16);
                    }

                    //for km
                    this.routingControl.on('routesfound', (e) => {
                        const route = e.routes[0]; // usually only one route
                        const distanceInMeters = route.summary.totalDistance;
                        const durationInSeconds = route.summary.totalTime;

                        console.log("Distance:", (distanceInMeters / 1000).toFixed(2), "km");
                        console.log("Duration:", (durationInSeconds / 60).toFixed(1), "minutes");

                        this.distance = (distanceInMeters / 1000).toFixed(1);
                        this.duration = (durationInSeconds / 60).toFixed(1);

                        console.log('distanceeeeeeeeeeeeeeeeeeeeeee: ', this.distance);
                    });

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

            if(this.is_navigate){
                console.log('cancel ang mapuserloc uy');
                return;
            }
            
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

        goNavigateShopLocation(lat, long, shop){

            console.log('watch id: ', this.watch_id);

            this.is_navigate = true;

            if(this.watch_id){
                navigator.geolocation.clearWatch(this.watch_id);
            }

            console.log('go navigate');
            this.getLocation(lat, long);

            this.selected_shop = shop;

            console.log('selected shop: ', this.selected_shop);
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
                                this.goNavigateShopLocation(shop.latitude, shop.longitude, shop);
                            });
                        }
                    });
                    
            })
        },
        searchLocation(){

            const shop = this.shops.find(shop => shop.name.toLowerCase().includes(this.search_text.toLowerCase()));

            if(!shop){
                this.search_msg = "No result";
            }
            else{
                this.search_msg = "";

                this.map_object.flyTo([shop.latitude, shop.longitude], 16);
            }

            console.log('search shop: ', shop);

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
        navigator.geolocation.clearWatch(this.watchID);
        this.map_object = null;
    }
}
</script>

<style>
.move-icon{
    width: 20px; height: 20px; background-color: white; border-radius: 50%; padding: 5px;
}
.map-navbar{
    width: 100%;
    height: 30%;
    background-color: rgba(0, 0, 0, 0.646);
    position: absolute;
    bottom: 0;
    z-index: 2;
    border-top: 2px solid white;
    box-sizing: border-box;
}
#map {
    height: 100vh;
    z-index: 1;
    position: relative;
    top: 60px;
    left: 0;
    background-color: #f0f0f0; /* Prevent brown background */
}

</style>