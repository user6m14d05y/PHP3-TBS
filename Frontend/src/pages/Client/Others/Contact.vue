<script setup>
import Header_Client from '@/pages/Includes/Layouts/Header_client.vue';
import Footer_Client from '@/pages/Includes/Layouts/Footer_client.vue';
import { setPageSeo } from '@/utils/seo';

setPageSeo({
    title: 'Contact TBS Flower Shop | Fresh flower consultation',
    description: 'Contact TBS Flower Shop for flower recommendations, occasion orders, and delivery support.',
    path: '/contact',
    image: '/favicon.ico',
});
</script>

<template>
    <Header_Client />
    <div class="min-h-screen bg-[#fbf7f8] text-gray-900">
        <section class="bg-gradient-to-r from-pink-50 via-rose-50 to-pink-100 py-16 md:py-20 text-center border-b border-pink-100">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <span class="text-pink-600 font-semibold tracking-[0.3em] uppercase text-xs mb-4 block">Chúng tôi luôn lắng nghe</span>
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-5">Liên hệ & góp ý</h1>
                <p class="text-base md:text-lg text-gray-600 leading-relaxed">
                    Bạn có câu hỏi, ý tưởng hay cần hỗ trợ? Đừng ngần ngại để lại lời nhắn cho TBS Flora.
                </p>
            </div>
        </section>

        <main class="py-12 md:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-start">
                    <div class="space-y-8">
                        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8">Thông tin cửa hàng</h2>
                            <div class="space-y-6">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center shrink-0 text-pink-600">
                                        <span class="material-symbols-outlined">location_on</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">Địa chỉ</h3>
                                        <p class="text-gray-600 leading-relaxed">
                                            123 Đường Phan Chu Trinh, Phường Thắng Lợi,<br />
                                            TP. Buôn Ma Thuột, Đắk Lắk
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center shrink-0 text-pink-600">
                                        <span class="material-symbols-outlined">call</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">Hotline</h3>
                                        <p class="text-gray-600">
                                            <a class="hover:text-pink-600 transition-colors font-semibold" href="tel:18001234">1800 1234</a><br />
                                            <span class="text-sm">Hỗ trợ từ 8:00 - 21:00 hàng ngày</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center shrink-0 text-pink-600">
                                        <span class="material-symbols-outlined">mail</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">Email</h3>
                                        <p class="text-gray-600">
                                            <a class="hover:text-pink-600 transition-colors font-semibold" href="mailto:support@tbsflora.com">support@tbsflora.com</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <div class="rounded-2xl overflow-hidden border border-gray-100 h-[300px] bg-white shadow-sm">
                            <iframe 
                                src="https://maps.google.com/maps?q=123%20Phan%20Chu%20Trinh,%20Th%E1%BA%AFng%20L%E1%BB%A3i,%20Th%C3%A0nh%20ph%E1%BB%91%20Bu%C3%B4n%20Ma%20Thu%E1%BB%99t,%20%C4%90%E1%BA%AFk%20L%E1%BA%AFk,%20Vietnam&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>

                    <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 lg:p-10">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Gửi tin nhắn</h2>
                        <p class="text-gray-600 mb-8">Điền thông tin bên dưới, chúng tôi sẽ liên hệ lại sớm nhất.</p>

                        <form action="#" class="space-y-6" method="POST">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2" for="name">Họ và tên *</label>
                                    <input class="block w-full rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-pink-300 focus:ring-pink-200 sm:text-sm transition-colors px-4 py-3" id="name" name="name" placeholder="Vd: Nguyễn Văn A" required type="text" />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2" for="email">Email *</label>
                                    <input class="block w-full rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-pink-300 focus:ring-pink-200 sm:text-sm transition-colors px-4 py-3" id="email" name="email" placeholder="Địa chỉ email của bạn" required type="email" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2" for="subject">Chủ đề *</label>
                                <select class="block w-full rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-pink-300 focus:ring-pink-200 sm:text-sm transition-colors px-4 py-3" id="subject" name="subject" required>
                                    <option disabled selected value="">Chọn chủ đề bạn muốn liên hệ</option>
                                    <option value="support">Hỗ trợ đơn hàng</option>
                                    <option value="feedback">Góp ý dịch vụ</option>
                                    <option value="partnership">Hợp tác kinh doanh</option>
                                    <option value="other">Vấn đề khác</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2" for="message">Lời nhắn *</label>
                                <textarea class="block w-full rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-pink-300 focus:ring-pink-200 sm:text-sm transition-colors px-4 py-3" id="message" name="message" placeholder="Viết nội dung lời nhắn của bạn ở đây..." required rows="5"></textarea>
                            </div>

                            <button class="w-full flex justify-center items-center gap-2 py-4 px-6 rounded-full text-base font-bold text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-400 transition-colors shadow-lg shadow-pink-100" type="submit">
                                <span>Gửi tin nhắn</span>
                                <span class="material-symbols-outlined text-xl">send</span>
                            </button>
                        </form>
                    </section>
                </div>
            </div>
        </main>
    </div>
    <Footer_Client />
</template>
