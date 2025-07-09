import { createRouter, createWebHistory} from "vue-router";

//IMPORT HERE ALL THE COMPONENTS
import Dashboard from "../seller/pages/Dashboard.vue";

const routes = [
    {
        path: "/seller/dashboard",
        name: Dashboard,
        component: Dashboard
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;