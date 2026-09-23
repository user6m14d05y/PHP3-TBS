<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import http, { getErrorMessage } from '@/utils/http';
import {
  formatVND,
  formatDate,
  ORDER_STATUS_LABELS,
  ORDER_STATUS_COLORS,
  PAYMENT_STATUS_LABELS,
  PAYMENT_STATUS_COLORS,
  CAN_CANCEL_ORDER
} from '@/utils/format';
import { defaultImageUrl } from '@/utils/api';
import { useAuthStore } from '@/stores/auth';
import Header_client from '@/pages/Includes/Layouts/Header_client.vue';
import Footer_client from '@/pages/Includes/Layouts/Footer_client.vue';

const router = useRouter();
const authStore = useAuthStore();

const orders = ref([]);
const loading = ref(true);
const pagination = ref({ current_page: 1, last_page: 1, total: 0 });

const statusFilter = ref('');
const showDetail = ref(null); // order đang xem chi tiết
const detailLoading = ref(false);
const cancelingCode = ref(null);

const STATUS_TABS = [
  { value: '', label: 'Tất cả' },
  { value: 'awaiting_payment', label: 'Chờ thanh toán' },
  { value: 'paid', label: 'Đã thanh toán' },
  { value: 'processing', label: 'Đang xử lý' },
  { value: 'shipping', label: 'Đang giao' },
  { value: 'completed', label: 'Hoàn thành' },
  { value: 'cancelled', label: 'Đã hủy' }
];

onMounted(async () => {
  if (!authStore.user) {
    router.replace({ name: 'login', query: { redirect: '/profile/order' } });
    return;
  }
  await fetchOrders();
});

const fetchOrders = async (page = 1) => {
  loading.value = true;
  try {
    const params = { page };
    if (statusFilter.value) params.status = statusFilter.value;
    const res = await http.get('/api/orders', { params });
    orders.value = res.data.data || [];
    pagination.value = {
      current_page: res.data.current_page || 1,
      last_page: res.data.last_page || 1,
      total: res.data.total || 0
    };
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Không thể tải đơn hàng', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
  } finally {
    loading.value = false;
  }
};

const changeFilter = (status) => {
  statusFilter.value = status;
  fetchOrders(1);
};

const changePage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return;
  fetchOrders(page);
};

const openDetail = async (orderCode) => {
  detailLoading.value = true;
  showDetail.value = null;
  try {
    const res = await http.get(`/api/orders/${encodeURIComponent(orderCode)}`);
    showDetail.value = res.data.data;
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Không thể tải chi tiết đơn hàng', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
  } finally {
    detailLoading.value = false;
  }
};

const cancelOrder = async (order) => {
  const result = await Swal.fire({
    icon: 'warning',
    title: 'Hủy đơn hàng?',
    text: `Bạn chắc chắn muốn hủy đơn ${order.order_code}?`,
    showCancelButton: true,
    confirmButtonText: 'Hủy đơn',
    cancelButtonText: 'Giữ lại',
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280'
  });

  if (!result.isConfirmed) return;

  cancelingCode.value = order.order_code;
  try {
    await http.post(`/api/orders/${encodeURIComponent(order.order_code)}/cancel`);
    Swal.fire({ icon: 'success', title: 'Đã hủy đơn hàng', confirmButtonColor: '#db2777' });
    showDetail.value = null;
    await fetchOrders(pagination.value.current_page);
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Hủy đơn thất bại', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
  } finally {
    cancelingCode.value = null;
  }
};
</script>
<template>
    <Header_client />
    <main class="flex-grow bg-surface-light dark:bg-surface-dark py-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center md:text-left">
                <h1 class="text-4xl font-display font-bold text-text-main-light dark:text-text-main-dark mb-2">Đơn Hàng Của Bạn</h1>
                <p class="text-text-muted-light dark:text-text-muted-dark">Xem lịch sử và trạng thái các đơn hàng đã đặt.</p>
            </div>

            <!-- Tabs lọc trạng thái -->
            <div class="flex flex-wrap gap-2 mb-8">
                <button v-for="tab in STATUS_TABS" :key="tab.value" @click="changeFilter(tab.value)"
                    :class="statusFilter === tab.value
                        ? 'bg-primary text-white'
                        : 'bg-background-light dark:bg-background-dark text-text-muted-light dark:text-text-muted-dark hover:bg-primary/10'"
                    class="px-4 py-2 rounded-full text-sm font-medium transition-colors border border-border-light dark:border-border-dark">
                    {{ tab.label }}
                </button>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="text-center py-20">
                <p class="text-text-muted-light dark:text-text-muted-dark">Đang tải đơn hàng...</p>
            </div>

            <!-- Empty -->
            <div v-else-if="orders.length === 0" class="text-center py-20">
                <i class="fa-solid fa-box-open text-gray-200 text-7xl mb-6"></i>
                <h2 class="font-display text-2xl font-bold text-gray-900 dark:text-white mb-3">Chưa có đơn hàng nào</h2>
                <p class="text-text-muted-light dark:text-text-muted-dark mb-8">Hãy khám phá và đặt những bó hoa tươi đẹp nhất cho người thân yêu.</p>
                <router-link to="/product" class="inline-block bg-primary text-white font-bold py-3 px-8 rounded-full hover:bg-primary-dark transition-colors">
                    Mua sắm ngay
                </router-link>
            </div>

            <!-- List -->
            <div v-else class="space-y-6">
                <div v-for="order in orders" :key="order.order_code"
                    class="bg-background-light dark:bg-background-dark rounded-lg shadow-sm border border-border-light dark:border-border-dark p-5 md:p-6">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-4 pb-4 border-b border-border-light dark:border-border-dark">
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                            <div>
                                <p class="text-xs text-text-muted-light dark:text-text-muted-dark mb-1">Mã đơn hàng</p>
                                <p class="font-medium text-sm">{{ order.order_code }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-text-muted-light dark:text-text-muted-dark mb-1">Ngày đặt</p>
                                <p class="font-medium text-sm">{{ formatDate(order.created_at) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-text-muted-light dark:text-text-muted-dark mb-1">Tổng tiền</p>
                                <p class="font-medium text-sm text-primary">{{ formatVND(order.total_amount) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="ORDER_STATUS_COLORS[order.status] || 'bg-gray-100 text-gray-700'">
                                {{ ORDER_STATUS_LABELS[order.status] || order.status }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="PAYMENT_STATUS_COLORS[order.payment_status] || 'bg-gray-100 text-gray-700'">
                                {{ PAYMENT_STATUS_LABELS[order.payment_status] || order.payment_status }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center space-x-4 min-w-0">
                            <div class="w-16 h-16 bg-surface-light dark:bg-surface-dark rounded border border-border-light dark:border-border-dark overflow-hidden flex-shrink-0">
                                <img alt="Hoa" class="w-full h-full object-cover" :src="defaultImageUrl" />
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-sm line-clamp-1">{{ order.items?.[0]?.product_name || '—' }}</p>
                                <p class="text-xs text-text-muted-light dark:text-text-muted-dark mt-1">
                                    {{ order.items?.length || 0 }} loại sản phẩm · {{ formatVND(order.total_amount) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <button @click="openDetail(order.order_code)"
                                class="text-sm font-semibold text-primary hover:underline whitespace-nowrap">
                                Xem chi tiết
                            </button>
                            <button v-if="CAN_CANCEL_ORDER(order)" @click="cancelOrder(order)"
                                :disabled="cancelingCode === order.order_code"
                                class="text-sm font-semibold text-red-500 hover:text-red-600 hover:underline whitespace-nowrap disabled:opacity-50">
                                {{ cancelingCode === order.order_code ? 'Đang hủy...' : 'Hủy đơn' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="pagination.last_page > 1" class="flex justify-center items-center gap-4 pt-4">
                    <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page <= 1"
                        class="px-4 py-2 rounded border border-border-light dark:border-border-dark text-sm disabled:opacity-40">
                        Trước
                    </button>
                    <span class="text-sm text-text-muted-light dark:text-text-muted-dark">
                        Trang {{ pagination.current_page }} / {{ pagination.last_page }}
                    </span>
                    <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page"
                        class="px-4 py-2 rounded border border-border-light dark:border-border-dark text-sm disabled:opacity-40">
                        Sau
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal chi tiết đơn hàng -->
    <div v-if="showDetail || detailLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showDetail = null">
        <div class="bg-white dark:bg-background-dark rounded-lg shadow-xl w-full max-w-2xl max-h-[85vh] overflow-y-auto">
            <div class="flex justify-between items-center p-5 border-b border-border-light dark:border-border-dark">
                <h3 class="text-xl font-display font-semibold">Chi tiết đơn hàng</h3>
                <button @click="showDetail = null" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div v-if="detailLoading" class="p-6 text-center text-text-muted-light dark:text-text-muted-dark">Đang tải...</div>
            <div v-else-if="showDetail" class="p-6 space-y-5">
                <div class="flex flex-wrap gap-x-8 gap-y-2 text-sm">
                    <p><span class="text-text-muted-light dark:text-text-muted-dark">Mã đơn:</span> <strong>{{ showDetail.order_code }}</strong></p>
                    <p><span class="text-text-muted-light dark:text-text-muted-dark">Ngày đặt:</span> {{ formatDate(showDetail.created_at) }}</p>
                    <p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="ORDER_STATUS_COLORS[showDetail.status] || 'bg-gray-100 text-gray-700'">
                            {{ ORDER_STATUS_LABELS[showDetail.status] || showDetail.status }}
                        </span>
                    </p>
                </div>

                <!-- Giao hàng -->
                <div class="bg-surface-light dark:bg-surface-dark rounded p-4 text-sm space-y-1">
                    <p class="font-semibold mb-1">Thông tin giao hàng</p>
                    <p>{{ showDetail.recipient_name }} - {{ showDetail.recipient_phone }}</p>
                    <p class="text-text-muted-light dark:text-text-muted-dark">{{ showDetail.shipping_address }}</p>
                    <p class="text-text-muted-light dark:text-text-muted-dark" v-if="showDetail.delivery_distance_km">
                        Khoảng cách: {{ showDetail.delivery_distance_km }} km
                    </p>
                    <p v-if="showDetail.note" class="text-text-muted-light dark:text-text-muted-dark">Ghi chú: {{ showDetail.note }}</p>
                </div>

                <!-- Items -->
                <div class="space-y-3">
                    <p class="font-semibold text-sm">Sản phẩm</p>
                    <div v-for="item in showDetail.items" :key="item.id" class="flex items-center gap-4 bg-surface-light dark:bg-surface-dark rounded p-3">
                        <div class="w-14 h-14 rounded border border-border-light dark:border-border-dark overflow-hidden flex-shrink-0">
                            <img alt="Sản phẩm" class="w-full h-full object-cover" :src="defaultImageUrl" />
                        </div>
                        <div class="flex-grow min-w-0">
                            <p class="font-medium text-sm">{{ item.product_name }}</p>
                            <p class="text-xs text-text-muted-light dark:text-text-muted-dark" v-if="item.variant_name">Size: {{ item.variant_name }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm">{{ formatVND(item.unit_price) }} × {{ item.quantity }}</p>
                            <p class="text-sm font-semibold text-primary">{{ formatVND(item.line_total) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Totals -->
                <div class="border-t border-border-light dark:border-border-dark pt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-text-muted-light dark:text-text-muted-dark">Tạm tính</span>
                        <span>{{ formatVND(showDetail.subtotal_amount) }}</span>
                    </div>
                    <div v-if="Number(showDetail.discount_amount) > 0" class="flex justify-between text-green-600">
                        <span>Giảm giá</span>
                        <span>-{{ formatVND(showDetail.discount_amount) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text-muted-light dark:text-text-muted-dark">Phí vận chuyển</span>
                        <span>{{ formatVND(showDetail.shipping_fee) }}</span>
                    </div>
                    <div class="flex justify-between font-semibold text-base pt-2 border-t border-border-light dark:border-border-dark">
                        <span>Tổng cộng</span>
                        <span class="text-primary">{{ formatVND(showDetail.total_amount) }}</span>
                    </div>
                </div>

                <!-- Hủy đơn -->
                <div v-if="CAN_CANCEL_ORDER(showDetail)" class="text-right">
                    <button @click="cancelOrder(showDetail)"
                        :disabled="cancelingCode === showDetail.order_code"
                        class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-5 py-2 rounded disabled:opacity-50">
                        {{ cancelingCode === showDetail.order_code ? 'Đang hủy...' : 'Hủy đơn hàng' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <Footer_client />
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
