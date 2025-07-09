import './bootstrap'
import 'leaflet/dist/leaflet.css';

import { createApp } from 'vue';
import router from "./router";
import Seller from "./Seller.vue";

createApp(Seller).use(router).mount('#seller');