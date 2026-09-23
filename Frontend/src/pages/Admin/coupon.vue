<script setup>
import header_admin from '../Includes/Layouts/Header_Admin.vue';
import navbar_admin from '../Includes/Layouts/Navbar_Admin.vue';
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { apiUrl } from '@/utils/api';

const isDark = ref(false);
const isSidebarOpen = ref(true);

// State for Coupons
const coupons = ref([]);
const statsCoupons = ref([]); // For overall calculations
const loading = ref(false);
const totalCoupons = ref(0);
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = ref(10);

// Filters & Search
const searchQuery = ref('');
const filterStatus = ref('');
const filterType = ref('');

// Modal Form State
const isModalOpen = ref(false);
const showConditions = ref(false); // Collapsible conditions indicator
const submitLoading = ref(false);

const initialForm = {
    id: null,
    code: '',
    name: '',
    description: '',
    discount_type: 'percentage',
    discount_value: '',
    max_discount_amount: '',
    min_order_amount: '',
    usage_limit: '',
    per_user_limit: 1,
    is_active: true,
    starts_at: '',
    expires_at: '',
    conditions: {
        min_quantity: '',
    }
};

const couponForm = ref(JSON.parse(JSON.stringify(initialForm)));
const errors = ref({});

// Theme Toggle
const toggleTheme = () => {
    isDark.value = !isDark.value;
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

// Fetch Paginated Coupons (Main Table)
const fetchCoupons = async (page = 1) => {
    loading.value = true;
    try {
        const token = localStorage.getItem('token');
        const config = { 
            headers: { Authorization: `Bearer ${token}` },
            params: {
                page,
                search: searchQuery.value || undefined,
                status: filterStatus.value || undefined,
                discount_type: filterType.value || undefined
            }
        };

        const response = await axios.get(apiUrl('/api/coupons'), config);
        if (response.data.status === 'success') {
            coupons.value = response.data.data;
            totalCoupons.value = response.data.total;
            currentPage.value = response.data.current_page;
            totalPages.value = response.data.last_page;
            perPage.value = response.data.per_page;
        }
    } catch (error) {
        console.error('Lỗi khi tải danh sách mã giảm giá:', error);
        Swal.fire({
            icon: 'error',
            title: 'Lỗi hệ thống',
            text: 'Không thể kết nối tới máy chủ.',
        });
    } finally {
        loading.value = false;
    }
};

// Fetch All Coupons for General Dashboard Stats
const fetchStatsData = async () => {
    try {
        const token = localStorage.getItem('token');
        const config = { 
            headers: { Authorization: `Bearer ${token}` },
            params: { all: true }
        };

        const response = await axios.get(apiUrl('/api/coupons'), config);
        if (response.data.status === 'success') {
            statsCoupons.value = response.data.data;
        }
    } catch (error) {
        console.error('Lỗi khi tải dữ liệu thống kê:', error);
    }
};

// Auto refresh lists when filter changes
watch([filterStatus, filterType], () => {
    fetchCoupons(1);
});

// Search watcher with basic debounce
let searchTimeout;
watch(searchQuery, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchCoupons(1);
    }, 4000000000000000000); // placeholder, let's run on input event or button click instead for instant feel or 300ms debounce
});

const onSearchInput = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchCoupons(1);
    }, 300);
};

// Reset Filters
const resetFilters = () => {
    searchQuery.value = '';
    filterStatus.value = '';
    filterType.value = '';
    fetchCoupons(1);
};

// Calculate Dashboard Stats Client-side
const stats = computed(() => {
    const total = statsCoupons.value.length;
    const now = new Date();

    let active = 0;
    let expired = 0;
    let totalUsed = 0;

    statsCoupons.value.forEach(c => {
        totalUsed += (c.used_count || 0);
        const starts = c.starts_at ? new Date(c.starts_at) : null;
        const expires = c.expires_at ? new Date(c.expires_at) : null;
        const isExpired = expires && expires < now;

        if (c.is_active && !isExpired && (!starts || starts <= now)) {
            active++;
        } else if (isExpired || !c.is_active) {
            expired++;
        }
    });

    return {
        total,
        active,
        expired,
        totalUsed
    };
});

onMounted(() => {
    fetchCoupons();
    fetchStatsData();
    
    const savedTheme = localStorage.getItem('theme');
    isDark.value = savedTheme === 'dark';
});

// Clipboard helper
const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `Đã sao chép mã: ${text}`,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
    }).catch(err => {
        console.error('Không thể sao chép văn bản: ', err);
    });
};

// Helper to format currency
const formatCurrency = (value) => {
    if (value === null || value === undefined || value === '') return '0 ₫';
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
};

// Helper to format date
const formatDate = (dateStr) => {
    if (!dateStr) return 'Vô thời hạn';
    const date = new Date(dateStr);
    return date.toLocaleString('vi-VN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Helper for status classes
const getStatusLabelAndClass = (coupon) => {
    const now = new Date();
    const starts = coupon.starts_at ? new Date(coupon.starts_at) : null;
    const expires = coupon.expires_at ? new Date(coupon.expires_at) : null;
    const isExpired = expires && expires < now;

    if (!coupon.is_active) {
        return {
            text: 'Tạm ẩn',
            class: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
        };
    }
    if (isExpired) {
        return {
            text: 'Hết hạn',
            class: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
        };
    }
    if (starts && starts > now) {
        return {
            text: 'Chờ chạy',
            class: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
        };
    }
    return {
        text: 'Đang hoạt động',
        class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
    };
};

// Open Add Modal
const openAddModal = () => {
    couponForm.value = JSON.parse(JSON.stringify(initialForm));
    errors.value = {};
    showConditions.value = false;
    isModalOpen.value = true;
};

// Open Edit Modal
const editCoupon = (coupon) => {
    errors.value = {};
    showConditions.value = !!(coupon.conditions && coupon.conditions.min_quantity);
    
    // Parse dates to input local timezone string (YYYY-MM-DDThh:mm)
    const formatForInput = (dateStr) => {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        const pad = (num) => String(num).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    };

    couponForm.value = {
        id: coupon.id,
        code: coupon.code,
        name: coupon.name || '',
        description: coupon.description || '',
        discount_type: coupon.discount_type,
        discount_value: coupon.discount_value,
        max_discount_amount: coupon.max_discount_amount || '',
        min_order_amount: coupon.min_order_amount || '',
        usage_limit: coupon.usage_limit || '',
        per_user_limit: coupon.per_user_limit || 1,
        is_active: !!coupon.is_active,
        starts_at: formatForInput(coupon.starts_at),
        expires_at: formatForInput(coupon.expires_at),
        conditions: {
            min_quantity: coupon.conditions?.min_quantity || '',
        }
    };

    isModalOpen.value = true;
};

// Quick Toggle is_active
const toggleCouponActive = async (coupon) => {
    try {
        const token = localStorage.getItem('token');
        const config = { headers: { Authorization: `Bearer ${token}` } };
        
        const response = await axios.patch(apiUrl(`/api/coupons/${coupon.id}`), {
            is_active: !coupon.is_active
        }, config);

        if (response.data.status === 'success') {
            coupon.is_active = !coupon.is_active;
            fetchStatsData();
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `Đã ${coupon.is_active ? 'kích hoạt' : 'hủy kích hoạt'} mã ${coupon.code}`,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }
    } catch (error) {
        console.error('Lỗi khi đổi trạng thái mã giảm giá:', error);
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Không thể cập nhật trạng thái.',
        });
    }
};

// Delete Coupon
const deleteCoupon = async (id) => {
    Swal.fire({
        title: 'Bạn có chắc chắn muốn xóa?',
        text: "Mã giảm giá này sẽ bị xóa vĩnh viễn khỏi hệ thống!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Đồng ý, xóa ngay!',
        cancelButtonText: 'Hủy bỏ',
        background: isDark.value ? '#1e293b' : '#ffffff',
        color: isDark.value ? '#f3f4f6' : '#111827'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const token = localStorage.getItem('token');
                const config = { headers: { Authorization: `Bearer ${token}` } };
                await axios.delete(apiUrl(`/api/coupons/${id}`), config);

                Swal.fire({
                    icon: 'success',
                    title: 'Đã xóa thành công!',
                    text: 'Mã giảm giá đã được gỡ bỏ.',
                    timer: 2000,
                    showConfirmButton: false
                });

                fetchCoupons(currentPage.value);
                fetchStatsData();
            } catch (error) {
                console.error('Lỗi khi xóa mã giảm giá:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không thể xóa mã giảm giá này.',
                });
            }
        }
    });
};

// Save Coupon (Create or Update)
const saveCoupon = async () => {
    submitLoading.value = true;
    errors.value = {};
    try {
        const token = localStorage.getItem('token');
        const config = { headers: { Authorization: `Bearer ${token}` } };

        // Clean values before sending
        const payload = {
            code: couponForm.value.code.toUpperCase().trim(),
            name: couponForm.value.name ? couponForm.value.name.trim() : null,
            description: couponForm.value.description ? couponForm.value.description.trim() : null,
            discount_type: couponForm.value.discount_type,
            discount_value: parseFloat(couponForm.value.discount_value),
            max_discount_amount: couponForm.value.max_discount_amount !== '' ? parseFloat(couponForm.value.max_discount_amount) : null,
            min_order_amount: couponForm.value.min_order_amount !== '' ? parseFloat(couponForm.value.min_order_amount) : null,
            usage_limit: couponForm.value.usage_limit !== '' ? parseInt(couponForm.value.usage_limit) : null,
            per_user_limit: couponForm.value.per_user_limit !== '' ? parseInt(couponForm.value.per_user_limit) : null,
            is_active: couponForm.value.is_active,
            starts_at: couponForm.value.starts_at || null,
            expires_at: couponForm.value.expires_at || null,
            conditions: showConditions.value && couponForm.value.conditions.min_quantity !== '' 
                ? { min_quantity: parseInt(couponForm.value.conditions.min_quantity) } 
                : null
        };

        let response;
        if (couponForm.value.id) {
            // Update
            response = await axios.patch(apiUrl(`/api/coupons/${couponForm.value.id}`), payload, config);
        } else {
            // Create
            response = await axios.post(apiUrl('/api/coupons'), payload, config);
        }

        if (response.data.status === 'success') {
            isModalOpen.value = false;
            Swal.fire({
                icon: 'success',
                title: couponForm.value.id ? 'Cập nhật thành công!' : 'Tạo mới thành công!',
                text: response.data.message || 'Mã giảm giá đã được lưu vào hệ thống.',
                timer: 2000,
                showConfirmButton: false
            });
            fetchCoupons(currentPage.value);
            fetchStatsData();
        }
    } catch (error) {
        console.error('Lỗi khi lưu mã giảm giá:', error);
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors || {};
            Swal.fire({
                icon: 'warning',
                title: 'Dữ liệu không hợp lệ',
                text: 'Vui lòng kiểm tra lại các thông tin lỗi bên dưới form.',
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: error.response?.data?.message || 'Có lỗi xảy ra khi lưu mã giảm giá.',
            });
        }
    } finally {
        submitLoading.value = false;
    }
};
</script>

<template>
  <div class="antialiased font-sans transition-colors duration-300">
    <div :class="isDark ? 'bg-[#0f172a] text-gray-100' : 'bg-gray-50 text-gray-900'" class="flex h-screen overflow-hidden">
      
      <!-- Component Navbar Trái -->
      <navbar_admin :isDark="isDark" :isSidebarOpen="isSidebarOpen" />

      <!-- Main Content Khung Bên Phải -->
      <div class="flex-1 flex flex-col overflow-hidden">
        
        <!-- Component Header Top -->
        <header_admin 
            :isDark="isDark" 
            @toggle-sidebar="isSidebarOpen = !isSidebarOpen" 
            @toggle-theme="toggleTheme" 
        />

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            
            <!-- Dashboard Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <!-- Stat 1: Total -->
                <div :class="isDark ? 'bg-gradient-to-br from-indigo-900/40 to-slate-800 border-indigo-500/20' : 'bg-gradient-to-br from-white to-indigo-50/20 border-indigo-100'" 
                     class="p-6 border shadow-sm rounded-xl transition-all duration-300 hover:scale-[1.02] flex items-center justify-between group">
                     <div>
                         <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider font-semibold mb-1">Tổng mã giảm giá</p>
                         <h3 class="text-3xl font-bold font-serif">{{ stats.total }}</h3>
                     </div>
                     <div class="w-12 h-12 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-500 group-hover:scale-110 transition-transform duration-300">
                         <i class="fa-solid fa-tags text-xl"></i>
                     </div>
                </div>

                <!-- Stat 2: Active -->
                <div :class="isDark ? 'bg-gradient-to-br from-emerald-900/40 to-slate-800 border-emerald-500/20' : 'bg-gradient-to-br from-white to-emerald-50/20 border-emerald-100'" 
                     class="p-6 border shadow-sm rounded-xl transition-all duration-300 hover:scale-[1.02] flex items-center justify-between group">
                     <div>
                         <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider font-semibold mb-1">Đang hoạt động</p>
                         <h3 class="text-3xl font-bold font-serif text-emerald-500">{{ stats.active }}</h3>
                     </div>
                     <div class="w-12 h-12 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform duration-300">
                         <i class="fa-solid fa-circle-check text-xl"></i>
                     </div>
                </div>

                <!-- Stat 3: Expired/Inactive -->
                <div :class="isDark ? 'bg-gradient-to-br from-amber-900/40 to-slate-800 border-amber-500/20' : 'bg-gradient-to-br from-white to-amber-50/20 border-amber-100'" 
                     class="p-6 border shadow-sm rounded-xl transition-all duration-300 hover:scale-[1.02] flex items-center justify-between group">
                     <div>
                         <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider font-semibold mb-1">Đã tắt / Hết hạn</p>
                         <h3 class="text-3xl font-bold font-serif text-amber-500">{{ stats.expired }}</h3>
                     </div>
                     <div class="w-12 h-12 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform duration-300">
                         <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                     </div>
                </div>

                <!-- Stat 4: Used Count -->
                <div :class="isDark ? 'bg-gradient-to-br from-violet-900/40 to-slate-800 border-violet-500/20' : 'bg-gradient-to-br from-white to-violet-50/20 border-violet-100'" 
                     class="p-6 border shadow-sm rounded-xl transition-all duration-300 hover:scale-[1.02] flex items-center justify-between group">
                     <div>
                         <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider font-semibold mb-1">Tổng lượt đã dùng</p>
                         <h3 class="text-3xl font-bold font-serif text-violet-500">{{ stats.totalUsed }}</h3>
                     </div>
                     <div class="w-12 h-12 rounded-lg bg-violet-500/10 flex items-center justify-center text-violet-500 group-hover:scale-110 transition-transform duration-300">
                         <i class="fa-solid fa-ticket text-xl"></i>
                     </div>
                </div>
            </div>

            <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-100'"
                class="w-full min-h-[500px] p-6 md:p-8 border shadow-sm rounded-xl transition-colors duration-300">

                <!-- Header Section -->
                <div :class="isDark ? 'border-gray-700' : 'border-gray-200'"
                    class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 pb-6 border-b gap-4">
                    <div>
                        <h2 :class="isDark ? 'text-white' : 'text-gray-900'"
                            class="text-3xl font-serif font-bold mb-2">Mã giảm giá</h2>
                        <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="font-light text-sm">
                            Quản lý các chương trình ưu đãi, voucher giảm giá toàn hệ thống cửa hàng.
                        </p>
                    </div>

                    <!-- Add Button -->
                    <button @click="openAddModal"
                        class="flex items-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-all duration-300 shadow-md shadow-blue-500/20 shrink-0 transform hover:scale-[1.02]">
                        <i class="fa-solid fa-plus mr-2 text-lg"></i> Thêm mã giảm giá
                    </button>
                </div>

                <!-- Filters & Search Bar -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <!-- Search Input -->
                    <div class="md:col-span-2 relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" v-model="searchQuery" @input="onSearchInput" placeholder="Tìm theo mã hoặc tên voucher..."
                            :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500/20' : 'bg-gray-50 border-gray-300 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500/20'"
                            class="w-full pl-10 pr-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all" />
                    </div>

                    <!-- Type Filter -->
                    <select v-model="filterType"
                        :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white focus:border-blue-500 focus:ring-blue-500/20' : 'bg-gray-50 border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500/20'"
                        class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all">
                        <option value="">Tất cả loại voucher</option>
                        <option value="percentage">Giảm theo phần trăm (%)</option>
                        <option value="fixed_amount">Giảm số tiền cố định (₫)</option>
                    </select>

                    <!-- Status Filter -->
                    <div class="flex gap-2">
                        <select v-model="filterStatus"
                            :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white focus:border-blue-500 focus:ring-blue-500/20' : 'bg-gray-50 border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500/20'"
                            class="flex-1 px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active">Đang hoạt động</option>
                            <option value="inactive">Tạm ẩn (Draft)</option>
                            <option value="expired">Đã hết hạn</option>
                        </select>
                        
                        <!-- Reset Button -->
                        <button @click="resetFilters" title="Đặt lại bộ lọc"
                            :class="isDark ? 'bg-gray-800 border-gray-600 text-gray-300 hover:bg-gray-700 hover:text-white' : 'bg-gray-100 border-gray-300 text-gray-600 hover:bg-gray-200 hover:text-gray-900'"
                            class="px-3 border rounded-lg text-sm transition-colors flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-arrows-rotate"></i>
                        </button>
                    </div>
                </div>

                <!-- Table Content -->
                <div v-if="loading" class="flex flex-col justify-center items-center py-16 space-y-4">
                    <div class="w-12 h-12 border-4 border-blue-600/30 border-t-blue-600 rounded-full animate-spin"></div>
                    <span :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-sm font-medium">Đang tải dữ liệu...</span>
                </div>

                <div v-else-if="coupons.length === 0" class="flex flex-col items-center justify-center py-16 space-y-4 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-500/10 flex items-center justify-center text-gray-400">
                        <i class="fa-solid fa-tags text-2xl"></i>
                    </div>
                    <div>
                        <h4 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-lg font-bold">Không tìm thấy mã giảm giá</h4>
                        <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-sm max-w-sm mt-1">
                            Vui lòng đổi từ khóa tìm kiếm hoặc bấm "Đặt lại bộ lọc" để hiển thị lại danh sách ban đầu.
                        </p>
                    </div>
                </div>

                <div v-else>
                    <div class="overflow-x-auto rounded-lg border shadow-inner" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                        <table class="w-full text-left border-collapse min-w-[1000px]">
                            <thead>
                                <tr :class="isDark ? 'bg-gray-800/60 text-gray-400 border-gray-700' : 'bg-gray-50/80 text-gray-600 border-gray-200'" class="border-b text-xs uppercase tracking-wider">
                                    <th class="px-5 py-4 font-semibold text-center w-14">STT</th>
                                    <th class="px-5 py-4 font-semibold">Mã Voucher</th>
                                    <th class="px-5 py-4 font-semibold w-72">Thông tin chung</th>
                                    <th class="px-5 py-4 font-semibold">Mức giảm</th>
                                    <th class="px-5 py-4 font-semibold">Sử dụng</th>
                                    <th class="px-5 py-4 font-semibold">Đơn tối thiểu</th>
                                    <th class="px-5 py-4 font-semibold">Hiệu lực</th>
                                    <th class="px-5 py-4 font-semibold text-center">Trạng thái</th>
                                    <th class="px-5 py-4 font-semibold text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody :class="isDark ? 'divide-gray-700' : 'divide-gray-200'" class="divide-y text-sm">
                                <tr v-for="(coupon, index) in coupons" :key="coupon.id || index" 
                                    :class="isDark ? 'hover:bg-gray-800/20' : 'hover:bg-gray-50/50'" class="transition-colors group">
                                    
                                    <!-- STT -->
                                    <td class="px-5 py-4 text-center font-medium" :class="isDark ? 'text-gray-400' : 'text-gray-500'">
                                        #{{ (currentPage - 1) * perPage + index + 1 }}
                                    </td>
                                    
                                    <!-- Code -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <span :class="isDark ? 'bg-indigo-950 text-indigo-300 border-indigo-800' : 'bg-indigo-50 text-indigo-700 border-indigo-200'" 
                                                  class="px-2.5 py-1 text-xs font-mono font-bold uppercase rounded border border-dashed flex items-center">
                                                {{ coupon.code }}
                                            </span>
                                            <button @click="copyToClipboard(coupon.code)" title="Copy mã"
                                                class="text-gray-400 hover:text-indigo-600 transition-colors p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                                                <i class="fa-regular fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    
                                    <!-- Info -->
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-sm" :class="isDark ? 'text-gray-200' : 'text-gray-900'">{{ coupon.name || 'Không có tên' }}</div>
                                        <div class="text-xs font-light mt-0.5 max-w-xs truncate" :class="isDark ? 'text-gray-400' : 'text-gray-500'" :title="coupon.description">
                                            {{ coupon.description || 'Không có mô tả chi tiết.' }}
                                        </div>
                                        <!-- Conditions Badge if exists -->
                                        <div v-if="coupon.conditions && coupon.conditions.min_quantity" class="mt-1">
                                            <span class="inline-flex items-center text-[10px] bg-amber-500/10 text-amber-500 border border-amber-500/20 px-1.5 py-0.5 rounded font-medium">
                                                <i class="fa-solid fa-cart-shopping mr-1"></i> Số lượng từ: {{ coupon.conditions.min_quantity }} cái
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <!-- Discount Value -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-sm" :class="isDark ? 'text-gray-100' : 'text-gray-900'">
                                                {{ coupon.discount_type === 'percentage' ? `${parseFloat(coupon.discount_value)}%` : formatCurrency(coupon.discount_value) }}
                                            </span>
                                            <span v-if="coupon.discount_type === 'percentage' && coupon.max_discount_amount" 
                                                  class="text-[11px]" :class="isDark ? 'text-gray-400' : 'text-gray-500'">
                                                Tối đa: {{ formatCurrency(coupon.max_discount_amount) }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Usage Limit -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex flex-col justify-center max-w-[120px]">
                                            <div class="flex justify-between text-xs mb-1" :class="isDark ? 'text-gray-300' : 'text-gray-600'">
                                                <span class="font-bold">{{ coupon.used_count || 0 }}</span>
                                                <span class="text-gray-400">/ {{ coupon.usage_limit || '∞' }} lượt</span>
                                            </div>
                                            <!-- Usage Progress bar -->
                                            <div v-if="coupon.usage_limit" class="w-full bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                                <div :class="(coupon.used_count / coupon.usage_limit) >= 0.9 ? 'bg-red-500' : (coupon.used_count / coupon.usage_limit) >= 0.7 ? 'bg-amber-500' : 'bg-emerald-500'"
                                                     class="h-full rounded-full transition-all duration-300"
                                                     :style="{ width: `${Math.min((coupon.used_count / coupon.usage_limit) * 100, 100)}%` }">
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Minimum Order Amount -->
                                    <td class="px-5 py-4 whitespace-nowrap font-medium text-sm" :class="isDark ? 'text-gray-200' : 'text-gray-800'">
                                        {{ coupon.min_order_amount ? formatCurrency(coupon.min_order_amount) : 'Không yêu cầu' }}
                                    </td>

                                    <!-- Validity (Starts/Expires) -->
                                    <td class="px-5 py-4 text-xs">
                                        <div class="flex items-center space-x-1 text-gray-500 dark:text-gray-400">
                                            <i class="fa-regular fa-calendar-plus"></i>
                                            <span>{{ formatDate(coupon.starts_at) }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1 text-gray-500 dark:text-gray-400 mt-1 font-medium">
                                            <i class="fa-regular fa-calendar-minus"></i>
                                            <span :class="coupon.expires_at && new Date(coupon.expires_at) < new Date() ? 'text-red-500 font-bold' : ''">
                                                {{ formatDate(coupon.expires_at) }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center space-y-1.5">
                                            <span :class="getStatusLabelAndClass(coupon).class" class="px-2.5 py-0.5 text-xs font-semibold rounded-full shadow-sm">
                                                {{ getStatusLabelAndClass(coupon).text }}
                                            </span>
                                            <!-- Interactive Toggle Switch -->
                                            <button @click="toggleCouponActive(coupon)" title="Nhấn để Bật/Tắt kích hoạt"
                                                class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                                :class="coupon.is_active ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'">
                                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                    :class="coupon.is_active ? 'translate-x-4' : 'translate-x-0'">
                                                </span>
                                            </button>
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-5 py-4 text-right whitespace-nowrap space-x-3">
                                        <button @click="editCoupon(coupon)" class="text-blue-500 hover:text-blue-700 transition-colors p-1 hover:bg-blue-500/10 rounded" title="Sửa mã giảm giá">
                                            <i class="fa-regular fa-pen-to-square text-lg"></i>
                                        </button>
                                        <button @click="deleteCoupon(coupon.id)" class="text-red-500 hover:text-red-700 transition-colors p-1 hover:bg-red-500/10 rounded" title="Xóa mã giảm giá">
                                            <i class="fa-regular fa-trash-can text-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Section -->
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 pt-4 gap-4">
                        <span :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-xs font-light">
                            Hiển thị từ <span class="font-bold text-blue-500">{{ Math.min((currentPage - 1) * perPage + 1, totalCoupons) }}</span> 
                            đến <span class="font-bold text-blue-500">{{ Math.min(currentPage * perPage, totalCoupons) }}</span> 
                            trong tổng số <span class="font-bold text-blue-500">{{ totalCoupons }}</span> kết quả
                        </span>

                        <div class="flex items-center space-x-1">
                            <!-- Prev Page -->
                            <button @click="fetchCoupons(currentPage - 1)" :disabled="currentPage === 1"
                                :class="isDark ? 'bg-gray-800 border-gray-700 hover:bg-gray-700 text-gray-300 disabled:opacity-30 disabled:hover:bg-gray-800' : 'bg-white border-gray-300 hover:bg-gray-50 text-gray-700 disabled:opacity-40 disabled:hover:bg-white'"
                                class="px-3 py-1.5 border rounded-lg text-xs font-semibold transition-colors flex items-center justify-center">
                                <i class="fa-solid fa-chevron-left mr-1"></i> Trước
                            </button>

                            <!-- Pages Buttons -->
                            <button v-for="page in totalPages" :key="page" @click="fetchCoupons(page)"
                                :class="currentPage === page 
                                    ? 'bg-blue-600 text-white border-blue-600 shadow-sm' 
                                    : (isDark ? 'bg-gray-800 border-gray-700 text-gray-300 hover:bg-gray-700' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50')"
                                class="w-8 h-8 border rounded-lg text-xs font-bold transition-all">
                                {{ page }}
                            </button>

                            <!-- Next Page -->
                            <button @click="fetchCoupons(currentPage + 1)" :disabled="currentPage === totalPages"
                                :class="isDark ? 'bg-gray-800 border-gray-700 hover:bg-gray-700 text-gray-300 disabled:opacity-30 disabled:hover:bg-gray-800' : 'bg-white border-gray-300 hover:bg-gray-50 text-gray-700 disabled:opacity-40 disabled:hover:bg-white'"
                                class="px-3 py-1.5 border rounded-lg text-xs font-semibold transition-colors flex items-center justify-center">
                                Sau <i class="fa-solid fa-chevron-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- ADD & EDIT MODAL -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity">
            <div :class="isDark ? 'bg-[#1e293b] border-gray-700 shadow-gray-950/50' : 'bg-white border-gray-200 shadow-2xl'" 
                class="w-full max-w-2xl rounded-xl border overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div :class="isDark ? 'border-gray-700 bg-gray-800/40' : 'border-gray-100 bg-gray-50'" class="flex justify-between items-center px-6 py-4 border-b">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                        <h3 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-lg font-bold font-serif">
                            {{ couponForm.id ? 'Sửa thông tin mã giảm giá' : 'Thêm mã giảm giá mới' }}
                        </h3>
                    </div>
                    <button @click="isModalOpen = false" :class="isDark ? 'text-gray-400 hover:text-white border-gray-700' : 'text-gray-500 hover:text-gray-900 border-gray-200'" class="w-8 h-8 flex items-center justify-center rounded-lg border transition-colors bg-white dark:bg-[#0f172a]">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                
                <!-- Modal Body (Scrollable) -->
                <div class="px-6 py-6 overflow-y-auto space-y-5 flex-1">
                    
                    <!-- Row 1: Code & Name -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-xs font-semibold uppercase tracking-wider mb-1.5">Mã Coupon <span class="text-red-500">*</span></label>
                            <input type="text" v-model="couponForm.code" placeholder="VD: NHAPMOI10"
                                :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-600 focus:border-blue-500 focus:ring-blue-500/20' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500/20'"
                                class="w-full px-4 py-2 border rounded-lg text-sm font-mono font-bold focus:outline-none focus:ring-4 transition-all" />
                            <span v-if="errors.code" class="text-red-500 text-xs mt-1 block font-medium">{{ errors.code[0] }}</span>
                        </div>
                        <div>
                            <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-xs font-semibold uppercase tracking-wider mb-1.5">Tên hiển thị <span class="text-red-500">*</span></label>
                            <input type="text" v-model="couponForm.name" placeholder="VD: Khách hàng mới"
                                :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-600 focus:border-blue-500 focus:ring-blue-500/20' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500/20'"
                                class="w-full px-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all" />
                            <span v-if="errors.name" class="text-red-500 text-xs mt-1 block font-medium">{{ errors.name[0] }}</span>
                        </div>
                    </div>

                    <!-- Row 2: Description -->
                    <div>
                        <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-xs font-semibold uppercase tracking-wider mb-1.5">Mô tả chi tiết</label>
                        <textarea v-model="couponForm.description" rows="2" placeholder="Áp dụng giảm 10% cho đơn đầu tiên từ 200.000₫..."
                            :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-600 focus:border-blue-500 focus:ring-blue-500/20' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500/20'"
                            class="w-full px-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all resize-none"></textarea>
                        <span v-if="errors.description" class="text-red-500 text-xs mt-1 block font-medium">{{ errors.description[0] }}</span>
                    </div>

                    <!-- Row 3: Discount Config (Discount Type, Value, Max Amount) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-xl" :class="isDark ? 'bg-slate-800/40 border border-slate-700' : 'bg-gray-50 border border-gray-100'">
                        <div>
                            <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-xs font-semibold uppercase tracking-wider mb-1.5">Loại giảm giá</label>
                            <select v-model="couponForm.discount_type"
                                :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white focus:border-blue-500 focus:ring-blue-500/20' : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500/20'"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all">
                                <option value="percentage">Phần trăm (%)</option>
                                <option value="fixed_amount">Số tiền cố định (₫)</option>
                            </select>
                            <span v-if="errors.discount_type" class="text-red-500 text-xs mt-1 block font-medium">{{ errors.discount_type[0] }}</span>
                        </div>
                        <div>
                            <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-xs font-semibold uppercase tracking-wider mb-1.5">
                                Giá trị giảm <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" step="any" v-model="couponForm.discount_value" placeholder="10 hoặc 50000"
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-600 focus:border-blue-500 focus:ring-blue-500/20' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500/20'"
                                    class="w-full pl-3 pr-8 py-2 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all" />
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-gray-400">
                                    {{ couponForm.discount_type === 'percentage' ? '%' : '₫' }}
                                </span>
                            </div>
                            <span v-if="errors.discount_value" class="text-red-500 text-xs mt-1 block font-medium">{{ errors.discount_value[0] }}</span>
                        </div>
                        <div>
                            <label :class="[
                                       isDark ? 'text-gray-300 font-semibold' : 'text-gray-700 font-semibold',
                                       couponForm.discount_type !== 'percentage' ? 'opacity-40 pointer-events-none' : ''
                                   ]" 
                                   class="block text-xs uppercase tracking-wider mb-1.5">
                                Giảm tối đa (Lên tới)
                            </label>
                            <div class="relative" :class="couponForm.discount_type !== 'percentage' ? 'opacity-40 pointer-events-none' : ''">
                                <input type="number" step="any" v-model="couponForm.max_discount_amount" placeholder="VD: 50000" :disabled="couponForm.discount_type !== 'percentage'"
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-600 focus:border-blue-500 focus:ring-blue-500/20' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500/20'"
                                    class="w-full pl-3 pr-8 py-2 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all" />
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-gray-400">₫</span>
                            </div>
                            <span v-if="errors.max_discount_amount" class="text-red-500 text-xs mt-1 block font-medium">{{ errors.max_discount_amount[0] }}</span>
                        </div>
                    </div>

                    <!-- Row 4: Min Order & Usage Limits -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-xs font-semibold uppercase tracking-wider mb-1.5">Giá trị đơn tối thiểu</label>
                            <div class="relative">
                                <input type="number" step="any" v-model="couponForm.min_order_amount" placeholder="Không yêu cầu"
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-600 focus:border-blue-500 focus:ring-blue-500/20' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500/20'"
                                    class="w-full pl-3 pr-8 py-2 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all" />
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-gray-400">₫</span>
                            </div>
                            <span v-if="errors.min_order_amount" class="text-red-500 text-xs mt-1 block font-medium">{{ errors.min_order_amount[0] }}</span>
                        </div>
                        <div>
                            <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-xs font-semibold uppercase tracking-wider mb-1.5">Tổng số lượt dùng</label>
                            <input type="number" v-model="couponForm.usage_limit" placeholder="Không giới hạn"
                                :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-600 focus:border-blue-500 focus:ring-blue-500/20' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500/20'"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all" />
                            <span v-if="errors.usage_limit" class="text-red-500 text-xs mt-1 block font-medium">{{ errors.usage_limit[0] }}</span>
                        </div>
                        <div>
                            <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-xs font-semibold uppercase tracking-wider mb-1.5">Lượt dùng / Mỗi User</label>
                            <input type="number" v-model="couponForm.per_user_limit" placeholder="Mặc định: 1"
                                :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-600 focus:border-blue-500 focus:ring-blue-500/20' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500/20'"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all" />
                            <span v-if="errors.per_user_limit" class="text-red-500 text-xs mt-1 block font-medium">{{ errors.per_user_limit[0] }}</span>
                        </div>
                    </div>

                    <!-- Row 5: Validity Dates -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-xs font-semibold uppercase tracking-wider mb-1.5">Thời gian bắt đầu</label>
                            <input type="datetime-local" v-model="couponForm.starts_at"
                                :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white focus:border-blue-500 focus:ring-blue-500/20' : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500/20'"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all" />
                            <span v-if="errors.starts_at" class="text-red-500 text-xs mt-1 block font-medium">{{ errors.starts_at[0] }}</span>
                        </div>
                        <div>
                            <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-xs font-semibold uppercase tracking-wider mb-1.5">Thời gian kết thúc</label>
                            <input type="datetime-local" v-model="couponForm.expires_at"
                                :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white focus:border-blue-500 focus:ring-blue-500/20' : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500/20'"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all" />
                            <span v-if="errors.expires_at" class="text-red-500 text-xs mt-1 block font-medium">{{ errors.expires_at[0] }}</span>
                        </div>
                    </div>

                    <!-- Advanced Conditions Collapsible Section -->
                    <div class="border rounded-xl overflow-hidden" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                        <button type="button" @click="showConditions = !showConditions"
                            :class="isDark ? 'bg-gray-800/40 text-gray-200' : 'bg-gray-50 text-gray-700'"
                            class="w-full px-4 py-3 flex justify-between items-center text-xs font-semibold uppercase tracking-wider transition-colors">
                            <span><i class="fa-solid fa-sliders mr-1.5 text-blue-500"></i> Cấu hình điều kiện bổ sung (Nâng cao)</span>
                            <i class="fa-solid transition-transform duration-200" :class="showConditions ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                        
                        <div v-show="showConditions" class="p-4 space-y-4" :class="isDark ? 'bg-slate-800/20' : 'bg-white'">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-xs font-semibold uppercase tracking-wider mb-1.5">
                                        Số lượng sản phẩm tối thiểu trong giỏ
                                    </label>
                                    <input type="number" v-model="couponForm.conditions.min_quantity" placeholder="Ví dụ: 2 cái"
                                        :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white focus:border-blue-500 focus:ring-blue-500/20' : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500/20'"
                                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all" />
                                    <span v-if="errors['conditions.min_quantity']" class="text-red-500 text-xs mt-1 block font-medium">{{ errors['conditions.min_quantity'][0] }}</span>
                                </div>
                                <div class="flex items-center">
                                    <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-xs italic leading-relaxed mt-4">
                                        * Điều kiện nâng cao giúp lọc chính xác điều kiện áp dụng giỏ hàng. Nhập trống để bỏ qua.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 6: Active Status Toggle -->
                    <div class="flex items-center space-x-3 p-4 rounded-xl border border-dashed" :class="isDark ? 'border-gray-700 bg-slate-800/10' : 'border-gray-200 bg-blue-50/10'">
                        <button type="button" @click="couponForm.is_active = !couponForm.is_active"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                            :class="couponForm.is_active ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'">
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                :class="couponForm.is_active ? 'translate-x-5' : 'translate-x-0'">
                            </span>
                        </button>
                        <div>
                            <span :class="isDark ? 'text-gray-200' : 'text-gray-900'" class="text-sm font-semibold block">Trạng thái phát hành</span>
                            <span :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-xs">
                                {{ couponForm.is_active ? 'Cho phép người dùng áp dụng mã giảm giá này khi thanh toán.' : 'Mã giảm giá ở dạng nháp, người dùng chưa thể sử dụng.' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div :class="isDark ? 'border-gray-700 bg-gray-800/40' : 'border-gray-100 bg-gray-50'" class="px-6 py-4 border-t flex justify-end space-x-3">
                    <button @click="isModalOpen = false" :disabled="submitLoading"
                        :class="isDark ? 'bg-gray-800 text-gray-300 hover:bg-gray-700 border-gray-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                        class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors border shadow-sm focus:outline-none">
                        Hủy bỏ
                    </button>
                    <button @click="saveCoupon" :disabled="submitLoading"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-all duration-300 shadow-md shadow-blue-500/10 flex items-center justify-center min-w-[120px]">
                        <span v-if="submitLoading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span>
                        {{ couponForm.id ? 'Lưu cập nhật' : 'Tạo voucher' }}
                    </button>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap');

.font-serif {
  font-family: 'Playfair Display', serif;
}
.font-sans {
  font-family: 'Inter', sans-serif;
}
</style>
