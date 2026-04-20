<script setup>
import { ref } from 'vue';
import Header_client from '@/pages/Includes/Layouts/Header_client.vue';
import Footer_client from '@/pages/Includes/Layouts/Footer_client.vue';

const addressOption = ref('new'); // Mặc định chọn form địa chỉ mới
</script>
<template>

    <Header_client />
    <div
        class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark min-h-screen flex flex-col transition-colors duration-300">
        <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="mb-8 text-center sm:text-left">
                <h1 class="text-3xl md:text-4xl font-display font-semibold mb-2">Thanh Toán</h1>
                <p class="text-text-light-muted dark:text-text-dark-muted">Vui lòng hoàn tất thông tin bên dưới để đặt
                    hàng.</p>
            </div>
            <div class="flex flex-col lg:flex-row gap-10">
                <div class="w-full lg:w-3/5 space-y-8">
                    <section
                        class="bg-surface-light dark:bg-surface-dark rounded-lg border border-border-light dark:border-border-dark p-6 sm:p-8 shadow-sm transition-colors duration-300">
                        <h2
                            class="text-xl font-display font-semibold mb-6 flex items-center gap-2 border-b border-border-light dark:border-border-dark pb-3">
                            <span class="material-symbols-outlined text-primary">local_shipping</span>
                            Thông Tin Giao Hàng
                        </h2>
                        <div class="flex p-1 bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark rounded-lg mb-6">
                            <button @click="addressOption = 'saved'" type="button" 
                                :class="{'bg-primary text-white shadow': addressOption === 'saved', 'text-text-light-muted dark:text-text-dark-muted hover:text-text-light dark:hover:text-text-dark': addressOption !== 'saved'}" 
                                class="flex-1 py-4 rounded-md text-sm font-medium transition-all">
                                Địa chỉ đã lưu
                            </button>
                            <button @click="addressOption = 'new'" type="button" 
                                :class="{'bg-primary text-white shadow': addressOption === 'new', 'text-text-light-muted dark:text-text-dark-muted hover:text-text-light dark:hover:text-text-dark': addressOption !== 'saved'}" 
                                class="flex-1 py-4 rounded-md text-sm font-medium transition-all">
                                Địa chỉ mới
                            </button>
                        </div>
                        
                        <!-- Form Địa Chỉ Mới -->
                        <form v-if="addressOption === 'new'" class="space-y-5 animate-fade-in">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="fullName">Họ và Tên <span
                                            class="text-primary">*</span></label>
                                    <input
                                        class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary transition-colors duration-300 shadow-sm"
                                        id="fullName" name="fullName" placeholder="Nhập họ và tên" required=""
                                        type="text" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="phone">Số Điện Thoại <span
                                            class="text-primary">*</span></label>
                                    <input
                                        class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary transition-colors duration-300 shadow-sm"
                                        id="phone" name="phone" placeholder="Nhập số điện thoại" required=""
                                        type="tel" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" for="address">Địa Chỉ Nhận Hàng <span
                                        class="text-primary">*</span></label>
                                <input
                                    class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary transition-colors duration-300 shadow-sm"
                                    id="address" name="address" placeholder="Số nhà, tên đường, phường/xã..."
                                    required="" type="text" />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="city">Tỉnh / Thành Phố <span
                                            class="text-primary">*</span></label>
                                    <select
                                        class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary transition-colors duration-300 shadow-sm"
                                        id="city" name="city" required="">
                                        <option disabled="" selected="" value="">Chọn Tỉnh/Thành</option>
                                        <option value="hcm">TP. Hồ Chí Minh</option>
                                        <option value="hn">Hà Nội</option>
                                        <option value="dn">Đà Nẵng</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="district">Quận / Huyện <span
                                            class="text-primary">*</span></label>
                                    <select
                                        class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary transition-colors duration-300 shadow-sm"
                                        id="district" name="district" required="">
                                        <option disabled="" selected="" value="">Chọn Quận/Huyện</option>
                                        <option value="q1">Quận 1</option>
                                        <option value="q2">Quận 2</option>
                                        <option value="q3">Quận 3</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" for="notes">Ghi Chú Đơn Hàng (Tùy
                                    chọn)</label>
                                <textarea
                                    class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary transition-colors duration-300 shadow-sm"
                                    id="notes" name="notes"
                                    placeholder="Ghi chú thêm về thời gian giao hàng, lời nhắn..." rows="3"></textarea>
                            </div>
                        </form>

                        <!-- Danh Sách Địa Chỉ Đã Lưu (Demo) -->
                        <div v-else class="space-y-4 animate-fade-in">
                            <label class="flex items-start p-4 border border-border-light dark:border-border-dark rounded-lg cursor-pointer hover:border-primary dark:hover:border-primary transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input checked class="mt-1 text-primary focus:ring-primary" name="savedAddress" type="radio" value="1" />
                                <div class="ml-3 flex-grow">
                                    <div class="flex justify-between">
                                        <span class="block font-medium text-text-light dark:text-text-dark">Nguyễn Văn A - 0901234567</span>
                                        <span class="text-xs bg-primary/10 text-primary px-2 py-1 rounded">Mặc định</span>
                                    </div>
                                    <span class="block text-sm text-text-light-muted dark:text-text-dark-muted mt-1">123 Đường Số 1, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh</span>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-4 border border-border-light dark:border-border-dark rounded-lg cursor-pointer hover:border-primary dark:hover:border-primary transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input class="mt-1 text-primary focus:ring-primary" name="savedAddress" type="radio" value="2" />
                                <div class="ml-3 flex-grow">
                                    <div class="flex justify-between">
                                        <span class="block font-medium text-text-light dark:text-text-dark">Nguyễn Văn B - 0987654321</span>
                                    </div>
                                    <span class="block text-sm text-text-light-muted dark:text-text-dark-muted mt-1">456 Lê Lợi, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh</span>
                                </div>
                            </label>
                        </div>
                    </section>
                    <section
                        class="bg-surface-light dark:bg-surface-dark rounded-lg border border-border-light dark:border-border-dark p-6 sm:p-8 shadow-sm transition-colors duration-300">
                        <h2
                            class="text-xl font-display font-semibold mb-6 flex items-center gap-2 border-b border-border-light dark:border-border-dark pb-3">
                            <span class="material-symbols-outlined text-primary">airport_shuttle</span>
                            Phương Thức Vận Chuyển
                        </h2>
                        <div class="space-y-4">
                            <label
                                class="flex items-center p-4 border border-border-light dark:border-border-dark rounded-lg cursor-pointer hover:border-primary dark:hover:border-primary transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input checked="" class="text-primary focus:ring-primary" name="shippingMethod"
                                    type="radio" value="standard" />
                                <div class="ml-4 w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-primary">local_shipping</span>
                                </div>
                                <div class="ml-4 flex-grow flex justify-between items-center">
                                    <div>
                                        <span class="block font-medium text-text-light dark:text-text-dark">Giao Hàng
                                            Tiêu Chuẩn</span>
                                        <span class="block text-sm text-text-light-muted dark:text-text-dark-muted">Nhận
                                            hàng trong 2-3 ngày làm việc</span>
                                    </div>
                                    <span class="font-medium text-text-light dark:text-text-dark">30.000 ₫</span>
                                </div>
                            </label>
                            <label
                                class="flex items-center p-4 border border-border-light dark:border-border-dark rounded-lg cursor-pointer hover:border-primary dark:hover:border-primary transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input class="text-primary focus:ring-primary" name="shippingMethod" type="radio"
                                    value="express" />
                                <div class="ml-4 w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-primary">airport_shuttle</span>
                                </div>
                                <div class="ml-4 flex-grow flex justify-between items-center">
                                    <div>
                                        <span class="block font-medium text-text-light dark:text-text-dark">Giao Hàng
                                            Hỏa Tốc</span>
                                        <span class="block text-sm text-text-light-muted dark:text-text-dark-muted">Nhận
                                            hàng trong vòng 2 giờ (chỉ áp dụng nội thành)</span>
                                    </div>
                                    <span class="font-medium text-text-light dark:text-text-dark">60.000 ₫</span>
                                </div>
                            </label>
                        </div>
                    </section>
                    <section
                        class="bg-surface-light dark:bg-surface-dark rounded-lg border border-border-light dark:border-border-dark p-6 sm:p-8 shadow-sm transition-colors duration-300">
                        <h2
                            class="text-xl font-display font-semibold mb-6 flex items-center gap-2 border-b border-border-light dark:border-border-dark pb-3">
                            <span class="material-symbols-outlined text-primary">payment</span>
                            Phương Thức Thanh Toán
                        </h2>
                        <div class="space-y-4">
                            <div
                                class="border border-border-light dark:border-border-dark rounded-lg overflow-hidden transition-colors has-[:checked]:border-primary">
                                <label
                                    class="flex items-center p-4 cursor-pointer hover:bg-background-light dark:hover:bg-background-dark transition-colors has-[:checked]:bg-primary/5">
                                    <input checked="" class="text-primary focus:ring-primary" name="paymentMethod"
                                        type="radio" value="cod" />
                                    <div class="ml-4 w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-blue-50 text-blue-500 dark:bg-blue-500/10 dark:text-blue-400">
                                        <span class="material-symbols-outlined">payments</span>
                                    </div>
                                    <span class="ml-3 font-medium text-text-light dark:text-text-dark flex-grow">Thanh
                                        toán khi nhận hàng (COD)</span>
                                </label>
                                <div
                                    class="px-4 pb-4 pt-0 ml-[4.5rem] text-sm text-text-light-muted dark:text-text-dark-muted">
                                    Quý khách sẽ thanh toán bằng tiền mặt khi nhân viên giao hàng giao hoa đến nơi.
                                </div>
                            </div>
                            <div
                                class="border border-border-light dark:border-border-dark rounded-lg overflow-hidden transition-colors has-[:checked]:border-primary">
                                <label
                                    class="flex items-center p-4 cursor-pointer hover:bg-background-light dark:hover:bg-background-dark transition-colors has-[:checked]:bg-primary/5">
                                    <input class="text-primary focus:ring-primary peer" name="paymentMethod"
                                        type="radio" value="bank" />
                                    <div class="ml-4 w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-green-50 text-green-500 dark:bg-green-500/10 dark:text-green-400">
                                        <span class="material-symbols-outlined">account_balance</span>
                                    </div>
                                    <span class="ml-3 font-medium text-text-light dark:text-text-dark flex-grow">Chuyển
                                        khoản ngân hàng</span>
                                </label>
                            </div>
                            <div
                                class="border border-border-light dark:border-border-dark rounded-lg overflow-hidden transition-colors has-[:checked]:border-primary">
                                <label
                                    class="flex items-center p-4 cursor-pointer hover:bg-background-light dark:hover:bg-background-dark transition-colors has-[:checked]:bg-primary/5">
                                    <input class="text-primary focus:ring-primary" name="paymentMethod" type="radio"
                                        value="momo" />
                                    <div class="ml-4 w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-[#a50064]/10 dark:bg-[#a50064]/20 p-1">
                                        <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-MoMo-Square.png" alt="Momo" class="w-full h-full object-contain" />
                                    </div>
                                    <span class="ml-3 font-medium text-text-light dark:text-text-dark flex-grow">Thanh
                                        toán qua Ví Momo</span>
                                </label>
                            </div>
                            <div
                                class="border border-border-light dark:border-border-dark rounded-lg overflow-hidden transition-colors has-[:checked]:border-primary">
                                <label
                                    class="flex items-center p-4 cursor-pointer hover:bg-background-light dark:hover:bg-background-dark transition-colors has-[:checked]:bg-primary/5">
                                    <input class="text-primary focus:ring-primary" name="paymentMethod" type="radio"
                                        value="zalo" />
                                    <div class="ml-4 w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-[#005BA6]/10 dark:bg-[#005BA6]/20 p-1">
                                        <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-ZaloPay-Square.png" alt="ZaloPay" class="w-full h-full object-contain rounded-md" />
                                    </div>
                                    <span class="ml-3 font-medium text-text-light dark:text-text-dark flex-grow">Thanh
                                        toán qua ZaloPay</span>
                                </label>
                            </div>
                            <div
                                class="border border-border-light dark:border-border-dark rounded-lg overflow-hidden transition-colors has-[:checked]:border-primary">
                                <label
                                    class="flex items-center p-4 cursor-pointer hover:bg-background-light dark:hover:bg-background-dark transition-colors has-[:checked]:bg-primary/5">
                                    <input class="text-primary focus:ring-primary" name="paymentMethod" type="radio"
                                        value="vnpay" />
                                    <div class="ml-4 w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 border border-border-light dark:border-border-dark p-1 bg-white">
                                        <img src="https://vinadesign.vn/uploads/images/2023/05/vnpay-logo-vinadesign-25-12-57-55.jpg" alt="VNPay" class="w-full h-full object-contain" />
                                    </div>
                                    <span class="ml-3 font-medium text-text-light dark:text-text-dark flex-grow">Thanh
                                        toán qua VNPay</span>
                                </label>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="w-full lg:w-2/5">
                    <div
                        class="bg-primary/5 dark:bg-primary/10 rounded-lg p-6 sm:p-8 sticky top-24 border border-primary/20 dark:border-primary/30">
                        <h2
                            class="text-xl font-display font-semibold mb-6 border-b border-primary/20 dark:border-primary/30 pb-3 text-text-light dark:text-text-dark">
                            Tóm Tắt Đơn Hàng</h2>
                        <div class="space-y-4 mb-6 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-20 h-20 flex-shrink-0 bg-surface-light dark:bg-surface-dark rounded border border-border-light dark:border-border-dark overflow-hidden relative">
                                    <img alt="Bó hoa hồng phấn" class="w-full h-full object-cover"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDIaVOWaLKAzFhGsu6n6SJkqIde9NuKubPjOKZMBMuHSLstwz-8vjT76uayiGXuOdEQsq829qJEcgxclge5VkDB0o83c5Pvm9_qmxYP_mpQWQdOuPpPexNrERAQJLtcDk8vLJMo_Tj01efEjg6b46pdOb1vh9mHVOL5-vMkzpIQvQBnYkpvg_MnyhC0LdyS67AudkhFNAqPIWgkX6Iw3hwuYwKlfSzt-tSfqmEvLIpQ_XzrC3e-5Ms7G5d-FULZxUbmVTOUlG48wLk" />
                                    <span
                                        class="absolute -top-2 -right-2 bg-text-light dark:bg-text-dark text-surface-light dark:text-surface-dark text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full">1</span>
                                </div>
                                <div class="flex-grow">
                                    <h3
                                        class="font-medium text-text-light dark:text-text-dark text-sm sm:text-base leading-tight mb-1">
                                        Bó Hoa Tình Yêu Mộng Mơ</h3>
                                    <p class="text-xs text-text-light-muted dark:text-text-dark-muted mb-2">Loại: Hoa
                                        Hồng Phấn</p>
                                    <p class="font-semibold text-text-light dark:text-text-dark">650.000 ₫</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-20 h-20 flex-shrink-0 bg-surface-light dark:bg-surface-dark rounded border border-border-light dark:border-border-dark overflow-hidden relative">
                                    <img alt="Bó hoa cúc tana" class="w-full h-full object-cover"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDOVypuFstfC1_m-HuK3MYq_8K7mUUSqp5X_-hhT2ayfBLvCphknsarxTl9nBQXqSMaS1L1B4DufPJyrt4qtNVZcVl0-da7oYjAL3gS7nfPD5IxgfDm02e2M_EzxYvMqy7-C9L6K_NvkGHQJyfkDNdSnoCz0c8T2Ne_eZA7jIjV6veL4sxKkMPeH06dJtmSqwVhLnFa5S35fuz-E2HmQ3SPEy_pX2y4r1jyc15POr-mPRJTZn9wsMF8Wwnv1bfuIqW5jjnbEmsLtPg" />
                                    <span
                                        class="absolute -top-2 -right-2 bg-text-light dark:bg-text-dark text-surface-light dark:text-surface-dark text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full">2</span>
                                </div>
                                <div class="flex-grow">
                                    <h3
                                        class="font-medium text-text-light dark:text-text-dark text-sm sm:text-base leading-tight mb-1">
                                        Hoa Cúc Tana Tinh Khôi</h3>
                                    <p class="text-xs text-text-light-muted dark:text-text-dark-muted mb-2">Size: Nhỏ
                                    </p>
                                    <p class="font-semibold text-text-light dark:text-text-dark">320.000 ₫</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-6 flex gap-2 border-b border-primary/20 dark:border-primary/30 pb-6">
                            <input
                                class="flex-grow rounded border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary shadow-sm text-sm"
                                placeholder="Mã giảm giá" type="text" />
                            <button
                                class="bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-light dark:text-text-dark px-4 py-2 rounded hover:bg-background-light dark:hover:bg-background-dark transition-colors font-medium text-sm"
                                type="button">Áp Dụng</button>
                        </div>
                        <div class="space-y-3 mb-6 text-sm">
                            <div class="flex justify-between text-text-light-muted dark:text-text-dark-muted">
                                <span>Tạm tính</span>
                                <span>1.290.000 ₫</span>
                            </div>
                            <div class="flex justify-between text-text-light-muted dark:text-text-dark-muted">
                                <span>Phí vận chuyển</span>
                                <span>30.000 ₫</span>
                            </div>
                        </div>
                        <div
                            class="flex justify-between items-center border-t border-primary/20 dark:border-primary/30 pt-4 mb-8">
                            <span class="font-display font-semibold text-lg text-text-light dark:text-text-dark">Tổng
                                Cộng</span>
                            <span class="font-display font-bold text-2xl text-primary">1.320.000 ₫</span>
                        </div>
                        <button
                            class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-4 px-6 rounded-lg transition-colors flex justify-center items-center gap-2 shadow-md"
                            type="submit">
                            <span class="material-symbols-outlined text-xl">lock</span>
                            Thanh Toán An Toàn
                        </button>
                        <p class="text-center text-xs text-text-light-muted dark:text-text-dark-muted mt-4">Thông tin
                            của bạn được bảo mật tuyệt đối.</p>
                    </div>
                </div>
            </div>
        </main>
        <Footer_client />
    </div>
</template>
<style scoped>

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background-color: #E5E7EB;
                border-radius: 20px;
            }

            .dark .custom-scrollbar::-webkit-scrollbar-thumb {
                background-color: #374151;
            }
            input{
                padding: 13px;
                outline-color: #D14D72;
            }
            select{
                padding: 13px;
                outline-color: #D14D72;
            }
            textarea{
                padding: 13px;
                outline-color: #D14D72;
            }
        </style>