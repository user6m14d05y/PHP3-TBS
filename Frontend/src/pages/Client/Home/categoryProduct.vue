<script setup>
import Footer_client from '@/pages/Includes/Layouts/Footer_client.vue';
import Header_client from '@/pages/Includes/Layouts/Header_client.vue';
import SlidebarCategory_client from '@/pages/Includes/Layouts/SlidebarCategory_client.vue';
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

// route
const route = useRoute();
const router = useRouter();

const products = ref([]);
const categories = ref([]);
const categories_item = ref([]);
const selectCategories_item = ref([]);
const selectURLId = ref(route.params.id);
const saveCategory_item = ref([]);
const savePrice = ref([]);

// Fetch data
const fetchAllCategories = async () => {
    try {
        const res = await axios.get('http://127.0.0.1:8888/api/category')
        categories.value = res.data.data;
        const findcategory_item = categories.value.find(item => item.id == selectURLId.value);
        if (findcategory_item) {
            saveCategory_item.value = findcategory_item;
        }
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
}

const fetchCategoryItem = async () => {
    try {
        const res = await axios.get(`http://127.0.0.1:8888/api/category-item?category_id=${selectURLId.value}`)
        categories_item.value = res.data.data;
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
}

const fetchProducts = async () => {
    try {
        const res = await axios.get('http://127.0.0.1:8888/api/product?category_id=' + selectURLId.value)
        products.value = res.data.data;
    } catch (error) {
        console.error('Error fetching products:', error);
    }
}

const filterProducts = computed(() => {
    let result = products.value;
    if (selectCategories_item.value.length > 0) {
        result = result.filter(product => selectCategories_item.value.includes(product.category_item_id))
    }
    return result;
})



const formatVND = (val) => Number(val).toLocaleString('vi-VN') + ' ₫';

const getMinPrice = (price) => {
    if (!price.variants?.length) return 0;
    const prices = price.variants.map(v => Number(v.sale_price || v.price));
    return Math.min(...prices);
};

const priceRanges = ref([
    { label: 'Dưới 500.000đ', min: 0, max: 500000 },
    { label: '500.000đ - 1.000.000đ', min: 500000, max: 1000000 },
    { label: 'Trên 1.000.000đ', min: 1000000, max: 99999999 }
]);

onMounted(() => {
    fetchAllCategories();
    fetchCategoryItem();
    fetchProducts();
});
</script>

<template>
    <div class="min-h-screen bg-white flex flex-col font-sans text-gray-900">
        <Header_client />

        <!-- Hero Section for Category -->
        <div class="bg-gray-50 border-b border-gray-100 py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div
                    class="flex items-center justify-center gap-2 text-[10px] uppercase tracking-[0.4em] text-gray-400 mb-6 font-sans font-medium">
                    <router-link to="/product" class="hover:text-pink-600 transition-colors cursor-pointer">Cửa
                        hàng</router-link>
                    <i class="fa-solid fa-chevron-right text-[7px]"></i>
                    <span class="text-gray-900">Danh mục</span>
                </div>
                <h1 class="text-5xl md:text-7xl font-serif font-bold text-gray-900 mb-6 italic leading-tight">{{
                    saveCategory_item.name }}</h1>
                <p class="text-lg md:text-xl font-light text-gray-600 max-w-2xl mx-auto leading-relaxed">Những đóa hoa
                    tinh khôi trong bộ sưu tập {{ saveCategory_item.name }}, mang đến vẻ đẹp thuần khiết và thăng hoa
                    cho
                    không gian của bạn.</p>
            </div>
        </div>

        <main
            class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full flex flex-col md:flex-row gap-16 font-sans text-gray-900">

            <!-- SIDEBAR -->
            <aside class="w-full md:w-75 shrink-0 transition-all shadow-lg">
                <div class="sticky top-5 space-y-7 p-10">

                    <div class="pb-6 border-b border-gray-100">
                        <h2 class="text-2xl font-serif font-bold text-gray-900 mb-2">Bộ Lọc</h2>
                        <p class="text-xs text-gray-400 font-light italic">Tinh chỉnh lựa chọn của bạn</p>
                    </div>

                    <!-- Lọc theo Danh mục con (Chọn được nhiều) -->
                    <div v-if="categories_item.length > 0">
                        <h3 class="text-[10px] font-bold text-gray-400 mb-6 uppercase tracking-[0.3em] font-sans">Chọn
                            loại hoa:</h3>
                        <div class="space-y-4 font-sans uppercase text-gray-500">
                            <label v-for="item in categories_item" :key="item.id"
                                class="flex items-center group cursor-pointer">
                                <div class="relative flex items-center justify-center">
                                    <input type="checkbox" :value="item.id" v-model="categories_item"
                                        class="appearance-none w-5 h-5 border border-gray-300 rounded-none checked:bg-pink-600 checked:border-pink-600 transition-all cursor-pointer">
                                    <i class="fa-solid fa-check absolute text-white text-[10px]"></i>
                                </div>
                                <span class="ml-3 text-[12px] transition-colors uppercase tracking-[0.15em]">
                                    {{ item.category_item.name }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Lọc theo Giá -->
                    <div>
                        <h3 class="text-[10px] font-bold text-gray-400 mb-6 uppercase tracking-[0.3em] font-sans">Khoảng
                            giá:</h3>
                        <div class="space-y-6 font-sans">
                            <label v-for="(priceRange, index) in priceRanges" :key="index"
                                class="flex items-center group cursor-pointer text-gray-500 hover:text-gray-900 relative pl-9">
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 flex items-center justify-center">
                                    <input type="checkbox"
                                        class="appearance-none w-5 h-5 border border-gray-300 rounded-none checked:bg-pink-600 checked:border-pink-600 transition-all cursor-pointer">
                                    <i class="fa-solid fa-check absolute text-white text-[10px]"></i>
                                </div>
                                <span class="text-[12px] font-medium uppercase tracking-widest">{{ priceRange.label
                                    }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Danh mục khác -->
                    <div>
                        <h3
                            class="text-[10px] font-bold text-gray-300 mb-6 uppercase tracking-[0.2em] font-sans font-medium">
                            Các chủ đề khác</h3>
                        <div class="flex flex-wrap gap-2">
                            <button v-for="item in categories" :key="item.id"
                                class="text-[11px] uppercase tracking-wider px-3 py-1 border border-gray-100 text-gray-400 hover:border-pink-200 hover:text-pink-600 transition-all rounded-none cursor-pointer font-sans">
                                {{ item.name }}
                            </button>
                        </div>
                    </div>

                </div>
            </aside>

            <!-- DANH SÁCH -->
            <section class="flex-1">
                <div class="flex justify-between items-end mb-12 pb-6 border-b border-gray-50 font-sans text-xs">
                    <span class="text-gray-400 font-light italic">
                        Sản phẩm / {{ saveCategory_item.name }} ({{ products.length }} kết quả)
                    </span>
                </div>

                <div v-if="products.length > 0"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-10 gap-y-16">
                    <div v-for="product in products" :key="product.id" class="group cursor-pointer">
                        <div class="relative h-80 mb-4 overflow-hidden bg-gray-100">
                            <img :src="'http://127.0.0.1:8888/images/' + product.thumbnail" :alt="product.name"
                                class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-700 ease-in-out">
                            <div
                                class="absolute bottom-4 left-0 right-0 flex justify-center opacity-0 group-hover:opacity-100 transition duration-300">
                                <button
                                    class="bg-white px-6 py-3 text-sm font-medium shadow-lg hover:bg-pink-600 hover:text-white transition w-10/12 uppercase tracking-widest font-bold">
                                    Thêm Vào Giỏ
                                </button>
                            </div>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase tracking-wider mb-1 block">{{ product.name
                                }}</span>
                            <h3 class="text-base font-medium text-gray-900">{{ product.name }}</h3>
                            <p class="text-sm font-bold text-gray-900">{{ product.price }}</p>
                        </div>
                    </div>
                </div>

                <div v-else class="bg-gray-50 p-24 flex flex-col items-center justify-center text-center">
                    <i class="fa-solid fa-box-open text-gray-200 text-6xl mb-10 font-light"></i>
                    <h3 class="text-3xl font-serif text-gray-900 mb-4 italic text-center">Chưa tìm thấy hoa mẫu theo yêu
                        cầu lọc</h3>
                    <button @click="selectedPriceRanges = []"
                        class="mt-4 text-xs font-bold border-b border-black pb-1 hover:text-pink-600 hover:border-pink-600 transition uppercase tracking-widest font-sans cursor-pointer">Xóa
                        bộ lọc giá</button>
                </div>

            </section>
        </main>

        <Footer_client />
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;600;700&display=swap');

.font-serif {
    font-family: 'Playfair Display', serif;
}

.font-sans {
    font-family: 'Inter', sans-serif;
}
</style>
