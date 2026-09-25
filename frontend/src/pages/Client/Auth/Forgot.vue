<script setup>
import { ref } from 'vue';

const email = ref('');

const handleForgotPassword = () => {
    // Handle forgot password logic here
    console.log('Sending reset link to:', email.value);
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-4xl w-full flex bg-white rounded-2xl shadow-2xl overflow-hidden min-h-[500px]">
      
      <!-- Left side: Brand banner (Hidden on small screens) -->
      <div class="hidden md:flex md:w-1/2 relative overflow-hidden bg-gradient-to-br from-pink-600 via-rose-500 to-pink-300">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.35),transparent_30%),radial-gradient(circle_at_70%_70%,rgba(255,255,255,0.25),transparent_28%)]"></div>
        <div class="relative z-10 flex h-full flex-col justify-end p-10 text-white">
          <router-link to="/" class="mb-auto inline-flex items-center gap-3 text-white">
            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 text-xl font-bold backdrop-blur">T</span>
            <span class="text-sm font-semibold uppercase tracking-[0.3em]">TBS Flower</span>
          </router-link>
          <h2 class="text-4xl font-serif font-bold mb-3 tracking-wide">Khôi phục tài khoản</h2>
          <p class="text-sm opacity-90 font-light tracking-wider leading-relaxed">
            Nhập email đã đăng ký để nhận hướng dẫn lấy lại mật khẩu và tiếp tục đặt hoa tại TBS.
          </p>
        </div>
      </div>

      <!-- Right side: Forgot Password Form -->
      <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center">
        <!-- Logo / Title -->
        <div class="mb-10 text-center md:text-left">
          <h2 class="text-3xl font-serif font-bold text-gray-900 tracking-tight mb-2">Quên mật khẩu?</h2>
          <p class="text-sm text-gray-500 leading-relaxed">
            Đừng lo lắng, chuyện này thường xuyên xảy ra. Vui lòng nhập địa chỉ email liên kết với tài khoản của bạn để nhận liên kết đặt lại mật khẩu.
          </p>
        </div>

        <form @submit.prevent="handleForgotPassword" class="space-y-6">
          
          <!-- Email Input -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Địa chỉ Email</label>
            <div class="mt-1">
              <input 
                id="email" 
                name="email" 
                type="email" 
                autocomplete="email" 
                required 
                v-model="email"
                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition duration-200"
                placeholder="you@example.com"
              >
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-2">
            <button 
              type="submit" 
              class="group relative w-full overflow-hidden rounded-full bg-gradient-to-r from-pink-600 via-rose-500 to-pink-500 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-pink-200/70 transition duration-300 ease-out hover:-translate-y-0.5 hover:shadow-xl hover:shadow-pink-200 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 active:translate-y-0 active:scale-[0.98]"
            >
              Gửi liên kết đặt lại
            </button>
          </div>
        </form>

        <!-- Back to Login Link -->
        <div class="mt-8 text-center text-sm">
          <router-link to="/login" class="inline-flex items-center justify-center rounded-full border border-pink-100 bg-pink-50 px-4 py-2 font-semibold text-pink-600 transition duration-200 hover:border-pink-200 hover:bg-pink-100 hover:text-pink-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Quay lại đăng nhập
          </router-link>
        </div>

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
