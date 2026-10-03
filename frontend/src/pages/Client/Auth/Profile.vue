<script setup>
import Header_client from '@/pages/Includes/Layouts/Header_client.vue';
import Footer_client from '@/pages/Includes/Layouts/Footer_client.vue';
</script>
<template>
    <Header_client />
    <main class="flex-grow bg-surface-light dark:bg-surface-dark py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center md:text-left">
                <h1 class="text-4xl font-display font-bold text-text-main-light dark:text-text-main-dark mb-2">Hồ Sơ Của
                    Bạn</h1>
                <p class="text-text-muted-light dark:text-text-muted-dark">Quản lý thông tin cá nhân và xem lịch sử mua
                    hàng.</p>
            </div>
            <div class="flex flex-col lg:flex-row gap-8">
                <div class="w-full lg:w-1/3">
                    <div
                        class="bg-background-light dark:bg-background-dark rounded-lg shadow-sm border border-border-light dark:border-border-dark pt-7 pb-7 px-4 sticky top-28">
                        <div
                            class="flex flex-col items-center text-center pb-6">
                            <div
                                class="w-24 h-24 rounded-full bg-primary-light text-primary flex items-center justify-center text-3xl font-display font-bold mb-4">
                                TB
                            </div>
                            <h2 class="text-2xl font-bold pb-2">Thành Bùi</h2>
                            <p class="text-sm text-text-muted-light dark:text-text-muted-dark">Thành viên từ Tháng 5,
                                2023</p>
                        </div>
                        <nav>
                            <ul>
                                <!-- Active link -->
                                <li
                                    class="py-4 px-5 border-border-light hover:bg-primary/10 hover:rounded-lg active:border-primary active:text-primary">
                                    <router-link replace to="/profile">
                                        <span class="material-symbols-outlined text-lg align-middle mr-2">person</span>
                                        Thông tin cá nhân
                                    </router-link>
                                </li>
                                <li class="py-4 px-5 border-border-light hover:bg-primary/10 hover:rounded-lg">
                                    <router-link replace to="/profile/order">
                                        <span class="material-symbols-outlined text-lg align-middle mr-2">local_shipping</span>
                                        Đơn hàng đã đặt
                                    </router-link>
                                </li>
                                <li class="py-4 px-5 border-border-light hover:bg-primary/10 hover:rounded-lg">
                                    <router-link replace to="/profile/favorite">
                                        <span class="material-symbols-outlined text-lg align-middle mr-2">favorite</span>
                                        Sản phẩm yêu thích
                                    </router-link>
                                </li>
                                <li class="py-4 px-5 border-border-light hover:bg-primary/10 hover:rounded-lg">
                                    <router-link replace to="/profile/voucher">
                                        <span class="material-symbols-outlined text-lg align-middle mr-2">confirmation_number</span>
                                        Voucher
                                    </router-link>
                                </li>
                                <li class="py-4 px-5 border-border-light hover:bg-primary/10 hover:rounded-lg">
                                    <router-link replace to="/profile/address">
                                        <span class="material-symbols-outlined text-lg align-middle mr-2">location_on</span>
                                        Địa chỉ
                                    </router-link>
                                </li>
                            </ul>
                        </nav>
                        <!-- <div class="pt-4">
                            <button
                                class="w-full py-2.5 px-4 border border-primary text-primary hover:bg-primary hover:text-white dark:hover:bg-primary dark:hover:text-white rounded-md transition-colors text-sm font-medium flex items-center justify-center py-4">
                                Chỉnh sửa thông tin
                            </button>
                        </div> -->
                    </div>
                </div>
                <div class="w-full lg:w-2/3">
                    <div
                        class="bg-background-light dark:bg-background-dark rounded-lg shadow-sm border border-border-light dark:border-border-dark p-6 md:p-8">
                        <div
                            class="flex justify-between items-center mb-6 pb-4 border-b border-border-light dark:border-border-dark">
                            <h3 class="text-2xl font-display font-semibold">Đơn Hàng Gần Đây</h3>
                            <a class="text-sm text-primary hover:underline font-medium" href="#">Xem tất cả</a>
                        </div>
                        <div class="space-y-6">
                            <div
                                class="border border-border-light dark:border-border-dark rounded-md p-4 hover:border-primary dark:hover:border-primary transition-colors">
                                <div
                                    class="flex flex-col sm:flex-row justify-between sm:items-center mb-4 pb-4 border-b border-border-light dark:border-border-dark gap-2">
                                    <div>
                                        <p class="text-xs text-text-muted-light dark:text-text-muted-dark mb-1">Mã đơn
                                            hàng</p>
                                        <p class="font-medium text-sm">#ORD-2023-8901</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-text-muted-light dark:text-text-muted-dark mb-1">Ngày đặt
                                        </p>
                                        <p class="font-medium text-sm">15/10/2023</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-text-muted-light dark:text-text-muted-dark mb-1">Tổng
                                            tiền</p>
                                        <p class="font-medium text-sm text-primary">1.250.000 ₫</p>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 px-5 py-2">
                                            Đã giao hàng
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div
                                            class="w-16 h-16 bg-surface-light dark:bg-surface-dark rounded border border-border-light dark:border-border-dark overflow-hidden flex-shrink-0">
                                            <img alt="Hoa Bó" class="w-full h-full object-cover"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCHVpDyAS693Rs6wqgQ5pxgSZP-zMoKBERsdADvQELpmLWuTuFy07WpL75Jd1Cqx9CPSfl-53OtK0WhwOH7-Zv5f-tDkvb-Si91xXbfXkPeaFGGHtf7RwWgmzkd1ynPSt3WOj2pBpilAN33ual0XRw--xiecRBioZX0Zvk_Vc_nmXJRhM9smioOsboMoPy6Uj9pAsaTZMybvph53TS0RBxbEkbD66DYHJZyi2cBdDwpgNIcT8Zm94exFySJibSE0uVmcTMg52Xvugs" />
                                        </div>
                                        <div>
                                            <p class="font-medium text-sm line-clamp-1">Hoa Bó Mùa Yêu Thương</p>
                                            <p class="text-xs text-text-muted-light dark:text-text-muted-dark mt-1">Số
                                                lượng: 1</p>
                                        </div>
                                    </div>
                                    <button
                                        class="text-sm font-medium text-primary hover:underline whitespace-nowrap ml-4">
                                        Chi tiết
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <Footer_client />
</template>
<style scoped>
body {
    font-family: 'Inter', sans-serif;
}

h1,
h2,
h3,
h4,
h5,
h6,
.font-display {
    font-family: 'Playfair Display', serif;
}
</style>