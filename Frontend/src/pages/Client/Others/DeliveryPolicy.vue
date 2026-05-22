<script setup>
import Header_Client from '@/pages/Includes/Layouts/Header_Client.vue';
import Footer_Client from '@/pages/Includes/Layouts/Footer_Client.vue';
</script>

<template>
    <Header_Client />
    <div class="min-h-screen bg-[#fbf7f8] text-gray-900">
        <section class="bg-gradient-to-r from-pink-50 via-rose-50 to-pink-100 py-16 md:py-20 text-center border-b border-pink-100">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <span class="text-pink-600 font-semibold tracking-[0.3em] uppercase text-xs mb-4 block">Thông tin khách hàng</span>
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-5">Chính sách giao hoa</h1>
                <p class="text-base md:text-lg text-gray-600 leading-relaxed">
                    Chúng tôi cam kết mang đến những bó hoa tươi thắm nhất, nguyên vẹn và đúng hẹn để mọi khoảnh khắc của bạn đều trọn vẹn.
                </p>
            </div>
        </section>

        <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <div class="space-y-8">
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-12 h-12 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center">
                            <span class="material-symbols-outlined">schedule</span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">1. Thời gian giao hàng</h2>
                    </div>
                    <p class="text-gray-600 leading-relaxed mb-4">TBS Flora cung cấp các khung giờ giao hàng linh hoạt để phù hợp với nhu cầu của quý khách:</p>
                    <ul class="list-disc pl-6 space-y-3 text-gray-600 leading-relaxed">
                        <li><strong class="text-gray-900">Giao hàng tiêu chuẩn:</strong> Từ 8:00 đến 20:00 các ngày trong tuần. Đơn hàng sẽ được giao trong vòng 2-4 tiếng kể từ khi xác nhận.</li>
                        <li><strong class="text-gray-900">Giao hàng hẹn giờ:</strong> Quý khách có thể chọn khung giờ cụ thể. Phụ phí có thể được áp dụng tùy vào khung giờ và địa điểm.</li>
                        <li><strong class="text-gray-900">Giao hàng hỏa tốc:</strong> Trong vòng 1-2 tiếng, chỉ áp dụng tại khu vực trung tâm thành phố.</li>
                    </ul>
                    <p class="text-sm text-gray-500 italic mt-4">* Trong các dịp lễ, Tết, thời gian giao hàng có thể chênh lệch do lượng đơn hàng tăng cao.</p>
                </section>

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-12 h-12 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center">
                            <span class="material-symbols-outlined">local_shipping</span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">2. Phí vận chuyển</h2>
                    </div>
                    <p class="text-gray-600 leading-relaxed mb-6">Phí giao hàng được tính dựa trên khoảng cách từ cửa hàng đến địa chỉ nhận:</p>
                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Khu vực</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Khoảng cách</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Phí giao hàng</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <tr>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">Trung tâm TP</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Dưới 5km</td>
                                    <td class="px-6 py-4 text-sm font-bold text-pink-600">Miễn phí</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">Nội thành</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">5km - 10km</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">30.000 VNĐ</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">Ngoại thành</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Trên 10km</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">50.000 VNĐ - 100.000 VNĐ</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-12 h-12 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center">
                            <span class="material-symbols-outlined">ac_unit</span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">3. Quy trình bảo quản khi vận chuyển</h2>
                    </div>
                    <ul class="list-disc pl-6 space-y-3 text-gray-600 leading-relaxed">
                        <li><strong class="text-gray-900">Cấp nước liên tục:</strong> Gốc hoa luôn được giữ ẩm trong suốt quá trình vận chuyển.</li>
                        <li><strong class="text-gray-900">Che chắn cẩn thận:</strong> Bó hoa/lẵng hoa được cố định chắc chắn, hạn chế va đập.</li>
                        <li><strong class="text-gray-900">Phương tiện phù hợp:</strong> Với đơn hàng lớn, chúng tôi sử dụng phương tiện chuyên dụng để đảm bảo chất lượng.</li>
                    </ul>
                </section>

                <section class="bg-pink-50 rounded-2xl border border-pink-100 p-6 md:p-8">
                    <div class="flex items-center gap-4 mb-4">
                        <span class="material-symbols-outlined text-3xl text-pink-600">verified</span>
                        <h2 class="text-2xl font-bold text-gray-900">4. Cam kết giao hoa tươi</h2>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        TBS Flora cam kết hoa được giao đúng mẫu mã, tươi mới và không dập nát. Nếu quý khách không hài lòng, vui lòng phản hồi trong vòng <strong class="text-gray-900">2 giờ</strong> kèm hình ảnh để được hỗ trợ đổi trả hoặc hoàn tiền.
                    </p>
                </section>
            </div>
        </main>
    </div>
    <Footer_Client />
</template>
