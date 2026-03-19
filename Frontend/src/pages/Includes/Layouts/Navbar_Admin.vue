<script setup>
import { ref, computed, watch } from 'vue';
import { useRoute } from 'vue-router';

const props = defineProps({
    isDark: Boolean,
    isSidebarOpen: Boolean
});

const route = useRoute();
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

watch(() => route.path, (newPath) => {
    if (newPath.startsWith('/admin/product') && isExpanded.value) {
        isProductMenuOpen.value = true;
    }
}, { immediate: true });

const getLinkClass = (path, exact = false) => {
    const isActive = exact ? route.path === path : route.path.startsWith(path);
    if (isActive) {
        return props.isDark ? 'bg-gray-700 text-white' : 'bg-gray-100 text-gray-900';
    }
    return props.isDark ? 'text-gray-400 hover:bg-gray-700 hover:text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900';
};
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
                <img src="../../../../public/favicon.ico" alt="Logo" class="w-15 h-15">   
            </h1>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto overflow-x-hidden custom-scrollbar">
            <!-- Dashboard -->
            <router-link to="/admin/dashboard" :class="getLinkClass('/admin/dashboard', true)" class="flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-colors w-full group/item cursor-pointer">
                <i class="fa-solid fa-gauge text-lg shrink-0 transition-transform duration-300" :class="isHovered ? 'scale-110' : ''"></i>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Dashboard</span>
            </router-link>
            <!-- Sản phẩm & Sub-menu -->
            <div>
                <button @click="toggleProductMenu" :class="getLinkClass('/admin/product')" class="w-full flex items-center justify-between px-3 py-3 rounded-lg text-sm font-medium transition-colors cursor-pointer group/item text-left">
                    <div class="flex items-center w-full">
                        <i class="fa-solid fa-box text-lg shrink-0 transition-transform duration-300 group-hover/item:scale-110"></i>
                        <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Sản phẩm</span>
                    </div>
                    <!-- Đổi sang v-show để đảm bảo transition mượt -->
                    <i v-show="isExpanded" :class="{'rotate-180': isProductMenuOpen}" class="fa-solid fa-chevron-down text-sm shrink-0 transition-transform duration-300"></i>
                </button>
                
                <!-- Sub Menu mượt (Accordion Transition) -->
                <div 
                    :class="isProductMenuOpen && isExpanded ? 'max-h-48 opacity-100 mt-1' : 'max-h-0 opacity-0 mt-0'"
                    class="overflow-hidden transition-all duration-300 ease-in-out w-full"
                >
                    <div class="pl-12 pr-4 py-2 space-y-1">
                        <!-- <router-link to="/admin/products" :class="isDark ? 'text-gray-400 hover:text-white hover:bg-gray-700/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'" class="block px-3 py-2 text-sm rounded-md transition-colors w-full">Tất cả sản phẩm</router-link> -->
                        <router-link to="/admin/product/color" :class="getLinkClass('/admin/product/color', true)" class="block px-3 py-2 text-sm rounded-md transition-colors w-full">Màu sắc</router-link>
                        <router-link to="/admin/product/size" :class="getLinkClass('/admin/product/size', true)" class="block px-3 py-2 text-sm rounded-md transition-colors w-full">Kích thước</router-link>
                    </div>
                </div>
            </div>

            <!-- Danh mục -->
            <router-link to="/admin/category" :class="getLinkClass('/admin/category')" class="flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-colors w-full group/item cursor-pointer">
                <i class="fa-solid fa-layer-group text-lg shrink-0 transition-transform duration-300 group-hover/item:scale-110"></i>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Danh mục</span>
            </router-link>

            <!-- Order -->
            <router-link to="/admin/order" :class="getLinkClass('/admin/order')" class="flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-colors w-full group/item cursor-pointer">
                <i class="fa-solid fa-cart-shopping text-lg shrink-0 transition-transform duration-300 group-hover/item:scale-110"></i>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Đơn hàng</span>
            </router-link>

            <!-- Người dùng -->
            <router-link to="/admin/user" :class="getLinkClass('/admin/user')" class="flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-colors w-full group/item cursor-pointer">
                <i class="fa-solid fa-users text-lg shrink-0 transition-transform duration-300 group-hover/item:scale-110"></i>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Người dùng</span>
            </router-link>

            <!-- Liên hệ -->
            <router-link to="/admin/contact" :class="getLinkClass('/admin/contact')" class="flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-colors w-full group/item cursor-pointer">
                <i class="fa-solid fa-address-book text-lg shrink-0 transition-transform duration-300 group-hover/item:scale-110"></i>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Liên hệ</span>
            </router-link>
            
            <!-- Settings -->
            <router-link to="/admin/setting" :class="getLinkClass('/admin/setting')" class="flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-colors w-full group/item cursor-pointer">
                <i class="fa-solid fa-gear text-lg shrink-0 transition-transform duration-300 group-hover/item:scale-110"></i>
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