<script setup>
import Header_Client from '@/pages/Includes/Layouts/Header_Client.vue';
import Footer_Client from '@/pages/Includes/Layouts/Footer_Client.vue';
</script>
<template>
    <Header_Client />
    <div class="bg-surface-light dark:bg-slate-800 py-16 md:py-24 text-center">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="text-primary font-semibold tracking-wider uppercase text-sm mb-4 block">Thông Tin Khách
                Hàng</span>
            <h1
                class="text-4xl md:text-5xl font-display font-bold text-heading-light dark:text-heading-dark mb-6 italic">
                Chính Sách Giao Hoa</h1>
            <p class="text-lg text-text-light dark:text-text-dark">
                Chúng tôi cam kết mang đến những bó hoa tươi thắm nhất, nguyên vẹn và đúng hẹn để mọi khoảnh khắc
                của bạn đều trọn vẹn.
            </p>
        </div>
    </div>
    <main class="flex-grow">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="prose prose-lg prose-pink dark:prose-invert max-w-none">
                <div class="mb-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-12 h-12 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center text-primary mr-4">
                            <span class="material-icons">schedule</span>
                        </div>
                        <h2 class="text-2xl font-display font-semibold text-heading-light dark:text-heading-dark m-0">
                            1. Thời Gian Giao Hàng</h2>
                    </div>
                    <p>TBS Flora cung cấp các khung giờ giao hàng linh hoạt để phù hợp với nhu cầu của quý khách:
                    </p>
                    <ul>
                        <li><strong>Giao hàng tiêu chuẩn:</strong> Từ 8:00 đến 20:00 các ngày trong tuần. Đơn hàng
                            sẽ được giao trong vòng 2-4 tiếng kể từ khi xác nhận.</li>
                        <li><strong>Giao hàng hẹn giờ:</strong> Quý khách có thể chọn khung giờ cụ thể (ví dụ: 9:00
                            - 11:00). Phụ phí có thể được áp dụng tùy vào khung giờ và địa điểm.</li>
                        <li><strong>Giao hàng hỏa tốc:</strong> Trong vòng 1-2 tiếng (chỉ áp dụng tại khu vực trung
                            tâm thành phố).</li>
                    </ul>
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic mt-2">* Lưu ý: Trong các dịp Lễ, Tết
                        (Valentine, 8/3, 20/10...), thời gian giao hàng có thể chênh lệch do lượng đơn hàng tăng
                        cao. Mong quý khách thông cảm và đặt hàng sớm.</p>
                </div>
                <div class="mb-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-12 h-12 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center text-primary mr-4">
                            <span class="material-icons">local_shipping</span>
                        </div>
                        <h2 class="text-2xl font-display font-semibold text-heading-light dark:text-heading-dark m-0">
                            2. Phí Vận Chuyển</h2>
                    </div>
                    <p>Phí giao hàng được tính dựa trên khoảng cách từ cửa hàng đến địa chỉ nhận:</p>
                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                        scope="col">Khu vực</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                        scope="col">Khoảng cách</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                        scope="col">Phí giao hàng</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                        Trung tâm TP</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        Dưới 5km</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-primary">Miễn
                                        phí</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                        Nội thành</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        5km - 10km</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        30.000 VNĐ</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                        Ngoại thành</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        Trên 10km</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        50.000 VNĐ - 100.000 VNĐ</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mb-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-12 h-12 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center text-primary mr-4">
                            <span class="material-icons">ac_unit</span>
                        </div>
                        <h2 class="text-2xl font-display font-semibold text-heading-light dark:text-heading-dark m-0">
                            3. Quy Trình Bảo Quản Khi Vận Chuyển</h2>
                    </div>
                    <p>Để đảm bảo hoa đến tay người nhận với tình trạng hoàn hảo nhất, chúng tôi áp dụng quy trình
                        đóng gói và vận chuyển nghiêm ngặt:</p>
                    <ul>
                        <li><strong>Cấp nước liên tục:</strong> Gốc hoa luôn được bọc trong bông tẩm nước hoặc ống
                            tuýp giữ ẩm chuyên dụng trong suốt quá trình vận chuyển.</li>
                        <li><strong>Che chắn cẩn thận:</strong> Bó hoa/lẵng hoa được cố định chắc chắn trong túi
                            giấy hoặc hộp carton thiết kế riêng, bảo vệ hoa khỏi gió, nắng và va đập.</li>
                        <li><strong>Phương tiện chuyên dụng:</strong> Đối với các đơn hàng lớn, hoa lan hồ điệp hoặc
                            kệ hoa chúc mừng, chúng tôi sử dụng xe tải có mái che để đảm bảo chất lượng.</li>
                    </ul>
                </div>
                <div
                    class="bg-pink-50 dark:bg-slate-800/50 p-8 rounded-2xl border border-pink-100 dark:border-slate-700">
                    <div class="flex items-center mb-4">
                        <div class="text-primary mr-3">
                            <span class="material-icons text-3xl">verified</span>
                        </div>
                        <h2 class="text-2xl font-display font-semibold text-heading-light dark:text-heading-dark m-0">
                            4. Cam Kết Giao Hoa Tươi</h2>
                    </div>
                    <p class="mb-0">
                        TBS Flora cam kết 100% hoa được giao đúng mẫu mã (hoặc tương tự 90% tùy thuộc vào mùa hoa),
                        tươi mới và không dập nát.
                        Nếu quý khách không hài lòng với chất lượng hoa khi nhận, vui lòng phản hồi ngay trong vòng
                        <strong>2 giờ</strong> kèm theo hình ảnh. Chúng tôi sẽ tiến hành đổi trả hoặc hoàn tiền 100%
                        theo chính sách bảo hành.
                    </p>
                </div>
            </div>
        </div>
    </main>
    <Footer_Client />
</template>
<style scoped>
body {
    font-family: 'Plus Jakarta Sans', sans-serif;
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