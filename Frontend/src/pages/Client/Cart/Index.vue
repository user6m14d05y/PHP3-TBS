<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../../stores/auth';
import Header_Client from '@/pages/Includes/Layouts/Header_Client.vue';
import Footer_Client from '@/pages/Includes/Layouts/Footer_Client.vue';

</script>
<template>
    <Header_Client />
    <div
        class="bg-background-light dark:bg-background-dark text-text-main-light dark:text-text-main-dark transition-colors duration-300 antialiased min-h-screen flex flex-col">

        <section class="bg-pink-100 border-b border-gray-100 py-24 transition-colors duration-300">
            <div class="container mx-auto px-4 text-center">
                <span class="text-xs uppercase tracking-[0.4em] mb-4 block font-medium text-pink-600">GIỎ HÀNG CỦA
                    BẠN</span>
                <h1 class="font-display text-5xl md:text-7xl font-bold text-white mb-6 italic leading-tight">
                    Giỏ Hàng</h1>
                <p class="text-lg md:text-xl font-light text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Kiểm tra lại các lựa chọn tuyệt vời của bạn trước khi tiến hành thanh toán.
                </p>
            </div>
        </section>
        <main class="flex-grow py-12 md:py-16">
            <div class="container mx-auto px-4 md:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-10">
                    <div class="w-full lg:w-2/3">
                        <div
                            class="bg-white dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark overflow-hidden transition-colors duration-300">
                            <div
                                class="hidden md:grid grid-cols-12 gap-4 p-6 border-b border-border-light dark:border-border-dark bg-gray-50 text-sm font-semibold text-text-muted-light dark:text-text-muted-dark uppercase tracking-wider">
                                <div class="col-span-6">Sản phẩm</div>
                                <div class="col-span-2 text-center">Giá</div>
                                <div class="col-span-2 text-center">Số lượng</div>
                                <div class="col-span-2 text-right">Tổng</div>
                            </div>
                            <div
                                class="p-6 border-b border-border-light dark:border-border-dark flex flex-col md:grid md:grid-cols-12 gap-4 items-center">
                                <div class="col-span-6 flex items-center w-full gap-4">
                                    <button aria-label="Remove item"
                                        class="text-gray-400 hover:text-red-500 transition-colors hidden md:block">
                                        <span class="material-symbols-outlined text-lg">close</span>
                                    </button>
                                    <img alt="Bó hoa hồng đỏ" class="w-20 h-24 object-cover rounded bg-gray-100"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAUYpnBoSn20lvTP2OPQiQSK7ySK26TWS0dW74JJoQjMX_MqByIVTRIXvBzvDG9YXC2bOJlWyUoUdV_Trd9asAzFxPK_GiwSIY3_bvOhxszJUHT8gx3Ag4XFk4D468mVWvTFXWa-O0gCiDqh2oZa7z4JmU7eoWQ-BGi8VwZSJ5rS6TVGFImd2Qv1Mp_TTXem-DsD-YX9cB7iJn5Wqy-pLUDZhMsZXBjJeOoRVWKAUeiOaI_LmlTioxMOwVYjWtpXUrpjrKOoiBoKuE" />
                                    <div>
                                        <p
                                            class="text-xs text-text-muted-light dark:text-text-muted-dark mb-1 uppercase tracking-wider">
                                            HOA BÓ</p>
                                        <h3
                                            class="font-display font-semibold text-lg hover:text-primary transition-colors cursor-pointer text-gray-900 dark:text-white">
                                            Bó Hoa Tình Yêu</h3>
                                        <button
                                            class="text-xs text-red-500 mt-2 md:hidden underline flex items-center gap-1">
                                            Xóa
                                        </button>
                                    </div>
                                </div>
                                <div class="col-span-2 text-center hidden md:block">
                                    <span class="font-medium">850.000 ₫</span>
                                </div>
                                <div class="col-span-2 flex justify-center w-full md:w-auto mt-4 md:mt-0">
                                    <div
                                        class="flex items-center border border-border-light dark:border-border-dark rounded bg-white dark:bg-surface-dark overflow-hidden">
                                        <button aria-label="Decrease quantity"
                                            class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-text-muted-light dark:text-text-muted-dark">
                                            <span class="material-symbols-outlined text-sm">remove</span>
                                        </button>
                                        <input aria-label="Quantity"
                                            class="w-10 h-8 text-center border-none focus:ring-0 text-sm bg-transparent font-medium p-0 text-text-main-light dark:text-text-main-dark"
                                            min="1" type="number" value="1" />
                                        <button aria-label="Increase quantity"
                                            class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-text-muted-light dark:text-text-muted-dark">
                                            <span class="material-symbols-outlined text-sm">add</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-span-2 flex justify-between w-full md:block md:text-right mt-4 md:mt-0">
                                    <span
                                        class="md:hidden text-text-muted-light dark:text-text-muted-dark text-sm">Tổng:</span>
                                    <span class="font-bold text-primary">850.000 ₫</span>
                                </div>
                            </div>
                            <div class="p-6 flex flex-col md:grid md:grid-cols-12 gap-4 items-center">
                                <div class="col-span-6 flex items-center w-full gap-4">
                                    <button aria-label="Remove item"
                                        class="text-gray-400 hover:text-red-500 transition-colors hidden md:block">
                                        <span class="material-symbols-outlined text-lg">close</span>
                                    </button>
                                    <img alt="Lẵng hoa chúc mừng" class="w-20 h-24 object-cover rounded bg-gray-100"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuC9s7CzhUSm2EpCUz5E7yMmGGozxT0niO5Vx5uPcTljnj7JpqGB7P8xbPV6wI_RiKofbZ0baGYkEDl85jIezsZwWJ0_xjdKE7jAsCXq-kLNeVWwGxqHEY_gCTYB5WlvThuFYBxmgIgLFu7xZHCrrM9RyXmPAe9b0EY4Z7rgxCQUBa9KuYKYmel99KkyvbBDgxk9izJXPA1Bu1jO7MmYI7jqHsPJhV2O2F49OyHcQEOyuI3CH0WK4RKjT40Coa155n4qWfeBKnQfJkU" />
                                    <div>
                                        <p
                                            class="text-xs text-text-muted-light dark:text-text-muted-dark mb-1 uppercase tracking-wider">
                                            HOA KHAI TRƯƠNG</p>
                                        <h3
                                            class="font-display font-semibold text-lg hover:text-primary transition-colors cursor-pointer text-gray-900 dark:text-white">
                                            Lẵng Hoa Cát Tường</h3>
                                    </div>
                                </div>
                                <div class="col-span-2 text-center hidden md:block">
                                    <span class="font-medium">1.250.000 ₫</span>
                                </div>
                                <div class="col-span-2 flex justify-center w-full md:w-auto mt-4 md:mt-0">
                                    <div
                                        class="flex items-center border border-border-light dark:border-border-dark rounded bg-white dark:bg-surface-dark overflow-hidden">
                                        <button aria-label="Decrease quantity"
                                            class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-text-muted-light dark:text-text-muted-dark">
                                            <span class="material-symbols-outlined text-sm">remove</span>
                                        </button>
                                        <input aria-label="Quantity"
                                            class="w-10 h-8 text-center border-none focus:ring-0 text-sm bg-transparent font-medium p-0 text-text-main-light dark:text-text-main-dark"
                                            min="1" type="number" value="1" />
                                        <button aria-label="Increase quantity"
                                            class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-text-muted-light dark:text-text-muted-dark">
                                            <span class="material-symbols-outlined text-sm">add</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-span-2 flex justify-between w-full md:block md:text-right mt-4 md:mt-0">
                                    <span
                                        class="md:hidden text-text-muted-light dark:text-text-muted-dark text-sm">Tổng:</span>
                                    <span class="font-bold text-primary">1.250.000 ₫</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-6">
                            <router-link :to="{ name: 'product' }" class="text-sm font-medium hover:text-primary transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">arrow_back</span>
                                Tiếp tục mua sắm
                            </router-link>
                            <button
                                class="text-sm font-medium border border-border-light px-6 py-3 rounded hover:bg-pink-500 hover:text-white transition-colors">
                                Cập nhật giỏ hàng
                            </button>
                        </div>
                    </div>
                    <div class="w-full lg:w-1/3 mt-8 lg:mt-0">
                        <div
                            class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark sticky top-24 transition-colors duration-300">
                            <h2
                                class="font-display text-2xl font-semibold mb-6 border-b border-border-light dark:border-border-dark pb-4 text-gray-900 dark:text-white">
                                Tổng Đơn Hàng</h2>
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-text-muted-light dark:text-text-muted-dark">Tạm tính:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">2.100.000 ₫</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-text-muted-light dark:text-text-muted-dark">Phí vận chuyển:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">Tính lúc thanh toán</span>
                                </div>
                            </div>
                            <div class="mb-6 pt-6 border-t border-border-light dark:border-border-dark">
                                <label class="block text-sm font-medium mb-2 text-gray-900 dark:text-white"
                                    for="coupon">Mã giảm giá (Nếu có)</label>
                                <div class="flex">
                                    <input
                                        class="form-input flex-grow rounded-l-md border-r-0 border-border-light bg-white dark:bg-gray-100 focus:ring-primary focus:border-primary text-sm dark:text-gray-800"
                                        id="coupon" placeholder="Nhập mã..." type="text" />
                                    <button
                                        class="bg-gray-800 dark:bg-gray-600 text-white px-4 py-2 rounded-r-md text-sm font-medium hover:bg-pink-500 dark:hover:bg-pink-500 transition-colors">
                                        Áp dụng
                                    </button>
                                </div>
                            </div>
                            <div class="border-t border-border-light dark:border-border-dark pt-4 mb-6">
                                <div class="flex justify-between items-end">
                                    <span class="text-base font-semibold text-gray-900 dark:text-white">Tổng
                                        cộng:</span>
                                    <span class="text-2xl font-bold text-primary">2.100.000 ₫</span>
                                </div>
                                <p class="text-xs text-text-muted-light dark:text-text-muted-dark text-right mt-1">(Đã
                                    bao gồm VAT)</p>
                            </div>
                            <router-link to="/checkout"
                                class="w-full bg-primary text-white font-semibold py-3 px-4 rounded-md hover:bg-primary-hover transition-colors flex items-center justify-center gap-2">
                                Tiến Hành Thanh Toán
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <Footer_Client />
</template>
<style scoped>
body {
    font-family: 'Roboto', sans-serif;
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