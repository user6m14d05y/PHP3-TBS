<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const isLoggedIn = ref(false);
const name = ref('');
const role = ref('');

onMounted(() => {
    const userSession = localStorage.getItem('user');
    if (userSession) {
        const userData = JSON.parse(userSession);
        isLoggedIn.value = userData.isLoggedIn;
        name.value = userData.name;
        role.value = userData.role;
    }
});

const logout = async () => {
    try {
        const token = localStorage.getItem('access_token');
        await axios.post('http://localhost:8888/api/Logout', {}, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        }); 
    } catch (error) {
        console.error("Lỗi logout server:", error);
    } finally {
        localStorage.removeItem('access_token');
        localStorage.removeItem('user');
        isLoggedIn.value = false;
        name.value = '';
        router.replace('/');
    }
};
</script>    

<template>
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
          <div class="flex items-center space-x-8">
            <router-link replace to="/" class="font-serif text-2xl font-bold tracking-wider text-black">
              <img src="../../../../public/favicon.ico" class="w-20 h-20" alt="">
            </router-link>
            <nav class="hidden md:flex space-x-8">
              <router-link replace to="/product" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition">Cửa hàng</router-link>
            </nav>
          </div>
          <div class="flex items-center space-x-6">
            <button class="text-gray-500 hover:text-gray-900 transition">
              <i class="fa-solid fa-magnifying-glass text-xl"></i>
            </button>
            <button class="text-gray-500 hover:text-gray-900 transition relative">
              <i class="fa-solid fa-bag-shopping text-xl"></i>
              <span class="absolute -top-1.5 -right-1.5 bg-black text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">2</span>
            </button>
            <router-link v-if="!isLoggedIn" to="/login" class="text-gray-500 hover:text-gray-900 transition relative">
              <i class="fa-regular fa-user text-xl"></i>
            </router-link>
            
            <div v-else class="hidden md:flex items-center space-x-4 ml-4 pl-4 border-l border-gray-200">
                <span class="text-sm font-semibold text-gray-800">Hi, {{ name }}</span>
                <button @click="logout" class="text-sm font-medium text-gray-500 hover:text-red-600 transition">Đăng xuất</button>
            </div>
          </div>
        </div>
      </div>
    </header>
    </template>