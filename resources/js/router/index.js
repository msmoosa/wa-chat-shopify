import { createRouter, createWebHashHistory } from 'vue-router';
import Welcome from '../components/Welcome.vue';
import Home from '../components/Home.vue';

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
        path: '/products',
        name: 'products',
        component: Welcome,
        props: (route) => ({
            shopDomain: initialData.shopDomain || '',
            products: initialData.products || [],
            error: initialData.error || ''
        }),
    },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;
