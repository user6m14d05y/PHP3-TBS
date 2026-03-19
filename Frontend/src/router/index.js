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

router.beforeEach((to, from) => {
  const token = localStorage.getItem('access_token');
  
  let role = 'user'; // Mặc định nếu không có role
  try {
    const userStr = localStorage.getItem('user');
    if (userStr) {
      const userObj = JSON.parse(userStr);
      // Bạn đang mã hóa role bằng btoa() ở tên biến '_r' trong file login.vue
      if (userObj && userObj._r) {
        role = atob(userObj._r); // Giải mã base64 (atob) để lấy ra text thực sự (ví dụ: 'admin')
      }
    }
  } catch (e) {
    console.error('Lỗi đọc phân quyền:', e);
  }

  const isAdminRoute = to.path.startsWith('/admin')
  const isLoginRoute = to.path.startsWith('/login')
  const isAuthRoute = ['login', 'register', 'forgot'].includes(to.name)


  if (isLoginRoute && token) {
    return { name: 'home' }
  }

  if (isAdminRoute) {
    if (!token) {
      return { name: 'home' }
    }
    
    if (role !== 'admin') {
      alert('Cảnh báo: Bạn không có quyền truy cập vào khu vực quản trị!')
      return { name: 'home' }
    }
  }
  
  if (isAuthRoute && token) {
    if (role === 'admin') {
      return { name: 'admin-dashboard' }
    } else {
      return { name: 'home' }
    }
  }

  return true;
})

export default router
