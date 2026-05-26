<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../../stores/auth';
import Header_Client from '@/pages/Includes/Layouts/Header_client.vue';
import Footer_Client from '@/pages/Includes/Layouts/Footer_client.vue';

</script>
<template>
    <Header_Client />
    <div class="min-h-screen bg-[#fbf7f8] text-gray-900">
        <section class="bg-gradient-to-r from-rose-50 via-amber-50 to-indigo-50 border-b border-pink-100/50 py-16 md:py-20">
            <div class="container mx-auto px-4 text-center">
                <span class="text-xs uppercase tracking-[0.35em] mb-4 block font-semibold text-pink-600">
                    GIỎ HÀNG CỦA BẠN
                </span>
                <h1 class="font-display text-4xl md:text-6xl font-bold text-gray-900 mb-5 leading-tight">
                    Giỏ hàng
                </h1>
                <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Kiểm tra lại sản phẩm trước khi tiến hành thanh toán.
                </p>
            </div>
        </section>

        <main class="py-10 md:py-14">
            <div class="container mx-auto px-4 md:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <div class="lg:col-span-8">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="hidden md:grid grid-cols-12 gap-4 px-6 py-4 bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <div class="col-span-6">Sản phẩm</div>
                                <div class="col-span-2 text-center">Giá</div>
                                <div class="col-span-2 text-center">Số lượng</div>
                                <div class="col-span-2 text-right">Tổng</div>
                            </div>

                            <div class="p-5 md:p-6 border-b border-gray-100 flex flex-col md:grid md:grid-cols-12 gap-5 md:items-center">
                                <div class="col-span-6 flex items-center gap-4">
                                    <button aria-label="Remove item" class="shrink-0 w-9 h-9 rounded-full border border-gray-200 text-gray-400 hover:border-red-200 hover:bg-red-50 hover:text-red-500 transition-colors hidden md:flex items-center justify-center">
                                        <span class="material-symbols-outlined text-lg">close</span>
                                    </button>
                                    <img alt="Bó hoa hồng đỏ" class="w-24 h-24 object-cover rounded-xl bg-gray-100 border border-gray-100"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAUYpnBoSn20lvTP2OPQiQSK7ySK26TWS0dW74JJoQjMX_MqByIVTRIXvBzvDG9YXC2bOJlWyUoUdV_Trd9asAzFxPK_GiwSIY3_bvOhxszJUHT8gx3Ag4XFk4D468mVWvTFXWa-O0gCiDqh2oZa7z4JmU7eoWQ-BGi8VwZSJ5rS6TVGFImd2Qv1Mp_TTXem-DsD-YX9cB7iJn5Wqy-pLUDZhMsZXBjJeOoRVWKAUeiOaI_LmlTioxMOwVYjWtpXUrpjrKOoiBoKuE" />
                                    <div class="min-w-0">
                                        <p class="text-xs text-pink-500 mb-1 uppercase tracking-wider font-semibold">HOA BÓ</p>
                                        <h3 class="font-display font-semibold text-lg text-gray-900">Bó Hoa Tình Yêu</h3>
                                        <button class="text-sm text-red-500 mt-2 md:hidden underline">Xóa</button>
                                    </div>
                                </div>
                                <div class="col-span-2 md:text-center flex justify-between md:block">
                                    <span class="md:hidden text-sm text-gray-500">Giá:</span>
                                    <span class="font-semibold text-gray-900">850.000 ₫</span>
                                </div>
                                <div class="col-span-2 flex md:justify-center justify-between items-center">
                                    <span class="md:hidden text-sm text-gray-500">Số lượng:</span>
                                    <div class="flex items-center border border-gray-200 rounded-full bg-white overflow-hidden">
                                        <button aria-label="Decrease quantity" class="w-9 h-9 flex items-center justify-center hover:bg-pink-50 text-gray-600 transition-colors">
                                            <span class="material-symbols-outlined text-sm">remove</span>
                                        </button>
                                        <input aria-label="Quantity" class="w-10 h-9 text-center border-none focus:ring-0 text-sm bg-transparent font-semibold p-0" min="1" type="number" value="1" />
                                        <button aria-label="Increase quantity" class="w-9 h-9 flex items-center justify-center hover:bg-pink-50 text-gray-600 transition-colors">
                                            <span class="material-symbols-outlined text-sm">add</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-span-2 flex justify-between md:block md:text-right">
                                    <span class="md:hidden text-sm text-gray-500">Tổng:</span>
                                    <span class="font-bold text-pink-600">850.000 ₫</span>
                                </div>
                            </div>

                            <div class="p-5 md:p-6 flex flex-col md:grid md:grid-cols-12 gap-5 md:items-center">
                                <div class="col-span-6 flex items-center gap-4">
                                    <button aria-label="Remove item" class="shrink-0 w-9 h-9 rounded-full border border-gray-200 text-gray-400 hover:border-red-200 hover:bg-red-50 hover:text-red-500 transition-colors hidden md:flex items-center justify-center">
                                        <span class="material-symbols-outlined text-lg">close</span>
                                    </button>
                                    <img alt="Lẵng hoa chúc mừng" class="w-24 h-24 object-cover rounded-xl bg-gray-100 border border-gray-100"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuC9s7CzhUSm2EpCUz5E7yMmGGozxT0niO5Vx5uPcTljnj7JpqGB7P8xbPV6wI_RiKofbZ0baGYkEDl85jIezsZwWJ0_xjdKE7jAsCXq-kLNeVWwGxqHEY_gCTYB5WlvThuFYBxmgIgLFu7xZHCrrM9RyXmPAe9b0EY4Z7rgxCQUBa9KuYKYmel99KkyvbBDgxk9izJXPA1Bu1jO7MmYI7jqHsPJhV2O2F49OyHcQEOyuI3CH0WK4RKjT40Coa155n4qWfeBKnQfJkU" />
                                    <div class="min-w-0">
                                        <p class="text-xs text-pink-500 mb-1 uppercase tracking-wider font-semibold">HOA KHAI TRƯƠNG</p>
                                        <h3 class="font-display font-semibold text-lg text-gray-900">Lẵng Hoa Cát Tường</h3>
                                        <button class="text-sm text-red-500 mt-2 md:hidden underline">Xóa</button>
                                    </div>
                                </div>
                                <div class="col-span-2 md:text-center flex justify-between md:block">
                                    <span class="md:hidden text-sm text-gray-500">Giá:</span>
                                    <span class="font-semibold text-gray-900">1.250.000 ₫</span>
                                </div>
                                <div class="col-span-2 flex md:justify-center justify-between items-center">
                                    <span class="md:hidden text-sm text-gray-500">Số lượng:</span>
                                    <div class="flex items-center border border-gray-200 rounded-full bg-white overflow-hidden">
                                        <button aria-label="Decrease quantity" class="w-9 h-9 flex items-center justify-center hover:bg-pink-50 text-gray-600 transition-colors">
                                            <span class="material-symbols-outlined text-sm">remove</span>
                                        </button>
                                        <input aria-label="Quantity" class="w-10 h-9 text-center border-none focus:ring-0 text-sm bg-transparent font-semibold p-0" min="1" type="number" value="1" />
                                        <button aria-label="Increase quantity" class="w-9 h-9 flex items-center justify-center hover:bg-pink-50 text-gray-600 transition-colors">
                                            <span class="material-symbols-outlined text-sm">add</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-span-2 flex justify-between md:block md:text-right">
                                    <span class="md:hidden text-sm text-gray-500">Tổng:</span>
                                    <span class="font-bold text-pink-600">1.250.000 ₫</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between gap-4 sm:items-center mt-6">
                            <router-link :to="{ name: 'product' }" class="text-sm font-semibold text-gray-700 hover:text-pink-600 transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">arrow_back</span>
                                Tiếp tục mua sắm
                            </router-link>
                            <button class="text-sm font-semibold border border-gray-200 bg-white px-6 py-3 rounded-full hover:border-pink-200 hover:bg-pink-50 hover:text-pink-600 transition-colors">
                                Cập nhật giỏ hàng
                            </button>
                        </div>
                    </div>

                    <div class="lg:col-span-4">
                        <div class="bg-white p-6 md:p-7 rounded-2xl border border-gray-100 shadow-sm sticky top-24">
                            <h2 class="font-display text-2xl font-bold mb-6 border-b border-gray-100 pb-4 text-gray-900">
                                Tổng đơn hàng
                            </h2>
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Tạm tính</span>
                                    <span class="font-semibold text-gray-900">2.100.000 ₫</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Phí vận chuyển</span>
                                    <span class="font-semibold text-gray-900">Tính lúc thanh toán</span>
                                </div>
                            </div>
                            <div class="mb-6 pt-6 border-t border-gray-100">
                                <label class="block text-sm font-semibold mb-2 text-gray-900" for="coupon">Mã giảm giá</label>
                                <div class="flex rounded-full border border-gray-200 overflow-hidden bg-gray-50 focus-within:border-pink-300 focus-within:bg-white">
                                    <input class="form-input flex-grow border-0 bg-transparent focus:ring-0 text-sm px-4" id="coupon" placeholder="Nhập mã..." type="text" />
                                    <button class="bg-gray-900 text-white px-5 py-3 text-sm font-semibold hover:bg-pink-600 transition-colors">
                                        Áp dụng
                                    </button>
                                </div>
                            </div>
                            <div class="border-t border-gray-100 pt-5 mb-6">
                                <div class="flex justify-between items-end gap-4">
                                    <span class="text-base font-bold text-gray-900">Tổng cộng</span>
                                    <span class="text-2xl md:text-3xl font-bold text-pink-600">2.100.000 ₫</span>
                                </div>
                                <p class="text-xs text-gray-500 text-right mt-1">Đã bao gồm VAT</p>
                            </div>
                            <router-link to="/checkout" class="w-full bg-pink-600 text-white font-bold py-4 px-4 rounded-full hover:bg-pink-700 transition-colors flex items-center justify-center gap-2 shadow-lg shadow-pink-100">
                                Tiến hành thanh toán
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
