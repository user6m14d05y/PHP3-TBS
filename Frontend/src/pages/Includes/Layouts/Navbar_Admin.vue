<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    isDark: Boolean,
    isSidebarOpen: Boolean
});

const isHovered = ref(false);
const isExpanded = computed(() => props.isSidebarOpen || isHovered.value);
const isProductMenuOpen = ref(false);

const toggleProductMenu = () => {
    if (!isExpanded.value) return; 
    isProductMenuOpen.value = !isProductMenuOpen.value;
};

watch(isExpanded, (newVal) => {
    if (!newVal) {
        isProductMenuOpen.value = false;
    }
});
</script>

<template>
    <!-- Sidebar / Navbar Trái -->
    <aside 
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
        :class="[
            isExpanded ? 'w-64 translate-x-0' : 'w-[4.5rem] -translate-x-full lg:translate-x-0',
            isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-200'
        ]"
        class="fixed z-30 inset-y-0 left-0 transition-all duration-300 ease-in-out border-r lg:static flex flex-col overflow-hidden whitespace-nowrap h-screen shrink-0"
    >
        <!-- Logo Header -->
        <div :class="isDark ? 'border-gray-700' : 'border-gray-200'" class="flex items-center justify-center h-16 border-b shrink-0 transition-all duration-300">
            <h1 :class="isDark ? 'text-white' : 'text-black'" class="text-2xl font-serif font-bold tracking-wider transition-opacity duration-300">
                {{ isExpanded ? 'T-ADMIN' : 'T' }}
            </h1>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto overflow-x-hidden custom-scrollbar">
            <!-- Dashboard -->
            <router-link to="/admin/dashboard" :class="isDark ? 'bg-gray-700 text-white' : 'bg-gray-100 text-gray-900'" class="flex items-center px-3 py-3 rounded-lg font-medium transition-colors w-full group/item cursor-pointer">
                <svg class="w-6 h-6 shrink-0 transition-transform duration-300" :class="isHovered ? 'scale-110' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Dashboard</span>
            </router-link>

            <!-- Danh mục -->
            <router-link to="/admin/categories" :class="isDark ? 'text-gray-400 hover:bg-gray-700 hover:text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" class="flex items-center px-3 py-3 rounded-lg font-medium transition-colors w-full group/item cursor-pointer">
                <svg class="w-6 h-6 shrink-0 transition-transform duration-300 group-hover/item:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Danh mục</span>
            </router-link>

            <!-- Sản phẩm & Sub-menu -->
            <div>
                <button @click="toggleProductMenu" :class="isDark ? 'text-gray-400 hover:bg-gray-700 hover:text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" class="w-full flex items-center justify-between px-3 py-3 rounded-lg font-medium transition-colors cursor-pointer group/item text-left">
                    <div class="flex items-center w-full">
                        <svg class="w-6 h-6 shrink-0 transition-transform duration-300 group-hover/item:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Sản phẩm</span>
                    </div>
                    <!-- Đổi sang v-show để đảm bảo transition mượt -->
                    <svg v-show="isExpanded" :class="{'rotate-180': isProductMenuOpen}" class="w-4 h-4 shrink-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <!-- Sub Menu mượt (Accordion Transition) -->
                <div 
                    :class="isProductMenuOpen && isExpanded ? 'max-h-48 opacity-100 mt-1' : 'max-h-0 opacity-0 mt-0'"
                    class="overflow-hidden transition-all duration-300 ease-in-out w-full"
                >
                    <div class="pl-12 pr-4 py-2 space-y-1">
                        <router-link to="/admin/products" :class="isDark ? 'text-gray-400 hover:text-white hover:bg-gray-700/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'" class="block px-3 py-2 text-sm rounded-md transition-colors w-full">Tất cả sản phẩm</router-link>
                        <router-link to="/admin/products/colors" :class="isDark ? 'text-gray-400 hover:text-white hover:bg-gray-700/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'" class="block px-3 py-2 text-sm rounded-md transition-colors w-full">Màu sắc (Color)</router-link>
                        <router-link to="/admin/products/sizes" :class="isDark ? 'text-gray-400 hover:text-white hover:bg-gray-700/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'" class="block px-3 py-2 text-sm rounded-md transition-colors w-full">Kích thước (Size)</router-link>
                    </div>
                </div>
            </div>

            <!-- Người dùng -->
            <router-link to="/admin/users" :class="isDark ? 'text-gray-400 hover:bg-gray-700 hover:text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" class="flex items-center px-3 py-3 rounded-lg font-medium transition-colors w-full group/item cursor-pointer">
                <svg class="w-6 h-6 shrink-0 transition-transform duration-300 group-hover/item:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Người dùng</span>
            </router-link>
            
            <!-- Settings -->
            <router-link to="/admin/settings" :class="isDark ? 'text-gray-400 hover:bg-gray-700 hover:text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" class="flex items-center px-3 py-3 rounded-lg font-medium transition-colors w-full group/item cursor-pointer">
                <svg class="w-6 h-6 shrink-0 transition-transform duration-300 group-hover/item:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Cài đặt</span>
            </router-link>
        </nav>
    </aside>
</template>

<style scoped>
/* Tuỳ chỉnh thanh cuộn cho thanh lịch giống Admin phổ thông hiện đại */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #475569;
}
</style>