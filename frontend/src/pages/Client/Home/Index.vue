<script setup>
import Footer_client from '@/pages/Includes/Layouts/Footer_client.vue';
import Header_client from '@/pages/Includes/Layouts/Header_client.vue';
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import { setPageSeo } from '@/utils/seo';
import { apiUrl, imageUrl, videoUrl } from '@/utils/api';
import QuickAddCartModal from '@/components/QuickAddCartModal.vue';
import CouponDetailModal from '@/components/CouponDetailModal.vue';
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
const couponCopyMessage = ref('');
const couponCopyErrorCode = ref(null);
const showCouponModal = ref(false);
const selectedCoupon = ref(null);

const openCouponModal = (coupon) => {
  selectedCoupon.value = coupon;
  showCouponModal.value = true;
};

const closeCouponModal = () => {
  showCouponModal.value = false;
  selectedCoupon.value = null;
};

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

const formatPlainVND = (value) => {
  return `${new Intl.NumberFormat('vi-VN').format(Number(value) || 0)} đ`;
};

const getCouponDiscountLabel = (coupon) => {
  if (coupon.discount_type === 'percentage') {
    return `GIẢM ${parseFloat(coupon.discount_value)}%`;
  }

  return `GIẢM ${formatPlainVND(coupon.discount_value)}`;
};

const hasCouponMaxDiscount = (coupon) => {
  return coupon.discount_type === 'percentage' && Number(coupon.max_discount_amount) > 0;
};

const parseCouponDate = (dateStr) => {
  if (!dateStr) return null;
  const date = new Date(dateStr);
  return Number.isNaN(date.getTime()) ? null : date;
};

const isCouponExpired = (coupon) => {
  const expiry = parseCouponDate(coupon.expires_at);
  return !!expiry && expiry < new Date();
};

const isCouponUnavailable = (coupon) => {
  const hasUsageLimit = coupon.usage_limit !== null && coupon.usage_limit !== undefined;
  const usageLimitReached = hasUsageLimit && Number(coupon.used_count || 0) >= Number(coupon.usage_limit);

  return coupon.is_active === false || usageLimitReached;
};

const isCouponNearExpiry = (coupon) => {
  const expiry = parseCouponDate(coupon.expires_at);
  if (!expiry || isCouponExpired(coupon)) return false;

  const now = new Date();
  const sevenDays = 7 * 24 * 60 * 60 * 1000;
  return expiry.getTime() - now.getTime() <= sevenDays;
};

const getCouponStatusLabel = (coupon) => {
  if (isCouponExpired(coupon)) return 'Đã hết hạn';
  if (isCouponUnavailable(coupon)) return 'Tạm hết lượt dùng';
  if (isCouponNearExpiry(coupon)) return 'Sắp hết hạn';

  return 'Sẵn sàng sử dụng';
};

const canCopyCoupon = (coupon) => {
  return !isCouponExpired(coupon) && !isCouponUnavailable(coupon);
};

const copyCouponCode = async (code) => {
  if (!code) return;

  couponCopyMessage.value = '';
  couponCopyErrorCode.value = null;

  try {
    if (!navigator.clipboard?.writeText) {
      throw new Error('Clipboard API is not available');
    }

    await navigator.clipboard.writeText(code);
    copiedCode.value = code;
    couponCopyMessage.value = `Đã sao chép mã ${code}`;

    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Đã sao chép mã',
      text: code,
      showConfirmButton: false,
      timer: 2000
    });

    setTimeout(() => {
      if (copiedCode.value === code) copiedCode.value = null;
    }, 3000);
  } catch {
    couponCopyErrorCode.value = code;
    couponCopyMessage.value = `Không thể sao chép mã ${code}. Vui lòng thử lại.`;

    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Không thể sao chép mã',
      text: 'Vui lòng thử lại hoặc sao chép thủ công.',
      showConfirmButton: false,
      timer: 2500
    });
  }
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
    <section
      v-if="coupons.length > 0"
      aria-labelledby="voucher-section-title"
      class="border-y border-[#eaded2] bg-[#fffaf6] py-10 sm:py-12"
    >
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <div class="mb-1.5 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.24em] text-pink-700">
              <span aria-hidden="true" class="h-px w-6 bg-pink-300"></span>
              <span>ƯU ĐÃI ĐẶC BIỆT</span>
            </div>
            <h2 id="voucher-section-title" class="font-serif text-2xl font-bold italic leading-tight text-[#17251d] sm:text-3xl lg:text-4xl">
              Mã Giảm Giá Dành Cho Bạn
            </h2>
            <p class="mt-1 text-xs text-[#5f6b62] sm:text-sm">
              Lưu mã nhận ưu đãi khi đặt hoa. Click vào thẻ để xem chi tiết điều kiện.
            </p>
          </div>

          <div class="inline-flex w-fit items-center gap-2 border border-pink-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-[#17251d] shadow-xs">
            <i aria-hidden="true" class="fa-solid fa-ticket text-pink-600 text-xs"></i>
            <span>{{ coupons.length }} mã ưu đãi sẵn sàng</span>
          </div>
        </div>

        <p class="sr-only" aria-live="polite">{{ couponCopyMessage }}</p>

        <!-- Compact Coupons Grid -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
          <article
            v-for="coupon in coupons"
            :key="coupon.id"
            @click="openCouponModal(coupon)"
            :class="[
              'group relative flex flex-row border bg-white shadow-xs transition-all duration-300 hover:border-pink-300 hover:shadow-md cursor-pointer overflow-hidden',
              canCopyCoupon(coupon) ? 'border-[#eaded2]' : 'border-gray-200 opacity-80'
            ]"
          >
            <!-- Left Stub (Discount highlight) -->
            <div class="w-28 sm:w-32 bg-[#fff8f5] border-r border-dashed border-[#eaded2] p-3 flex flex-col justify-center items-center text-center shrink-0">
              <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#7b8a80]">Ưu đãi</span>
              <span class="text-lg sm:text-xl font-black text-pink-700 tracking-tight my-1 leading-tight break-words">
                {{ getCouponDiscountLabel(coupon) }}
              </span>
              <span v-if="hasCouponMaxDiscount(coupon)" class="text-[9px] text-[#5f6b62] leading-tight line-clamp-1">
                Tối đa {{ formatPlainVND(coupon.max_discount_amount) }}
              </span>
              <span
                :class="[
                  'mt-1.5 inline-block px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider border',
                  isCouponExpired(coupon) || isCouponUnavailable(coupon)
                    ? 'bg-gray-100 text-gray-500 border-gray-200'
                    : isCouponNearExpiry(coupon)
                      ? 'bg-amber-50 text-amber-700 border-amber-200'
                      : 'bg-emerald-50 text-emerald-700 border-emerald-200'
                ]"
              >
                {{ getCouponStatusLabel(coupon) }}
              </span>
            </div>

            <!-- Right Stub (Information & Action) -->
            <div class="flex-1 p-3.5 flex flex-col justify-between min-w-0 bg-white">
              <div>
                <div class="flex items-start justify-between gap-1.5">
                  <h3 class="font-serif text-sm font-bold text-[#17251d] line-clamp-1 transition group-hover:text-pink-700">
                    {{ coupon.name || 'Mã giảm giá TBS Flora' }}
                  </h3>
                  <span class="shrink-0 text-[10px] font-semibold text-pink-700 group-hover:underline">
                    Chi tiết <i class="fa-solid fa-chevron-right text-[8px] ml-0.5"></i>
                  </span>
                </div>

                <p class="mt-1 text-xs text-[#5f6b62] line-clamp-1">
                  {{ coupon.description || (coupon.min_order_amount ? `Đơn từ ${formatPlainVND(coupon.min_order_amount)}` : 'Áp dụng mọi đơn hàng') }}
                </p>

                <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px] text-[#7b8a80]">
                  <span>
                    <i class="fa-solid fa-bag-shopping mr-1 text-pink-600 text-[10px]"></i>
                    {{ coupon.min_order_amount ? `Đơn từ ${formatPlainVND(coupon.min_order_amount)}` : 'Mọi đơn' }}
                  </span>
                  <span>•</span>
                  <span>
                    <i class="fa-regular fa-clock mr-1 text-pink-600 text-[10px]"></i>
                    {{ formatCouponDate(coupon.expires_at) }}
                  </span>
                </div>
              </div>

              <!-- Bottom Bar: Code + Action button -->
              <div class="mt-2.5 pt-2 border-t border-dashed border-[#eaded2] flex items-center justify-between gap-2">
                <div class="min-w-0">
                  <span class="inline-block font-mono text-xs font-black tracking-wider border border-dashed border-pink-300 bg-pink-50 px-2 py-0.5 text-pink-800 select-all">
                    {{ coupon.code }}
                  </span>
                </div>

                <button
                  @click.stop="copyCouponCode(coupon.code)"
                  type="button"
                  :disabled="!canCopyCoupon(coupon)"
                  :aria-label="`Lấy mã ${coupon.code}`"
                  :class="!canCopyCoupon(coupon)
                    ? 'cursor-not-allowed bg-gray-200 text-gray-500 border border-gray-300'
                    : copiedCode === coupon.code
                      ? 'bg-emerald-700 text-white'
                      : 'bg-pink-700 text-white hover:bg-pink-800'"
                  class="inline-flex min-h-7 items-center justify-center gap-1.5 px-3 py-1 text-[11px] font-black uppercase tracking-wider transition shrink-0"
                >
                  <i :class="copiedCode === coupon.code ? 'fa-solid fa-check' : 'fa-regular fa-copy'" class="text-[10px]"></i>
                  <span>{{ copiedCode === coupon.code ? 'ĐÃ LƯU' : 'LẤY MÃ' }}</span>
                </button>
              </div>
            </div>
          </article>
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
          <router-link :to="'/product/' + product.slug" class="relative h-96 mb-4 overflow-hidden bg-gray-100 block shadow-sm group-hover:shadow-xl transition-shadow duration-500">
            <span v-if="isNewProduct(product)"
              class="absolute top-4 right-4 z-10 inline-flex items-center justify-center border border-white bg-pink-600 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-md">
              New
            </span>
            <span v-if="getDiscountPercent(getBestVariant(product))"
              class="absolute left-4 top-4 z-10 bg-emerald-600 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-md">
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

    <CouponDetailModal
      :show="showCouponModal"
      :coupon="selectedCoupon"
      @close="closeCouponModal"
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
