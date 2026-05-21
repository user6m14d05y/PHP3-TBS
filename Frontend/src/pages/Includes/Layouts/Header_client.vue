<script setup>
import { onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '../../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

// Trạng thái sẽ tự động cập nhật (Reactive) từ State Pinia
const isLoggedIn = computed(() => !!authStore.user);
const name = computed(() => authStore.user?.name || '');
// const role = computed(() => authStore.user?.role || '');

const logout = async () => {
    try {
        const token = localStorage.getItem('access_token');
        await axios.post('http://localhost:8888/api/Logout', {}, {
            headers: { Authorization: `Bearer ${token}` }
        }); 
    } catch (error) {
        console.error("Lỗi logout server:", error);
    } finally {
        authStore.logout(); // Dọn dẹp token ở store
        router.replace('/');
    }
};
</script>    

<template>
    <header class="sticky top-0 z-50 bg-[#fff9f9] border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
          <div class="flex items-center space-x-8">
            <router-link replace to="/" class="font-serif text-2xl font-bold tracking-wider text-black">
              <img src="../../../../public/favicon.ico" class="w-20 h-20" alt="">
            </router-link>
            <nav class="hidden md:flex space-x-8">
              <router-link replace to="/product" class="text-sm font-medium text-gray-500 hover:text-pink-600 transition">Cửa hàng</router-link>
            </nav>
          </div>
          <div class="flex items-center space-x-6">
            <button class="text-gray-500 hover:text-pink-600 transition">
              <i class="fa-solid fa-magnifying-glass text-xl"></i>
            </button>
            <router-link replace to="/cart" class="text-gray-500 hover:text-pink-600 transition relative">
              <i class="fa-solid fa-bag-shopping text-xl"></i>
              <span class="absolute -top-1.5 -right-1.5 bg-pink-600 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">2</span>
            </router-link>
            <router-link v-if="!isLoggedIn" to="/login" class="text-gray-500 hover:text-pink-600 transition relative">
              <i class="fa-regular fa-user text-xl"></i>
            </router-link>
            
            <div v-else class="relative group hidden md:flex items-center space-x-4 ml-4 pl-4 border-l border-gray-200 cursor-pointer py-2">
                <div class="text-sm font-semibold text-gray-800">Hi, {{ name }}</div>
                 <!-- Dropdown -->
                <div
                    class="absolute right-0 top-full mt-1 w-56 bg-white border border-gray-100 shadow-lg rounded-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                    <div>
                        <!-- Profile -->
                        <router-link to="/profile"
                            class="flex items-center px-4 py-4 text-sm text-gray-700 hover:bg-gray-50 hover:text-black transition-colors">
                            <i class="fa-regular fa-user mr-2"></i>
                            Tài khoản
                        </router-link>
                    </div>

                    <div v-if="role === 'admin'" class="border-t border-gray-100 my-1"></div>

                    <!-- Admin -->
                    <div v-if="role === 'admin'">
                        <router-link to="/admin/dashboard"
                            class="flex items-center px-4 py-4 text-sm text-gray-700 hover:bg-gray-50 hover:text-black transition-colors">
                            <i class="fa-solid fa-user-tie mr-2"></i>
                            Admin
                        </router-link>
                    </div>

                    <div>
                        <!-- Logout -->
                        <button @click="logout"
                            class="w-full flex items-center px-4 py-3 text-sm text-left text-red-600 hover:bg-gray-50 hover:text-red-700 transition-colors">
                            <i class="fa-solid fa-right-from-bracket mr-2"></i>
                            Đăng xuất
                        </button>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </header>
    </template>