import { createRouter, createWebHistory } from 'vue-router';
import AbandonedCarts from '../components/AbandonedCarts.vue';
import Home from '../components/Home.vue';
import Templates from '../components/Templates.vue';

// Store initial data from Laravel
let initialData = {};

export function setInitialData(data) {
    initialData = data;
}

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home,
        props: (route) => ({
            shopDomain: initialData.shopDomain || ''
        }),
    },
    {
        path: '/abandoned-carts',
        name: 'abandoned-carts',
        component: AbandonedCarts,
        props: (route) => ({
            shopDomain: initialData.shopDomain 
        }),
    },
    {
        path: '/templates',
        name: 'templates',
        component: Templates,
        props: (route) => ({
            shopDomain: initialData.shopDomain 
        }),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
