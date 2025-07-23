import { createRouter, createWebHistory} from "vue-router";

//IMPORT HERE ALL THE COMPONENTS
import Dashboard from "../seller/pages/Dashboard.vue";
import Login from "../forms/Login.vue";
import Notification from "../seller/pages/Notification.vue";
import Messages from "../seller/pages/Messages.vue";
import Map from "../seller/pages/Map.vue";
import Profile from "../seller/pages/Profile.vue";
import Followers from "../seller/pages/Followers.vue";
import Products from "../seller/pages/Products.vue";

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
        path: "/seller/notifications",
        name: "Notification",
        component: Notification
    },
    {
        path: "/seller/profile",
        name: "Profile",
        component: Profile
    },
        {
        path: "/seller/messages",
        name: "Messages",
        component: Messages
    },    {
        path: "/seller/map",
        name: "Map",
        component: Map
    },
    {
        path: "/seller/followers",
        name: "Followers",
        component: Followers
    },
    {
        path: "/seller/products",
        name: "Products",
        component: Products
    },
    {
        path: '/seller',
        redirect: '/seller/dashboard'
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
