<script setup>
import Footer_client from '@/pages/Includes/Layouts/Footer_client.vue';
import Header_client from '@/pages/Includes/Layouts/Header_client.vue';
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

// Dummy data for products
const featuredProducts = ref([]);

const fetchProducts = () => {
  axios.get('http://localhost:8888/api/product?limit=4')
  .then(response => {
    featuredProducts.value = response.data.data;
  })
  .catch(error => {
    console.error('Error fetching products:', error);
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

const getCategoryName = (product) => {
  return product.category_item?.name || product.category?.name || 'Sản phẩm';
};

const isNewProduct = (product) => {
  if (!product.created_at) return false;

  const createdAt = new Date(product.created_at);
  const sevenDaysAgo = new Date();
  sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);

  return createdAt >= sevenDaysAgo;
};

onMounted(() => {
  fetchProducts();
});
</script>

<template>
  <div class="min-h-screen bg-white font-sans text-gray-900">
    <Header_client />


    <!-- Hero Banner -->
    <div class="relative w-full h-[650px] bg-gray-100 flex items-center justify-center overflow-hidden">
      <img
        src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80"
        alt="Hero Banner" class="absolute inset-0 w-full h-full object-cover">
      <div class="absolute inset-0 bg-black/30"></div>

      <div class="relative z-10 text-center text-white px-4 max-w-3xl">
        <span class="text-sm uppercase tracking-[0.3em] mb-4 block font-medium">Bộ Sưu Tập Mới Nhất</span>
        <h1 class="text-5xl md:text-7xl font-serif font-bold text-white-900 mb-6 italic leading-tight">Mùa Yêu Thương
        </h1>
        <p class="text-lg md:text-xl font-light mb-10 text-gray-100">Khám phá phong cách tối giản mang đậm chất riêng,
          tôn vinh vẻ đẹp thuần khiết và thanh lịch từ bên trong bạn.</p>
        <router-link replace to="/product"
          class="cursor-pointer bg-white px-6 py-5 text-sm font-medium text-black shadow-lg hover:bg-pink-600 hover:text-white transition w-10/12 uppercase tracking-widest font-bold">
          MUA SẮM NGAY
        </router-link>
      </div>
    </div>

    <!-- Featured Products -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div class="flex justify-between items-end mb-10">
        <div>
          <h2 class="text-3xl font-serif font-bold text-gray-900 mb-2">Hàng Mới Về</h2>
          <p class="text-gray-500 font-light">Những xu hướng thời trang nổi bật nhất tuần này.</p>
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
            <img :src="'http://localhost:8888/images/' + product.thumbnail" :alt="product.name"
              class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-700 ease-in-out">
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
            <p class="mt-1 text-sm text-pink-600 font-semibold">{{ getProductPrice(product) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Banner Split -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-w-[1400px] mx-auto px-2 mb-20">
      <div class="relative h-[500px] overflow-hidden group">
        <img
          src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
          alt="Style Edit" class="w-full h-full object-cover transition duration-1000 group-hover:scale-105">
        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition duration-500"></div>
        <div class="absolute inset-0 flex flex-col justify-center items-center text-white text-center p-6">
          <h3 class="text-3xl font-serif font-bold mb-4">The Style Edit</h3>
          <button
            class="border border-white px-8 py-3 text-sm font-medium hover:bg-white hover:text-black transition duration-300">Khám
            Phá</button>
        </div>
      </div>
      <div
        class="relative h-[500px] overflow-hidden group bg-gray-100 flex flex-col justify-center items-center text-center p-12">
        <span class="text-gray-400 text-sm uppercase tracking-[0.2em] mb-4">Cam Kết Chất Lượng</span>
        <h3 class="text-3xl font-serif font-bold text-gray-900 mb-6">Chất Liệu Bền Vững</h3>
        <p class="text-gray-600 font-light mb-8 max-w-md leading-relaxed">Chúng tôi sử dụng sợi tái chế và vải hữu cơ
          thiên nhiên, góp phần bảo vệ môi trường mà vẫn giữ được sự tinh tế, sang trọng cho người mặc.</p>
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