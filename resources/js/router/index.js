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
import BuyerConversation from "../buyer/message/BuyerConversation.vue";
import ShopView from "../buyer/home/ShopView.vue";
import ShopAbout from "../buyer/home/shopview_pages/ShopAbout.vue";
import ShopProducts from "../buyer/home/shopview_pages/ShopProducts.vue";
import ShopReview from "../buyer/home/shopview_pages/ShopReview.vue";
import BuyerShops from "../buyer/home/BuyerShops.vue";
import AddProduct from "../seller/pages/AddProduct.vue";
import ViewNotification from "../seller/notifications/ViewNotification.vue";
import Chats from "../seller/messages/Chats.vue";
import AccountSetting from "../buyer/profile/AccountSetting.vue";
import DeleteAccount from "../buyer/profile/DeleteAccount.vue";
import ViewAllNotification from "../buyer/pages/ViewAllNotification.vue";
import ViewProduct from "../seller/notifications/ViewProduct.vue";
import TransactionRecord from "../seller/pages/TransactionRecord.vue";
import AddRecord from "../seller/pages/AddRecord.vue";

//ADMIN SIDE
import AdminDashboard from "../admin/pages/AdminDashboard.vue";
import ManageAccount from "../admin/pages/ManageAccount.vue";
import SellerProfile from "../admin/pages/SellerProfile.vue";
import Admin_viewProduct from "../admin/pages/Admin_viewProduct.vue";
import ManageReviews from "../admin/pages/ManageReviews.vue";
import AdminMap from "../admin/pages/AdminMap.vue";
import AdminNotificationPage from "../admin/pages/AdminNotificationPage.vue";

const routes = [
    {
        path: "/admin/notifications",
        name: "AdminNotificationPage",
        component: AdminNotificationPage
    },
    {
        path: "/admin/map",
        name: "AdminMap",
        component: AdminMap
    },
    {
        path: "/admin/manage-reviews",
        name: "ManageReviews",
        component: ManageReviews
    },
    {
        path: "/admin/view-product/:id",
        name: "Admin_viewProduct",
        component: Admin_viewProduct
    },
    {
        path: "/admin/seller-profile/:id",
        name: "SellerProfile",
        component: SellerProfile
    },
    {
        path: "/admin/manage-account",
        name: "ManageAccount",
        component: ManageAccount
    },
    {
        path: "/admin/dashboard",
        name: "AdminDashboard",
        component: AdminDashboard
    },
    {
        path: "/buyer/browse/shop/:id",
        name: "ShopView",
        component: ShopView,
        children: [
            {
                path: "about",
                name: "ShopAbout",
                component: ShopAbout,
            },
            {
                path: "products",
                name: "ShopProducts",
                component: ShopProducts,
            },
            {
                path: "review",
                name: "ShopReview",
                component: ShopReview,
            }
        ]
    },
    {
        path: "/buyer/message/conversation/:id",
        name: "BuyerConversation",
        component: BuyerConversation,
    },
    {
        path: "/buyer/notification/view-all",
        name: "ViewAllNotification",
        component: ViewAllNotification
    },
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
        path: "/buyer/browse-shop/:name/:category/:filter",
        name: "BuyerShops",
        component: BuyerShops,
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
        component: Notification,
        children: [
            {
                path: "view",
                name: "ViewNotification",
                component: ViewNotification
            }
        ]
    },
    {
        path: '/seller/products/view/:id',
        name: 'ViewProduct',
        component: ViewProduct
    },
    {
        path: '/seller/transaction-record',
        name: 'TransactionRecord',
        component: TransactionRecord,
    },
    {
        path: '/seller/add-record',
        name: 'AddRecord',
        component: AddRecord,
    },
    {
        path: "/seller/profile",
        name: "Profile",
        component: Profile
    },
    {
        path: "/seller/messages/chats",
        name: "Chats",
        component: Chats
    },
    {
        path: "/seller/messages",
        name: "Messages",
        component: Messages
    },
    {
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
        path: "/buyer/profile/deactivate",
        name: "AccountSetting",
        component: AccountSetting,
    },
    {
        path: "/buyer/profile/delete",
        name: "DeleteAccount",
        component: DeleteAccount,
    },
    {
        path: '/seller/product/add',
        name: 'AddProduct',
        component: AddProduct
    },
    {
        path: '/seller',
        redirect: '/seller/dashboard',
        component: Dashboard,
    },
    {
        path: "/seller/products/view/:id",
        name: "ViewProduct",
        component: ViewProduct
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
