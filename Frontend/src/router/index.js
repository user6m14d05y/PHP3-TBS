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
    {
      path: '/product',
      name: 'product',
      component: () => import('../pages/Client/Home/product.vue')
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

router.beforeEach((to, from) => {
  const token = localStorage.getItem('access_token');

  let role = 'user';
  try {
    const userStr = localStorage.getItem('user');
    if (userStr) {
      const userObj = JSON.parse(userStr);
      if (userObj && userObj._r) {
        role = atob(userObj._r);
      }
    }
  } catch (e) {
    console.error('Lỗi đọc phân quyền:', e);
  }

  const isAuthRoute = ['login', 'register', 'forgot'].includes(to.name);

  if (to.meta.requiresAuth) {
    if (!token) {
      return { name: 'home', replace: true };
    }
    if (to.meta.requiresAdmin && role !== 'admin') {
      alert('Cảnh báo: Bạn không có quyền truy cập vào khu vực quản trị!');
      return { name: 'home', replace: true };
    }
  }

  if (isAuthRoute && token) {
    if (role === 'admin') {
      return { name: 'admin-dashboard' };
    } else {
      return { name: 'home' };
    }
  }

  return true;
})

export default router
