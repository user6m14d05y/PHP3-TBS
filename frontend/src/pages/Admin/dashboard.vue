<script setup>
import header_admin from '../Includes/Layouts/Header_Admin.vue';
import navbar_admin from '../Includes/Layouts/Navbar_Admin.vue';
import { ref, onMounted } from 'vue';

const isDark = ref(false);
const isSidebarOpen = ref(true);

// Lấy chế độ dark mode từ localStorage (nếu có)
onMounted(() => {
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
        
        <!-- Main Dashboard Content -->
        <main :class="isDark ? 'bg-[#0f172a]' : 'bg-gray-50'" class="flex-1 overflow-x-hidden overflow-y-auto p-6 transition-colors duration-300">
            <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-100'" class="w-full h-full min-h-[500px] p-8 border shadow-sm rounded-lg transition-colors duration-300">
                <div :class="isDark ? 'border-gray-700' : 'border-gray-200'" class="mb-10 pb-6 border-b">
                    <h2 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-3xl font-serif font-bold mb-2">Tổng Quan (Overview)</h2>
                    <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="font-light">Bảng điều khiển quản lý trung tâm. Thiết kế tinh gọn.</p>
                </div>
                
                <!-- Thống kê Tĩnh -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
                    <div :class="isDark ? 'bg-gray-700/40 border-gray-700' : 'bg-gray-50 border-gray-100'" class="p-6 border rounded-lg">
                        <div class="flex items-center justify-between mb-4">
                           <span :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider font-semibold">Tổng Doanh Thu</span>
                           <div :class="isDark ? 'bg-gray-800 border-gray-600' : 'bg-white border-gray-100'" class="p-2 rounded shadow-sm border">
                                <svg :class="isDark ? 'text-gray-300' : 'text-gray-600'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                           </div>
                        </div>
                        <h3 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-3xl font-serif font-bold mt-1">24.500.000 ₫</h3>
                        <p :class="isDark ? 'text-green-400' : 'text-green-600'" class="text-xs mt-4 flex items-center font-medium"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg> Tăng 12% so với tháng trước</p>
                    </div>
                    
                    <div :class="isDark ? 'bg-gray-700/40 border-gray-700' : 'bg-gray-50 border-gray-100'" class="p-6 border rounded-lg">
                        <div class="flex items-center justify-between mb-4">
                            <span :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider font-semibold">Đơn Hàng Mới</span>
                            <div :class="isDark ? 'bg-gray-800 border-gray-600' : 'bg-white border-gray-100'" class="p-2 rounded shadow-sm border">
                                <svg :class="isDark ? 'text-gray-300' : 'text-gray-600'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                        </div>
                        <h3 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-3xl font-serif font-bold mt-1">12</h3>
                        <p :class="isDark ? 'text-yellow-400' : 'text-yellow-600'" class="text-xs mt-4 font-medium flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Đang chờ xử lý: 3 đơn</p>
                    </div>
                    
                    <div :class="isDark ? 'bg-gray-700/40 border-gray-700' : 'bg-gray-50 border-gray-100'" class="p-6 border rounded-lg">
                        <div class="flex items-center justify-between mb-4">
                           <span :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider font-semibold">Người Dùng Hiện Tại</span>
                           <div :class="isDark ? 'bg-gray-800 border-gray-600' : 'bg-white border-gray-100'" class="p-2 rounded shadow-sm border">
                                <svg :class="isDark ? 'text-gray-300' : 'text-gray-600'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                           </div>
                        </div>
                        <h3 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-3xl font-serif font-bold mt-1">1,482</h3>
                        <p :class="isDark ? 'text-green-400' : 'text-green-600'" class="text-xs mt-4 flex items-center font-medium"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg> 24 đăng ký mới hôm nay</p>
                    </div>
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