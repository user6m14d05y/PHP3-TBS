<script setup>
import header_admin from '../Includes/Layouts/Header_Admin.vue';
import navbar_admin from '../Includes/Layouts/Navbar_Admin.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const isDark = ref(false);
const isSidebarOpen = ref(true);

// Modal state
const isModalOpen = ref(false);
const categoryForm = ref({ id: null, name: '', image: null });
const categories = ref([]);
const message = ref('');


const fetchCategories = async () => {
    try {
        const response = await axios.get('http://localhost:8888/api/category');
        categories.value = response.data.data;
        if(message.value){
            Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: message.value,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
        }
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
};

// Lấy chế độ dark mode từ localStorage (nếu có)
onMounted(() => {
    fetchCategories();
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        isDark.value = true;
    } else if (savedTheme === 'light') {
        isDark.value = false;
    } else {
        isDark.value = false;
    }
});

const toggleTheme = () => {
    isDark.value = !isDark.value;
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};
const handleFileUpload = (e) => {
    categoryForm.value.image = e.target.files[0];
};
const openAddModal = () => {
    categoryForm.value = { id: null, name: '', image: null };
    isModalOpen.value = true;
    message.value = "Thêm danh mục thành công!";
};
const editCategory = (cat) => {
    categoryForm.value = { id: cat.id, name: cat.name, image: null };
    isModalOpen.value = true;
    message.value = "Sửa danh mục thành công!";

};
const deleteCategory = async (id) => {
    if (confirm("Bạn có chắc chắn muốn xóa danh mục này khỏi hệ thống?")) {
        try {
            await axios.delete(`http://localhost:8888/api/category/${id}`);
            fetchCategories(); 
            message.value = "Xóa danh mục thành công!";
        } catch (error) {
            console.error('Lỗi khi xóa:', error);
            alert("Có lỗi xảy ra khi xóa danh mục!");
        }
    }
};

const saveCategory = async () => {
    try {
        const formData = new FormData();
        formData.append('name', categoryForm.value.name);
        
        if (categoryForm.value.image) {
            formData.append('image', categoryForm.value.image);
        }

        if (categoryForm.value.id) {
            await axios.post(`http://localhost:8888/api/category/update/${categoryForm.value.id}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        } else {
            await axios.post('http://localhost:8888/api/category', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        }
        
        isModalOpen.value = false;
        fetchCategories(); 
    } catch (error) {
        console.error('Lỗi khi lưu:', error);
        alert("Có lỗi xảy ra, vui lòng kiểm tra lại!");
    }
};
</script>

<template>
    <div class="antialiased font-sans transition-colors duration-300">
        <div :class="isDark ? 'bg-[#0f172a] text-gray-100' : 'bg-gray-50 text-gray-900'"
            class="flex h-screen overflow-hidden">

            <!-- Component Navbar Trái -->
            <navbar_admin :isDark="isDark" :isSidebarOpen="isSidebarOpen" />

            <!-- Main Content Khung Bên Phải -->
            <div class="flex-1 flex flex-col overflow-hidden relative">

                <!-- Component Header Top -->
                <header_admin :isDark="isDark" @toggle-sidebar="isSidebarOpen = !isSidebarOpen"
                    @toggle-theme="toggleTheme" />

                <main class="flex-1 overflow-y-auto p-4 md:p-8">
                    <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-100'"
                        class="w-full min-h-[500px] p-6 md:p-8 border shadow-sm rounded-xl transition-colors duration-300">
                        
                        <!-- Header & Button in Flexbox -->
                        <div :class="isDark ? 'border-gray-700' : 'border-gray-200'" class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-6 border-b gap-4 sm:gap-0">
                            <div>
                                <h2 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-3xl font-serif font-bold mb-2">Danh mục</h2>
                                <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="font-light text-sm">Bảng điều khiển quản lý danh mục hiển thị.</p>
                            </div>
                            
                            <!-- Thêm danh mục Button Moves Right -->
                            <button @click="openAddModal" 
                                class="flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm shrink-0">
                                <i class="fa-solid fa-plus mr-2 text-lg"></i> Thêm danh mục
                            </button>
                        </div>

                        <!-- Bảng dữ liệu (HTML Table) -->
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
                                        :class="isDark ? 'hover:bg-gray-800/30' : 'hover:bg-gray-50'" class="transition-colors group">
                                        <td class="px-6 py-4 text-sm font-medium" :class="isDark ? 'text-gray-400' : 'text-gray-500'">#{{ index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="w-14 h-14 rounded-lg overflow-hidden border" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                                                <img :src="'http://localhost:8888/images/' + cat.img" class="w-full h-full object-cover" :alt="cat.name">
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold" :class="isDark ? 'text-gray-200' : 'text-gray-900'">{{ cat.name }}</td>
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
                </main>

                <!-- Modal Thêm Danh Mục -->
                <div v-if="isModalOpen" class="absolute inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity">
                    <div :class="isDark ? 'bg-[#1e293b] border-gray-700 shadow-gray-900/50' : 'bg-white border-gray-200 shadow-2xl'" 
                        class="w-full max-w-md rounded-xl border overflow-hidden transform transition-all">
                        
                        <!-- Modal Header -->
                        <div :class="isDark ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50'" class="flex justify-between items-center px-6 py-4 border-b">
                            <h3 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-lg font-semibold">{{ categoryForm.id ? 'Sửa danh mục' : 'Thêm danh mục' }}</h3>
                            <button @click="isModalOpen = false" :class="isDark ? 'text-gray-400 hover:text-white border-gray-700' : 'text-gray-500 hover:text-gray-900 border-gray-200'" class="w-8 h-8 flex items-center justify-center rounded-lg border transition-colors bg-white dark:bg-[#0f172a]">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        
                        <!-- Modal Body -->
                        <div class="px-6 py-6 space-y-6">
                            <!-- Input Tên -->
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">Tên danh mục <span class="text-red-500">*</span></label>
                                <input type="text" v-model="categoryForm.name" placeholder="VD: Hoa Hồng."
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500/20' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500/20'"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all" />
                            </div>
                            
                            <!-- Input Hình ảnh -->
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">Hình ảnh danh mục <span class="text-red-500">*</span></label>
                                <div :class="isDark ? 'border-gray-600 bg-[#0f172a] hover:border-blue-500' : 'border-gray-300 bg-gray-50 hover:border-blue-500'"
                                    class="relative border-2 border-dashed rounded-lg p-8 flex flex-col items-center justify-center transition-colors cursor-pointer group">
                                    <input type="file" @change="handleFileUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" />
                                    <i :class="isDark ? 'text-gray-500 group-hover:text-blue-400' : 'text-gray-400 group-hover:text-blue-500'" class="fa-solid fa-cloud-arrow-up text-4xl mb-3 transition-colors"></i>
                                    <p :class="isDark ? 'text-gray-400' : 'text-gray-600'" class="text-sm font-medium">Nhấn để tải tệp ảnh lên</p>
                                    <p :class="isDark ? 'text-gray-500' : 'text-gray-500'" class="text-xs mt-1.5">Hỗ trợ PNG, JPG (Tối đa 2MB)</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modal Footer -->
                        <div :class="isDark ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50'" class="px-6 py-4 border-t flex justify-end space-x-3">
                            <button @click="isModalOpen = false" 
                                :class="isDark ? 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white border-gray-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 border'"
                                class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm focus:outline-none">
                                Hủy bỏ
                            </button>
                            <button @click="saveCategory"
                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm focus:outline-none focus:ring-4 focus:ring-blue-500/30">
                                Lưu danh mục
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

.font-serif {
    font-family: 'Playfair Display', serif;
}

.font-sans {
    font-family: 'Inter', sans-serif;
}
</style>