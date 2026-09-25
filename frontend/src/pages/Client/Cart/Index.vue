<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import { useAuthStore } from '../../../stores/auth';
import { useCartStore } from '../../../stores/cart';
import { getErrorMessage } from '../../../utils/http';
import { formatVND } from '../../../utils/format';
import { defaultImageUrl, imageUrl } from '../../../utils/api';
import Header_Client from '@/pages/Includes/Layouts/Header_client.vue';
import Footer_Client from '@/pages/Includes/Layouts/Footer_client.vue';

const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();

const updatingIds = ref([]);
const removingId = ref(null);

const items = computed(() => cartStore.items);
const subtotal = computed(() => cartStore.subtotal);
const isEmpty = computed(() => cartStore.loaded && cartStore.items.length === 0);

onMounted(async () => {
  if (!authStore.user) {
    router.replace({ name: 'login', query: { redirect: '/cart' } });
    return;
  }
  try {
    await cartStore.fetchCart(true);
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Không thể tải giỏ hàng',
      text: getErrorMessage(error),
      confirmButtonColor: '#db2777'
    });
  }
});

const changeQty = async (item, delta) => {
  const newQty = Number(item.quantity) + delta;
  if (newQty < 1) return;
  if (item.stock != null && newQty > Number(item.stock)) {
    Swal.fire({
      toast: true,
      icon: 'warning',
      title: 'Số lượng trong kho không đủ',
      position: 'top-end',
      showConfirmButton: false,
      timer: 2000
    });
    return;
  }

  updatingIds.value.push(item.id);
  try {
    await cartStore.updateQty(item.id, newQty);
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Cập nhật thất bại',
      text: getErrorMessage(error),
      confirmButtonColor: '#db2777'
    });
  } finally {
    updatingIds.value = updatingIds.value.filter((id) => id !== item.id);
  }
};

const removeItem = async (item) => {
  const result = await Swal.fire({
    icon: 'question',
    title: 'Xóa sản phẩm?',
    text: `Bạn muốn xóa "${item.product_name}" khỏi giỏ hàng?`,
    showCancelButton: true,
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Giữ lại',
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280'
  });

  if (!result.isConfirmed) return;

  removingId.value = item.id;
  try {
    await cartStore.removeItem(item.id);
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Xóa thất bại',
      text: getErrorMessage(error),
      confirmButtonColor: '#db2777'
    });
  } finally {
    removingId.value = null;
  }
};
</script>
<template>
    <Header_Client />
    <div class="min-h-screen bg-[#fbf7f8] text-gray-900">
        <section class="bg-gradient-to-r from-rose-50 via-amber-50 to-indigo-50 border-b border-pink-100/50 py-16 md:py-20">
            <div class="container mx-auto px-4 text-center">
                <span class="text-xs uppercase tracking-[0.35em] mb-4 block font-semibold text-pink-600">
                    GIỎ HÀNG CỦA BẠN
                </span>
                <h1 class="font-display text-4xl md:text-6xl font-bold text-gray-900 mb-5 leading-tight">
                    Giỏ hàng
                </h1>
                <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Kiểm tra lại sản phẩm trước khi tiến hành thanh toán.
                </p>
            </div>
        </section>

        <main class="py-10 md:py-14">
            <div class="container mx-auto px-4 md:px-6 lg:px-8">
                <!-- Empty state -->
                <div v-if="isEmpty" class="text-center py-20">
                    <i class="fa-solid fa-bag-shopping text-gray-200 text-7xl mb-6"></i>
                    <h2 class="font-display text-2xl font-bold text-gray-900 mb-3">Giỏ hàng trống</h2>
                    <p class="text-gray-500 mb-8">Bạn chưa có sản phẩm nào trong giỏ hàng.</p>
                    <router-link to="/product" class="inline-block bg-pink-600 text-white font-bold py-3 px-8 rounded-full hover:bg-pink-700 transition-colors shadow-lg shadow-pink-100">
                        Tiếp tục mua sắm
                    </router-link>
                </div>

                <div v-else-if="!cartStore.loaded" class="text-center py-20">
                    <p class="text-gray-500">Đang tải giỏ hàng...</p>
                </div>

                <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <div class="lg:col-span-8">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="hidden md:grid grid-cols-12 gap-4 px-6 py-4 bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <div class="col-span-6">Sản phẩm</div>
                                <div class="col-span-2 text-center">Giá</div>
                                <div class="col-span-2 text-center">Số lượng</div>
                                <div class="col-span-2 text-right">Tổng</div>
                            </div>

                            <div v-for="item in items" :key="item.id"
                                class="p-5 md:p-6 border-b border-gray-100 flex flex-col md:grid md:grid-cols-12 gap-5 md:items-center"
                                :class="{ 'opacity-60 pointer-events-none': removingId === item.id }">
                                <div class="col-span-6 flex items-center gap-4">
                                    <button @click="removeItem(item)" aria-label="Remove item" class="shrink-0 w-9 h-9 rounded-full border border-gray-200 text-gray-400 hover:border-red-200 hover:bg-red-50 hover:text-red-500 transition-colors hidden md:flex items-center justify-center">
                                        <span class="material-symbols-outlined text-lg">close</span>
                                    </button>
                                    <img alt="Sản phẩm" class="w-24 h-24 object-cover rounded-xl bg-gray-100 border border-gray-100"
                                        :src="item.product_thumbnail ? imageUrl(item.product_thumbnail) : defaultImageUrl" />
                                    <div class="min-w-0">
                                        <h3 class="font-display font-semibold text-lg text-gray-900">{{ item.product_name }}</h3>
                                        <p v-if="item.size_name" class="text-sm text-gray-500 mt-1">Kích thước: {{ item.size_name }}</p>
                                        <button @click="removeItem(item)" class="text-sm text-red-500 mt-2 md:hidden underline">Xóa</button>
                                    </div>
                                </div>
                                <div class="col-span-2 md:text-center flex justify-between md:block">
                                    <span class="md:hidden text-sm text-gray-500">Giá:</span>
                                    <span class="font-semibold text-gray-900">{{ formatVND(item.unit_price) }}</span>
                                </div>
                                <div class="col-span-2 flex md:justify-center justify-between items-center">
                                    <span class="md:hidden text-sm text-gray-500">Số lượng:</span>
                                    <div class="flex items-center border border-gray-200 rounded-full bg-white overflow-hidden">
                                        <button @click="changeQty(item, -1)" :disabled="Number(item.quantity) <= 1 || updatingIds.includes(item.id)"
                                            aria-label="Decrease quantity"
                                            class="w-9 h-9 flex items-center justify-center hover:bg-pink-50 text-gray-600 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                            <span class="material-symbols-outlined text-sm">remove</span>
                                        </button>
                                        <span class="w-10 h-9 text-center flex items-center justify-center text-sm font-semibold">{{ item.quantity }}</span>
                                        <button @click="changeQty(item, 1)" :disabled="(item.stock != null && Number(item.quantity) >= Number(item.stock)) || updatingIds.includes(item.id)"
                                            aria-label="Increase quantity"
                                            class="w-9 h-9 flex items-center justify-center hover:bg-pink-50 text-gray-600 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                            <span class="material-symbols-outlined text-sm">add</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-span-2 flex justify-between md:block md:text-right">
                                    <span class="md:hidden text-sm text-gray-500">Tổng:</span>
                                    <span class="font-bold text-pink-600">{{ formatVND(item.line_total) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between gap-4 sm:items-center mt-6">
                            <router-link :to="{ name: 'product' }" class="text-sm font-semibold text-gray-700 hover:text-pink-600 transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">arrow_back</span>
                                Tiếp tục mua sắm
                            </router-link>
                        </div>
                    </div>

                    <div class="lg:col-span-4">
                        <div class="bg-white p-6 md:p-7 rounded-2xl border border-gray-100 shadow-sm sticky top-24">
                            <h2 class="font-display text-2xl font-bold mb-6 border-b border-gray-100 pb-4 text-gray-900">
                                Tổng đơn hàng
                            </h2>
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Tạm tính</span>
                                    <span class="font-semibold text-gray-900">{{ formatVND(subtotal) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Phí vận chuyển</span>
                                    <span class="font-semibold text-gray-900">Tính lúc thanh toán</span>
                                </div>
                            </div>
                            <div class="border-t border-gray-100 pt-5 mb-6">
                                <div class="flex justify-between items-end gap-4">
                                    <span class="text-base font-bold text-gray-900">Tổng cộng</span>
                                    <span class="text-2xl md:text-3xl font-bold text-pink-600">{{ formatVND(subtotal) }}</span>
                                </div>
                                <p class="text-xs text-gray-500 text-right mt-1">Đã bao gồm VAT</p>
                            </div>
                            <router-link :class="{ 'pointer-events-none opacity-50': items.length === 0 }"
                                to="/checkout"
                                class="w-full bg-pink-600 text-white font-bold py-4 px-4 rounded-full hover:bg-pink-700 transition-colors flex items-center justify-center gap-2 shadow-lg shadow-pink-100">
                                Tiến hành thanh toán
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <Footer_Client />
</template>
<style scoped>
body {
    font-family: 'Roboto', sans-serif;
}

h1,
h2,
h3,
h4,
h5,
h6,
.font-display {
    font-family: 'Playfair Display', serif;
}
</style>
