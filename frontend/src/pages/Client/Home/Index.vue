<script setup>
import Footer_client from '@/pages/Includes/Layouts/Footer_client.vue';
import Header_client from '@/pages/Includes/Layouts/Header_client.vue';
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import { setPageSeo } from '@/utils/seo';
import { apiUrl, imageUrl, videoUrl } from '@/utils/api';
import QuickAddCartModal from '@/components/QuickAddCartModal.vue';
import { useCartStore } from '@/stores/cart';
import { useAuthStore } from '@/stores/auth';
import Swal from 'sweetalert2';

const router = useRouter();
const route = useRoute();
const cartStore = useCartStore();
const authStore = useAuthStore();

const showAddModal = ref(false);
const selectedProduct = ref(null);
const isQuickAdding = ref(false);

const handleQuickAdd = async (product) => {
  if (isQuickAdding.value) return;
  if (product.variants && product.variants.length > 1) {
    selectedProduct.value = product;
    showAddModal.value = true;
  } else {
    const variant = product.variants?.[0];
    if (!variant || variant.stock === 0) {
       Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Sản phẩm đã hết hàng', showConfirmButton: false, timer: 3000 });
       return;
    }
    
    isQuickAdding.value = true;
    try {
      await cartStore.addToCart(variant.id, 1);
      Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Đã thêm vào giỏ hàng!', showConfirmButton: false, timer: 3000 });
    } catch (error) {
      if (error.response?.status === 401) {
        authStore.logout();
        router.push({ name: 'login', query: { redirect: route.fullPath } });
        return;
      }
      Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Thêm vào giỏ hàng thất bại', showConfirmButton: false, timer: 3000 });
    } finally {
      isQuickAdding.value = false;
    }
  }
};

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

// Coupons data & actions
const coupons = ref([]);
const isLoadingCoupons = ref(false);
const copiedCode = ref(null);

const fetchCoupons = () => {
  isLoadingCoupons.value = true;
  axios.get(apiUrl('/api/coupons/public'))
  .then(response => {
    if (response.data.status === 'success') {
      coupons.value = response.data.data || [];
    }
  })
  .catch(error => {
    console.error('Error fetching coupons:', error);
  })
  .finally(() => {
    isLoadingCoupons.value = false;
  });
};

const formatCouponDate = (dateStr) => {
  if (!dateStr) return 'Vô thời hạn';
  const d = new Date(dateStr);
  return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')}/${d.getFullYear()}`;
};

const copyCouponCode = (code) => {
  navigator.clipboard.writeText(code);
  copiedCode.value = code;
  Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: `Đã sao chép mã: ${code}`,
    showConfirmButton: false,
    timer: 2000
  });
  setTimeout(() => {
    if (copiedCode.value === code) copiedCode.value = null;
  }, 3000);
};

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
  fetchCoupons();
});
</script>

<template>
  <div class="min-h-screen bg-white font-sans text-gray-900">
    <Header_client />


    <!-- Hero Banner -->
    <section class="relative flex h-screen min-h-[560px] w-full items-center justify-center overflow-hidden bg-gray-950 sm:min-h-[640px] lg:min-h-[760px]">
      <video
        class="absolute inset-0 h-full w-full object-cover"
        autoplay
        muted
        loop
        playsinline
        :poster="imageUrl('video-fallback.jpg')"
        aria-label="Hero Banner"
      >
        <source :src="videoUrl('video.webm')" type="video/webm">
        <source :src="videoUrl('video.mp4')" type="video/mp4">
      </video>
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

      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-8 justify-center">
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

    <!-- Voucher / Coupon Promotion Section -->
    <section v-if="coupons.length > 0" class="bg-gradient-to-b from-pink-50/40 via-white to-white py-14 border-b border-pink-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-8 gap-4">
          <div>
            <div class="flex items-center space-x-2 text-pink-600 font-semibold text-xs uppercase tracking-[0.25em] mb-2">
              <i class="fa-solid fa-gift text-sm"></i>
              <span>Ưu Đãi Đặc Biệt</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 italic">Mã Giảm Giá Dành Cho Bạn</h2>
            <p class="text-gray-500 font-light text-sm mt-1">Lưu ngay voucher để nhận ưu đãi giảm giá tốt nhất khi đặt hoa.</p>
          </div>
          <span class="text-xs bg-pink-100/70 text-pink-700 px-3.5 py-1.5 rounded-full font-semibold border border-pink-200/60 shadow-xs flex items-center">
            <i class="fa-solid fa-ticket mr-1.5 text-pink-500"></i> {{ coupons.length }} mã ưu đãi sẵn sàng
          </span>
        </div>

        <!-- Coupons Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="coupon in coupons"
            :key="coupon.id"
            class="relative flex bg-white border border-pink-200/80 rounded-2xl shadow-sm hover:shadow-xl hover:border-pink-300 transition-all duration-300 overflow-hidden group hover:-translate-y-1"
          >
            <!-- Left Ticket Accent Strip -->
            <div class="w-3 bg-gradient-to-b from-pink-500 to-rose-400 shrink-0"></div>

            <!-- Main Coupon Content -->
            <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
              <div>
                <div class="flex items-center justify-between gap-2 mb-2">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-pink-50 text-pink-600 border border-pink-100">
                    {{ coupon.discount_type === 'percentage' ? `Giảm ${parseFloat(coupon.discount_value)}%` : `Giảm ${formatPrice(coupon.discount_value)}` }}
                  </span>
                  <span v-if="coupon.discount_type === 'percentage' && coupon.max_discount_amount" class="text-[11px] text-gray-400">
                    Tối đa {{ formatPrice(coupon.max_discount_amount) }}
                  </span>
                </div>

                <h3 class="font-bold text-gray-900 text-base group-hover:text-pink-600 transition-colors line-clamp-1">
                  {{ coupon.name || 'Mã giảm giá hấp dẫn' }}
                </h3>

                <p class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">
                  {{ coupon.description || (coupon.min_order_amount ? `Áp dụng cho đơn hàng từ ${formatPrice(coupon.min_order_amount)}.` : 'Áp dụng cho mọi đơn hàng.') }}
                </p>
              </div>

              <!-- Extra Conditions & Expiry -->
              <div class="mt-4 pt-3 border-t border-dashed border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                <span v-if="coupon.min_order_amount">
                  Đơn từ: <strong class="text-gray-700 font-semibold">{{ formatPrice(coupon.min_order_amount) }}</strong>
                </span>
                <span v-else class="text-emerald-600 font-medium">Mọi giá trị đơn</span>

                <span>
                  <i class="fa-regular fa-clock mr-1"></i>HSD: {{ formatCouponDate(coupon.expires_at) }}
                </span>
              </div>
            </div>

            <!-- Dashed Divider with Ticket Cutout Notches -->
            <div class="relative w-px bg-gray-200 flex flex-col justify-between items-center my-2">
              <div class="w-3 h-3 bg-white rounded-full -mt-3.5 -ml-1 border border-pink-200"></div>
              <div class="w-3 h-3 bg-white rounded-full -mb-3.5 -ml-1 border border-pink-200"></div>
            </div>

            <!-- Right Action Stub (Code + Copy Button) -->
            <div class="p-4 flex flex-col items-center justify-center bg-pink-50/30 w-28 shrink-0 text-center">
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Mã</span>
              <span class="font-mono font-bold text-xs text-pink-700 bg-pink-100/80 px-2 py-1 rounded border border-dashed border-pink-300 select-all mb-2.5">
                {{ coupon.code }}
              </span>

              <button
                @click="copyCouponCode(coupon.code)"
                type="button"
                :class="copiedCode === coupon.code ? 'bg-emerald-600 text-white' : 'bg-pink-600 text-white hover:bg-pink-700 shadow-sm shadow-pink-200'"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider transition-all transform active:scale-95 flex items-center justify-center space-x-1 w-full"
              >
                <i :class="copiedCode === coupon.code ? 'fa-solid fa-check' : 'fa-regular fa-copy'" class="text-xs"></i>
                <span>{{ copiedCode === coupon.code ? 'Đã lưu' : 'Lấy mã' }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

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
        <div v-for="product in featuredProducts" :key="product.id" class="group cursor-pointer flex flex-col transition-all duration-500 ease-out hover:-translate-y-2">
          <router-link :to="'/product/' + product.slug" class="relative h-96 mb-4 overflow-hidden bg-gray-100 block rounded-xl shadow-sm group-hover:shadow-xl transition-shadow duration-500">
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
              <button @click.stop.prevent="handleQuickAdd(product)"
                :disabled="isQuickAdding"
                class="bg-white px-6 py-3 text-sm font-medium shadow-lg hover:bg-pink-600 hover:text-white transition w-10/12 uppercase tracking-widest font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                {{ isQuickAdding ? 'Đang thêm...' : 'Thêm Vào Giỏ' }}
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

    <QuickAddCartModal 
      :show="showAddModal" 
      :product="selectedProduct" 
      @close="showAddModal = false" 
    />
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
