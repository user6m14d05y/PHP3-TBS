<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../../stores/auth';

const email = ref('');
const password = ref('');
const isSubmitting = ref(false);

// show alert 2
import Swal from 'sweetalert2';

const router = useRouter();
const authStore = useAuthStore();

const login = async () => {

  if (email.value === ""){
    alert("Vui lòng nhập email của bạn");
    return;
  }
  if (password.value === ""){
    alert("Vui lòng nhập mật khẩu của bạn")
    return;
  }

  isSubmitting.value = true;
  try {
    const response = await axios.post('http://localhost:8888/api/Login', {
        email: email.value,
        password: password.value
    });
    
    if (response.data.status === "success"){
      // save token to storage
      localStorage.setItem('access_token', response.data.access_token);
      
      // Update Pinia state immediately to avoid reload issue
      authStore.user = response.data.user;
      authStore.isLoaded = true;
      
      if (response.data.user.role === 'admin') {
         router.replace('/admin');
      } else {
         router.replace('/');
      }
    }
  } catch (error) {
    let errorMessage = "Đăng nhập thất bại!";
    
    // Read error massage from php
    if (error.response && error.response.data && error.response.data.message) {
        errorMessage = error.response.data.message;
    }
    
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: errorMessage,
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
    })
  } finally {
      isSubmitting.value = false;
  }

}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-4xl w-full flex bg-white rounded-2xl shadow-2xl overflow-hidden min-h-[600px]">
      
      <!-- Left side: Brand banner (Hidden on small screens) -->
      <div class="hidden md:flex md:w-1/2 relative overflow-hidden bg-gradient-to-br from-pink-600 via-rose-500 to-pink-300">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.35),transparent_30%),radial-gradient(circle_at_70%_70%,rgba(255,255,255,0.25),transparent_28%)]"></div>
        <div class="relative z-10 flex h-full flex-col justify-end p-10 text-white">
          <router-link to="/" class="mb-auto inline-flex items-center gap-3 text-white">
            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 text-xl font-bold backdrop-blur">T</span>
            <span class="text-sm font-semibold uppercase tracking-[0.3em]">TBS Flower</span>
          </router-link>
          <h2 class="text-4xl font-serif font-bold mb-3 tracking-wide">Chào mừng trở lại</h2>
          <p class="text-sm opacity-90 font-light tracking-wider leading-relaxed">
            Đăng nhập để tiếp tục đặt hoa, theo dõi đơn hàng và nhận ưu đãi mới nhất từ TBS.
          </p>
        </div>
      </div>

      <!-- Right side: Login Form -->
      <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center">
        <!-- Logo / Title -->
        <div class="mb-10 text-center md:text-left">
          <h2 class="text-3xl font-serif font-bold text-gray-900 tracking-tight mb-2">Đăng nhập</h2>
          <p class="text-sm text-gray-500">Vui lòng nhập thông tin để tiếp tục.</p>
        </div>

        <form @submit.prevent="login" class="space-y-6">
          
          <!-- Email Input -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Địa chỉ Email</label>
            <div class="mt-1">
              <input 
                id="email" 
                name="email" 
                type="email" 
                autocomplete="email"  
                v-model="email"
                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition duration-200"
                placeholder="you@example.com"
              >
            </div>
          </div>

          <!-- Password Input -->
          <div>
            <div class="mt-4">
              <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
            </div>
            <div class="mt-1 relative">
              <input 
                id="password" 
                name="password" 
                type="password" 
                autocomplete="current-password"  
                v-model="password"
                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition duration-200"
                placeholder="••••••••"
              >
            </div>
          </div>

          <!-- Remember Me & Forgot Password -->
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded cursor-pointer">
              <label for="remember-me" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                Ghi nhớ đăng nhập
              </label>
            </div>

            <div class="text-sm">
              <router-link to="/forgot" class="font-semibold text-pink-600 transition duration-200 hover:text-pink-700 hover:underline underline-offset-4">
                Quên mật khẩu?
              </router-link>
            </div>
          </div>

          <!-- Submit Button -->
          <div>
            <button 
              type="submit" 
              class="group relative w-full overflow-hidden rounded-full bg-gradient-to-r from-pink-600 via-rose-500 to-pink-500 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-pink-200/70 transition duration-300 ease-out hover:-translate-y-0.5 hover:shadow-xl hover:shadow-pink-200 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 active:translate-y-0 active:scale-[0.98] disabled:pointer-events-none disabled:opacity-70"
              :disabled="isSubmitting"
            >
              <span v-if="isSubmitting" class="mr-2">
                 <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </span>
              {{ isSubmitting ? 'Đang xử lý...' : 'Đăng nhập' }}
            </button>
          </div>
        </form>

        <!-- Divider -->
        <div class="mt-8">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-4 bg-white text-gray-500">
                Hoặc đăng nhập bằng
              </span>
            </div>
          </div>

          <!-- Social Buttons -->
          <div class="mt-6 grid grid-cols-2 gap-4">
            <button class="w-full flex items-center justify-center py-2.5 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition duration-200">
              <img class="h-5 w-5" src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
              <span class="ml-2">Google</span>
            </button>
            <button class="w-full flex items-center justify-center py-2.5 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition duration-200">
              <img class="h-5 w-5" src="https://www.svgrepo.com/show/475647/facebook-color.svg" alt="Facebook">
              <span class="ml-2">Facebook</span>
            </button>
          </div>
        </div>

        <!-- Register Link -->
        <p class="mt-8 text-center text-sm text-gray-600">
          Chưa có tài khoản?
          <router-link to="/register" class="font-semibold text-pink-600 transition duration-200 hover:text-pink-700 hover:underline underline-offset-4">
            Đăng ký ngay
          </router-link>
        </p>

      </div>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap');

.font-serif {
  font-family: 'Playfair Display', serif;
}
.font-sans {
  font-family: 'Inter', sans-serif;
}
</style>