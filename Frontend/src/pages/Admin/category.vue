<script setup>
import header_admin from '../Includes/Layouts/Header_Admin.vue';
import navbar_admin from '../Includes/Layouts/Navbar_Admin.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { apiUrl, imageUrl } from '@/utils/api';

const isDark = ref(false);
const isSidebarOpen = ref(true);

// ========== DANH MỤC CHA ==========
const categories = ref([]);
const isModalOpen = ref(false);
const categoryForm = ref({
    id: null,
    name: '',
    slug: '',
    seo_title: '',
    meta_description: '',
    seo_content: '',
    image: null,
});
const message = ref('');

const fetchCategories = async () => {
    try {
        const response = await axios.get(apiUrl('/api/category'));
        categories.value = response.data.data;
        if (message.value) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: message.value, showConfirmButton: false, timer: 3000, timerProgressBar: true });
            message.value = '';
        }
    } catch (error) {
        console.error('Lỗi khi tải danh mục:', error);
    }
};

const openAddModal = () => {
    categoryForm.value = { id: null, name: '', slug: '', seo_title: '', meta_description: '', seo_content: '', image: null };
    isModalOpen.value = true;
    message.value = 'Thêm danh mục thành công!';
};

const editCategory = (cat) => {
    categoryForm.value = {
        id: cat.id,
        name: cat.name,
        slug: cat.slug || '',
        seo_title: cat.seo_title || '',
        meta_description: cat.meta_description || '',
        seo_content: cat.seo_content || '',
        image: null,
    };
    isModalOpen.value = true;
    message.value = 'Sửa danh mục thành công!';
};

const saveCategory = async () => {
    try {
        const formData = new FormData();
        formData.append('name', categoryForm.value.name);
        formData.append('slug', categoryForm.value.slug || '');
        formData.append('seo_title', categoryForm.value.seo_title || '');
        formData.append('meta_description', categoryForm.value.meta_description || '');
        formData.append('seo_content', categoryForm.value.seo_content || '');
        if (categoryForm.value.image) formData.append('image', categoryForm.value.image);

        if (categoryForm.value.id) {
            await axios.post(apiUrl(`/api/category/update/${categoryForm.value.id}`), formData, { headers: { 'Content-Type': 'multipart/form-data' } });
        } else {
            await axios.post(apiUrl('/api/category'), formData, { headers: { 'Content-Type': 'multipart/form-data' } });
        }
        isModalOpen.value = false;
        await fetchCategories();
    } catch (error) {
        console.error('Lỗi khi lưu danh mục:', error);
        alert('Có lỗi xảy ra, vui lòng kiểm tra lại!');
    }
};

const deleteCategory = async (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa danh mục này? Các danh mục con cũng sẽ bị xóa theo!')) {
        try {
            await axios.delete(apiUrl(`/api/category/${id}`));
            message.value = 'Xóa danh mục thành công!';
            await fetchCategories();
        } catch (error) {
            console.error('Lỗi khi xóa:', error);
            alert('Có lỗi xảy ra khi xóa danh mục!');
        }
    }
};

const handleFileUpload = (e) => { categoryForm.value.image = e.target.files[0]; };

// ========== DANH MỤC CON ==========
const categoryItems = ref([]);          
const selectedParent = ref(null);       
const isItemModalOpen = ref(false);
const itemForm = ref({
    id: null,
    category_id: null,
    name: '',
    slug: '',
    seo_title: '',
    meta_description: '',
    seo_content: '',
});
const itemMessage = ref('');

const fetchCategoryItems = async (category) => {
    if (selectedParent.value?.id === category.id) {
        selectedParent.value = null;
        categoryItems.value = [];
        return;
    }
    selectedParent.value = category;
    try {
        const response = await axios.get(apiUrl('/api/category-item'), {
            params: { category_id: category.id },
        });
        categoryItems.value = response.data.data;
        if (itemMessage.value) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: itemMessage.value, showConfirmButton: false, timer: 3000, timerProgressBar: true });
            itemMessage.value = '';
        }
    } catch (error) {
        console.error('Lỗi khi tải danh mục con:', error);
    }
};

const openAddItemModal = () => {
    itemForm.value = {
        id: null,
        category_id: selectedParent.value.id,
        name: '',
        slug: '',
        seo_title: '',
        meta_description: '',
        seo_content: '',
    };
    isItemModalOpen.value = true;
    itemMessage.value = 'Thêm danh mục con thành công!';
};

const editItem = (item) => {
    itemForm.value = {
        id: item.id,
        category_id: item.category_id,
        name: item.name,
        slug: item.slug || '',
        seo_title: item.seo_title || '',
        meta_description: item.meta_description || '',
        seo_content: item.seo_content || '',
    };
    isItemModalOpen.value = true;
    itemMessage.value = 'Sửa danh mục con thành công!';
};

const saveItem = async () => {
    try {
        if (itemForm.value.id) {
            await axios.post(apiUrl(`/api/category-item/update/${itemForm.value.id}`), itemForm.value);
        } else {
            await axios.post(apiUrl('/api/category-item'), itemForm.value);
        }
        isItemModalOpen.value = false;
        await fetchCategoryItems(selectedParent.value);
    } catch (error) {
        console.error('Lỗi khi lưu danh mục con:', error);
        alert('Có lỗi xảy ra!');
    }
};

const deleteItem = async (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa danh mục con này?')) {
        try {
            await axios.delete(apiUrl(`/api/category-item/${id}`));
            itemMessage.value = 'Xóa danh mục con thành công!';
            await fetchCategoryItems(selectedParent.value);
        } catch (error) {
            console.error('Lỗi khi xóa:', error);
            alert('Có lỗi xảy ra khi xóa!');
        }
    }
};

// Theme
onMounted(() => {
    fetchCategories();
    const savedTheme = localStorage.getItem('theme');
    isDark.value = savedTheme === 'dark';
});

const toggleTheme = () => {
    isDark.value = !isDark.value;
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};
</script>

<template>
    <div class="antialiased font-sans transition-colors duration-300">
        <div :class="isDark ? 'bg-[#0f172a] text-gray-100' : 'bg-gray-50 text-gray-900'" class="flex h-screen overflow-hidden">

            <navbar_admin :isDark="isDark" :isSidebarOpen="isSidebarOpen" />

            <div class="flex-1 flex flex-col overflow-hidden relative">
                <header_admin :isDark="isDark" @toggle-sidebar="isSidebarOpen = !isSidebarOpen" @toggle-theme="toggleTheme" />

                <main class="flex-1 overflow-y-auto p-4 md:p-8 space-y-6">

                    <!-- ===== BẢNG DANH MỤC CHA ===== -->
                    <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-100'"
                        class="w-full p-6 md:p-8 border shadow-sm rounded-xl transition-colors duration-300">

                        <div :class="isDark ? 'border-gray-700' : 'border-gray-200'" class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-6 border-b gap-4">
                            <div>
                                <h2 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-3xl font-serif font-bold mb-2">Danh mục</h2>
                                <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="font-light text-sm">Quản lý danh mục cha. Nhấn vào tên để xem danh mục con.</p>
                            </div>
                            <button @click="openAddModal" class="flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm shrink-0">
                                <i class="fa-solid fa-plus mr-2 text-lg"></i> Thêm danh mục
                            </button>
                        </div>

                        <div class="overflow-x-auto rounded-lg border" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr :class="isDark ? 'bg-gray-800/50 text-gray-400 border-gray-700' : 'bg-gray-50 text-gray-600 border-gray-200'" class="border-b text-xs uppercase tracking-wider">
                                        <th class="px-6 py-4 font-semibold">STT</th>
                                        <th class="px-6 py-4 font-semibold">Hình ảnh</th>
                                        <th class="px-6 py-4 font-semibold">Tên danh mục</th>
                                        <th class="px-6 py-4 font-semibold text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody :class="isDark ? 'divide-gray-700' : 'divide-gray-200'" class="divide-y">
                                    <template v-for="(cat, index) in categories" :key="cat.id || index">
                                        <tr :class="[isDark ? 'hover:bg-gray-800/30' : 'hover:bg-gray-50', selectedParent?.id === cat.id ? (isDark ? 'bg-blue-900/20' : 'bg-blue-50') : '']"
                                            class="transition-colors group cursor-pointer"
                                            @click="fetchCategoryItems(cat)">
                                            <td class="px-6 py-4 text-sm font-medium" :class="isDark ? 'text-gray-400' : 'text-gray-500'">#{{ index + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <div class="w-14 h-14 rounded-lg overflow-hidden border transition-transform duration-300 group-hover:scale-105" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                                                    <img :src="imageUrl(cat.img)" class="w-full h-full object-cover" :alt="cat.name">
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-bold transition-colors inline-flex items-center gap-1.5" 
                                                    :class="[isDark ? 'text-gray-200 group-hover:text-blue-400' : 'text-gray-700 group-hover:text-blue-600', selectedParent?.id === cat.id ? (isDark ? 'text-blue-400' : 'text-blue-600') : '']">
                                                    {{ cat.name }}
                                                    <i class="fa-solid fa-chevron-right text-[10px] transition-transform duration-300 group-hover:translate-x-1"
                                                        :class="selectedParent?.id === cat.id ? 'rotate-90 text-blue-500' : ''"></i>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right space-x-4 whitespace-nowrap" @click.stop>
                                                <button @click="editCategory(cat)" class="text-blue-500 hover:text-blue-700 transition-colors">
                                                    <i class="fa-regular fa-pen-to-square text-lg"></i>
                                                </button>
                                                <button @click="deleteCategory(cat.id)" class="text-red-500 hover:text-red-700 transition-colors">
                                                    <i class="fa-regular fa-trash-can text-lg"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <!-- Hàng danh mục con xổ xuống -->
                                        <tr :class="isDark ? 'bg-gray-800/10' : 'bg-blue-50/10'">
                                            <td colspan="4" class="px-8 py-0">
                                                <div class="grid transition-all duration-300 ease-in-out"
                                                    :style="{ gridTemplateRows: selectedParent?.id === cat.id ? '1fr' : '0fr' }">
                                                    <div class="overflow-hidden">
                                                        <div class="flex flex-col space-y-4 py-6 transition-all duration-300"
                                                            :class="selectedParent?.id === cat.id ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-2'">
                                                    <div class="flex justify-between items-center pb-2 border-b border-dashed" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                                                        <span class="text-xs font-bold uppercase tracking-wider text-pink-500">
                                                            Danh mục con của: {{ cat.name }}
                                                        </span>
                                                        <button @click.stop="openAddItemModal" class="flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors shadow-sm">
                                                            <i class="fa-solid fa-plus mr-1"></i> Thêm mục con
                                                        </button>
                                                    </div>
                                                    
                                                    <div v-if="categoryItems.length === 0" class="text-xs text-gray-400 italic py-2">
                                                        Chưa có danh mục con nào. Nhấn "Thêm mục con" để bắt đầu.
                                                    </div>
                                                    
                                                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 pt-1">
                                                        <div v-for="item in categoryItems" :key="item.id" 
                                                            :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-200'"
                                                            class="flex justify-between items-center p-3 rounded-lg border shadow-sm transition-all hover:border-pink-500/50">
                                                            <span class="text-sm font-semibold" :class="isDark ? 'text-gray-200' : 'text-gray-800'">
                                                                {{ item.name }}
                                                            </span>
                                                            <div class="flex space-x-2" @click.stop>
                                                                <button @click="editItem(item)" class="text-blue-500 hover:text-blue-700 transition-colors p-1">
                                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                                </button>
                                                                <button @click="deleteItem(item.id)" class="text-red-500 hover:text-red-700 transition-colors p-1">
                                                                    <i class="fa-regular fa-trash-can"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </main>

                <!-- MODAL THÊM VÀ SỬA DANH MỤC CHA -->
                <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-200 shadow-2xl'" class="w-full max-w-2xl rounded-xl border overflow-hidden">
                        <div :class="isDark ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50'" class="flex justify-between items-center px-6 py-4 border-b">
                            <h3 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-lg font-semibold">{{ categoryForm.id ? 'Sửa danh mục' : 'Thêm danh mục' }}</h3>
                            <button @click="isModalOpen = false" :class="isDark ? 'text-gray-400 hover:text-white border-gray-700' : 'text-gray-500 hover:text-gray-900 border-gray-200'" class="w-8 h-8 flex items-center justify-center rounded-lg border transition-colors bg-white dark:bg-[#0f172a]">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div class="max-h-[75vh] overflow-y-auto px-6 py-6 space-y-5">
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">Tên danh mục <span class="text-red-500">*</span></label>
                                <input type="text" v-model="categoryForm.name" placeholder="VD: Hoa Hồng"
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900'"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all" />
                            </div>
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">Slug SEO</label>
                                <input type="text" v-model="categoryForm.slug" placeholder="hoa-hong"
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900'"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all" />
                            </div>
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">SEO title</label>
                                <input type="text" v-model="categoryForm.seo_title" maxlength="70" placeholder="Hoa hồng tươi giao nhanh"
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900'"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all" />
                            </div>
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">Meta description</label>
                                <textarea v-model="categoryForm.meta_description" maxlength="170" rows="3" placeholder="Mô tả ngắn cho Google khi hiển thị danh mục hoa."
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900'"
                                    class="w-full resize-none px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all"></textarea>
                            </div>
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">SEO content</label>
                                <textarea v-model="categoryForm.seo_content" rows="5" placeholder="Nội dung giới thiệu danh mục, chất lượng hoa, dịp sử dụng và cam kết giao hàng."
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900'"
                                    class="w-full resize-y px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all"></textarea>
                            </div>
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">Hình ảnh danh mục</label>
                                <div :class="isDark ? 'border-gray-600 bg-[#0f172a] hover:border-blue-500' : 'border-gray-300 bg-gray-50 hover:border-blue-500'"
                                    class="relative border-2 border-dashed rounded-lg p-8 flex flex-col items-center justify-center transition-colors cursor-pointer group">
                                    <input type="file" @change="handleFileUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" />
                                    <i :class="isDark ? 'text-gray-500 group-hover:text-blue-400' : 'text-gray-400 group-hover:text-blue-500'" class="fa-solid fa-cloud-arrow-up text-4xl mb-3 transition-colors"></i>
                                    <p :class="isDark ? 'text-gray-400' : 'text-gray-600'" class="text-sm font-medium">
                                        {{ categoryForm.image ? categoryForm.image.name : 'Nhấn để tải tệp ảnh lên' }}
                                    </p>
                                    <p class="text-xs mt-1.5 text-gray-500">Hỗ trợ PNG, JPG (Tối đa 2MB)</p>
                                </div>
                            </div>
                        </div>
                        <div :class="isDark ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50'" class="px-6 py-4 border-t flex justify-end space-x-3">
                            <button @click="isModalOpen = false" :class="isDark ? 'bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 border'" class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">Hủy bỏ</button>
                            <button @click="saveCategory" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm focus:ring-4 focus:ring-blue-500/30">Lưu danh mục</button>
                        </div>
                    </div>
                </div>

                <!-- ===== MODAL DANH MỤC CON ===== -->
                <div v-if="isItemModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-200 shadow-2xl'" class="w-full max-w-2xl rounded-xl border overflow-hidden">
                        <div :class="isDark ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50'" class="flex justify-between items-center px-6 py-4 border-b">
                            <h3 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-lg font-semibold">
                                {{ itemForm.id ? 'Sửa danh mục con' : 'Thêm danh mục con' }}
                                <span class="text-blue-500 text-sm font-normal ml-1">({{ selectedParent?.name }})</span>
                            </h3>
                            <button @click="isItemModalOpen = false" :class="isDark ? 'text-gray-400 hover:text-white border-gray-700' : 'text-gray-500 hover:text-gray-900 border-gray-200'" class="w-8 h-8 flex items-center justify-center rounded-lg border transition-colors bg-white dark:bg-[#0f172a]">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div class="max-h-[75vh] overflow-y-auto px-6 py-6 space-y-5">
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">Tên danh mục con <span class="text-red-500">*</span></label>
                                <input type="text" v-model="itemForm.name" placeholder="VD: Hoa Hồng Đỏ, Hoa Cẩm Chướng..."
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900'"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all" />
                            </div>
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">Slug SEO</label>
                                <input type="text" v-model="itemForm.slug" placeholder="hoa-hong-do"
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900'"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all" />
                            </div>
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">SEO title</label>
                                <input type="text" v-model="itemForm.seo_title" maxlength="70" placeholder="Hoa hồng đỏ tươi giao nhanh"
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900'"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all" />
                            </div>
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">Meta description</label>
                                <textarea v-model="itemForm.meta_description" maxlength="170" rows="3" placeholder="Mô tả ngắn cho Google khi hiển thị nhóm hoa con."
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900'"
                                    class="w-full resize-none px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all"></textarea>
                            </div>
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">SEO content</label>
                                <textarea v-model="itemForm.seo_content" rows="5" placeholder="Nội dung mô tả nhóm hoa, màu sắc, ý nghĩa, dịp tặng và cách bảo quản."
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900'"
                                    class="w-full resize-y px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all"></textarea>
                            </div>
                        </div>
                        <div :class="isDark ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50'" class="px-6 py-4 border-t flex justify-end space-x-3">
                            <button @click="isItemModalOpen = false" :class="isDark ? 'bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 border'" class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">Hủy bỏ</button>
                            <button @click="saveItem" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm focus:ring-4 focus:ring-green-500/30">Lưu danh mục con</button>
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
