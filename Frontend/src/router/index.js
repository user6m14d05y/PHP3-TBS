import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../pages/Client/Home/Index.vue')
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../pages/Client/Auth/login.vue')
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../pages/Client/Auth/Register.vue')
    },
    {
      path: '/forgot',
      name: 'forgot',
      component: () => import('../pages/Client/Auth/Forgot.vue')
    },

    // Admin
    {
      path: '/admin/dashboard',
      name: 'admin-dashboard',
      component: () => import('../pages/Admin/dashboard.vue')
    },
  ],
})

export default router
