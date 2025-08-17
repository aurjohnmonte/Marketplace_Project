<template>
  <div>
    <!-- <button @click="showlocation()">Show location</button> -->
    <div id="map"> 
      <div class="locate-header">
        <div class="locate-btn" @click="goLocateShop">
          <img src="../../../images/placeholder.png">
          <label>Locate Shop</label>
        </div>  
      </div>
    </div>
  </div>
</template>

<script>
import L from 'leaflet';
import { useDataStore } from '../../stores/dataStore';

export default {
  data() {
    return {
      store: useDataStore(),
      map_object: null,
      shop_marker: null,
      lat: null,
      long: null,
    }
  },
  methods: {
    goLocateShop() {
      if (this.map_object && this.shop_marker) {
        this.map_object.setView([this.lat, this.long], 15, {
          animate: true,
          duration: 1
        });
        this.shop_marker.openPopup();
      }
    },
    initializeMap() {
      L.Marker.prototype._animateZoom = function (opt) {
        if (!this._map) return;
        const pos = this._map._latLngToNewLayerPoint(this._latlng, opt.zoom, opt.center).round();
        this._setPos(pos);
      };

      L.Popup.prototype._animateZoom = function (opt) {
        if (!this._map) return;
        const pos = this._map._latLngToNewLayerPoint(this._latlng, opt.zoom, opt.center).round();
        const anchor = this._getAnchor();
        L.DomUtil.setPosition(this._container, pos.add(anchor));
      };

      this.map_object = L.map('map', {
        center: [9.0753, 125.5126],
        zoom: 13,
        maxZoom: 19,
        minZoom: 3,
      });

      L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri — Sources: Esri, Garmin, USGS, etc.',
        maxZoom: 19,
        minZoom: 3,
      }).addTo(this.map_object);

      this.lat = this.store.currentUser_info.shop.latitude;
      this.long = this.store.currentUser_info.shop.longitude;
      const shop_name = this.store.currentUser_info.shop.name;
      const address = this.store.currentUser_info.shop.address;

      this.shop_marker = L.marker([this.lat, this.long])
        .addTo(this.map_object)
        .bindPopup(`
          <strong>YOUR SHOP</strong><br>
          <strong style='color: #D25E27;'>${shop_name}</strong><br>
          <label style='font-size: 12px;'>${address}</label><br><br>
          <button id="go-btn" style="padding: 5px;">Visit your profile</button>
        `);

      this.shop_marker.on('popupopen', () => {
        document.getElementById('go-btn')?.addEventListener('click', () => {
          this.$router.push({ name: 'Profile' });
        });
      });
    }
  },
  mounted() {
    this.initializeMap();
  }
};

</script>

<style scoped>
#map{
  position: relative;
  top: 0;
  left: 0;
  width: 100%;
  margin: 0;
  padding: 0;
}
.locate-header{
  width: 100%;
  display: flex;
  justify-content: end;
  position: relative;
  z-index: 3000;
  align-items: center;
}
.locate-btn{
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  gap: 5px;
  border: 1px solid rgb(192, 192, 192);
  background-color: white;
  padding: 5px;
  margin: 20px;
  cursor: pointer;
  border-radius: 10%;
}
.locate-btn img{
  width: 25px;
  height: 25px;
  cursor: pointer;
}
.locate-btn label{
  font-size: 15px;
  margin: 0;
  cursor: pointer;
}
</style>