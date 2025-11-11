import './bootstrap'
import 'leaflet/dist/leaflet.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'; 

import router from './router';
import Seller from "./Admin.vue";

const app = createApp(Seller);
const pinia = createPinia();

pinia.use(piniaPluginPersistedstate);

app.use(pinia);
app.use(router);

app.mount('#admin');