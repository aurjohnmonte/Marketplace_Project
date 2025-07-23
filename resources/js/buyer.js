import './bootstrap'
import 'leaflet/dist/leaflet.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'; 

import App from './Buyer.vue';
import router from './router';

const app = createApp(App);
const pinia = createPinia();

pinia.use(piniaPluginPersistedstate); 

app.use(pinia);
app.use(router);

app.mount('#buyer');