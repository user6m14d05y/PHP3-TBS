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
    {
      path: '/admin/category',
      name: 'admin-category',
      component: () => import('../pages/Admin/category.vue')
    },
    {
      path: '/admin/product',
      name: 'admin-product',
      component: () => import('../pages/Admin/product.vue')
    },
    {
      path: '/admin/product/color',
      name: 'admin-product-color',
      component: () => import('../pages/Admin/color.vue')
    },
    {
      path: '/admin/product/size',
      name: 'admin-product-size',
      component: () => import('../pages/Admin/size.vue')
    },
    {
      path: '/admin/user',
      name: 'admin-user',
      component: () => import('../pages/Admin/user.vue')
    },
    {
      path: '/admin/contact',
      name: 'admin-contact',
      component: () => import('../pages/Admin/contact.vue')
    },
    {
      path: '/admin/setting',
      name: 'admin-setting',
      component: () => import('../pages/Admin/setting.vue')
    },
    {
      path: '/admin/order',
      name: 'admin-order',
      component: () => import('../pages/Admin/order.vue')
    },
  ],
})

export default router
