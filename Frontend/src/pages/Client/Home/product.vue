<script setup>
import Footer_client from '@/pages/Includes/Layouts/Footer_client.vue';
import Header_client from '@/pages/Includes/Layouts/Header_client.vue';
import SlidebarProduct_client from '@/pages/Includes/Layouts/SlidebarProduct_client.vue';
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const products = ref([]);
const categories = ref([]);
const selectedPriceRanges = ref([]);
const sortOption = ref('default');

const fetchCategories = async () => {
  try {
    const response = await axios.get('http://localhost:8888/api/category');
    categories.value = response.data.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const fetchProducts = async () => {
  try {
    const response = await axios.get('http://localhost:8888/api/product?limit=100');
    products.value = response.data.data;
  } catch (error) {
    console.error('Error fetching products:', error);
  }
};

onMounted(() => {
  fetchCategories();
  fetchProducts();
});

// Danh sách Mức giá
const priceRanges = ref([
  { label: 'Dưới 500.000đ', min: 0, max: 500000 },
  { label: '500.000đ - 1.000.000đ', min: 500000, max: 1000000 },
  { label: 'Trên 1.000.000đ', min: 1000000, max: 99999999 }
]);

const filteredProducts = computed(() => {
  let filtered = [...products.value];
  if (selectedPriceRanges.value.length > 0) {
    filtered = filtered.filter(p => {
      const price = getMinPrice(p);
      return selectedPriceRanges.value.some(range => price >= range.min && price <= range.max);
    });
  }
  return filtered;
});

const getMinPrice = (price) => {
  if (!price.variants?.length) return 0;
  const prices = price.variants.map(v => Number(v.sale_price || v.price));
  return Math.min(...prices);
}

const getMaxPrice = (price) => {
  if (!price.variants?.length) return 0;
  const prices = price.variants.map(v => Number(v.sale_price || v.price));
  return Math.max(...prices);
}

const formatVND = (price) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
}

const goToCategory = (id) => {
  router.push(`/product/category/${id}`);
};



</script>

<template>
  <div class="min-h-screen bg-white flex flex-col font-sans text-gray-900">
    <Header_client />

    <!-- Hero Section for Products -->
    <div class="bg-pink-100 border-b border-gray-100 py-24">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-xs uppercase tracking-[0.4em] mb-4 block font-medium text-pink-600">Tuyển tập hoa mới
          nhất</span>
        <h1 class="text-5xl md:text-7xl font-serif font-bold text-white mb-6 italic leading-tight">Cửa hàng hoa</h1>
        <p class="text-lg md:text-xl font-light text-gray-600 max-w-2xl mx-auto leading-relaxed">Khám phá phong cách tối
          giản mang đậm chất riêng, tôn vinh vẻ đẹp thuần khiết và thanh lịch từ thiên nhiên.</p>
      </div>
    </div>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full flex flex-col md:flex-row gap-16">

      <!-- SIDEBAR -->
      <aside class="w-full md:w-75 shrink-0 transition-all shadow-lg ">
        <div class="sticky top-5 space-y-7 p-10">

          <div class="pb-6 border-b border-gray-100">
            <h2 class="text-2xl font-serif font-bold text-gray-900 mb-2">Bộ Lọc</h2>
            <p class="text-xs text-gray-400 font-light italic">Lọc theo sở thích của bạn</p>
          </div>

          <!-- Lọc Danh Mục -->
          <div>
            <h3 class="text-[10px] font-bold text-gray-400 mb-6 uppercase tracking-[0.3em]">Danh Mục Hoa</h3>
            <div class="space-y-4">
              <button v-for="cat in categories" :key="cat.id" @click="goToCategory(cat.id)"
                class="flex items-center group cursor-pointer w-full text-left py-1">
                <i
                  class="fa-solid fa-chevron-right text-[8px] text-gray-200 mr-3 transition-all group-hover:text-pink-600 group-hover:pl-1"></i>
                <span
                  class="text-sm font-medium text-gray-500 group-hover:text-pink-600 transition-colors uppercase tracking-[0.1em]">{{
                  cat.name }}</span>
              </button>
            </div>
          </div>

          <!-- Mức giá -->
          <div>
            <h3 class="text-[10px] font-bold text-gray-400 mb-6 uppercase tracking-[0.3em]">Lọc Theo Giá</h3>
            <div class="space-y-6">
              <label v-for="(range, index) in priceRanges" :key="index"
                class="flex items-center group cursor-pointer relative pl-9">
                <input type="checkbox" :value="range" v-model="selectedPriceRanges"
                  class="absolute left-0 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-900 border-gray-300 rounded-none cursor-pointer focus:ring-black transition-colors">
                <span
                  class="text-sm font-medium text-gray-500 group-hover:text-pink-600 transition-colors uppercase tracking-widest text-xs">{{
                  range.label }}</span>
              </label>
            </div>
          </div>
        </div>
      </aside>

      <!-- DANH SÁCH -->
      <section class="flex-1">
        <div class="flex justify-between items-end mb-12 pb-6 border-b border-gray-50">
          <div>
            <p class="text-sm text-gray-400 font-light italic">
              Đang hiển thị <span class="font-bold text-pink-600">{{ filteredProducts.length }}</span> hoa mẫu tinh tế
            </p>
          </div>
          <div class="flex items-center gap-6">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Sắp xếp:</span>
            <select v-model="sortOption"
              class="text-[11px] font-bold border-b border-gray-200 pb-1 bg-transparent hover: outline-none cursor-pointer transition-colors uppercase tracking-widest">
              <option value="default">Mặc định</option>
              <option value="newest">Mới nhất</option>
              <option value="price-asc">Giá thấp đến cao</option>
              <option value="price-desc">Giá cao xuống thấp</option>
            </select>
          </div>
        </div>

        <div v-if="filteredProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <div v-for="product in filteredProducts" :key="product.id" class="group cursor-pointer">
              <div class="relative h-80 mb-4 overflow-hidden bg-gray-100">
                <img :src="'http://localhost:8888/images/' + product.thumbnail" :alt="product.name"
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
                <span class="text-xs text-gray-500 uppercase tracking-wider mb-1 block">{{ product.category_item.name }}</span>
                <h3 class="text-base font-medium text-gray-900">{{ product.name }}</h3>
                <p class="text-sm font-bold text-gray-900">{{ formatVND(getMinPrice(product)) }}</p>  
              </div>
            </div>
        </div>

        <div v-else class="bg-gray-50 p-24 flex flex-col items-center justify-center text-center">
          <i class="fa-solid fa-box-open text-gray-200 text-6xl mb-10 font-light"></i>
          <h3 class="text-3xl font-serif text-gray-900 mb-4 italic">Chưa tìm thấy hoa mẫu phù hợp</h3>
          <button @click="selectedPriceRanges = []"
            class="mt-4 text-xs font-bold border-b border-black pb-1 hover:text-pink-600 hover:border-pink-600 transition uppercase tracking-widest cursor-pointer">Xóa
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