<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '../../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const isLoggedIn = computed(() => !!authStore.user);
const name = computed(() => authStore.user?.name || '');
const email = computed(() => authStore.user?.email || '');

const props = defineProps({
    isDark: Boolean
});

const emit = defineEmits(['toggle-sidebar', 'toggle-theme']);

const logout = async () => {
    try {
        const token = localStorage.getItem('access_token');
        await axios.post('http://localhost:8888/api/Logout', {}, {
            headers: { Authorization: `Bearer ${token}` }
        });
    } catch (error) {
        console.error("Lỗi logout server:", error);
    } finally {
        authStore.logout();
        router.replace('/');
    }
};

</script>

<template>
    <!-- Header Top -->
    <header :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-200'"
        class="flex items-center justify-between h-16 px-6 border-b transition-colors duration-300 relative z-10">
        <!-- Left Side / Toggle Button Menu -->
        <div class="flex items-center">
            <button @click="emit('toggle-sidebar')"
                :class="isDark ? 'text-gray-400 hover:text-white' : 'text-gray-500 hover:text-black'"
                class="focus:outline-none">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Right Side -->
        <div class="flex items-center space-x-5">

            <!-- Chế độ Tối/Sáng -->
            <button @click="emit('toggle-theme')"
                :class="isDark ? 'text-yellow-400 hover:text-yellow-300' : 'text-gray-500 hover:text-gray-900'"
                class="focus:outline-none transition-colors" title="Chế độ Tối/Sáng">
                <!-- Moon Icon (khi sáng) -->
                <i v-if="!isDark" class="fa-solid fa-moon text-xl"></i>
                <!-- Sun Icon (khi tối) -->
                <i v-else class="fa-solid fa-sun text-xl"></i>
            </button>

            <!-- Thông báo -->
            <button :class="isDark ? 'text-gray-400 hover:text-white' : 'text-gray-500 hover:text-gray-900'"
                class="relative focus:outline-none transition-colors" title="Thông báo">
                <i class="fa-regular fa-bell text-xl"></i>
                <span :class="isDark ? 'border-[#1e293b]' : 'border-white'"
                    class="absolute top-0 right-0 block w-2 h-2 bg-red-500 rounded-full border"></span>
            </button>

            <!-- Đường Line Nhỏ -->
            <div :class="isDark ? 'bg-gray-700' : 'bg-gray-300'" class="h-6 w-px hidden sm:block"></div>

            <!-- Người dùng & Avatar & Dropdown Hover -->
            <!-- Thêm thẻ py-4 để mở rộng vùng nhận diện hover, tránh bị mất focus khi di chuột -->
            <div class="relative flex items-center space-x-3 cursor-pointer group py-4">
                <img :class="isDark ? 'border-gray-600 group-hover:border-gray-400' : 'border-gray-200 group-hover:border-gray-400'"
                    class="w-9 h-9 rounded-full object-cover border transition"
                    src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                    alt="Avatar">
                <div class="hidden md:block">
                    <p :class="isDark ? 'text-white' : 'text-gray-900'" class="text-sm font-medium">{{ name }}</p>
                    <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-xs">{{ email }}</p>
                </div>

                <!-- Dropdown -->
                <div :class="isDark ? 'bg-[#1e293b] border-gray-700 shadow-gray-900/50' : 'bg-white border-gray-200 shadow-lg'"
                    class="absolute right-0 top-[100%] w-56 border rounded-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                    <div class="py-2">
                        <!-- Profile -->
                        <router-link to="/profile"
                            :class="isDark ? 'text-gray-300 hover:bg-gray-700 hover:text-white' : 'text-gray-700 hover:bg-gray-50 hover:text-black'"
                            class="flex items-center px-4 py-2 text-sm transition-colors">
                            <i class="fa-regular fa-user mr-2"></i>
                            Hồ sơ
                        </router-link>
                    </div>

                    <div :class="isDark ? 'border-gray-700' : 'border-gray-100'" class="border-t my-1"></div>

                    <!-- Home -->
                    <router-link to="/"
                        :class="isDark ? 'text  -gray-300 hover:bg-gray-700 hover:text-white' : 'text-gray-700 hover:bg-gray-50 hover:text-black'"
                        class="flex items-center px-4 py-2 text-sm transition-colors">
                        <i class="fa-solid fa-user-tie mr-2"></i>
                        Quản trị
                    </router-link>

                    <div class="py-2">
                        <!-- Logout -->
                        <button @click="logout"
                            :class="isDark ? 'text-red-400 hover:bg-gray-700 hover:text-red-300' : 'text-red-600 hover:bg-gray-50 hover:text-red-700'"
                            class="w-full flex items-center px-4 py-2 text-sm text-left transition-colors">
                            <i class="fa-solid fa-right-from-bracket mr-2"></i>
                            Đăng xuất
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped></style>