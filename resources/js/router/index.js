import {createRouter, createWebHistory} from 'vue-router';

const router = createRouter({
    history: createWebHistory(import.meta.env.VITE_BASE_URL || '/'),
    routes: [
        { path: '/', component: () => import('../views/Home.vue'), name: 'home'},
        { path: '/post/:id', component: () => import('../views/Post.vue'), name: 'post'},
        { path: '/date/:id', component: () => import('../views/Date.vue'), name: 'date'},
        { path: '/attr/:id', component: () => import('../views/Attr.vue'), name: 'attr'},
    ],
});

export default router;