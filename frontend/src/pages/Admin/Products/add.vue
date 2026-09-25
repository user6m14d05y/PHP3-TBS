<script setup>
import HeaderAdmin from '../../Includes/Layouts/Header_Admin.vue';
import NavbarAdmin from '../../Includes/Layouts/Navbar_Admin.vue';
import ProductForm from './ProductForm.vue';
import { onMounted, ref } from 'vue';

const isDark = ref(false);
const isSidebarOpen = ref(true);

const toggleTheme = () => {
  isDark.value = !isDark.value;
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

onMounted(() => {
  isDark.value = localStorage.getItem('theme') === 'dark';
});
</script>

<template>
  <div class="antialiased font-sans transition-colors duration-300">
    <div :class="isDark ? 'bg-[#0f172a] text-gray-100' : 'bg-gray-50 text-gray-900'" class="flex h-screen overflow-hidden">
      <NavbarAdmin :isDark="isDark" :isSidebarOpen="isSidebarOpen" />

      <div class="relative flex flex-1 flex-col overflow-hidden">
        <HeaderAdmin
          :isDark="isDark"
          @toggle-sidebar="isSidebarOpen = !isSidebarOpen"
          @toggle-theme="toggleTheme"
        />

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
          <div class="mx-auto w-full max-w-screen-2xl">
            <div class="mb-6">
              <router-link :to="{ name: 'admin-product' }" class="text-sm font-semibold text-pink-600 hover:text-pink-700">
                <i class="fa-solid fa-arrow-left mr-1"></i>
                Quay lại danh sách
              </router-link>
              <h1 :class="isDark ? 'text-white' : 'text-gray-900'" class="mt-3 text-3xl font-serif font-bold">
                Thêm sản phẩm
              </h1>
              <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="mt-1 text-sm">
                Tạo sản phẩm mới với dữ liệu SEO riêng cho Google.
              </p>
            </div>

            <ProductForm mode="create" :isDark="isDark" />
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
