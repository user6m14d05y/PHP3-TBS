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

        <main>
            
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