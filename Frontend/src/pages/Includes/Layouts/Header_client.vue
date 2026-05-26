<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '../../../stores/auth';
import { apiUrl, imageUrl } from '@/utils/api';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const isHomePage = computed(() => route.path === '/');
const isScrolled = ref(false);
const hasLightHeader = computed(() => !isHomePage.value || isScrolled.value);

const isLoggedIn = computed(() => !!authStore.user);
const name = computed(() => authStore.user?.name || '');
const role = computed(() => authStore.user?.role || 'user');

const isSearchOpen = ref(false);
const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);
const searchError = ref('');
const searchInput = ref(null);
let searchTimer = null;

const formatVND = (price) => {
    if (!price) return 'Liên hệ';
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(Number(price));
};

const openSearch = async () => {
    isSearchOpen.value = true;
    await nextTick();
    searchInput.value?.focus();
};

const closeSearch = () => {
    isSearchOpen.value = false;
    searchQuery.value = '';
    searchResults.value = [];
    searchError.value = '';
    isSearching.value = false;
    if (searchTimer) clearTimeout(searchTimer);
};

const fetchSearchResults = () => {
    if (searchTimer) clearTimeout(searchTimer);

    const query = searchQuery.value.trim();
    searchError.value = '';

    if (query.length < 2) {
        searchResults.value = [];
        isSearching.value = false;
        return;
    }

    isSearching.value = true;
    searchTimer = setTimeout(async () => {
        try {
            const response = await axios.get(apiUrl('/api/product/search'), {
                params: { q: query, limit: 6 },
            });
            searchResults.value = response.data.data || [];
        } catch (error) {
            console.error('Lỗi tìm kiếm sản phẩm:', error);
            searchError.value = 'Không thể tải gợi ý sản phẩm.';
            searchResults.value = [];
        } finally {
            isSearching.value = false;
        }
    }, 300);
};

const goToProduct = (product) => {
    if (!product?.slug) return;
    router.push(`/product/${product.slug}`);
    closeSearch();
};

const handleKeydown = (event) => {
    if (event.key === 'Escape') closeSearch();
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 24;
};

const logout = async () => {
    try {
        const token = localStorage.getItem('access_token');
        await axios.post(apiUrl('/api/Logout'), {}, {
            headers: { Authorization: `Bearer ${token}` }
        });
    } catch (error) {
        console.error('Lỗi logout server:', error);
    } finally {
        authStore.logout();
        router.replace('/');
    }
};

onMounted(() => {
    handleScroll();
    window.addEventListener('keydown', handleKeydown);
    window.addEventListener('scroll', handleScroll, { passive: true });
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeydown);
    window.removeEventListener('scroll', handleScroll);
    if (searchTimer) clearTimeout(searchTimer);
});

const getLinkClass = (to) => {
    const isActive = to === '/' 
        ? false 
        : route.path.startsWith(to);

    const baseClasses = 'text-sm font-medium transition relative py-1 after:absolute after:bottom-0 after:left-0 after:h-[2px] after:w-full after:transition-all after:duration-300';

    if (isActive) {
        return hasLightHeader.value
            ? `${baseClasses} text-pink-600 after:bg-pink-600 after:scale-x-100`
            : `${baseClasses} text-white after:bg-white after:scale-x-100`;
    } else {
        return hasLightHeader.value
            ? `${baseClasses} text-gray-500 hover:text-pink-600 after:origin-left after:scale-x-0 hover:after:scale-x-100 after:bg-pink-600/60`
            : `${baseClasses} text-white/90 hover:text-white after:origin-left after:scale-x-0 hover:after:scale-x-100 after:bg-white/60`;
    }
};
</script>

<template>
    <header
        class="sticky top-0 z-50 transition-all duration-300"
        :class="hasLightHeader ? 'bg-[#fff9f9]/95 border-b border-gray-100 text-gray-900 shadow-sm backdrop-blur-md' : 'bg-transparent border-transparent -mb-20 text-white'"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-8">
                    <router-link replace to="/" class="font-serif text-2xl font-bold tracking-wider text-black">
                        <img src="../../../../public/favicon.ico" class="w-20 h-20" alt="TBS Flower Shop">
                    </router-link>
                    <nav class="hidden md:flex space-x-8">
                        <router-link replace to="/product" :class="getLinkClass('/product')">Cửa hàng</router-link>
                    </nav>
                    <nav class="hidden md:flex space-x-8">
                        <router-link replace to="/" :class="getLinkClass('/')">Về chúng tôi</router-link>
                    </nav>
                    <nav class="hidden md:flex space-x-8">
                        <router-link replace to="/" :class="getLinkClass('/')">Dịch vụ</router-link>
                    </nav>
                    <nav class="hidden md:flex space-x-8">
                        <router-link replace to="/contact" :class="getLinkClass('/contact')">Liên hệ</router-link>
                    </nav>
                </div>

                <div class="flex items-center space-x-6">
                    <button @click="openSearch" class="transition" :class="hasLightHeader ? 'text-gray-500 hover:text-pink-600' : 'text-white/90 hover:text-white'" aria-label="Tìm kiếm sản phẩm">
                        <i class="fa-solid fa-magnifying-glass text-xl"></i>
                    </button>

                    <router-link replace to="/cart" class="transition relative" :class="hasLightHeader ? 'text-gray-500 hover:text-pink-600' : 'text-white/90 hover:text-white'">
                        <i class="fa-solid fa-bag-shopping text-xl"></i>
                        <span class="absolute -top-1.5 -right-1.5 bg-pink-600 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold shadow-sm">2</span>
                    </router-link>

                    <router-link v-if="!isLoggedIn" to="/login" class="transition relative" :class="hasLightHeader ? 'text-gray-500 hover:text-pink-600' : 'text-white/90 hover:text-white'">
                        <i class="fa-regular fa-user text-xl"></i>
                    </router-link>

                    <div v-else class="relative group hidden md:flex items-center space-x-4 ml-4 pl-4 cursor-pointer py-2" :class="hasLightHeader ? 'border-l border-gray-200' : 'border-l border-white/20'">
                        <div class="text-sm font-semibold" :class="hasLightHeader ? 'text-gray-800' : 'text-white'">Hi, {{ name }}</div>
                        <div class="absolute right-0 top-full mt-1 w-56 bg-white border border-gray-100 shadow-lg rounded-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <router-link to="/profile" class="flex items-center px-4 py-4 text-sm text-gray-700 hover:bg-gray-50 hover:text-black transition-colors">
                                <i class="fa-regular fa-user mr-2"></i>
                                Tài khoản
                            </router-link>

                            <div v-if="role === 'admin'" class="border-t border-gray-100 my-1"></div>

                            <router-link v-if="role === 'admin'" to="/admin" class="flex items-center px-4 py-4 text-sm text-gray-700 hover:bg-gray-50 hover:text-black transition-colors">
                                <i class="fa-solid fa-user-tie mr-2"></i>
                                Admin
                            </router-link>

                            <button @click="logout" class="w-full flex items-center px-4 py-3 text-sm text-left text-red-600 hover:bg-gray-50 hover:text-red-700 transition-colors">
                                <i class="fa-solid fa-right-from-bracket mr-2"></i>
                                Đăng xuất
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <Transition name="search-fade">
        <div v-if="isSearchOpen" class="fixed inset-0 z-[100] bg-black/40 backdrop-blur-sm px-4 py-6" @click.self="closeSearch">
            <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all">
                <div class="flex items-center gap-4 px-5 py-4 border-b border-gray-100">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                    <input
                        ref="searchInput"
                        v-model="searchQuery"
                        @input="fetchSearchResults"
                        type="text"
                        placeholder="Tìm hoa hồng, hoa sinh nhật, hoa khai trương..."
                        class="flex-1 border-0 outline-none focus:ring-0 text-base text-gray-900 placeholder:text-gray-400"
                    />
                    <button @click="closeSearch" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-pink-50 hover:text-pink-600 text-gray-500 transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="max-h-[70vh] overflow-y-auto p-3">
                    <div v-if="searchQuery.trim().length < 2" class="px-4 py-8 text-center text-sm text-gray-400">
                        Nhập ít nhất 2 ký tự để tìm sản phẩm.
                    </div>

                    <div v-else-if="isSearching" class="px-4 py-8 text-center text-sm text-gray-400">
                        <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                        Đang tìm sản phẩm...
                    </div>

                    <div v-else-if="searchError" class="px-4 py-8 text-center text-sm text-red-500">
                        {{ searchError }}
                    </div>

                    <div v-else-if="searchResults.length === 0" class="px-4 py-8 text-center text-sm text-gray-400">
                        Không tìm thấy sản phẩm phù hợp.
                    </div>

                    <div v-else class="space-y-2">
                        <button
                            v-for="product in searchResults"
                            :key="product.id"
                            @click="goToProduct(product)"
                            class="w-full flex items-center gap-4 p-3 rounded-2xl hover:bg-pink-50 transition-all duration-200 text-left group"
                        >
                            <img
                                :src="imageUrl(product.thumbnail)"
                                :alt="product.name"
                                class="w-16 h-16 rounded-xl object-cover bg-gray-100 border border-gray-100"
                            />
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] uppercase tracking-widest text-gray-400 font-semibold mb-1">
                                    {{ product.category_name || 'Sản phẩm' }}
                                </p>
                                <h3 class="font-semibold text-gray-900 truncate group-hover:text-pink-600 transition-colors">
                                    {{ product.name }}
                                </h3>
                                <p class="text-sm font-bold text-pink-600 mt-1">
                                    {{ formatVND(product.price) }}
                                </p>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-gray-300 group-hover:text-pink-600 transition-colors"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.search-fade-enter-active,
.search-fade-leave-active {
    transition: opacity 0.25s ease;
}

.search-fade-enter-from,
.search-fade-leave-to {
    opacity: 0;
}

.search-fade-enter-active > div,
.search-fade-leave-active > div {
    transition: transform 0.25s ease, opacity 0.25s ease;
}

.search-fade-enter-from > div,
.search-fade-leave-to > div {
    opacity: 0;
    transform: translateY(-12px) scale(0.98);
}
</style>
