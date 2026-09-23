<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import http, { getErrorMessage } from '@/utils/http';
import { formatVND, formatDate } from '@/utils/format';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const order = ref(null);
const loading = ref(true);
const error = ref('');

const isBankTransfer = computed(() => order.value?.payment_method === 'bank');

onMounted(async () => {
  const orderCode = route.query.order;
  if (!orderCode) {
    router.replace('/');
    return;
  }

  if (!authStore.user) {
    router.replace({ name: 'login', query: { redirect: route.fullPath } });
    return;
  }

  try {
    const res = await http.get(`/api/orders/${encodeURIComponent(String(orderCode))}`);
    order.value = res.data.data;
  } catch (e) {
    error.value = getErrorMessage(e, 'Không thể tải thông tin đơn hàng.');
  } finally {
    loading.value = false;
  }
});
</script>
<template>
<div class="bg-background font-body text-on-surface antialiased min-h-screen flex flex-col justify-center items-center">
<!-- Minimal Header for Focused View -->
<header class="absolute top-0 w-full flex items-center p-6 bg-transparent">
<button @click="router.replace('/')" aria-label="Trở về trang chủ" class="flex items-center justify-center w-10 h-10 rounded-full bg-surface-container-lowest shadow-[0_4px_12px_rgba(138,77,93,0.08)] hover:bg-surface-container-low transition-colors duration-300">
<span class="material-symbols-outlined text-primary" data-icon="arrow_back">arrow_back</span>
</button>
</header>
<main class="w-full max-w-2xl px-6 py-12 flex flex-col items-center">
<!-- Success Card -->
<div class="bg-surface-container-lowest rounded-xl p-8 md:p-12 w-full flex flex-col items-center text-center shadow-[0_20px_40px_rgba(138,77,93,0.06)] relative overflow-hidden">
<!-- Decorative Floral Element (Abstract SVG) -->
<div class="absolute -top-12 -right-12 w-48 h-48 opacity-20 pointer-events-none text-primary">
<svg fill="currentColor" viewbox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
<path d="M50 0C60 30 80 40 100 50C80 60 60 70 50 100C40 70 20 60 0 50C20 40 40 30 50 0Z"></path>
</svg>
</div>
<div class="absolute -bottom-16 -left-16 w-64 h-64 opacity-10 pointer-events-none text-primary">
<svg fill="currentColor" viewbox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
<circle cx="50" cy="50" fill="none" r="40" stroke="currentColor" stroke-dasharray="4 4" stroke-width="2"></circle>
<circle cx="50" cy="50" fill="currentColor" r="20"></circle>
</svg>
</div>
<!-- Image/Illustration -->
<div class="mb-8 w-48 h-48 rounded-full overflow-hidden border-4 border-surface-container-low relative">
<img alt="Thanh toán thành công" class="w-full h-full object-cover" data-alt="A soft focus, close-up photograph of a delicate pink rose bouquet tied with a ribbon, elegant and romantic" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBHPDgC6q7Dl8fRI22cjRnUr5tR3JEmmPrWSZ2Z7XpOlTgXgcBQBIaRv0aRrmbutPDmq1sHffWfGlAQJxuwUR3mAccjbFJdgdyarcoNdOZWvBXFJowgQ2U4tz5VyMJMsL672-y4IDAa4VLGce71pKDG-HAfOebLRnSkytC2oNsp0W6fmcxRnkJMV9WLXutjJm8LdpKtV0EZsus-Ix11ERcQFvh_5g4jYBquflxMj7gFpJijSx7ygZEbWa1sM65SjJKGTjgTytS23wU"/>
<!-- Checkmark Overlay -->
<div class="absolute inset-0 flex items-center justify-center bg-primary/20 backdrop-blur-sm">
<div class="w-16 h-16 bg-surface-container-lowest rounded-full flex items-center justify-center shadow-lg">
<span class="material-symbols-outlined text-4xl text-primary" data-icon="check_circle" data-weight="fill" style="font-variation-settings: 'FILL' 1;">check_circle</span>
</div>
</div>
</div>
<p v-if="loading" class="font-body text-lg text-on-surface-variant mb-8">Đang tải thông tin đơn hàng...</p>
<p v-else-if="error" class="font-body text-lg text-red-500 mb-8">{{ error }}</p>
<template v-else-if="order">
<h1 class="font-headline text-3xl md:text-4xl text-on-surface mb-4 tracking-tight">Cảm ơn bạn đã đặt hàng!</h1>
<p class="font-body text-lg text-on-surface-variant max-w-md mb-8">
                Đơn hàng của bạn đã được xác nhận. Chúng tôi đang chuẩn bị những bông hoa tươi đẹp nhất cho bạn.
            </p>
<!-- Order Details -->
<div class="w-full max-w-sm bg-surface-container-low rounded-lg p-6 mb-10 flex flex-col gap-4">
<div class="flex justify-between items-center border-b border-outline-variant/15 pb-4">
<span class="font-label text-sm uppercase tracking-wider text-on-surface-variant">Mã đơn hàng</span>
<span class="font-body font-bold text-on-surface">{{ order.order_code }}</span>
</div>
<div class="flex justify-between items-center border-b border-outline-variant/15 pb-4">
<span class="font-label text-sm uppercase tracking-wider text-on-surface-variant">Ngày đặt</span>
<span class="font-body font-bold text-primary">{{ formatDate(order.created_at) }}</span>
</div>
<div class="flex justify-between items-center border-b border-outline-variant/15 pb-4">
<span class="font-label text-sm uppercase tracking-wider text-on-surface-variant">Tổng tiền</span>
<span class="font-body font-bold text-primary">{{ formatVND(order.total_amount) }}</span>
</div>
<div class="flex justify-between items-center">
<span class="font-label text-sm uppercase tracking-wider text-on-surface-variant">Thanh toán</span>
<span class="font-body font-bold text-on-surface">{{ order.payment_method === 'bank' ? 'Chuyển khoản ngân hàng' : 'Thanh toán khi nhận hàng (COD)' }}</span>
</div>
</div>
<!-- Bank transfer info -->
<div v-if="isBankTransfer && order.shop" class="w-full max-w-sm bg-primary/5 rounded-lg p-6 mb-10 text-left">
<h3 class="font-headline font-semibold text-lg text-on-surface mb-4 text-center">Thông tin chuyển khoản</h3>
<div class="space-y-2 text-sm">
<p><span class="font-label uppercase tracking-wider text-on-surface-variant">Ngân hàng:</span> <strong>{{ order.shop.bank_name || 'Chưa cập nhật' }}</strong></p>
<p><span class="font-label uppercase tracking-wider text-on-surface-variant">Số tài khoản:</span> <strong>{{ order.shop.bank_account_number || 'Chưa cập nhật' }}</strong></p>
<p><span class="font-label uppercase tracking-wider text-on-surface-variant">Chủ tài khoản:</span> <strong>{{ order.shop.bank_account_holder || 'Chưa cập nhật' }}</strong></p>
<p><span class="font-label uppercase tracking-wider text-on-surface-variant">Số tiền:</span> <strong class="text-primary">{{ formatVND(order.total_amount) }}</strong></p>
<p><span class="font-label uppercase tracking-wider text-on-surface-variant">Nội dung CK:</span> <strong>{{ order.order_code }}</strong></p>
</div>
<p class="text-xs text-on-surface-variant mt-4 text-center">Sau khi chuyển khoản, shop sẽ xác nhận và tiến hành chuẩn bị đơn hàng của bạn.</p>
</div>
<!-- Actions -->
<div class="flex flex-col sm:flex-row gap-4 w-full max-w-sm">
<button @click="router.replace('/product')" class="flex-1 h-12 bg-primary text-on-primary font-body font-bold text-sm tracking-wide rounded-lg flex items-center justify-center transition-transform hover:scale-[1.02] active:scale-95">
                    Tiếp tục mua sắm
                </button>
<button @click="router.replace('/profile/order')" class="flex-1 h-12 bg-transparent border-none text-primary font-body font-bold text-sm tracking-wide rounded-lg flex items-center justify-center hover:bg-outline-variant/15 transition-colors">
                    Xem đơn hàng
                </button>
</div>
</template>
</div>
</main>
</div>
</template>

<style scoped>
</style>
