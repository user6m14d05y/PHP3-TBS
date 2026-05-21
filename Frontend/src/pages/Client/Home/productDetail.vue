<script setup>
import Footer_client from '@/pages/Includes/Layouts/Footer_client.vue';
import Header_client from '@/pages/Includes/Layouts/Header_client.vue';
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

const route = useRoute();
const product = ref(null);
const loading = ref(true);
const activeImage = ref('');
const selectedVariant = ref(null);
const quantity = ref(1);
const relatedProducts = ref([]);

const expandedSections = ref({
  description: true,
  shipping: false,
  care: false
});

const toggleSection = (sec) => {
  expandedSections.value[sec] = !expandedSections.value[sec];
};

const loadProductData = async (slug) => {
  loading.value = true;
  try {
    const response = await axios.get(`http://localhost:8888/api/product/${slug}`);
    if (response.data && response.data.data) {
      product.value = response.data.data;
    } else if (response.data) {
      product.value = response.data;
    }
  } catch (error) {
    console.error('Product load failed:', error);
    product.value = null;
  } finally {
    if (product.value) {
      // Set initial gallery image
      activeImage.value = product.value.thumbnail;
      
      // Set initial selected variant
      if (product.value.variants && product.value.variants.length > 0) {
        const sorted = [...product.value.variants].sort((a, b) => {
          const pa = Number(a.sale_price || a.price);
          const pb = Number(b.sale_price || b.price);
          return pa - pb;
        });
        selectedVariant.value = sorted[0];
      } else {
        selectedVariant.value = null;
      }
      
      // Load related products
      await fetchRelatedProducts(product.value);
    }
    loading.value = false;
  }
};

const fetchRelatedProducts = async (currentProduct) => {
  try {
    const response = await axios.get('http://localhost:8888/api/product?limit=100');
    const list = response.data.data || response.data;
    relatedProducts.value = list
      .filter(p => p.category_id === currentProduct.category_id && String(p.id) !== String(currentProduct.id))
      .slice(0, 4);
  } catch (e) {
    console.error('Error fetching related products:', e);
  }
};

onMounted(() => {
  loadProductData(route.params.slug);
});

watch(() => route.params.slug, (newSlug) => {
  if (newSlug) {
    loadProductData(newSlug);
    quantity.value = 1;
  }
});

// Combine main thumbnail and gallery images
const allImages = computed(() => {
  if (!product.value) return [];
  const imgs = [];
  if (product.value.thumbnail) {
    imgs.push(product.value.thumbnail);
  }
  if (product.value.images && product.value.images.length > 0) {
    product.value.images.forEach(img => {
      if (img.image_path) imgs.push(img.image_path);
    });
  }
  return [...new Set(imgs)]; // Remove duplicates
});

const getDiscountPercent = (variant) => {
  if (!variant || !variant.sale_price) return 0;
  const disc = ((variant.price - variant.sale_price) / variant.price) * 100;
  return Math.round(disc);
};

const formatVND = (price) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const decreaseQty = () => {
  if (quantity.value > 1) quantity.value--;
};

const increaseQty = () => {
  const maxStock = selectedVariant.value ? selectedVariant.value.stock : 99;
  if (quantity.value < maxStock) {
    quantity.value++;
  } else {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'warning',
      title: 'Đã đạt giới hạn số lượng trong kho!',
      showConfirmButton: false,
      timer: 2000
    });
  }
};

const selectVariant = (variant) => {
  selectedVariant.value = variant;
  quantity.value = 1; // Reset quantity on variant switch
};

const handleAddToCart = () => {
  if (!product.value || !selectedVariant.value) return;
  Swal.fire({
    icon: 'success',
    title: 'Đã thêm vào giỏ hàng!',
    text: `Đã thêm ${quantity.value} sản phẩm "${product.value.name}" (Kích thước: ${selectedVariant.value?.size?.name || 'Tiêu chuẩn'}) vào giỏ hàng của bạn.`,
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true,
    confirmButtonColor: '#db2777'
  });
};

const getMinPrice = (p) => {
  if (!p.variants?.length) return 0;
  const prices = p.variants.map(v => Number(v.sale_price || v.price));
  return Math.min(...prices);
};
</script>

<template>
  <div class="min-h-screen bg-white flex flex-col font-sans text-gray-900">
    <Header_client />

    <!-- LOADING STATE (SHIMMER SKELETON) -->
    <div v-if="loading" class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28 w-full space-y-12">
      <!-- Breadcrumb Shimmer -->
      <div class="h-4 bg-gray-100 rounded w-1/4 animate-pulse"></div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
        <!-- Gallery Shimmer -->
        <div class="lg:col-span-6 space-y-6">
          <div class="h-[500px] bg-gray-100 rounded-2xl animate-pulse"></div>
          <div class="flex gap-4">
            <div v-for="i in 4" :key="i" class="w-20 h-20 bg-gray-100 rounded-lg animate-pulse"></div>
          </div>
        </div>

        <!-- Info Shimmer -->
        <div class="lg:col-span-6 space-y-8">
          <div class="space-y-4">
            <div class="h-4 bg-gray-100 rounded w-1/6 animate-pulse"></div>
            <div class="h-10 bg-gray-100 rounded w-3/4 animate-pulse"></div>
            <div class="h-6 bg-gray-100 rounded w-1/3 animate-pulse"></div>
          </div>
          <div class="h-16 bg-gray-100 rounded w-full animate-pulse"></div>
          <div class="space-y-2">
            <div class="h-4 bg-gray-100 rounded w-1/4 animate-pulse"></div>
            <div class="flex gap-3">
              <div v-for="i in 3" :key="i" class="w-16 h-10 bg-gray-100 rounded animate-pulse"></div>
            </div>
          </div>
          <div class="h-12 bg-gray-100 rounded w-1/2 animate-pulse"></div>
        </div>
      </div>
    </div>

    <!-- NOT FOUND STATE -->
    <div v-else-if="!product" class="flex-grow flex flex-col items-center justify-center py-32 text-center">
      <i class="fa-solid fa-face-frown text-gray-200 text-7xl mb-6"></i>
      <h2 class="text-3xl font-serif font-bold text-gray-900 mb-4">Không tìm thấy sản phẩm</h2>
      <p class="text-gray-500 mb-8 max-w-md">Sản phẩm này không tồn tại hoặc đã bị gỡ khỏi hệ thống.</p>
      <router-link to="/product" class="bg-pink-600 hover:bg-pink-700 text-white px-8 py-3 rounded-lg text-sm font-bold uppercase tracking-widest transition shadow-lg shadow-pink-100">
        Quay lại cửa hàng
      </router-link>
    </div>

    <!-- MAIN PRODUCT DETAIL SECTION -->
    <main v-else class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 w-full">
      <!-- Breadcrumbs -->
      <nav class="flex items-center gap-2.5 text-xs text-gray-400 font-medium uppercase tracking-wider mb-12">
        <router-link to="/" class="hover:text-pink-600 transition">Trang chủ</router-link>
        <i class="fa-solid fa-chevron-right text-[8px] text-gray-300"></i>
        <router-link to="/product" class="hover:text-pink-600 transition">Cửa hàng</router-link>
        <i class="fa-solid fa-chevron-right text-[8px] text-gray-300"></i>
        <span class="text-gray-500">{{ product.category?.name || 'Sản phẩm' }}</span>
        <i class="fa-solid fa-chevron-right text-[8px] text-gray-300"></i>
        <span class="text-pink-600 truncate max-w-xs">{{ product.name }}</span>
      </nav>

      <!-- Grid layout -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
        <!-- LEFT COLUMN: PHOTO GALLERY -->
        <section class="lg:col-span-6 space-y-6">
          <!-- Main Display Picture -->
          <div class="relative aspect-square overflow-hidden bg-gray-50 border border-gray-100 rounded-2xl group">
            <img :src="'http://localhost:8888/images/' + activeImage" :alt="product.name"
              class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-in-out">
            
            <!-- Sale Badge on main pic -->
            <span v-if="selectedVariant && selectedVariant.sale_price"
              class="absolute top-4 left-4 bg-pink-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-full uppercase tracking-widest shadow-lg shadow-pink-600/20">
              Giảm {{ getDiscountPercent(selectedVariant) }}%
            </span>
          </div>

          <!-- Gallery Thumbnails -->
          <div v-if="allImages.length > 1" class="flex flex-wrap gap-4">
            <button v-for="(img, idx) in allImages" :key="idx"
              @click="activeImage = img"
              class="w-20 h-20 rounded-xl overflow-hidden border-2 bg-gray-50 transition-all duration-200 cursor-pointer"
              :class="activeImage === img ? 'border-pink-600 scale-95 shadow-md shadow-pink-50' : 'border-gray-200 hover:border-pink-300'">
              <img :src="'http://localhost:8888/images/' + img" class="w-full h-full object-cover object-center" />
            </button>
          </div>
        </section>

        <!-- RIGHT COLUMN: PRODUCT INFO & VARIANT CHOICE -->
        <section class="lg:col-span-6 space-y-8">
          <!-- Title & Category Info -->
          <div>
            <span class="text-xs font-bold text-pink-600 uppercase tracking-[0.2em] block mb-3">
              {{ product.category_item?.name || product.category?.name || 'Hoa mẫu thiết kế' }}
            </span>
            <h1 class="text-4xl lg:text-5xl font-serif font-bold text-gray-900 tracking-tight leading-tight mb-4">
              {{ product.name }}
            </h1>
            
            <!-- Overall Rating placeholder (Premium element) -->
            <div class="flex items-center gap-3">
              <div class="flex text-amber-400 text-xs">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
              </div>
              <span class="text-xs text-gray-400 font-medium">5.0 (24 lượt mua hàng)</span>
            </div>
          </div>

          <!-- Variant Pricing display -->
          <div class="p-6 bg-pink-50/50 rounded-2xl border border-pink-100/30">
            <div v-if="selectedVariant" class="flex items-baseline gap-4">
              <span class="text-3xl font-bold text-pink-600">
                {{ formatVND(selectedVariant.sale_price || selectedVariant.price) }}
              </span>
              <span v-if="selectedVariant.sale_price" class="text-base text-gray-400 line-through">
                {{ formatVND(selectedVariant.price) }}
              </span>
            </div>
            <div v-else class="text-2xl font-bold text-pink-600">
              Liên hệ đặt hoa
            </div>
            <div class="mt-2 flex items-center gap-2 text-xs text-gray-400 font-medium uppercase tracking-wider">
              <i class="fa-solid fa-circle text-[6px] text-emerald-500"></i>
              <span>Giao hoa hỏa tốc trong 2 giờ nội thành</span>
            </div>
          </div>

          <!-- Size Variant pills selector -->
          <div v-if="product.variants && product.variants.length > 0">
            <div class="flex justify-between items-center mb-3">
              <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Chọn Kích Thước (Size)</span>
              <span v-if="selectedVariant" class="text-xs text-gray-400 italic">
                Còn lại trong kho: <span class="font-bold text-pink-600">{{ selectedVariant.stock }}</span> bó
              </span>
            </div>
            <div class="flex flex-wrap gap-3">
              <button v-for="variant in product.variants" :key="variant.id"
                @click="selectVariant(variant)"
                :disabled="variant.stock === 0"
                class="px-5 py-3 rounded-xl border text-xs font-bold uppercase tracking-widest transition-all duration-200 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                :class="selectedVariant?.id === variant.id
                  ? 'bg-pink-600 border-pink-600 text-white shadow-lg shadow-pink-100 scale-95'
                  : 'border-gray-200 text-gray-700 bg-white hover:border-pink-600 hover:text-pink-600'">
                {{ variant.size?.name || 'Standard' }}
              </button>
            </div>
          </div>

          <!-- Quantity selection and Cart button -->
          <div v-if="selectedVariant && selectedVariant.stock > 0" class="flex flex-col sm:flex-row items-stretch gap-4 pt-4">
            <!-- Counter block -->
            <div class="flex items-center justify-between border border-gray-200 rounded-xl px-4 py-3 shrink-0 sm:w-36 bg-white">
              <button @click="decreaseQty" class="text-gray-400 hover:text-pink-600 transition cursor-pointer text-sm py-1 px-2 focus:outline-none">
                <i class="fa-solid fa-minus"></i>
              </button>
              <span class="font-bold text-sm text-gray-900 w-8 text-center">{{ quantity }}</span>
              <button @click="increaseQty" class="text-gray-400 hover:text-pink-600 transition cursor-pointer text-sm py-1 px-2 focus:outline-none">
                <i class="fa-solid fa-plus"></i>
              </button>
            </div>

            <!-- CTA button -->
            <button @click="handleAddToCart"
              class="flex-1 bg-pink-600 hover:bg-pink-700 text-white px-8 py-4 rounded-xl text-xs font-bold uppercase tracking-[0.2em] shadow-xl shadow-pink-100 hover:scale-[1.01] active:scale-95 transition-all duration-200 cursor-pointer text-center">
              Thêm Vào Giỏ Hàng
            </button>
          </div>
          <div v-else-if="selectedVariant" class="bg-red-50 text-red-600 px-6 py-4 rounded-xl text-sm font-semibold uppercase tracking-wider text-center border border-red-100">
            Hết hàng tạm thời
          </div>

          <!-- DESCRIPTION & POLICIES ACCORDIONS -->
          <div class="border-t border-gray-100 pt-6 space-y-4">
            <!-- 1. Description accordion -->
            <div class="border-b border-gray-100 pb-4">
              <button @click="toggleSection('description')" class="w-full flex justify-between items-center py-2 text-left font-serif text-lg font-bold text-gray-900 focus:outline-none">
                <span>Mô Tả Sản Phẩm</span>
                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300" :class="{ 'rotate-180': expandedSections.description }"></i>
              </button>
              <div class="grid transition-all duration-300 ease-in-out" :style="{ gridTemplateRows: expandedSections.description ? '1fr' : '0fr' }">
                <div class="overflow-hidden">
                  <p class="text-sm text-gray-500 leading-relaxed pt-3 whitespace-pre-line font-light italic">
                    {{ product.description || 'Sản phẩm hoa mẫu cao cấp được thiết kế tỉ mỉ, sáng tạo bởi đội ngũ thợ lành nghề, mang lại vẻ đẹp thuần khiết và sự trang trọng tuyệt đối cho mọi không gian.' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- 2. Shipping policy accordion -->
            <div class="border-b border-gray-100 pb-4">
              <button @click="toggleSection('shipping')" class="w-full flex justify-between items-center py-2 text-left font-serif text-lg font-bold text-gray-900 focus:outline-none">
                <span>Giao Hoa & Đổi Trả</span>
                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300" :class="{ 'rotate-180': expandedSections.shipping }"></i>
              </button>
              <div class="grid transition-all duration-300 ease-in-out" :style="{ gridTemplateRows: expandedSections.shipping ? '1fr' : '0fr' }">
                <div class="overflow-hidden">
                  <div class="text-xs text-gray-500 space-y-2 pt-3 font-medium uppercase tracking-wider leading-relaxed">
                    <p><i class="fa-solid fa-truck text-pink-600 mr-2"></i> Miễn phí vận chuyển bán kính 5km cho đơn hàng trên 1.000.000đ.</p>
                    <p><i class="fa-solid fa-clock text-pink-600 mr-2"></i> Đảm bảo thời gian giao chính xác trong khung giờ hẹn trước.</p>
                    <p><i class="fa-solid fa-rotate-left text-pink-600 mr-2"></i> Chụp ảnh hoa thực tế gửi khách duyệt trước khi đi giao.</p>
                    <p><i class="fa-solid fa-shield text-pink-600 mr-2"></i> Hoàn trả 100% nếu hoa bị héo úa hoặc hư hỏng trong quá trình vận chuyển.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 3. Flower care instructions accordion -->
            <div class="border-b border-gray-100 pb-4">
              <button @click="toggleSection('care')" class="w-full flex justify-between items-center py-2 text-left font-serif text-lg font-bold text-gray-900 focus:outline-none">
                <span>Chăm Sóc & Bảo Quản</span>
                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300" :class="{ 'rotate-180': expandedSections.care }"></i>
              </button>
              <div class="grid transition-all duration-300 ease-in-out" :style="{ gridTemplateRows: expandedSections.care ? '1fr' : '0fr' }">
                <div class="overflow-hidden">
                  <div class="text-xs text-gray-500 space-y-2 pt-3 font-light leading-relaxed">
                    <p>• Tránh ánh nắng mặt trời trực tiếp và nơi có nhiệt độ cao hoặc phòng máy lạnh quá lạnh.</p>
                    <p>• Đối với hoa cắm bình: Thay nước mỗi ngày 1 lần và cắt tỉa bớt phần cuống hoa úa.</p>
                    <p>• Đối với giỏ/lẵng hoa: Tưới/phun sương nhẹ trực tiếp vào phần xốp hoa giữ nước mỗi ngày một lần.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- RELATED PRODUCTS SECTION -->
      <section v-if="relatedProducts.length > 0" class="mt-32 pt-16 border-t border-gray-100">
        <div class="text-center mb-16">
          <span class="text-xs uppercase tracking-[0.3em] text-pink-600 font-medium mb-3 block">Có thể bạn sẽ thích</span>
          <h2 class="text-4xl font-serif font-bold text-gray-900 italic">Sản phẩm liên quan</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <div v-for="related in relatedProducts" :key="related.id" class="group cursor-pointer flex flex-col">
            <router-link :to="'/product/' + related.slug" class="relative h-72 mb-4 overflow-hidden bg-gray-50 border border-gray-100 rounded-xl block">
              <img :src="'http://localhost:8888/images/' + related.thumbnail" :alt="related.name"
                class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-700 ease-in-out">
            </router-link>
            <div>
              <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-widest mb-1 block">
                {{ related.category?.name || 'Hoa thiết kế' }}
              </span>
              <router-link :to="'/product/' + related.slug">
                <h3 class="text-sm font-semibold text-gray-800 group-hover:text-pink-600 transition-colors duration-300 line-clamp-1">
                  {{ related.name }}
                </h3>
              </router-link>
              <p class="text-xs font-bold text-pink-600 mt-1">{{ formatVND(getMinPrice(related)) }}</p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <Footer_client />
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap');

.font-serif {
  font-family: 'Playfair Display', serif;
}

.font-sans {
  font-family: 'Inter', sans-serif;
}

.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
