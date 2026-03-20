<script setup>
import header_admin from '../Includes/Layouts/Header_Admin.vue';
import navbar_admin from '../Includes/Layouts/Navbar_Admin.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const isDark = ref(false);
const isSidebarOpen = ref(true);

const sizes = ref([]);
const message = ref('');


const fetchSize = async () => {
    try {
        const response = await axios.get('http://127.0.0.1:8888/api/size');
        sizes.value = response.data.data;
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
        console.error('Error fetching Size:', error);
    }
};

onMounted(() => {
    fetchSize();
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        isDark.value = true;
    } else if (savedTheme === 'light') {
        isDark.value = false;
    } else {
        isDark.value = false;
    }
});

const isModalOpen = ref(false);
const sizeForm = ref({
    id: null,
    name: '',
});

const openAddModal = () => {
    sizeForm.value = { id: null, name: ''};
    isModalOpen.value = true;
    message.value = "Thêm cỡ bó thành công!";
};

const editSize = (size) => {
    sizeForm.value = { id: size.id, name: size.name };
    isModalOpen.value = true;
    message.value = "Sửa cỡ bó thành công!";

};
const deleteSize = async (id) => {
    if (confirm("Bạn có chắc chắn muốn xóa cỡ bó này khỏi hệ thống?")) {
        try {
            await axios.delete(`http://127.0.0.1:8888/api/size/${id}`);
            fetchSize(); 
            message.value = "Xóa kích thước thành công!";
        } catch (error) {
            console.error('Lỗi khi xóa:', error);
            alert("Có lỗi xảy ra khi xóa kích thước!");
        }
    }
};

const saveSize = async () => {
    try {
        const token = localStorage.getItem('token');
        const config = { headers: { Authorization: `Bearer ${token}` } };

        if (sizeForm.value.id) {
            // Update existing Size
            await axios.post(`http://127.0.0.1:8888/api/size/update/${sizeForm.value.id}`, sizeForm.value, config);
        } else {
            // Create new Size
            await axios.post('http://127.0.0.1:8888/api/size', sizeForm.value, config);
        }

        isModalOpen.value = false;
        await fetchSize();
    } catch (error) {
        console.error('Error saving Size:', error);
    }
};

const toggleTheme = () => {
    isDark.value = !isDark.value;
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
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
                                    class="text-3xl font-serif font-bold mb-2">Kích thước</h2>
                                <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="font-light text-sm">Bảng
                                    điều khiển quản lý kích thước hiển thị.</p>
                            </div>

                            <!-- Thêm danh mục Button Moves Right -->
                            <button @click="openAddModal"
                                class="flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm shrink-0">
                                <i class="fa-solid fa-plus mr-2 text-lg"></i> Thêm kích thước
                            </button>
                        </div>

                        <!-- Bảng dữ liệu (HTML Table) -->
                        <div class="overflow-x-auto rounded-lg border" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr :class="isDark ? 'bg-gray-800/50 text-gray-400 border-gray-700' : 'bg-gray-50 text-gray-600 border-gray-200'" class="border-b text-xs uppercase tracking-wider">
                                        <th class="px-6 py-4 font-semibold">STT</th>
                                        <th class="px-6 py-4 font-semibold">Cỡ bó </th>
                                        <th class="px-6 py-4 font-semibold text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody :class="isDark ? 'divide-gray-700' : 'divide-gray-200'" class="divide-y">
                                    <tr v-for="(size, index) in sizes" :key="size.id || index" 
                                        :class="isDark ? 'hover:bg-gray-800/30' : 'hover:bg-gray-50'" class="transition-colors group">
                                        <td class="px-6 py-4 text-sm font-medium" :class="isDark ? 'text-gray-400' : 'text-gray-500'">#{{ index + 1 }}</td>
                                        <td class="px-6 py-4 text-sm font-bold" :class="isDark ? 'text-gray-200' : 'text-gray-900'">{{ size.name }}</td>
                                        <td class="px-6 py-4 text-right space-x-4 whitespace-nowrap">
                                            <button @click="editSize(size)" class="text-blue-500 hover:text-blue-700 transition-colors">
                                                <i class="fa-regular fa-pen-to-square text-lg"></i>
                                            </button>
                                            <button @click="deleteSize(size.id)" class="text-red-500 hover:text-red-700 transition-colors">
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
                            <h3 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-lg font-semibold">{{ sizeForm.id ? 'Sửa kích thước' : 'Thêm kích thước' }}</h3>
                            <button @click="isModalOpen = false" :class="isDark ? 'text-gray-400 hover:text-white border-gray-700' : 'text-gray-500 hover:text-gray-900 border-gray-200'" class="w-8 h-8 flex items-center justify-center rounded-lg border transition-colors bg-white dark:bg-[#0f172a]">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        
                        <!-- Modal Body -->
                        <div class="px-6 py-6 space-y-6">
                            <!-- Input Tên -->
                            <div>
                                <label :class="isDark ? 'text-gray-300' : 'text-gray-700'" class="block text-sm font-medium mb-1.5">Tên cỡ bó / Loại giỏ <span class="text-red-500">*</span></label>
                                <input type="text" v-model="sizeForm.name" placeholder="VD: Bó nhỏ, Bó vừa, Giỏ hoa lớn..."
                                    :class="isDark ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500/20' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500/20'"
                                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-4 transition-all" />
                            </div>
                        </div>
                        
                        <!-- Modal Footer -->
                        <div :class="isDark ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50'" class="px-6 py-4 border-t flex justify-end space-x-3">
                            <button @click="isModalOpen = false" 
                                :class="isDark ? 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white border-gray-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 border'"
                                class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm focus:outline-none">
                                Hủy bỏ
                            </button>
                            <button @click="saveSize"
                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm focus:outline-none focus:ring-4 focus:ring-blue-500/30">
                                Lưu kích thước
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