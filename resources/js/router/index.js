import { createRouter, createWebHistory} from "vue-router";

//IMPORT HERE ALL THE COMPONENTS
import Dashboard from "../seller/pages/Dashboard.vue";
import Login from "../forms/Login.vue";

const routes = [
    {
        path: "/seller/dashboard",
        name: "Dashboard",
        component: Dashboard
    },
    {
        path: "/login",
        name: "Login",
        component: Login
    },
    {
        path: "/seller/profile/1"
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
