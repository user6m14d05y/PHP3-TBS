<script setup>
import Footer_client from '@/pages/Includes/Layouts/Footer_client.vue';
import Header_client from '@/pages/Includes/Layouts/Header_client.vue';
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { setPageSeo } from '@/utils/seo';
import { apiUrl, imageUrl, videoUrl } from '@/utils/api';

const router = useRouter();

// Products & Categories data
const featuredProducts = ref([]);
const categories = ref([]);

const fetchProducts = () => {
  axios.get(apiUrl('/api/product?limit=4'))
  .then(response => {
    featuredProducts.value = response.data.data;
  })
  .catch(error => {
    console.error('Error fetching products:', error);
  });
}

const fetchCategories = () => {
  axios.get(apiUrl('/api/category'))
  .then(response => {
    categories.value = response.data.data;
  })
  .catch(error => {
    console.error('Error fetching categories:', error);
  });
}

const formatPrice = (price) => {
  if (!price) return 'Liên hệ';

  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(Number(price));
};

const getProductPrice = (product) => {
  const variant = product.variants?.find((item) => item.sale_price) || product.variants?.[0];

  return formatPrice(variant?.sale_price || variant?.price);
};

const getBestVariant = (product) => {
  if (!product.variants?.length) return null;

  return [...product.variants]
    .filter((variant) => Number(variant.price) > 0)
    .sort((a, b) => Number(a.sale_price || a.price) - Number(b.sale_price || b.price))[0] || null;
};

const getDiscountPercent = (variant) => {
  const price = Number(variant?.price);
  const salePrice = Number(variant?.sale_price);

  if (!price || !salePrice || salePrice >= price) return 0;

  return Math.round(((price - salePrice) / price) * 100);
};

const getCategoryName = (product) => {
  return product.categoryItem?.name || product.category_item?.name || product.category?.name || 'Sản phẩm';
};

const isNewProduct = (product) => {
  if (!product.created_at) return false;

  const createdAt = new Date(product.created_at);
  const sevenDaysAgo = new Date();
  sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);

  return createdAt >= sevenDaysAgo;
};

const selectCategory = (categoryId) => {
  router.push({ path: '/product', query: { category: categoryId } });
};

setPageSeo({
  title: 'TBS Flower Shop | Hoa tươi thiết kế, giao nhanh trong ngày',
  description: 'TBS Flower Shop cung cấp hoa tươi thiết kế theo dịp, giao nhanh trong ngày, tối ưu cho quà tặng, khai trương và sự kiện.',
  path: '/',
  image: '/favicon.ico',
});

onMounted(() => {
  fetchProducts();
  fetchCategories();
});
</script>

<template>
  <div class="min-h-screen bg-white font-sans text-gray-900">
    <Header_client />


    <!-- Hero Banner -->
    <section class="relative flex h-screen min-h-[560px] w-full items-center justify-center overflow-hidden bg-gray-950 sm:min-h-[640px] lg:min-h-[760px]">
      <video
        class="absolute inset-0 h-full w-full object-cover"
        :src="videoUrl('video.mp4')"
        autoplay
        muted
        loop
        playsinline
        preload="metadata"
        aria-label="Hero Banner"
      ></video>
      <div class="absolute inset-0 bg-black/45 sm:bg-black/40 lg:bg-black/35"></div>

      <div class="relative z-10 mx-auto w-full max-w-3xl px-5 text-center text-white sm:px-6">
        <span class="mb-3 block text-[11px] font-semibold uppercase tracking-[0.28em] sm:mb-4 sm:text-sm sm:tracking-[0.3em]">Bộ Sưu Tập Mới Nhất</span>
        <h1 class="mb-4 font-serif text-4xl font-bold italic leading-tight text-white sm:mb-5 sm:text-6xl lg:text-7xl">Mùa Yêu Thương</h1>
        <p class="mx-auto mb-7 max-w-xl text-sm font-light leading-relaxed text-gray-100 sm:mb-9 sm:max-w-2xl sm:text-lg lg:text-xl">
          Khám phá những thiết kế hoa tươi tinh tế, được tuyển chọn theo mùa và gửi gắm trọn vẹn cảm xúc trong từng bó hoa.
        </p>
        <router-link replace to="/product"
          class="inline-flex min-h-11 items-center justify-center bg-white px-6 py-3.5 text-xs font-bold uppercase tracking-widest text-black shadow-lg transition hover:bg-pink-600 hover:text-white sm:min-h-12 sm:px-10 sm:py-4 sm:text-sm">
          MUA SẮM NGAY
        </router-link>
      </div>
    </section>


    <!-- Category Product -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-b border-pink-50">
      <div class="text-center mb-12">
        <span class="text-xs uppercase tracking-[0.3em] text-pink-600 font-medium mb-3 block">Danh Mục Sản Phẩm</span>
        <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 italic">Khám Phá Các Bộ Sưu Tập Hoa</h2>
        <div class="w-12 h-0.5 bg-pink-300 mx-auto mt-4"></div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-5 gap-8 justify-center">
        <div 
          v-for="cat in categories" 
          :key="cat.id" 
          @click="selectCategory(cat.id)"
          class="group cursor-pointer flex flex-col items-center text-center transition-all duration-300"
        >
          <!-- Circular (Bo tròn) Image Container -->
          <div class="w-32 h-32 md:w-40 md:h-40 rounded-full overflow-hidden border border-pink-100/50 shadow-md group-hover:shadow-xl group-hover:shadow-pink-100 group-hover:border-pink-300 transition-all duration-500 relative flex items-center justify-center bg-pink-50/20 mb-4">
            <!-- Smooth Zoom on Hover -->
            <img 
              :src="imageUrl(cat.img)" 
              :alt="cat.name"
              class="w-full h-full object-cover group-hover:scale-110 transition duration-700 ease-in-out"
              @error="(e) => e.target.src = 'https://images.unsplash.com/photo-1526047932273-341f2a7631f9?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'"
            >
            <!-- Overlay shade -->
            <div class="absolute inset-0 bg-pink-900/0 group-hover:bg-pink-900/5 transition duration-500 rounded-full"></div>
          </div>
          
          <!-- Category Title -->
          <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-800 group-hover:text-pink-600 transition-colors duration-300">
            {{ cat.name }}
          </h3>
        </div>
      </div>
    </div>

    <!-- Featured Products -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div class="flex justify-between items-end mb-10">
        <div>
          <h2 class="text-3xl font-serif font-bold text-gray-900 mb-2">Hàng Mới Về</h2>
          <p class="text-gray-500 font-light">Những mẫu hoa tươi được yêu thích nhất trong tuần này.</p>
        </div>
        <RouterLink to="/product"
          class="hidden sm:block text-sm font-medium text-black border-b border-black pb-1 hover:text-gray-600 hover:border-gray-600 transition">
          Xem tất cả
        </RouterLink>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <div v-for="product in featuredProducts" :key="product.id" class="group cursor-pointer flex flex-col">
          <router-link :to="'/product/' + product.slug" class="relative h-96 mb-4 overflow-hidden bg-gray-100 block">
            <span v-if="isNewProduct(product)"
              class="absolute top-4 right-4 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full border-2 border-white bg-pink-600 text-white text-[10px] font-bold uppercase tracking-wider shadow-lg shadow-pink-200">
              New
            </span>
            <span v-if="getDiscountPercent(getBestVariant(product))"
              class="absolute left-4 top-4 z-10 rounded-full bg-emerald-600 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white shadow-lg">
              -{{ getDiscountPercent(getBestVariant(product)) }}%
            </span>
            <img :src="imageUrl(product.thumbnail)" :alt="product.image_alt || product.name"
              class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-700 ease-in-out"
              loading="lazy"
              decoding="async">
            <div
              class="absolute bottom-4 left-0 right-0 flex justify-center opacity-0 group-hover:opacity-100 transition duration-300 z-20">
              <button @click.stop.prevent
                class="bg-white px-6 py-3 text-sm font-medium shadow-lg hover:bg-pink-600 hover:text-white transition w-10/12 uppercase tracking-widest font-bold">
                Thêm Vào Giỏ
              </button>
            </div>
          </router-link>
          <div>
            <span class="text-xs text-gray-500 uppercase tracking-wider mb-1 block">{{ getCategoryName(product) }}</span>
            <router-link :to="'/product/' + product.slug">
              <h3 class="text-base font-medium text-gray-900 group-hover:text-pink-600 transition-colors duration-300">{{ product.name }}</h3>
            </router-link>
            <div class="mt-1 flex flex-wrap items-center gap-2">
              <p class="text-sm text-pink-600 font-semibold">{{ getProductPrice(product) }}</p>
              <span v-if="getDiscountPercent(getBestVariant(product))" class="text-xs font-semibold text-emerald-600">
                Giảm {{ getDiscountPercent(getBestVariant(product)) }}%
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Banner Split -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-w-[1400px] mx-auto px-2 mb-20">
      <div class="relative h-[500px] overflow-hidden group">
        <img
          src="https://images.unsplash.com/photo-1526047932273-341f2a7631f9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
          alt="Bộ sưu tập hoa tươi theo mùa" class="w-full h-full object-cover transition duration-1000 group-hover:scale-105">
        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition duration-500"></div>
        <div class="absolute inset-0 flex flex-col justify-center items-center text-white text-center p-6">
          <h3 class="text-3xl font-serif font-bold mb-4">Hoa Theo Mùa</h3>
          <button
            class="border border-white px-8 py-3 text-sm font-medium hover:bg-white hover:text-black transition duration-300">Khám
            Phá</button>
        </div>
      </div>
      <div
        class="relative h-[500px] overflow-hidden group bg-gray-100 flex flex-col justify-center items-center text-center p-12">
        <span class="text-gray-400 text-sm uppercase tracking-[0.2em] mb-4">Cam Kết Chất Lượng</span>
        <h3 class="text-3xl font-serif font-bold text-gray-900 mb-6">Hoa Tươi Mỗi Ngày</h3>
        <p class="text-gray-600 font-light mb-8 max-w-md leading-relaxed">Chúng tôi tuyển chọn hoa tươi theo ngày, thiết kế chỉn chu và chụp ảnh xác nhận trước khi giao để mỗi món quà luôn giữ được sự tinh tế.</p>
        <button
          class="text-sm font-medium text-black border-b border-black pb-1 hover:text-gray-500 hover:border-gray-500 transition">Tìm
          Hiểu Thêm</button>
      </div>
    </div>

    <Footer_client />
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
