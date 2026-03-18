<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const isLoggedIn = ref(false);
const name = ref('');


const props = defineProps({
    isDark: Boolean
});

const emit = defineEmits(['toggle-sidebar', 'toggle-theme']);

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
        router.push('/login');
    }
};

</script>

<template>
    <!-- Header Top -->
    <header :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-200'" class="flex items-center justify-between h-16 px-6 border-b transition-colors duration-300 relative z-10">
        <!-- Left Side / Toggle Button Menu -->
        <div class="flex items-center">
            <button @click="emit('toggle-sidebar')" :class="isDark ? 'text-gray-400 hover:text-white' : 'text-gray-500 hover:text-black'" class="focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
        
        <!-- Right Side -->
        <div class="flex items-center space-x-5">
            
            <!-- Chế độ Tối/Sáng -->
            <button @click="emit('toggle-theme')" :class="isDark ? 'text-yellow-400 hover:text-yellow-300' : 'text-gray-500 hover:text-gray-900'" class="focus:outline-none transition-colors" title="Chế độ Tối/Sáng">
                <!-- Moon Icon (khi sáng) -->
                <svg v-if="!isDark" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                <!-- Sun Icon (khi tối) -->
                <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </button>

            <!-- Thông báo -->
            <button :class="isDark ? 'text-gray-400 hover:text-white' : 'text-gray-500 hover:text-gray-900'" class="relative focus:outline-none transition-colors" title="Thông báo">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span :class="isDark ? 'border-[#1e293b]' : 'border-white'" class="absolute top-0 right-0 block w-2 h-2 bg-red-500 rounded-full border"></span>
            </button>

            <!-- Đường Line Nhỏ -->
            <div :class="isDark ? 'bg-gray-700' : 'bg-gray-300'" class="h-6 w-px hidden sm:block"></div>

            <!-- Người dùng & Avatar & Dropdown Hover -->
            <!-- Thêm thẻ py-4 để mở rộng vùng nhận diện hover, tránh bị mất focus khi di chuột -->
            <div class="relative flex items-center space-x-3 cursor-pointer group py-4">
                <img :class="isDark ? 'border-gray-600 group-hover:border-gray-400' : 'border-gray-200 group-hover:border-gray-400'" class="w-9 h-9 rounded-full object-cover border transition" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Avatar">
                <div class="hidden md:block">
                    <p :class="isDark ? 'text-white' : 'text-gray-900'" class="text-sm font-medium">Quản trị viên</p>
                    <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-xs">admin@demo.com</p>
                </div>
                
                <!-- Dropdown -->
                <div :class="isDark ? 'bg-[#1e293b] border-gray-700 shadow-gray-900/50' : 'bg-white border-gray-200 shadow-lg'" class="absolute right-0 top-[100%] w-56 border rounded-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                    <div class="py-2">
                        <!-- Home -->
                        <router-link to="/" :class="isDark ? 'text  -gray-300 hover:bg-gray-700 hover:text-white' : 'text-gray-700 hover:bg-gray-50 hover:text-black'" class="flex items-center px-4 py-2 text-sm transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Trang chủ
                        </router-link>
                        <!-- Profile -->
                        <router-link to="/admin/profile" :class="isDark ? 'text-gray-300 hover:bg-gray-700 hover:text-white' : 'text-gray-700 hover:bg-gray-50 hover:text-black'" class="flex items-center px-4 py-2 text-sm transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Hồ sơ (Profile)
                        </router-link>
                        <!-- Settings -->
                        <router-link to="/admin/settings" :class="isDark ? 'text-gray-300 hover:bg-gray-700 hover:text-white' : 'text-gray-700 hover:bg-gray-50 hover:text-black'" class="flex items-center px-4 py-2 text-sm transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Cài đặt
                        </router-link>
                    </div>
                    
                    <div :class="isDark ? 'border-gray-700' : 'border-gray-100'" class="border-t my-1"></div>
                    
                    <div class="py-2">
                        <!-- Logout -->
                        <button @click="logout" :class="isDark ? 'text-red-400 hover:bg-gray-700 hover:text-red-300' : 'text-red-600 hover:bg-gray-50 hover:text-red-700'" class="w-full flex items-center px-4 py-2 text-sm text-left transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Đăng xuất
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header> 
</template>

<style scoped></style>