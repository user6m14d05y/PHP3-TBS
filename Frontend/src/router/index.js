import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

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
    {
      path: '/product',
      name: 'product',
      component: () => import('../pages/Client/Home/product.vue')
    },
    {
      path: '/cart',
      name: 'cart',
      component: () => import('../pages/Client/Cart/Index.vue')
    },
    {
      path: '/checkout',
      name: 'checkout',
      component: () => import('../pages/Client/Cart/Checkout.vue')
    },
    {
      path: '/order-success',
      name: 'order-success',
      component: () => import('../pages/Client/Cart/OrderSuccess.vue')
    },
    
    {
      path: '/product/category/:id',
      name: 'product-category',
      component: () => import('../pages/Client/Home/categoryProduct.vue')
    },

    {
      path: '/admin/dashboard',
      name: 'admin-dashboard',
      component: () => import('../pages/Admin/dashboard.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/category',
      name: 'admin-category',
      component: () => import('../pages/Admin/category.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/product',
      name: 'admin-product',
      component: () => import('../pages/Admin/product.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/product/size',
      name: 'admin-product-size',
      component: () => import('../pages/Admin/size.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/user',
      name: 'admin-user',
      component: () => import('../pages/Admin/user.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/contact',
      name: 'admin-contact',
      component: () => import('../pages/Admin/contact.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/setting',
      name: 'admin-setting',
      component: () => import('../pages/Admin/setting.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/order',
      name: 'admin-order',
      component: () => import('../pages/Admin/order.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    },
  ],
})

router.beforeEach(async (to, from) => {
  const authStore = useAuthStore();
  
  // Auto fetch user data from server if not loaded to ensure security
  if (!authStore.isLoaded) {
    await authStore.fetchUser();
  }

  const isLoggedIn = !!authStore.user;
  const role = authStore.user?.role || 'user';
  const isAuthRoute = ['login', 'register', 'forgot'].includes(to.name);

  // Block if route requires login
  if (to.meta.requiresAuth) {
    if (!isLoggedIn) {
      return { name: 'home', replace: true };
    }
    // Block if route requires admin but not admin
    if (to.meta.requiresAdmin && role !== 'admin') {
      alert('Cảnh báo: Bạn không có quyền truy cập vào khu vực quản trị!');
      return { name: 'home', replace: true };
    }
  }

  // If entering Login/Register page but already logged in
  if (isAuthRoute && isLoggedIn) {
    if (role === 'admin') {
      return { name: 'admin-dashboard' };
    } else {
      return { name: 'home' };
    }
  }

  return true;
})

export default router
