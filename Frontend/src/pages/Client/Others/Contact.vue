<script setup>
import { ref } from 'vue';
import Header_Client from '@/pages/Includes/Layouts/Header_Client.vue';
import Footer_Client from '@/pages/Includes/Layouts/Footer_Client.vue';
</script>

<template>
    <Header_Client />
    <div class="bg-primary-light dark:bg-surface-dark py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-primary font-medium tracking-widest uppercase text-sm mb-4 block">Chúng Tôi Luôn Lắng
                Nghe</span>
            <h1
                class="text-4xl md:text-5xl lg:text-6xl font-display font-bold text-gray-900 dark:text-white mb-6 italic">
                Liên Hệ &amp; Góp Ý</h1>
            <p class="max-w-2xl mx-auto text-lg text-text-muted-light dark:text-text-muted-dark">
                Bạn có câu hỏi, ý tưởng hay cần hỗ trợ? Đừng ngần ngại để lại lời nhắn cho TBS Flora. Chúng tôi sẽ
                phản hồi bạn trong thời gian sớm nhất.
            </p>
        </div>
    </div>
    <main class="flex-grow py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24">
                <div class="space-y-12">
                    <div>
                        <h2 class="text-3xl font-display font-bold text-gray-900 dark:text-white mb-8">Thông Tin Cửa
                            Hàng</h2>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-12 h-12 rounded-full bg-primary-light dark:bg-gray-800 flex items-center justify-center flex-shrink-0 text-primary">
                                    <span class="material-symbols-outlined">location_on</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Địa chỉ
                                    </h3>
                                    <p class="text-text-muted-light dark:text-text-muted-dark leading-relaxed">
                                        123 Đường Hoa Hồng, Phường Mùa Xuân,<br />
                                        Quận 1, TP. Hồ Chí Minh
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-12 h-12 rounded-full bg-primary-light dark:bg-gray-800 flex items-center justify-center flex-shrink-0 text-primary">
                                    <span class="material-symbols-outlined">call</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Hotline
                                        (Miễn phí)</h3>
                                    <p class="text-text-muted-light dark:text-text-muted-dark">
                                        <a class="hover:text-primary transition-colors" href="tel:18001234">1800
                                            1234</a><br />
                                        <span class="text-sm">Hỗ trợ từ 8:00 - 21:00 hàng ngày</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-12 h-12 rounded-full bg-primary-light dark:bg-gray-800 flex items-center justify-center flex-shrink-0 text-primary">
                                    <span class="material-symbols-outlined">mail</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Email</h3>
                                    <p class="text-text-muted-light dark:text-text-muted-dark">
                                        <a class="hover:text-primary transition-colors"
                                            href="mailto:support@tbsflora.com">support@tbsflora.com</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="rounded-xl overflow-hidden shadow-sm border border-border-light dark:border-border-dark h-[300px] bg-surface-light dark:bg-surface-dark relative">
                        <div
                            class="absolute inset-0 flex items-center justify-center flex-col text-text-muted-light dark:text-text-muted-dark">
                            <span class="material-symbols-outlined text-4xl mb-2 opacity-50">map</span>
                            <span class="text-sm font-medium">Bản đồ vị trí cửa hàng</span>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl shadow-xl shadow-primary/5 dark:shadow-none border border-border-light/50 dark:border-border-dark p-8 md:p-12">
                    <h2 class="text-3xl font-display font-bold text-gray-900 dark:text-white mb-2">Gửi Tin Nhắn</h2>
                    <p class="text-text-muted-light dark:text-text-muted-dark mb-8">Điền thông tin bên dưới, chúng
                        tôi sẽ liên hệ lại sớm nhất.</p>
                    <form action="#" class="space-y-6" method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    for="name">Họ và Tên *</label>
                                <input
                                    class="block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:border-primary focus:ring-primary dark:focus:border-primary dark:focus:ring-primary sm:text-sm transition-colors px-4 py-3"
                                    id="name" name="name" placeholder="Vd: Nguyễn Văn A" required="" type="text" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    for="email">Email *</label>
                                <input
                                    class="block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:border-primary focus:ring-primary dark:focus:border-primary dark:focus:ring-primary sm:text-sm transition-colors px-4 py-3"
                                    id="email" name="email" placeholder="Địa chỉ email của bạn" required=""
                                    type="email" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                for="subject">Chủ đề *</label>
                            <select
                                class="block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:border-primary focus:ring-primary dark:focus:border-primary dark:focus:ring-primary sm:text-sm transition-colors px-4 py-3"
                                id="subject" name="subject" required="">
                                <option disabled="" selected="" value="">Chọn chủ đề bạn muốn liên hệ</option>
                                <option value="support">Hỗ trợ đơn hàng</option>
                                <option value="feedback">Góp ý dịch vụ</option>
                                <option value="partnership">Hợp tác kinh doanh</option>
                                <option value="other">Vấn đề khác</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                for="message">Lời nhắn *</label>
                            <textarea
                                class="block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:border-primary focus:ring-primary dark:focus:border-primary dark:focus:ring-primary sm:text-sm transition-colors px-4 py-3"
                                id="message" name="message" placeholder="Viết nội dung lời nhắn của bạn ở đây..."
                                required="" rows="5"></textarea>
                        </div>
                        <div>
                            <button
                                class="w-full flex justify-center items-center gap-2 py-4 px-6 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-background-dark transition-colors"
                                type="submit">
                                <span>Gửi Tin Nhắn</span>
                                <span class="material-symbols-outlined text-xl">send</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <Footer_Client />
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