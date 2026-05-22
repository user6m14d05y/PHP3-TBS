<script setup>
import header_admin from '../Includes/Layouts/Header_Admin.vue';
import navbar_admin from '../Includes/Layouts/Navbar_Admin.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const isDark = ref(false);
const isSidebarOpen = ref(true);

// ========== STATE ==========
const products   = ref([]);
const categories = ref([]);
const categoryItems = ref([]);
const sizes      = ref([]);
const currentPage = ref(1);
const perPage = ref(12);
const totalProducts = ref(0);
const lastPage = ref(1);

const isModalOpen = ref(false);
const isEditMode  = ref(false);
const editingId   = ref(null);
const maxUploadSize = 60 * 1024 * 1024;

// Form sản phẩm
const form = ref({
    name: '',
    description: '',
    category_id: '',
    category_item_id: '',
    is_active: true,
    thumbnail: null,
    thumbnailPreview: null,
    gallery: [],         // File objects
    galleryPreviews: [], // Preview URLs
    existingImages: [],  // Ảnh đã có trên server (khi edit)
    variants: [{ size_id: '', price: '', sale_price: '', stock: 0 }],
});

// ========== FETCH DATA ==========
const fetchProducts = async (page = currentPage.value) => {
    try {
        const res = await axios.get('http://localhost:8888/api/product', {
            params: {
                page,
                limit: perPage.value,
            },
        });

        products.value = res.data.data;
        currentPage.value = res.data.current_page;
        perPage.value = res.data.per_page;
        totalProducts.value = res.data.total;
        lastPage.value = res.data.last_page;
    } catch (e) {
        console.error('Lỗi khi tải sản phẩm:', e);
    }
};

const changePage = (page) => {
    if (page < 1 || page > lastPage.value || page === currentPage.value) return;
    fetchProducts(page);
};

const fetchCategories = async () => {
    try {
        const res = await axios.get('http://localhost:8888/api/category');
        categories.value = res.data.data;
    } catch (e) {
        console.error('Lỗi khi tải danh mục:', e);
    }
};

const fetchCategoryItems = async (categoryId) => {
    if (!categoryId) { categoryItems.value = []; return; }
    try {
        const res = await axios.get(`http://localhost:8888/api/category-item?category_id=${categoryId}`);
        categoryItems.value = res.data.data;
    } catch (e) {
        console.error('Lỗi khi tải danh mục con:', e);
    }
};

const fetchSizes = async () => {
    try {
        const res = await axios.get('http://localhost:8888/api/size');
        sizes.value = res.data.data;
    } catch (e) {
        console.error('Lỗi khi tải size:', e);
    }
};

// ========== MODAL ==========
const resetForm = () => {
    form.value = {
        name: '', description: '', category_id: '', category_item_id: '',
        is_active: true, thumbnail: null, thumbnailPreview: null,
        gallery: [], galleryPreviews: [], existingImages: [],
        variants: [{ size_id: '', price: '', sale_price: '', stock: 0 }],
    };
    categoryItems.value = [];
};

const openAddModal = () => {
    resetForm();
    isEditMode.value = false;
    editingId.value  = null;
    isModalOpen.value = true;
};

const openEditModal = async (product) => {
    resetForm();
    isEditMode.value  = true;
    editingId.value   = product.id;

    form.value.name             = product.name;
    form.value.description      = product.description || '';
    form.value.category_id      = product.category_id || '';
    form.value.category_item_id = product.category_item_id || '';
    form.value.is_active        = product.is_active;
    form.value.thumbnailPreview = product.thumbnail
        ? `http://localhost:8888/images/${product.thumbnail}`
        : null;
    form.value.existingImages   = product.images || [];

    form.value.variants = product.variants && product.variants.length > 0
        ? product.variants.map(v => ({
            size_id:    v.size_id || '',
            price:      v.price,
            sale_price: v.sale_price || '',
            stock:      v.stock,
        }))
        : [{ size_id: '', price: '', sale_price: '', stock: 0 }];

    if (product.category_id) {
        await fetchCategoryItems(product.category_id);
    }

    isModalOpen.value = true;
};

const closeModal = () => { isModalOpen.value = false; };

// ========== UPLOAD ẢNH ==========
const getPendingUploadSize = (files = []) => {
    const thumbnailSize = form.value.thumbnail?.size || 0;
    const gallerySize = form.value.gallery.reduce((total, file) => total + file.size, 0);
    const newFilesSize = files.reduce((total, file) => total + file.size, 0);

    return thumbnailSize + gallerySize + newFilesSize;
};

const handleThumbnail = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const currentThumbnailSize = form.value.thumbnail?.size || 0;
    if (getPendingUploadSize([file]) - currentThumbnailSize > maxUploadSize) {
        Swal.fire({
            icon: 'warning',
            title: 'Ảnh quá lớn',
            text: 'Tổng dung lượng ảnh upload tối đa là 60MB. Vui lòng giảm số lượng hoặc nén ảnh.',
            confirmButtonColor: '#3b82f6',
        });
        e.target.value = '';
        return;
    }

    form.value.thumbnail = file;
    form.value.thumbnailPreview = URL.createObjectURL(file);
};

const handleGallery = (e) => {
    const files = Array.from(e.target.files);

    if (getPendingUploadSize(files) > maxUploadSize) {
        Swal.fire({
            icon: 'warning',
            title: 'Ảnh quá lớn',
            text: 'Tổng dung lượng ảnh upload tối đa là 60MB. Vui lòng giảm số lượng hoặc nén ảnh.',
            confirmButtonColor: '#3b82f6',
        });
        e.target.value = '';
        return;
    }

    files.forEach(file => {
        form.value.gallery.push(file);
        form.value.galleryPreviews.push(URL.createObjectURL(file));
    });
    // Reset input để có thể chọn lại cùng file
    e.target.value = '';
};

const removeNewGallery = (index) => {
    form.value.gallery.splice(index, 1);
    form.value.galleryPreviews.splice(index, 1);
};

const removeExistingImage = async (image, index) => {
    const confirm = await Swal.fire({
        title: 'Xóa ảnh này?',
        text: 'Ảnh sẽ bị xóa vĩnh viễn!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonText: 'Hủy',
        confirmButtonText: 'Xóa',
    });
    if (!confirm.isConfirmed) return;

    try {
        await axios.delete(`http://localhost:8888/api/product/image/${image.id}`);
        form.value.existingImages.splice(index, 1);
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Xóa ảnh thành công', showConfirmButton: false, timer: 2000 });
    } catch (e) {
        console.error('Lỗi xóa ảnh:', e);
    }
};

// ========== VARIANTS ==========
const addVariant = () => {
    form.value.variants.push({ size_id: '', price: '', sale_price: '', stock: 0 });
};

const removeVariant = (index) => {
    if (form.value.variants.length > 1) {
        form.value.variants.splice(index, 1);
    }
};

// ========== SAVE ==========
const saveProduct = async () => {
    if (!form.value.name.trim()) {
        Swal.fire({ icon: 'warning', title: 'Thiếu tên sản phẩm', text: 'Vui lòng nhập tên sản phẩm!', confirmButtonColor: '#3b82f6' });
        return;
    }

    const hasEmptyPrice = form.value.variants.some(v => !v.price || v.price <= 0);
    if (hasEmptyPrice) {
        Swal.fire({ icon: 'warning', title: 'Thiếu giá biến thể', text: 'Mỗi biến thể phải có giá!', confirmButtonColor: '#3b82f6' });
        return;
    }

    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('description', form.value.description);
    formData.append('category_id', form.value.category_id);
    formData.append('category_item_id', form.value.category_item_id);
    formData.append('is_active', form.value.is_active ? 1 : 0);

    if (form.value.thumbnail) {
        formData.append('thumbnail', form.value.thumbnail);
    }

    form.value.gallery.forEach(file => {
        formData.append('gallery[]', file);
    });

    formData.append('variants', JSON.stringify(form.value.variants));

    try {
        if (isEditMode.value) {
            await axios.post(`http://localhost:8888/api/product/update/${editingId.value}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        } else {
            await axios.post('http://localhost:8888/api/product', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        }

        closeModal();
        await fetchProducts();

        Swal.fire({
            toast: true, position: 'top-end', icon: 'success',
            title: isEditMode.value ? 'Cập nhật sản phẩm thành công!' : 'Thêm sản phẩm thành công!',
            showConfirmButton: false, timer: 3000, timerProgressBar: true
        });
    } catch (e) {
        console.error('Lỗi khi lưu sản phẩm:', e);

        const message = e.response?.status === 413
            ? 'Dung lượng ảnh upload quá lớn. Vui lòng giảm số lượng hoặc nén ảnh trước khi lưu.'
            : e.response?.data?.message || 'Có lỗi xảy ra, kiểm tra lại dữ liệu!';

        Swal.fire({ icon: 'error', title: 'Lỗi!', text: message, confirmButtonColor: '#3b82f6' });
    }
};

// ========== DELETE ==========
const deleteProduct = async (id) => {
    const result = await Swal.fire({
        title: 'Xóa sản phẩm này?',
        text: 'Tất cả biến thể và ảnh sẽ bị xóa theo!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonText: 'Hủy',
        confirmButtonText: 'Xóa',
    });
    if (!result.isConfirmed) return;

    try {
        await axios.delete(`http://localhost:8888/api/product/${id}`);
        await fetchProducts();
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Xóa sản phẩm thành công!', showConfirmButton: false, timer: 3000, timerProgressBar: true });
    } catch (e) {
        console.error('Lỗi khi xóa:', e);
    }
};

// ========== HELPERS ==========
const formatPrice = (price) => {
    if (!price) return '—';
    return Number(price).toLocaleString('vi-VN') + 'đ';
};

const getPriceRange = (variants) => {
    if (!variants || variants.length === 0) return 'Chưa có giá';
    const prices = variants.map(v => Number(v.sale_price || v.price));
    const min = Math.min(...prices);
    const max = Math.max(...prices);
    return min === max ? formatPrice(min) : `${formatPrice(min)} — ${formatPrice(max)}`;
};

const toggleTheme = () => {
    isDark.value = !isDark.value;
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    isDark.value = savedTheme === 'dark';
    fetchProducts();
    fetchCategories();
    fetchSizes();
});
</script>

<template>
    <div class="antialiased font-sans transition-colors duration-300">
        <div :class="isDark ? 'bg-[#0f172a] text-gray-100' : 'bg-gray-50 text-gray-900'" class="flex h-screen overflow-hidden">

            <navbar_admin :isDark="isDark" :isSidebarOpen="isSidebarOpen" />

            <div class="flex-1 flex flex-col overflow-hidden relative">
                <header_admin :isDark="isDark" @toggle-sidebar="isSidebarOpen = !isSidebarOpen" @toggle-theme="toggleTheme" />

                <main class="flex-1 overflow-y-auto p-4 md:p-8">
                    <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-100'"
                        class="w-full p-6 md:p-8 border shadow-sm rounded-xl transition-colors duration-300">

                        <!-- Header -->
                        <div :class="isDark ? 'border-gray-700' : 'border-gray-200'"
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-6 border-b gap-4">
                            <div>
                                <h2 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-3xl font-serif font-bold mb-2">Sản phẩm</h2>
                                <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="font-light text-sm">Quản lý sản phẩm và biến thể (size, giá).</p>
                            </div>
                            <button @click="openAddModal"
                                class="flex items-center px-4 py-2.5 bg-pink-600 hover:bg-pink-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm shrink-0">
                                <i class="fa-solid fa-plus mr-2 text-lg"></i> Thêm sản phẩm
                            </button>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto rounded-lg border" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr :class="isDark ? 'bg-gray-800/50 text-gray-400 border-gray-700' : 'bg-gray-50 text-gray-600 border-gray-200'"
                                        class="border-b text-xs uppercase tracking-wider">
                                        <th class="px-4 py-4 font-semibold w-12">STT</th>
                                        <th class="px-4 py-4 font-semibold w-20">Ảnh</th>
                                        <th class="px-4 py-4 font-semibold">Tên sản phẩm</th>
                                        <th class="px-4 py-4 font-semibold">Danh mục</th>
                                        <th class="px-4 py-4 font-semibold">Khoảng giá</th>
                                        <th class="px-4 py-4 font-semibold">Biến thể</th>
                                        <th class="px-4 py-4 font-semibold text-center">Trạng thái</th>
                                        <th class="px-4 py-4 font-semibold text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody :class="isDark ? 'divide-gray-700' : 'divide-gray-200'" class="divide-y">
                                    <tr v-if="products.length === 0">
                                        <td colspan="8" class="text-center py-16 text-gray-400">
                                            <i class="fa-solid fa-box-open text-4xl mb-3 block opacity-40"></i>
                                            <p>Chưa có sản phẩm nào. Nhấn "Thêm sản phẩm" để bắt đầu.</p>
                                        </td>
                                    </tr>
                                    <tr v-for="(product, index) in products" :key="product.id"
                                        :class="isDark ? 'hover:bg-gray-800/30' : 'hover:bg-gray-50'" class="transition-colors">
                                        <td class="px-4 py-4 text-sm" :class="isDark ? 'text-gray-400' : 'text-gray-500'">#{{ (currentPage - 1) * perPage + index + 1 }}</td>
                                        <td class="px-4 py-4">
                                            <router-link :to="'/product/' + product.slug" class="block w-14 h-14 rounded-lg overflow-hidden border hover:border-pink-500 hover:scale-105 transition-all duration-300" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                                                <img v-if="product.thumbnail"
                                                    :src="`http://localhost:8888/images/${product.thumbnail}`"
                                                    class="w-full h-full object-cover" :alt="product.name" />
                                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-100">
                                                    <i class="fa-regular fa-image text-gray-400 text-xl"></i>
                                                </div>
                                            </router-link>
                                        </td>
                                        <td class="px-4 py-4">
                                            <router-link :to="'/product/' + product.slug" class="group/name">
                                                <p class="font-semibold text-sm group-hover/name:text-pink-600 transition-colors" :class="isDark ? 'text-white' : 'text-gray-900'">{{ product.name }}</p>
                                            </router-link>
                                            <p class="text-xs mt-0.5" :class="isDark ? 'text-gray-500' : 'text-gray-400'">{{ product.images?.length || 0 }} ảnh gallery</p>
                                        </td>
                                        <td class="px-4 py-4 text-sm" :class="isDark ? 'text-gray-300' : 'text-gray-700'">
                                            <span class="font-medium">{{ product.category?.name || '—' }}</span>
                                            <span v-if="product.category_item" class="text-xs block" :class="isDark ? 'text-gray-500' : 'text-gray-400'">
                                                └ {{ product.category_item.name }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-emerald-500">
                                            {{ getPriceRange(product.variants) }}
                                        </td>
                                        <td class="px-4 py-4 text-sm" :class="isDark ? 'text-gray-300' : 'text-gray-700'">
                                            <span v-if="product.variants?.length > 0">
                                                <span v-for="v in product.variants" :key="v.id"
                                                    class="inline-block mr-1 mb-1 px-2 py-0.5 rounded text-xs font-medium"
                                                    :class="isDark ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'">
                                                    {{ v.size?.name || 'N/A' }}
                                                </span>
                                            </span>
                                            <span v-else class="text-gray-400 text-xs">Chưa có</span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
                                                :class="product.is_active
                                                    ? 'bg-emerald-100 text-emerald-700'
                                                    : 'bg-red-100 text-red-700'">
                                                {{ product.is_active ? 'Hiển thị' : 'Ẩn' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right space-x-3 whitespace-nowrap">
                                            <button @click="openEditModal(product)" class="text-blue-500 hover:text-blue-700 transition-colors">
                                                <i class="fa-regular fa-pen-to-square text-lg"></i>
                                            </button>
                                            <button @click="deleteProduct(product.id)" class="text-red-500 hover:text-red-700 transition-colors">
                                                <i class="fa-regular fa-trash-can text-lg"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="totalProducts > 0" class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                            <p class="text-sm" :class="isDark ? 'text-gray-400' : 'text-gray-500'">
                                Hiển thị
                                <span class="font-semibold">{{ (currentPage - 1) * perPage + 1 }}</span>
                                -
                                <span class="font-semibold">{{ Math.min(currentPage * perPage, totalProducts) }}</span>
                                trong tổng
                                <span class="font-semibold">{{ totalProducts }}</span>
                                sản phẩm
                            </p>

                            <div class="flex items-center gap-2">
                                <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                                    class="px-3 py-2 rounded-lg border text-sm font-medium disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                                    :class="isDark ? 'border-gray-700 text-gray-300 hover:bg-gray-800' : 'border-gray-200 text-gray-700 hover:bg-gray-50'">
                                    Trước
                                </button>

                                <button v-for="page in lastPage" :key="page" @click="changePage(page)"
                                    class="w-9 h-9 rounded-lg border text-sm font-semibold transition-colors"
                                    :class="page === currentPage
                                        ? 'bg-pink-600 border-pink-600 text-white'
                                        : isDark ? 'border-gray-700 text-gray-300 hover:bg-gray-800' : 'border-gray-200 text-gray-700 hover:bg-gray-50'">
                                    {{ page }}
                                </button>

                                <button @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage"
                                    class="px-3 py-2 rounded-lg border text-sm font-medium disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                                    :class="isDark ? 'border-gray-700 text-gray-300 hover:bg-gray-800' : 'border-gray-200 text-gray-700 hover:bg-gray-50'">
                                    Sau
                                </button>
                            </div>
                        </div>

                    </div>
                </main>

                <!-- ===== MODAL THÊM / SỬA SẢN PHẨM ===== -->
                <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-200'"
                        class="w-full max-w-3xl max-h-[92vh] flex flex-col rounded-2xl border shadow-2xl overflow-hidden">

                        <!-- Modal Header -->
                        <div :class="isDark ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50'"
                            class="flex justify-between items-center px-6 py-4 border-b shrink-0">
                            <h3 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-xl font-bold font-serif">
                                {{ isEditMode ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới' }}
                            </h3>
                            <button @click="closeModal"
                                :class="isDark ? 'text-gray-400 hover:text-white border-gray-600' : 'text-gray-500 hover:text-gray-900 border-gray-300'"
                                class="w-8 h-8 flex items-center justify-center rounded-lg border transition-colors">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Modal Body (Scrollable) -->
                        <div class="overflow-y-auto flex-1 px-6 py-6 space-y-6">

                            <!-- Tên sản phẩm -->
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-semibold mb-1.5">
                                    Tên sản phẩm <span class="text-red-500">*</span>
                                </label>
                                <input type="text" v-model="form.name" placeholder="VD: Bó hoa hồng đỏ"
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900'"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all" />
                            </div>

                            <!-- Danh mục -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-semibold mb-1.5">Danh mục cha</label>
                                    <select v-model="form.category_id" @change="fetchCategoryItems(form.category_id)"
                                        :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                                        <option value="">-- Chọn danh mục --</option>
                                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-semibold mb-1.5">Danh mục con</label>
                                    <select v-model="form.category_item_id"
                                        :disabled="categoryItems.length === 0"
                                        :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 disabled:opacity-40">
                                        <option value="">-- Chọn danh mục con --</option>
                                        <option v-for="item in categoryItems" :key="item.id" :value="item.id">{{ item.name }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Mô tả -->
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-semibold mb-1.5">Mô tả sản phẩm</label>
                                <textarea v-model="form.description" rows="3" placeholder="Mô tả chi tiết về sản phẩm..."
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900'"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 resize-none transition-all"></textarea>
                            </div>

                            <!-- Ảnh đại diện (Thumbnail) -->
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-semibold mb-1.5">Ảnh đại diện</label>
                                <div class="flex items-start gap-4">
                                    <!-- Preview thumbnail -->
                                    <div v-if="form.thumbnailPreview" class="w-24 h-24 rounded-xl overflow-hidden border-2 border-blue-400 shrink-0">
                                        <img :src="form.thumbnailPreview" class="w-full h-full object-cover" />
                                    </div>
                                    <!-- Upload zone -->
                                    <label :class="isDark ? 'border-gray-600 bg-[#0f172a] hover:border-blue-500' : 'border-gray-300 bg-gray-50 hover:border-blue-500'"
                                        class="flex-1 relative border-2 border-dashed rounded-xl p-5 flex flex-col items-center justify-center cursor-pointer transition-colors group">
                                        <input type="file" @change="handleThumbnail" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" />
                                        <i class="fa-solid fa-cloud-arrow-up text-3xl mb-2" :class="isDark ? 'text-gray-500 group-hover:text-blue-400' : 'text-gray-400 group-hover:text-blue-500'"></i>
                                        <p class="text-xs text-center" :class="isDark ? 'text-gray-400' : 'text-gray-500'">{{ form.thumbnail ? form.thumbnail.name : 'Nhấn để chọn ảnh chính' }}</p>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-semibold mb-1.5">
                                    Ảnh <span class="font-normal text-gray-400">(Có thể chọn nhiều ảnh)</span>
                                </label>

                                <div v-if="form.existingImages.length > 0" class="mb-3">
                                    <p class="text-xs text-gray-400 mb-2">Ảnh hiện tại:</p>
                                    <div class="flex flex-wrap gap-3">
                                        <div v-for="(img, idx) in form.existingImages" :key="img.id"
                                            class="relative w-20 h-20 rounded-xl overflow-hidden border-2 group"
                                            :class="isDark ? 'border-gray-600' : 'border-gray-200'">
                                            <img :src="`http://localhost:8888/images/${img.image_path}`" class="w-full h-full object-cover" />
                                            <button @click="removeExistingImage(img, idx)"

                                                class="absolute inset-0 bg-red-500/70 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                                <i class="fa-solid fa-trash text-white text-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview ảnh mới chọn -->
                                <div v-if="form.galleryPreviews.length > 0" class="flex flex-wrap gap-3 mb-3">
                                    <div v-for="(prev, idx) in form.galleryPreviews" :key="idx"
                                        class="relative w-20 h-20 rounded-xl overflow-hidden border-2 border-blue-400 group">
                                        <img :src="prev" class="w-full h-full object-cover" />
                                        <button @click="removeNewGallery(idx)"
                                            class="absolute inset-0 bg-red-500/70 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                            <i class="fa-solid fa-trash text-white text-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Upload zone gallery -->
                                <label :class="isDark ? 'border-gray-600 bg-[#0f172a] hover:border-blue-500' : 'border-gray-300 bg-gray-50 hover:border-blue-500'"
                                    class="relative border-2 border-dashed rounded-xl p-5 flex flex-col items-center justify-center cursor-pointer transition-colors group">
                                    <input type="file" @change="handleGallery" multiple class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" />
                                    <i class="fa-solid fa-images text-3xl mb-2" :class="isDark ? 'text-gray-500 group-hover:text-blue-400' : 'text-gray-400 group-hover:text-blue-500'"></i>
                                    <p class="text-xs" :class="isDark ? 'text-gray-400' : 'text-gray-500'">Nhấn để thêm ảnh (nhiều ảnh)</p>
                                </label>
                            </div>

                            <!-- Biến thể (Variants) -->
                            <div>
                                <div class="flex justify-between items-center mb-3">
                                    <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="text-sm font-semibold">
                                        Biến thể sản phẩm <span class="text-red-500">*</span>
                                    </label>
                                    <button @click="addVariant" type="button"
                                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-colors">
                                        <i class="fa-solid fa-plus"></i> Thêm biến thể
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <div v-for="(variant, idx) in form.variants" :key="idx"
                                        :class="isDark ? 'bg-gray-800/50 border-gray-700' : 'bg-gray-50 border-gray-200'"
                                        class="grid grid-cols-12 gap-3 items-center p-3 rounded-xl border">

                                        <!-- Size -->
                                        <div class="col-span-3">
                                            <label class="text-xs text-gray-400 mb-1 block">Kích thước</label>
                                            <select v-model="variant.size_id"
                                                :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'"
                                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                                                <option value="">-- Size --</option>
                                                <option v-for="s in sizes" :key="s.id" :value="s.id">{{ s.name }}</option>
                                            </select>
                                        </div>

                                        <!-- Giá -->
                                        <div class="col-span-3">
                                            <label class="text-xs text-gray-400 mb-1 block">Giá (đ) *</label>
                                            <input type="number" v-model="variant.price" placeholder="350000" min="0"
                                                :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-600' : 'bg-white border-gray-300 text-gray-900'"
                                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                                        </div>

                                        <!-- Giá sale -->
                                        <div class="col-span-3">
                                            <label class="text-xs text-gray-400 mb-1 block">Giá sale (đ)</label>
                                            <input type="number" v-model="variant.sale_price" placeholder="Không bắt buộc" min="0"
                                                :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-600' : 'bg-white border-gray-300 text-gray-900'"
                                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                                        </div>

                                        <!-- Tồn kho -->
                                        <div class="col-span-2">
                                            <label class="text-xs text-gray-400 mb-1 block">Tồn kho</label>
                                            <input type="number" v-model="variant.stock" min="0"
                                                :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'"
                                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                                        </div>

                                        <!-- Xóa biến thể -->
                                        <div class="col-span-1 flex justify-center pt-5">
                                            <button @click="removeVariant(idx)" type="button"
                                                :disabled="form.variants.length === 1"
                                                class="text-red-400 hover:text-red-600 disabled:opacity-20 disabled:cursor-not-allowed transition-colors">
                                                <i class="fa-solid fa-circle-minus text-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Trạng thái -->
                            <div class="flex items-center gap-3">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="form.is_active" class="sr-only peer" />
                                    <div class="w-11 h-6 bg-gray-300 peer-checked:bg-blue-600 rounded-full peer-focus:ring-2 peer-focus:ring-blue-500/30 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                                </label>
                                <span class="text-sm font-medium" :class="isDark ? 'text-gray-300' : 'text-gray-700'">
                                    {{ form.is_active ? 'Đang hiển thị trên trang' : 'Đang ẩn khỏi trang' }}
                                </span>
                            </div>

                        </div>

                        <!-- Modal Footer -->
                        <div :class="isDark ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50'"
                            class="px-6 py-4 border-t flex justify-end gap-3 shrink-0">
                            <button @click="closeModal"
                                :class="isDark ? 'bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 border'"
                                class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">Hủy bỏ</button>
                            <button @click="saveProduct"
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm focus:ring-4 focus:ring-blue-500/30">
                                {{ isEditMode ? 'Cập nhật' : 'Thêm sản phẩm' }}
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap');

.font-serif { font-family: 'Playfair Display', serif; }
.font-sans  { font-family: 'Inter', sans-serif; }
</style>