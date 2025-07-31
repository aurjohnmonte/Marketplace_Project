import { defineStore } from 'pinia';

export const useDataStore = defineStore('data', {
  state: () => ({
    selectedProduct: null,
    currentUser_info: null,
    nearbyShops: null,
    user_location: {
      latitude: null,
      longitude: null,
    }
  }),
  actions: {
    setSelectedProduct(product) {
      this.selectedProduct = product;
    },
    setUserInfo(user){
      this.currentUser_info = user;
    },
    setUser_location(latitude, longitude){

      this.user_location.latitude = latitude;
      this.user_location.longitude = longitude;
    },
    setNearbyShops(shops){

      this.nearbyShops = shops;
    },
    reset(){
      this.selectedProduct = null;
      this.currentUser_info = null;
      this.user_location = {
        latitude: null,
        longitude: null
      };
      this.nearbyShops = null;
    }
  },
  persist: true
});
