<script setup>
import header_admin from '../Includes/Layouts/Header_Admin.vue';
import navbar_admin from '../Includes/Layouts/Navbar_Admin.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const isDark = ref(false);
const isSidebarOpen = ref(true);

// ========== DANH MỤC CHA ==========
const categories = ref([]);
const isModalOpen = ref(false);
const categoryForm = ref({ id: null, name: '', image: null });
const message = ref('');

const fetchCategories = async () => {
    try {
        const response = await axios.get('http://127.0.0.1:8888/api/category');
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
    categoryForm.value = { id: null, name: '', image: null };
    isModalOpen.value = true;
    message.value = 'Thêm danh mục thành công!';
};

const editCategory = (cat) => {
    categoryForm.value = { id: cat.id, name: cat.name, image: null };
    isModalOpen.value = true;
    message.value = 'Sửa danh mục thành công!';
};

const saveCategory = async () => {
    try {
        const formData = new FormData();
        formData.append('name', categoryForm.value.name);
        if (categoryForm.value.image) formData.append('image', categoryForm.value.image);

        if (categoryForm.value.id) {
            await axios.post(`http://localhost:8888/api/category/update/${categoryForm.value.id}`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
        } else {
            await axios.post('http://localhost:8888/api/category', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
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
            await axios.delete(`http://localhost:8888/api/category/${id}`);
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
const itemForm = ref({ id: null, category_id: null, name: '' });
const itemMessage = ref('');

const fetchCategoryItems = async (category) => {
    selectedParent.value = category;
    try {
        const response = await axios.get(`http://localhost:8888/api/category-item?category_id=${category.id}`);
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
    itemForm.value = { id: null, category_id: selectedParent.value.id, name: '' };
    isItemModalOpen.value = true;
    itemMessage.value = 'Thêm danh mục con thành công!';
};

const editItem = (item) => {
    itemForm.value = { id: item.id, category_id: item.category_id, name: item.name };
    isItemModalOpen.value = true;
    itemMessage.value = 'Sửa danh mục con thành công!';
};

const saveItem = async () => {
    try {
        if (itemForm.value.id) {
            await axios.post(`http://127.0.0.1:8888/api/category-item/update/${itemForm.value.id}`, itemForm.value);
        } else {
            await axios.post('http://127.0.0.1:8888/api/category-item', itemForm.value);
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
            await axios.delete(`http://127.0.0.1:8888/api/category-item/${id}`);
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
                                    <tr v-for="(cat, index) in categories" :key="cat.id || index"
                                        :class="[isDark ? 'hover:bg-gray-800/30' : 'hover:bg-gray-50', selectedParent?.id === cat.id ? (isDark ? 'bg-blue-900/20' : 'bg-blue-50') : '']"
                                        class="transition-colors group">
                                        <td class="px-6 py-4 text-sm font-medium" :class="isDark ? 'text-gray-400' : 'text-gray-500'">#{{ index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="w-14 h-14 rounded-lg overflow-hidden border" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                                                <img :src="'http://127.0.0.1:8888/images/' + cat.img" class="w-full h-full object-cover" :alt="cat.name">
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <!-- Nhấn tên danh mục cha để xem danh mục con -->
                                            <button @click="fetchCategoryItems(cat)" class="text-sm font-bold hover:underline transition-colors" 
                                                :class="isDark ? 'text-black-400 hover:text-black-300' : 'text-black-600 hover:text-black-800'">
                                                {{ cat.name }}
                                                <i class="fa-solid fa-chevron-right text-xs ml-1"></i>
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-4 whitespace-nowrap">
                                            <button @click="editCategory(cat)" class="text-blue-500 hover:text-blue-700 transition-colors">
                                                <i class="fa-regular fa-pen-to-square text-lg"></i>
                                            </button>
                                            <button @click="deleteCategory(cat.id)" class="text-red-500 hover:text-red-700 transition-colors">
                                                <i class="fa-regular fa-trash-can text-lg"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ===== MODAL QUẢN LÝ DANH MỤC CON ===== -->
                    <div v-if="selectedParent" class="fixed inset-0 z-40 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                        <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-100'"
                            class="w-full max-w-4xl max-h-[90vh] flex flex-col border shadow-2xl rounded-xl transition-colors duration-300 overflow-hidden relative">

                            <!-- Modal Header -->
                            <div :class="isDark ? 'border-gray-700 bg-gray-800/50' : 'border-gray-200 bg-gray-50'" class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b gap-4">
                                <div>
                                    <h2 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-2xl font-serif font-bold mb-1">
                                        Danh mục con của: <span class="text-blue-500">{{ selectedParent.name }}</span>
                                    </h2>
                                    <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="font-light text-sm">Quản lý các loại hoa/mục thuộc danh mục cha này.</p>
                                </div>
                                <div class="flex space-x-3">
                                    <button @click="openAddItemModal" class="flex items-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm shrink-0">
                                        <i class="fa-solid fa-plus mr-2"></i> Thêm mục con
                                    </button>
                                    <button @click="selectedParent = null" :class="isDark ? 'bg-gray-700 hover:bg-red-600 text-white' : 'bg-gray-200 hover:bg-red-500 hover:text-white text-gray-700'" class="w-10 h-10 flex items-center justify-center rounded-lg transition-colors">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Modal Body (Bảng danh sách) -->
                            <div class="p-6 overflow-y-auto flex-1">
                                <div v-if="categoryItems.length === 0" class="text-center py-12" :class="isDark ? 'text-gray-500' : 'text-gray-400'">
                                    <i class="fa-solid fa-folder-open text-5xl mb-4 block opacity-50"></i>
                                    <p class="text-base font-medium text-gray-600 dark:text-gray-300">Chưa có danh mục con nào.</p>
                                    <p class="text-sm mt-1">Nhấn "Thêm mục con" ở trên để bắt đầu tạo.</p>
                                </div>

                                <div v-else class="overflow-x-auto rounded-lg border" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr :class="isDark ? 'bg-gray-800/50 text-gray-400 border-gray-700' : 'bg-gray-50 text-gray-600 border-gray-200'" class="border-b text-xs uppercase tracking-wider">
                                                <th class="px-6 py-4 font-semibold w-24">STT</th>
                                                <th class="px-6 py-4 font-semibold">Tên mặt hàng/Mục con</th>
                                                <th class="px-6 py-4 font-semibold text-right w-32">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody :class="isDark ? 'divide-gray-700' : 'divide-gray-200'" class="divide-y relative">
                                            <tr v-for="(item, index) in categoryItems" :key="item.id || index"
                                                :class="isDark ? 'hover:bg-gray-800/50' : 'hover:bg-gray-50'" class="transition-colors group">
                                                <td class="px-6 py-4 text-sm font-medium" :class="isDark ? 'text-gray-400' : 'text-gray-500'">#{{ index + 1 }}</td>
                                                <td class="px-6 py-4 text-sm font-bold" :class="isDark ? 'text-gray-200' : 'text-gray-900'">{{ item.name }}</td>
                                                <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                                                    <button @click="editItem(item)" class="text-blue-500 hover:text-blue-700 transition-colors p-2 rounded hover:bg-blue-50 dark:hover:bg-blue-900/30">
                                                        <i class="fa-regular fa-pen-to-square text-lg"></i>
                                                    </button>
                                                    <button @click="deleteItem(item.id)" class="text-red-500 hover:text-red-700 transition-colors p-2 rounded hover:bg-red-50 dark:hover:bg-red-900/30">
                                                        <i class="fa-regular fa-trash-can text-lg"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </main>

                <!-- MODAL THÊM VÀ SỬA DANH MỤC CHA -->
                <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-200 shadow-2xl'" class="w-full max-w-md rounded-xl border overflow-hidden">
                        <div :class="isDark ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50'" class="flex justify-between items-center px-6 py-4 border-b">
                            <h3 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-lg font-semibold">{{ categoryForm.id ? 'Sửa danh mục' : 'Thêm danh mục' }}</h3>
                            <button @click="isModalOpen = false" :class="isDark ? 'text-gray-400 hover:text-white border-gray-700' : 'text-gray-500 hover:text-gray-900 border-gray-200'" class="w-8 h-8 flex items-center justify-center rounded-lg border transition-colors bg-white dark:bg-[#0f172a]">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div class="px-6 py-6 space-y-5">
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">Tên danh mục <span class="text-red-500">*</span></label>
                                <input type="text" v-model="categoryForm.name" placeholder="VD: Hoa Hồng"
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900'"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all" />
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
                    <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-200 shadow-2xl'" class="w-full max-w-md rounded-xl border overflow-hidden">
                        <div :class="isDark ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50'" class="flex justify-between items-center px-6 py-4 border-b">
                            <h3 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-lg font-semibold">
                                {{ itemForm.id ? 'Sửa danh mục con' : 'Thêm danh mục con' }}
                                <span class="text-blue-500 text-sm font-normal ml-1">({{ selectedParent?.name }})</span>
                            </h3>
                            <button @click="isItemModalOpen = false" :class="isDark ? 'text-gray-400 hover:text-white border-gray-700' : 'text-gray-500 hover:text-gray-900 border-gray-200'" class="w-8 h-8 flex items-center justify-center rounded-lg border transition-colors bg-white dark:bg-[#0f172a]">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div class="px-6 py-6">
                            <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">Tên danh mục con <span class="text-red-500">*</span></label>
                            <input type="text" v-model="itemForm.name" placeholder="VD: Hoa Hồng Đỏ, Hoa Cẩm Chướng..."
                                :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500' : 'bg-white border-gray-300 text-gray-900'"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all" />
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