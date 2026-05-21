<script setup>
import Header_Client from '@/pages/Includes/Layouts/Header_Client.vue';
import Footer_Client from '@/pages/Includes/Layouts/Footer_Client.vue';
</script>

<template>
    <Header_Client />
    <div class="min-h-screen bg-[#fbf7f8] text-gray-900">
        <section class="bg-gradient-to-r from-pink-50 via-rose-50 to-pink-100 py-16 md:py-20 text-center border-b border-pink-100">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <span class="text-pink-600 font-semibold tracking-[0.3em] uppercase text-xs mb-4 block">Chính sách hỗ trợ</span>
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-5">Đổi trả & hoàn tiền</h1>
                <p class="text-base md:text-lg text-gray-600 leading-relaxed">
                    TBS luôn mong muốn mang đến sản phẩm hoa tươi chất lượng. Nếu có sự cố xảy ra, chúng tôi luôn sẵn sàng hỗ trợ bạn theo chính sách dưới đây.
                </p>
            </div>
        </section>

        <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <div class="space-y-8">
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-5 pb-4 border-b border-gray-100">1. Trường hợp được áp dụng đổi trả</h2>
                    <p class="text-gray-600 leading-relaxed mb-4">Chúng tôi chấp nhận đổi trả sản phẩm hoặc hoàn tiền trong các trường hợp sau:</p>
                    <ul class="list-disc pl-6 space-y-3 text-gray-600 leading-relaxed">
                        <li><strong class="text-gray-900">Hoa không đúng với mẫu mã:</strong> Sản phẩm thực tế khác biệt quá lớn so với hình ảnh trên website.</li>
                        <li><strong class="text-gray-900">Hoa bị dập nát, héo úa:</strong> Hoa bị hư hỏng nghiêm trọng trong quá trình vận chuyển.</li>
                        <li><strong class="text-gray-900">Giao sai sản phẩm:</strong> Khách hàng nhận được sản phẩm không đúng với đơn đặt hàng.</li>
                        <li><strong class="text-gray-900">Lỗi phụ kiện:</strong> Giỏ, bình, hộp đựng bị nứt vỡ hoặc thiệp ghi sai thông điệp do lỗi của TBS.</li>
                    </ul>
                </section>

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-5 pb-4 border-b border-gray-100">2. Trường hợp không được đổi trả</h2>
                    <ul class="list-disc pl-6 space-y-3 text-gray-600 leading-relaxed">
                        <li>Người nhận không hài lòng với loại hoa, màu sắc nhưng đúng với đơn đặt hàng ban đầu.</li>
                        <li>Hoa héo do khách hàng không bảo quản đúng cách sau khi nhận.</li>
                        <li>Khách hàng cung cấp sai thông tin giao hàng dẫn đến việc giao muộn làm hoa kém tươi.</li>
                        <li>Người nhận từ chối nhận hoa.</li>
                    </ul>
                </section>

                <section class="bg-pink-50 rounded-2xl border border-pink-100 p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Thời gian yêu cầu đổi trả</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Quý khách vui lòng thông báo cho TBS về sự cố trong vòng <strong class="text-pink-600 text-xl">02 giờ</strong> kể từ thời điểm nhận hoa thành công.
                    </p>
                    <p class="text-sm mt-3 text-gray-500 leading-relaxed">
                        Do đặc thù hoa tươi là mặt hàng mau hỏng, việc thông báo sớm giúp chúng tôi xử lý và xác minh tình trạng hoa chính xác hơn.
                    </p>
                </section>

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8 pb-4 border-b border-gray-100">4. Quy trình xử lý đổi trả / hoàn tiền</h2>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="w-9 h-9 rounded-full bg-pink-600 text-white flex items-center justify-center font-bold shrink-0">1</div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Liên hệ phản ánh</h3>
                                <p class="text-gray-600 leading-relaxed">Quý khách liên hệ Hotline hoặc Zalo của TBS, cung cấp mã đơn hàng và hình ảnh/video tình trạng sản phẩm lúc nhận.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-9 h-9 rounded-full bg-pink-600 text-white flex items-center justify-center font-bold shrink-0">2</div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Xác minh thông tin</h3>
                                <p class="text-gray-600 leading-relaxed">Nhân viên CSKH sẽ tiếp nhận và xác minh tình trạng hoa trong thời gian sớm nhất.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-9 h-9 rounded-full bg-pink-600 text-white flex items-center justify-center font-bold shrink-0">3</div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Phương án giải quyết</h3>
                                <p class="text-gray-600 leading-relaxed">Nếu lỗi thuộc về TBS, chúng tôi sẽ đề xuất giao lại sản phẩm mới, hoàn trả một phần hoặc hoàn trả toàn bộ tiền.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-9 h-9 rounded-full bg-pink-600 text-white flex items-center justify-center font-bold shrink-0">4</div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Thực hiện hoàn tiền</h3>
                                <p class="text-gray-600 leading-relaxed">Thời gian hoàn tiền từ 1-3 ngày làm việc hoặc tùy thuộc vào thời gian xử lý của cổng thanh toán.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Cần hỗ trợ nhanh?</h3>
                    <p class="mb-6 text-gray-600">Liên hệ bộ phận CSKH để được giải quyết ngay lập tức.</p>
                    <a class="inline-flex items-center justify-center px-6 py-3 rounded-full text-white bg-pink-600 hover:bg-pink-700 font-semibold transition-colors" href="tel:1900xxxx">
                        <span class="material-symbols-outlined mr-2 text-sm">phone</span> Hotline: 1900 xxxx
                    </a>
                </section>
            </div>
        </main>
    </div>
    <Footer_Client />
</template>
