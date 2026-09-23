<script setup>
import header_admin from '../Includes/Layouts/Header_Admin.vue';
import navbar_admin from '../Includes/Layouts/Navbar_Admin.vue';
import { ref, onMounted } from 'vue';
import Swal from 'sweetalert2';
import http, { getErrorMessage } from '@/utils/http';
import {
  formatVND,
  formatDate,
  ORDER_STATUS_LABELS,
  ORDER_STATUS_COLORS,
  PAYMENT_STATUS_LABELS,
  PAYMENT_STATUS_COLORS
} from '@/utils/format';

const isDark = ref(false);
const isSidebarOpen = ref(true);

const orders = ref([]);
const loading = ref(false);
const pagination = ref({ current_page: 1, last_page: 1, total: 0 });
const filterStatus = ref('');
const filterPayment = ref('');
const search = ref('');

const showDetail = ref(null);
const detailLoading = ref(false);
const actingCode = ref(null);

const STATUS_TABS = [
  { value: '', label: 'Tất cả' },
  { value: 'awaiting_payment', label: 'Chờ thanh toán' },
  { value: 'paid', label: 'Đã thanh toán' },
  { value: 'processing', label: 'Đang xử lý' },
  { value: 'shipping', label: 'Đang giao' },
  { value: 'completed', label: 'Hoàn thành' },
  { value: 'cancelled', label: 'Đã hủy' }
];

// Nút chuyển trạng thái hợp lệ theo trạng thái hiện tại
const nextStatuses = (order) => {
  const map = {
    awaiting_payment: ['processing'],
    paid: ['processing'],
    processing: ['shipping'],
    shipping: ['completed']
  };
  return map[order.status] || [];
};

// Lấy chế độ dark mode từ localStorage (nếu có)
onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        isDark.value = true;
    } else if (savedTheme === 'light') {
        isDark.value = false;
    } else {
        isDark.value = false;
    }
    fetchOrders(1);
});

const toggleTheme = () => {
    isDark.value = !isDark.value;
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

const fetchOrders = async (page = 1) => {
  loading.value = true;
  try {
    const params = { page };
    if (filterStatus.value) params.status = filterStatus.value;
    if (filterPayment.value) params.payment_status = filterPayment.value;
    if (search.value.trim()) params.search = search.value.trim();

    const res = await http.get('/api/admin/orders', { params });
    orders.value = res.data.data || [];
    pagination.value = {
      current_page: res.data.current_page || 1,
      last_page: res.data.last_page || 1,
      total: res.data.total || 0
    };
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Không thể tải đơn hàng', text: getErrorMessage(error) });
  } finally {
    loading.value = false;
  }
};

const changeFilter = (status) => {
  filterStatus.value = status;
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
    const res = await http.get(`/api/admin/orders/${encodeURIComponent(orderCode)}`);
    showDetail.value = res.data.data;
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Không thể tải chi tiết', text: getErrorMessage(error) });
  } finally {
    detailLoading.value = false;
  }
};

const confirmAction = async (title, text, confirmText, action) => {
  const result = await Swal.fire({
    icon: 'question',
    title,
    text,
    showCancelButton: true,
    confirmButtonText: confirmText,
    cancelButtonText: 'Hủy bỏ'
  });

  if (!result.isConfirmed) return;

  actingCode.value = showDetail.value?.order_code;
  try {
    await action();
    await Swal.fire({ icon: 'success', title: 'Thành công', timer: 1500, showConfirmButton: false });
    showDetail.value = null;
    await fetchOrders(pagination.value.current_page);
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Thao tác thất bại', text: getErrorMessage(error) });
  } finally {
    actingCode.value = null;
  }
};

const confirmPayment = () => {
  const order = showDetail.value;
  confirmAction(
    'Xác nhận thanh toán?',
    `Xác nhận khách đã thanh toán đơn ${order.order_code} (${formatVND(order.total_amount)})?`,
    'Xác nhận',
    () => http.post(`/api/orders/${encodeURIComponent(order.order_code)}/payment-succeeded`)
  );
};

const failPayment = () => {
  const order = showDetail.value;
  confirmAction(
    'Đánh dấu thanh toán thất bại?',
    `Đơn ${order.order_code} sẽ bị hủy và hoàn lại tồn kho, nhả voucher.`,
    'Hủy đơn',
    () => http.post(`/api/orders/${encodeURIComponent(order.order_code)}/payment-failed`)
  );
};

const changeStatus = (target) => {
  const order = showDetail.value;
  const label = ORDER_STATUS_LABELS[target] || target;
  confirmAction(
    'Cập nhật trạng thái?',
    `Chuyển đơn ${order.order_code} sang "${label}"?`,
    'Cập nhật',
    () => http.patch(`/api/admin/orders/${encodeURIComponent(order.order_code)}/status`, { status: target })
  );
};

const cancelOrder = () => {
  const order = showDetail.value;
  confirmAction(
    'Hủy đơn hàng?',
    `Đơn ${order.order_code} sẽ bị hủy, hoàn lại tồn kho và nhả voucher.`,
    'Hủy đơn',
    () => http.patch(`/api/admin/orders/${encodeURIComponent(order.order_code)}/status`, { status: 'cancelled' })
  );
};
</script>

<template>
  <div class="antialiased font-sans transition-colors duration-300">
    <div :class="isDark ? 'bg-[#0f172a] text-gray-100' : 'bg-gray-50 text-gray-900'" class="flex h-screen overflow-hidden">

      <!-- Component Navbar Trái (Đã nối props đầy đủ) -->
      <navbar_admin :isDark="isDark" :isSidebarOpen="isSidebarOpen" />

      <!-- Main Content Khung Bên Phải -->
      <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Component Header Top (Đã nối props và emit sự kiện) -->
        <header_admin
            :isDark="isDark"
            @toggle-sidebar="isSidebarOpen = !isSidebarOpen"
            @toggle-theme="toggleTheme"
        />

                <main class="flex-1 overflow-y-auto p-4 md:p-8">
                    <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-100'"
                        class="w-full min-h-[500px] p-6 md:p-8 border shadow-sm rounded-xl transition-colors duration-300">

                        <!-- Header & Button in Flexbox -->
                        <div :class="isDark ? 'border-gray-700' : 'border-gray-200'"
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-6 border-b gap-4 sm:gap-0">
                            <div>
                                <h2 :class="isDark ? 'text-white' : 'text-gray-900'"
                                    class="text-3xl font-serif font-bold mb-2">Quản Lý Đơn hàng</h2>
                                <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="font-light text-sm">Bảng
                                    điều khiển quản lý đơn hàng hiển thị.</p>
                            </div>
                            <div class="flex gap-2">
                                <input v-model="search" @keyup.enter="fetchOrders(1)"
                                    :class="isDark ? 'bg-gray-800 border-gray-700 text-gray-200' : 'bg-white border-gray-300 text-gray-900'"
                                    class="px-4 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-pink-500"
                                    placeholder="Tìm mã đơn, khách, SĐT..." />
                                <button @click="fetchOrders(1)"
                                    :class="isDark ? 'bg-pink-600 hover:bg-pink-700' : 'bg-pink-600 hover:bg-pink-700'"
                                    class="text-white px-4 py-2 rounded-lg text-sm font-semibold">
                                    Tìm
                                </button>
                            </div>
                        </div>

                        <!-- Tabs lọc trạng thái -->
                        <div class="flex flex-wrap gap-2 mb-6">
                            <button v-for="tab in STATUS_TABS" :key="tab.value" @click="changeFilter(tab.value)"
                                :class="filterStatus === tab.value
                                    ? (isDark ? 'bg-pink-600 text-white' : 'bg-pink-600 text-white')
                                    : (isDark ? 'bg-gray-800 text-gray-400 hover:bg-gray-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200')"
                                class="px-4 py-2 rounded-full text-xs font-semibold transition-colors">
                                {{ tab.label }}
                            </button>
                        </div>

                        <!-- Bảng dữ liệu (HTML Table) -->
                        <div class="overflow-x-auto rounded-lg border"
                            :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr :class="isDark ? 'bg-gray-800/50 text-gray-400 border-gray-700' : 'bg-gray-50 text-gray-600 border-gray-200'"
                                        class="border-b text-xs uppercase tracking-wider">
                                        <th class="px-6 py-4 font-semibold">Mã đơn</th>
                                        <th class="px-6 py-4 font-semibold">Khách hàng</th>
                                        <th class="px-6 py-4 font-semibold">Ngày đặt</th>
                                        <th class="px-6 py-4 font-semibold text-right">Tổng tiền</th>
                                        <th class="px-6 py-4 font-semibold">Trạng thái</th>
                                        <th class="px-6 py-4 font-semibold">Thanh toán</th>
                                        <th class="px-6 py-4 font-semibold text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody :class="isDark ? 'divide-gray-700' : 'divide-gray-200'" class="divide-y">
                                    <tr v-if="loading" :class="isDark ? 'text-gray-400' : 'text-gray-500'">
                                        <td colspan="7" class="px-6 py-10 text-center">Đang tải đơn hàng...</td>
                                    </tr>
                                    <tr v-else-if="orders.length === 0" :class="isDark ? 'text-gray-400' : 'text-gray-500'">
                                        <td colspan="7" class="px-6 py-10 text-center">Không có đơn hàng nào.</td>
                                    </tr>
                                    <tr v-for="order in orders" :key="order.order_code"
                                        :class="isDark ? 'hover:bg-gray-800/40' : 'hover:bg-gray-50'"
                                        class="transition-colors">
                                        <td class="px-6 py-4 font-semibold text-sm">{{ order.order_code }}</td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-medium">{{ order.recipient_name }}</p>
                                            <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-xs">{{ order.recipient_phone }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-sm">{{ formatDate(order.created_at) }}</td>
                                        <td class="px-6 py-4 text-sm font-bold text-right">{{ formatVND(order.total_amount) }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                                :class="ORDER_STATUS_COLORS[order.status] || 'bg-gray-100 text-gray-700'">
                                                {{ ORDER_STATUS_LABELS[order.status] || order.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                                :class="PAYMENT_STATUS_COLORS[order.payment_status] || 'bg-gray-100 text-gray-700'">
                                                {{ PAYMENT_STATUS_LABELS[order.payment_status] || order.payment_status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button @click="openDetail(order.order_code)"
                                                :class="isDark ? 'text-pink-400 hover:text-pink-300' : 'text-pink-600 hover:text-pink-700'"
                                                class="text-sm font-semibold">
                                                Chi tiết
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="pagination.last_page > 1" class="flex justify-center items-center gap-4 mt-6">
                            <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page <= 1"
                                :class="isDark ? 'bg-gray-800 text-gray-300 border-gray-700' : 'bg-white text-gray-700 border-gray-200'"
                                class="px-4 py-2 rounded border text-sm disabled:opacity-40">
                                Trước
                            </button>
                            <span :class="isDark ? 'text-gray-400' : 'text-gray-600'" class="text-sm">
                                Trang {{ pagination.current_page }} / {{ pagination.last_page }} · {{ pagination.total }} đơn
                            </span>
                            <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page"
                                :class="isDark ? 'bg-gray-800 text-gray-300 border-gray-700' : 'bg-white text-gray-700 border-gray-200'"
                                class="px-4 py-2 rounded border text-sm disabled:opacity-40">
                                Sau
                            </button>
                        </div>
                    </div>
                </main>
      </div>
    </div>

    <!-- Modal chi tiết đơn hàng -->
    <div v-if="showDetail || detailLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showDetail = null">
        <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-200'"
            class="rounded-xl border shadow-xl w-full max-w-3xl max-h-[85vh] overflow-y-auto">
            <div class="flex justify-between items-center p-5 border-b" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                <h3 class="text-xl font-serif font-bold">Chi tiết đơn hàng</h3>
                <button @click="showDetail = null" :class="isDark ? 'text-gray-400 hover:text-gray-200' : 'text-gray-400 hover:text-gray-600'">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div v-if="detailLoading" class="p-6 text-center text-gray-500">Đang tải...</div>
            <div v-else-if="showDetail" class="p-6 space-y-5">
                <div class="flex flex-wrap gap-x-8 gap-y-2 text-sm">
                    <p><span :class="isDark ? 'text-gray-400' : 'text-gray-500'">Mã đơn:</span> <strong>{{ showDetail.order_code }}</strong></p>
                    <p><span :class="isDark ? 'text-gray-400' : 'text-gray-500'">Khách:</span> {{ showDetail.user?.name || '—' }} ({{ showDetail.user?.email || '—' }})</p>
                    <p><span :class="isDark ? 'text-gray-400' : 'text-gray-500'">Ngày đặt:</span> {{ formatDate(showDetail.created_at) }}</p>
                    <p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="ORDER_STATUS_COLORS[showDetail.status] || 'bg-gray-100 text-gray-700'">
                            {{ ORDER_STATUS_LABELS[showDetail.status] || showDetail.status }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ml-1" :class="PAYMENT_STATUS_COLORS[showDetail.payment_status] || 'bg-gray-100 text-gray-700'">
                            {{ PAYMENT_STATUS_LABELS[showDetail.payment_status] || showDetail.payment_status }}
                        </span>
                    </p>
                </div>

                <!-- Giao hàng -->
                <div :class="isDark ? 'bg-gray-800' : 'bg-gray-50'" class="rounded p-4 text-sm space-y-1">
                    <p class="font-semibold mb-1">Thông tin giao hàng</p>
                    <p>{{ showDetail.recipient_name }} - {{ showDetail.recipient_phone }}</p>
                    <p :class="isDark ? 'text-gray-400' : 'text-gray-500'">{{ showDetail.shipping_address }}</p>
                    <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" v-if="showDetail.delivery_distance_km">
                        Khoảng cách: {{ showDetail.delivery_distance_km }} km · Phương thức: {{ showDetail.shipping_method === 'express' ? 'Hỏa tốc' : 'Tiêu chuẩn' }}
                    </p>
                    <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" v-if="showDetail.note">Ghi chú: {{ showDetail.note }}</p>
                </div>

                <!-- Items -->
                <div class="space-y-2">
                    <p class="font-semibold text-sm">Sản phẩm</p>
                    <div v-for="item in showDetail.items" :key="item.id" class="flex justify-between items-center gap-4 text-sm">
                        <div>
                            <p class="font-medium">{{ item.product_name }} <span v-if="item.variant_name" class="text-gray-400">({{ item.variant_name }})</span></p>
                            <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-xs">{{ formatVND(item.unit_price) }} × {{ item.quantity }}</p>
                        </div>
                        <p class="font-semibold">{{ formatVND(item.line_total) }}</p>
                    </div>
                </div>

                <!-- Totals -->
                <div class="border-t pt-4 space-y-2 text-sm" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                    <div class="flex justify-between"><span :class="isDark ? 'text-gray-400' : 'text-gray-500'">Tạm tính</span><span>{{ formatVND(showDetail.subtotal_amount) }}</span></div>
                    <div v-if="Number(showDetail.discount_amount) > 0" class="flex justify-between text-green-600"><span>Giảm giá{{ showDetail.coupon ? ` (${showDetail.coupon.code})` : '' }}</span><span>-{{ formatVND(showDetail.discount_amount) }}</span></div>
                    <div class="flex justify-between"><span :class="isDark ? 'text-gray-400' : 'text-gray-500'">Phí vận chuyển</span><span>{{ formatVND(showDetail.shipping_fee) }}</span></div>
                    <div class="flex justify-between font-bold text-base pt-2 border-t" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                        <span>Tổng cộng</span><span class="text-pink-600">{{ formatVND(showDetail.total_amount) }}</span>
                    </div>
                </div>

                <!-- Bank transfer info -->
                <div v-if="showDetail.payment_method === 'bank'" :class="isDark ? 'bg-gray-800' : 'bg-gray-50'" class="rounded p-4 text-sm space-y-1">
                    <p class="font-semibold mb-1">Khách chuyển khoản</p>
                    <p>Ngân hàng: <strong>{{ showDetail.shop?.bank_name || '—' }}</strong></p>
                    <p>Số TK: <strong>{{ showDetail.shop?.bank_account_number || '—' }}</strong></p>
                    <p>Chủ TK: <strong>{{ showDetail.shop?.bank_account_holder || '—' }}</strong></p>
                    <p>Nội dung CK: <strong>{{ showDetail.order_code }}</strong></p>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-2 pt-2 border-t" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                    <button v-if="showDetail.payment_status !== 'paid' && showDetail.status !== 'cancelled'"
                        @click="confirmPayment" :disabled="actingCode === showDetail.order_code"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded disabled:opacity-50">
                        Xác nhận thanh toán
                    </button>
                    <button v-if="showDetail.status === 'awaiting_payment' && showDetail.payment_status !== 'paid'"
                        @click="failPayment" :disabled="actingCode === showDetail.order_code"
                        class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded disabled:opacity-50">
                        Thanh toán thất bại (Hủy)
                    </button>
                    <template v-for="target in nextStatuses(showDetail)" :key="target">
                        <button @click="changeStatus(target)" :disabled="actingCode === showDetail.order_code"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded disabled:opacity-50">
                            Chuyển: {{ ORDER_STATUS_LABELS[target] }}
                        </button>
                    </template>
                    <button v-if="['unpaid', 'pending'].includes(showDetail.payment_status) && showDetail.status !== 'cancelled'"
                        @click="cancelOrder" :disabled="actingCode === showDetail.order_code"
                        class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold px-4 py-2 rounded disabled:opacity-50">
                        Hủy đơn hàng
                    </button>
                </div>
            </div>
        </div>
    </div>
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
