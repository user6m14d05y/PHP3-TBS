<script setup>
import header_admin from '../Includes/Layouts/Header_Admin.vue';
import navbar_admin from '../Includes/Layouts/Navbar_Admin.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const isDark = ref(false);
const isSidebarOpen = ref(true);
const users = ref([]);

const fetchUsers = async () => {
    try {
        const response = await axios.get('http://localhost:8888/api/user');
        users.value = response.data.data; 
    } catch (error) {
        console.error('Error fetching users:', error);
    }
};


onMounted(() => {
    fetchUsers();
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        isDark.value = true;
    } else if (savedTheme === 'light') {
        isDark.value = false;
    } else {
        isDark.value = false;
    }
});

const toggleTheme = () => {
    isDark.value = !isDark.value;
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

const formatDate = (dateStr) => {
    if (!dateStr) return '—';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${hours}:${minutes} ${day}/${month}/${year}`;
};
</script>

<template>
  <div class="antialiased font-sans transition-colors duration-300">
    <div :class="isDark ? 'bg-[#0f172a] text-gray-100' : 'bg-gray-50 text-gray-900'" class="flex h-screen overflow-hidden">
      
      <!-- Component Navbar Trái (Đã nối props đầy đủ) -->
      <navbar_admin :isDark="isDark" :isSidebarOpen="isSidebarOpen" />

      <!-- Main Content Khung Bên Phải -->
      <div class="flex-1 flex flex-col overflow-hidden">
        
        <!-- Component Header Top (Đã nối props và emit sự kiện) -->
        <header_admin 
            :isDark="isDark" 
            @toggle-sidebar="isSidebarOpen = !isSidebarOpen" 
            @toggle-theme="toggleTheme" 
        />

                <main class="flex-1 overflow-y-auto p-4 md:p-8">
                    <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-100'"
                        class="w-full min-h-[500px] p-6 md:p-8 border shadow-sm rounded-xl transition-colors duration-300">

                        <!-- Header & Button in Flexbox -->
                        <div :class="isDark ? 'border-gray-700' : 'border-gray-200'"
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-6 border-b gap-4 sm:gap-0">
                            <div>
                                <h2 :class="isDark ? 'text-white' : 'text-gray-900'"
                                    class="text-3xl font-serif font-bold mb-2">Người dùng</h2>
                                <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="font-light text-sm">Bảng
                                    điều khiển quản lý người dùng hiển thị.</p>
                            </div>

                            <!-- Thêm danh mục Button Moves Right -->
                            <button @click="openAddModal"
                                class="flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm shrink-0">
                                <i class="fa-solid fa-plus mr-2 text-lg"></i> Thêm người dùng
                            </button>
                        </div>

                        <!-- TABLE -->
                        <div class="overflow-x-auto rounded-lg border"
                            :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr :class="isDark ? 'bg-gray-800/50 text-gray-400 border-gray-700' : 'bg-gray-50 text-gray-600 border-gray-200'"
                                        class="border-b text-xs uppercase tracking-wider">
                                        <th class="px-6 py-4 font-semibold">STT</th>
                                        <th class="px-6 py-4 font-semibold">Tên</th>
                                        <th class="px-6 py-4 font-semibold">Email</th>
                                        <th class="px-6 py-4 font-semibold">Ngày tạo</th>
                                        <th class="px-6 py-4 font-semibold">Phân Quyền</th>
                                        <th class="px-6 py-4 font-semibold text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody :class="isDark ? 'divide-gray-700' : 'divide-gray-200'" class="divide-y">
                                    <tr v-for="(user, index) in users" :key="user.id"
                                        :class="isDark ? 'hover:bg-gray-700/50 text-gray-300 border-gray-700' : 'hover:bg-gray-50 text-gray-700 border-gray-200'"
                                        class="border-b transition-colors">
                                        <td class="px-6 py-4">{{ index + 1 }}</td>
                                        <td class="px-6 py-4 font-medium">{{ user.name }}</td>
                                        <td class="px-6 py-4">{{ user.email }}</td>
                                        <td class="px-6 py-4 text-sm font-medium tabular-nums" :class="isDark ? 'text-gray-400' : 'text-gray-500'">
                                             {{ formatDate(user.created_at) }}
                                         </td>
                                        <td class="px-6 py-4">
                                            <span :class="user.role === 'admin'
                                                ? 'bg-purple-100 text-purple-700'
                                                : 'bg-green-100 text-green-700'"
                                                class="px-2 py-1 rounded-full text-xs font-semibold">
                                                {{ user.role }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-4 whitespace-nowrap">
                                            <button class="text-blue-500 hover:text-blue-700 transition-colors">
                                                <i class="fa-regular fa-pen-to-square text-lg"></i>
                                            </button>
                                            <button class="text-red-500 hover:text-red-700 transition-colors">
                                                <i class="fa-regular fa-trash-can text-lg"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
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