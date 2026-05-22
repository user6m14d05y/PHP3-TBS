<script setup>
import header_admin from '../Includes/Layouts/Header_Admin.vue';
import navbar_admin from '../Includes/Layouts/Navbar_Admin.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const isDark = ref(false);
const isSidebarOpen = ref(true);
const contacts = ref([]);
const currentPage = ref(1);
const perPage = ref(20);
const totalContacts = ref(0);
const lastPage = ref(1);
const loading = ref(false);
const selectedContact = ref(null);
const isDetailOpen = ref(false);

const fetchContacts = async (page = currentPage.value) => {
    loading.value = true;
    try {
        const response = await axios.get('http://localhost:8888/api/contact', {
            params: {
                page,
                limit: perPage.value,
            },
        });

        contacts.value = response.data.data;
        currentPage.value = response.data.current_page;
        perPage.value = response.data.per_page;
        totalContacts.value = response.data.total;
        lastPage.value = response.data.last_page;
    } catch (error) {
        console.error('Lỗi khi tải liên hệ:', error);
    } finally {
        loading.value = false;
    }
};

const changePage = (page) => {
    if (page < 1 || page > lastPage.value || page === currentPage.value) return;
    fetchContacts(page);
};

const formatDate = (date) => {
    if (!date) return '—';

    return new Intl.DateTimeFormat('vi-VN', {
        hour: '2-digit',
        minute: '2-digit',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    }).format(new Date(date));
};

const openDetail = (contact) => {
    selectedContact.value = contact;
    isDetailOpen.value = true;
};

const closeDetail = () => {
    isDetailOpen.value = false;
    selectedContact.value = null;
};

const replyContact = (contact) => {
    window.location.href = `mailto:${contact.email}?subject=${encodeURIComponent('Phản hồi từ TBS Flower Shop')}`;
};

onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    isDark.value = savedTheme === 'dark';
    fetchContacts();
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

            <div class="flex-1 flex flex-col overflow-hidden">
                <header_admin
                    :isDark="isDark"
                    @toggle-sidebar="isSidebarOpen = !isSidebarOpen"
                    @toggle-theme="toggleTheme"
                />

                <main class="flex-1 overflow-y-auto p-4 md:p-8">
                    <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-100'"
                        class="w-full min-h-[500px] p-6 md:p-8 border shadow-sm rounded-xl transition-colors duration-300">

                        <div :class="isDark ? 'border-gray-700' : 'border-gray-200'"
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-6 border-b gap-4">
                            <div>
                                <h2 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-3xl font-serif font-bold mb-2">Liên hệ</h2>
                                <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="font-light text-sm">
                                    Quản lý email khách hàng đã đăng ký nhận tư vấn và tin tức.
                                </p>
                            </div>
                            <button @click="fetchContacts()"
                                class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                                <i class="fa-solid fa-rotate-right mr-2"></i> Tải lại
                            </button>
                        </div>

                        <div class="overflow-x-auto rounded-lg border" :class="isDark ? 'border-gray-700' : 'border-gray-200'">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr :class="isDark ? 'bg-gray-800/50 text-gray-400 border-gray-700' : 'bg-gray-50 text-gray-600 border-gray-200'"
                                        class="border-b text-xs uppercase tracking-wider">
                                        <th class="px-6 py-4 font-semibold w-20">STT</th>
                                        <th class="px-6 py-4 font-semibold">Email</th>
                                        <th class="px-6 py-4 font-semibold">Ngày đăng ký</th>
                                        <th class="px-6 py-4 font-semibold text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody :class="isDark ? 'divide-gray-700' : 'divide-gray-200'" class="divide-y">
                                    <tr v-if="loading">
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                            <i class="fa-solid fa-spinner fa-spin text-2xl mb-3 block"></i>
                                            Đang tải dữ liệu liên hệ...
                                        </td>
                                    </tr>
                                    <tr v-else-if="contacts.length === 0">
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                            <i class="fa-regular fa-envelope-open text-3xl mb-3 block opacity-50"></i>
                                            Chưa có liên hệ nào.
                                        </td>
                                    </tr>
                                    <tr v-for="(contact, index) in contacts" v-else :key="contact.id"
                                        :class="isDark ? 'hover:bg-gray-800/30' : 'hover:bg-gray-50'" class="transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium" :class="isDark ? 'text-gray-400' : 'text-gray-500'">
                                            #{{ (currentPage - 1) * perPage + index + 1 }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                                    :class="isDark ? 'bg-blue-500/10 text-blue-400' : 'bg-blue-50 text-blue-600'">
                                                    <i class="fa-regular fa-envelope"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold" :class="isDark ? 'text-white' : 'text-gray-900'">{{ contact.email }}</p>
                                                    <p class="text-xs" :class="isDark ? 'text-gray-500' : 'text-gray-400'">ID: {{ contact.id }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm" :class="isDark ? 'text-gray-300' : 'text-gray-700'">
                                            {{ formatDate(contact.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                            <button @click="openDetail(contact)"
                                                class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded-lg border transition-colors"
                                                :class="isDark ? 'border-gray-700 text-gray-300 hover:bg-gray-800' : 'border-gray-200 text-gray-700 hover:bg-gray-50'">
                                                <i class="fa-regular fa-eye mr-1.5"></i> Chi tiết
                                            </button>
                                            <button @click="replyContact(contact)"
                                                class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded-lg bg-pink-600 hover:bg-pink-700 text-white transition-colors">
                                                <i class="fa-regular fa-paper-plane mr-1.5"></i> Trả lời
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="totalContacts > 0" class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                            <p class="text-sm" :class="isDark ? 'text-gray-400' : 'text-gray-500'">
                                Hiển thị
                                <span class="font-semibold">{{ (currentPage - 1) * perPage + 1 }}</span>
                                -
                                <span class="font-semibold">{{ Math.min(currentPage * perPage, totalContacts) }}</span>
                                trong tổng
                                <span class="font-semibold">{{ totalContacts }}</span>
                                liên hệ
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
                                        ? 'bg-blue-600 border-blue-600 text-white'
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

                <div v-if="isDetailOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-200 shadow-2xl'"
                        class="w-full max-w-lg rounded-2xl border overflow-hidden">
                        <div :class="isDark ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50'"
                            class="flex items-center justify-between px-6 py-4 border-b">
                            <h3 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-xl font-bold font-serif">Chi tiết liên hệ</h3>
                            <button @click="closeDetail"
                                class="w-8 h-8 flex items-center justify-center rounded-lg border transition-colors"
                                :class="isDark ? 'text-gray-400 hover:text-white border-gray-700' : 'text-gray-500 hover:text-gray-900 border-gray-200'">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <div v-if="selectedContact" class="p-6 space-y-5">
                            <div class="rounded-xl p-5 border" :class="isDark ? 'border-gray-700 bg-[#0f172a]' : 'border-gray-100 bg-gray-50'">
                                <p class="text-xs font-bold uppercase tracking-widest mb-2" :class="isDark ? 'text-gray-500' : 'text-gray-400'">Email khách hàng</p>
                                <p class="text-lg font-bold break-all" :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedContact.email }}</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="rounded-xl p-4 border" :class="isDark ? 'border-gray-700' : 'border-gray-100'">
                                    <p class="text-xs font-bold uppercase tracking-widest mb-2" :class="isDark ? 'text-gray-500' : 'text-gray-400'">Ngày đăng ký</p>
                                    <p class="text-sm font-semibold" :class="isDark ? 'text-gray-200' : 'text-gray-700'">{{ formatDate(selectedContact.created_at) }}</p>
                                </div>
                                <div class="rounded-xl p-4 border" :class="isDark ? 'border-gray-700' : 'border-gray-100'">
                                    <p class="text-xs font-bold uppercase tracking-widest mb-2" :class="isDark ? 'text-gray-500' : 'text-gray-400'">Cập nhật</p>
                                    <p class="text-sm font-semibold" :class="isDark ? 'text-gray-200' : 'text-gray-700'">{{ formatDate(selectedContact.updated_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <div :class="isDark ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50'" class="px-6 py-4 border-t flex justify-end gap-3">
                            <button @click="closeDetail" class="px-5 py-2.5 rounded-lg text-sm font-medium border transition-colors"
                                :class="isDark ? 'border-gray-700 text-gray-300 hover:bg-gray-800' : 'border-gray-300 text-gray-700 hover:bg-gray-100'">
                                Đóng
                            </button>
                            <button v-if="selectedContact" @click="replyContact(selectedContact)"
                                class="px-5 py-2.5 bg-pink-600 hover:bg-pink-700 text-white rounded-lg text-sm font-semibold transition-colors">
                                Trả lời email
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
