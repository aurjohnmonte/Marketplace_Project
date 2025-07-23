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
import Home from "../buyer/pages/Home.vue";
import BuyerMap from "../buyer/pages/BuyerMap.vue";
import BuyerMessage from "../buyer/pages/BuyerMessage.vue";
import BuyerFollowing from "../buyer/pages/BuyerFollowing.vue";
import BuyerProfile from "../buyer/pages/BuyerProfile.vue";
import BuyerHome from "../buyer/home/BuyerHome.vue";
import BuyerBrowse from "../buyer/home/BuyerBrowse.vue";
import BuyerProduct from "../buyer/home/BuyerProduct.vue";

const routes = [
    {
        path: "/buyer/product/:id",
        name: "BuyerProduct",
        component: BuyerProduct,
    },
    {
        path: "/buyer/home",
        name: "BuyerHome",
        component: BuyerHome,
    },
    {
        path: "/buyer/browse",
        name: "BuyerBrowse",
        component: BuyerBrowse
    },
    {
        path: "/buyer/browse/:name/:category/:filter",
        name: "BuyerBrowse",
        component: BuyerBrowse,  
    },
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
        path: "/buyer",
        name: "Home",
        component: Home
    },
    {
        path: "/buyer/map",
        name: "BuyerMap",
        component: BuyerMap
    },
    {
        path: "/buyer/message",
        name: "BuyerMessage",
        component: BuyerMessage
    },
    {
        path: "/buyer/following",
        name: "BuyerFollowing",
        component: BuyerFollowing
    },
    {
        path: "/buyer/profile",
        name: "BuyerProfile",
        component: BuyerProfile
    },
    {
        path: '/seller',
        redirect: '/seller/dashboard',
        component: Dashboard,
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
