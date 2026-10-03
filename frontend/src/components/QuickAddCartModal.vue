<script setup>
import { ref, computed } from 'vue';
import Swal from 'sweetalert2';
import { useCartStore } from '@/stores/cart';
import { getErrorMessage } from '@/utils/http';
import { useAuthStore } from '@/stores/auth';
import { useRouter, useRoute } from 'vue-router';
import { imageUrl } from '@/utils/api';

const props = defineProps({
  show: Boolean,
  product: Object
});

const emit = defineEmits(['close']);

const cartStore = useCartStore();
const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const selectedVariant = ref(null);
const quantity = ref(1);
const isSubmitting = ref(false);

const resetState = () => {
  selectedVariant.value = null;
  quantity.value = 1;
};

// Initialize best variant
const initVariant = () => {
  if (props.product?.variants?.length) {
    const sorted = [...props.product.variants].sort((a, b) => {
      const pa = Number(a.sale_price || a.price);
      const pb = Number(b.sale_price || b.price);
      return pa - pb;
    });
    selectedVariant.value = sorted[0];
  }
};

const close = () => {
  resetState();
  emit('close');
};

const decreaseQty = () => {
  if (quantity.value > 1) quantity.value--;
};

const increaseQty = () => {
  const maxStock = selectedVariant.value ? selectedVariant.value.stock : 99;
  if (quantity.value < maxStock) quantity.value++;
};

const handleAddToCart = async () => {
  if (!selectedVariant.value?.id) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'warning',
      title: 'Vui lòng chọn kích thước',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
    });
    return;
  }

  isSubmitting.value = true;
  try {
    await cartStore.addToCart(selectedVariant.value.id, quantity.value);
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Đã thêm vào giỏ hàng!',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
    });
    close();
  } catch (error) {
    if (error.response?.status === 401) {
      authStore.logout();
      router.push({ name: 'login', query: { redirect: route.fullPath } });
      return;
    }
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Thêm vào giỏ hàng thất bại',
      text: getErrorMessage(error, 'Không thể thêm sản phẩm vào giỏ hàng.'),
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
    });
  } finally {
    isSubmitting.value = false;
  }
};

const formatVND = (price) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50" @click.self="close" @vue:mounted="initVariant">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden animate-fade-in-up">
      <!-- Header -->
      <div class="flex justify-between items-center p-4 border-b border-gray-100 bg-gray-50">
        <h3 class="font-serif font-bold text-lg text-gray-900">Thêm vào giỏ hàng</h3>
        <button @click="close" class="text-gray-400 hover:text-red-500 transition cursor-pointer">
          <i class="fa-solid fa-xmark text-xl"></i>
        </button>
      </div>

      <!-- Body -->
      <div class="p-6" v-if="product">
        <div class="flex gap-4 items-start mb-6">
          <img :src="imageUrl(product.thumbnail)" alt="" class="w-24 h-24 object-cover rounded-lg border border-gray-100">
          <div>
            <h4 class="font-bold text-gray-900 mb-1 line-clamp-2">{{ product.name }}</h4>
            <div v-if="selectedVariant" class="flex gap-2 items-baseline">
              <span class="font-bold text-pink-600">{{ formatVND(selectedVariant.sale_price || selectedVariant.price) }}</span>
              <span v-if="selectedVariant.sale_price" class="text-xs text-gray-400 line-through">{{ formatVND(selectedVariant.price) }}</span>
            </div>
          </div>
        </div>

        <div class="mb-4">
          <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Kích thước</span>
          <div class="flex flex-wrap gap-2">
            <button v-for="variant in product.variants" :key="variant.id"
              @click="selectedVariant = variant"
              :disabled="variant.stock === 0"
              class="px-4 py-2 border text-xs font-bold uppercase transition-all duration-200 cursor-pointer rounded"
              :class="selectedVariant?.id === variant.id ? 'bg-pink-600 border-pink-600 text-white' : 'border-gray-200 text-gray-700 hover:border-pink-600 hover:text-pink-600 disabled:opacity-30'">
              {{ variant.size?.name || 'Tiêu chuẩn' }}
            </button>
          </div>
        </div>

        <div class="flex items-center gap-4 mt-6">
          <div class="flex items-center justify-between border border-gray-200 px-3 py-2 rounded w-32 shrink-0">
            <button @click="decreaseQty" class="text-gray-500 hover:text-pink-600 px-1 cursor-pointer"><i class="fa-solid fa-minus text-xs"></i></button>
            <span class="font-bold text-sm">{{ quantity }}</span>
            <button @click="increaseQty" class="text-gray-500 hover:text-pink-600 px-1 cursor-pointer"><i class="fa-solid fa-plus text-xs"></i></button>
          </div>
          <button @click="handleAddToCart" :disabled="isSubmitting" class="flex-1 bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 px-4 rounded transition text-sm uppercase tracking-wider cursor-pointer">
            {{ isSubmitting ? 'Đang thêm...' : 'Thêm vào giỏ' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in-up {
  animation: fadeInUp 0.3s ease-out;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
