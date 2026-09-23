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
            <router-link replace to="/admin" class="text-2xl font-serif font-bold tracking-wider transition-opacity duration-300">
                <img src="../../../../public/favicon.ico" alt="Logo" class="w-15 h-15">   
            </router-link>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto overflow-x-hidden custom-scrollbar">
            <!-- Dashboard -->
            <router-link replace to="/admin/dashboard" :class="getLinkClass('/admin/dashboard', true)" class="flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-colors w-full group/item cursor-pointer">
                <i class="fa-solid fa-gauge text-lg shrink-0 transition-transform duration-300" :class="isHovered ? 'scale-110' : ''"></i>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Dashboard</span>
            </router-link>
            <!-- Sản phẩm & Sub-menu -->
            <div>
                <div :class="getLinkClass('/admin/product')" class="w-full flex items-center justify-between rounded-lg text-sm font-medium transition-colors group/item text-left overflow-hidden">
                    <!-- Phần bên trái (Icon + Text): Chuyển link -->
                    <router-link replace to="/admin/product" class="flex items-center flex-1 px-3 py-3 cursor-pointer">
                        <i class="fa-solid fa-box text-lg shrink-0 transition-transform duration-300 group-hover/item:scale-110"></i>
                        <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Sản phẩm</span>
                    </router-link>
                    <!-- Phần bên phải (Mũi tên): Đóng / mở menu -->
                    <div @click.stop.prevent="toggleProductMenu" v-show="isExpanded" class="px-3 py-3 cursor-pointer hover:bg-black/10 dark:hover:bg-white/10 transition-colors shrink-0 flex items-center h-full">
                        <i :class="{'rotate-180': isProductMenuOpen}" class="fa-solid fa-chevron-down text-sm shrink-0 transition-transform duration-300"></i>
                    </div>
                </div>
                
                <!-- Sub Menu mượt (Accordion Transition) -->
                <div 
                    :class="isProductMenuOpen && isExpanded ? 'max-h-48 opacity-100 mt-1' : 'max-h-0 opacity-0 mt-0'"
                    class="overflow-hidden transition-all duration-300 ease-in-out w-full"
                >
                    <div class="pl-12 pr-4 py-2 space-y-1">
                        <router-link replace to="/admin/product/size" :class="getLinkClass('/admin/product/size', true)" class="block px-3 py-2 text-sm rounded-md transition-colors w-full">Kích thước</router-link>
                    </div>
                </div>
            </div>

            <!-- Danh mục -->
            <router-link replace to="/admin/category" :class="getLinkClass('/admin/category')" class="flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-colors w-full group/item cursor-pointer">
                <i class="fa-solid fa-layer-group text-lg shrink-0 transition-transform duration-300 group-hover/item:scale-110"></i>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Danh mục</span>
            </router-link>

            <!-- Coupon -->
            <router-link replace to="/admin/coupon" :class="getLinkClass('/admin/coupon')" class="flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-colors w-full group/item cursor-pointer">
                <i class="fa-solid fa-tags text-lg shrink-0 transition-transform duration-300 group-hover/item:scale-110"></i>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Mã giảm giá</span>
            </router-link>

            <!-- Order -->
            <router-link replace to="/admin/order" :class="getLinkClass('/admin/order')" class="flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-colors w-full group/item cursor-pointer">
                <i class="fa-solid fa-cart-shopping text-lg shrink-0 transition-transform duration-300 group-hover/item:scale-110"></i>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Đơn hàng</span>
            </router-link>

            <!-- Người dùng -->
            <router-link replace to="/admin/user" :class="getLinkClass('/admin/user')" class="flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-colors w-full group/item cursor-pointer">
                <i class="fa-solid fa-users text-lg shrink-0 transition-transform duration-300 group-hover/item:scale-110"></i>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Người dùng</span>
            </router-link>

            <!-- Liên hệ -->
            <router-link replace to="/admin/contact" :class="getLinkClass('/admin/contact')" class="flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-colors w-full group/item cursor-pointer">
                <i class="fa-solid fa-address-book text-lg shrink-0 transition-transform duration-300 group-hover/item:scale-110"></i>
                <span :class="isExpanded ? 'opacity-100 ml-4' : 'opacity-0 w-0 h-0 overflow-hidden'" class="transition-all duration-300">Liên hệ</span>
            </router-link>
            
            <!-- Settings -->
            <router-link replace to="/admin/setting" :class="getLinkClass('/admin/setting')" class="flex items-center px-3 py-3 rounded-lg text-sm font-medium transition-colors w-full group/item cursor-pointer">
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