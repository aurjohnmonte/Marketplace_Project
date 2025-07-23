import { defineStore } from 'pinia';

export const useDataStore = defineStore('data', {
  state: () => ({
    selectedProduct: null
  }),
  actions: {
    setSelectedProduct(product) {
      this.selectedProduct = product;
    }
  },
  persist: true
});
