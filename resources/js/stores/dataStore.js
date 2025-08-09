import { defineStore } from 'pinia';

export const useDataStore = defineStore('data', {
  state: () => ({
    selectedProduct: null,
    currentUser_info: null,
    selected_shop: null,
    selectedCoordinate: {
      latitude: null,
      longitude: null,
    },
    selectedNotification: null,
    products: [],
    nearbyShops: null,
    user_location: {
      latitude: null,
      longitude: null,
    },
    notifications: [],
  }),
  actions: {
    setNotifications(notifications){
      this.notifications = notifications;
    },
    setSelectedNotification(notification){
      this.selectedNotification = notification;
    },
    setSelectedShop(shop){
      this.selected_shop = shop;
    },
    setSelectedProduct(product) {
      this.selectedProduct = product;
    },
    resetSelectedCoordinate(){
      this.selectedCoordinate = {
        latitude: null,
        longitude: null,
      };
    },
    setSelectedCoordinate(lat, long){

      this.selectedCoordinate.latitude = lat;
      this.selectedCoordinate.longitude = long;
    },
    setProducts(products){
      this.products = products;
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
      this.selectedCoordinate = {
        latitude: null,
        longitude: null,
      };
      this.selected_shop = null;
      this.products = [];
      this.selectedNotification = null;
      this.notifications = [];
    }
  },
  persist: true
});
