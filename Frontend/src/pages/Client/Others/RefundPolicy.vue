<script setup>
import Header_Client from '@/pages/Includes/Layouts/Header_Client.vue';
import Footer_Client from '@/pages/Includes/Layouts/Footer_Client.vue';
</script>
<template>

    <Header_Client />
        <section class="bg-surface-light dark:bg-surface-dark py-20 px-4 text-center">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-sm font-bold tracking-widest text-primary uppercase mb-4">Chính Sách Hỗ Trợ</h2>
                <h1 class="text-5xl md:text-6xl font-display font-bold text-text-light dark:text-text-dark mb-6 italic">
                    Đổi Trả &amp; Hoàn Tiền</h1>
                <p class="text-lg text-text-muted-light dark:text-text-muted-dark leading-relaxed">
                    TBS luôn mong muốn mang đến những sản phẩm hoa tươi chất lượng nhất. Tuy nhiên, nếu có bất kỳ sự cố
                    nào xảy ra, chúng tôi luôn sẵn sàng hỗ trợ bạn theo chính sách dưới đây.
                </p>
            </div>
        </section>
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div
                class="prose prose-lg dark:prose-invert max-w-none prose-headings:font-display prose-headings:font-semibold prose-a:text-primary">
                <h2 class="text-2xl mb-6 border-b pb-2 border-gray-200 dark:border-gray-800">1. Trường hợp được áp dụng
                    đổi trả</h2>
                <p>Chúng tôi chấp nhận đổi trả sản phẩm hoặc hoàn tiền trong các trường hợp sau:</p>
                <ul class="list-disc pl-6 mb-8 space-y-2 text-text-muted-light dark:text-text-muted-dark">
                    <li><strong class="text-text-light dark:text-text-dark">Hoa không đúng với mẫu mã:</strong> Sản phẩm
                        thực tế khác biệt quá lớn so với hình ảnh trên website (không tính sự khác biệt nhỏ do tính chất
                        mùa vụ của hoa).</li>
                    <li><strong class="text-text-light dark:text-text-dark">Hoa bị dập nát, héo úa:</strong> Hoa bị hư
                        hỏng nghiêm trọng trong quá trình vận chuyển trước khi đến tay người nhận.</li>
                    <li><strong class="text-text-light dark:text-text-dark">Giao sai sản phẩm:</strong> Khách hàng nhận
                        được sản phẩm không đúng với đơn đặt hàng.</li>
                    <li><strong class="text-text-light dark:text-text-dark">Lỗi phụ kiện:</strong> Giỏ, bình, hộp đựng
                        bị nứt vỡ hoặc thiệp ghi sai thông điệp do lỗi của TBS.</li>
                </ul>
                <h2 class="text-2xl mb-6 border-b pb-2 border-gray-200 dark:border-gray-800 mt-12">2. Trường hợp KHÔNG
                    được đổi trả</h2>
                <p>Chúng tôi xin phép từ chối đổi trả trong các trường hợp sau:</p>
                <ul class="list-disc pl-6 mb-8 space-y-2 text-text-muted-light dark:text-text-muted-dark">
                    <li>Người nhận không hài lòng với loại hoa, màu sắc nhưng đúng với đơn đặt hàng ban đầu.</li>
                    <li>Hoa héo do khách hàng không bảo quản đúng cách sau khi nhận.</li>
                    <li>Khách hàng cung cấp sai thông tin giao hàng dẫn đến việc giao muộn làm hoa kém tươi.</li>
                    <li>Người nhận từ chối nhận hoa.</li>
                </ul>
                <h2 class="text-2xl mb-6 border-b pb-2 border-gray-200 dark:border-gray-800 mt-12">3. Thời gian yêu cầu
                    đổi trả</h2>
                <div
                    class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg mb-8 border border-gray-100 dark:border-gray-800">
                    <p class="mb-0 text-text-light dark:text-text-dark">Quý khách vui lòng thông báo cho TBS về sự cố
                        trong vòng <strong class="text-primary text-xl">02 giờ</strong> kể từ thời điểm nhận hoa thành
                        công.</p>
                    <p class="text-sm mt-2 text-text-muted-light dark:text-text-muted-dark">Do đặc thù của sản phẩm hoa
                        tươi là mặt hàng mau hỏng, việc thông báo sớm giúp chúng tôi có thể xử lý và xác minh tình trạng
                        hoa một cách chính xác nhất.</p>
                </div>
                <h2 class="text-2xl mb-6 border-b pb-2 border-gray-200 dark:border-gray-800 mt-12">4. Quy trình xử lý
                    đổi trả / hoàn tiền</h2>
                <div class="space-y-8 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0 mr-4">
                            <div
                                class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">
                                1</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mt-0 mb-2">Liên hệ phản ánh</h3>
                            <p class="text-text-muted-light dark:text-text-muted-dark mb-0">Quý khách liên hệ qua
                                Hotline hoặc Zalo của TBS, cung cấp mã đơn hàng và hình ảnh/video chụp rõ tình trạng sản
                                phẩm ngay lúc nhận.</p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex-shrink-0 mr-4">
                            <div
                                class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">
                                2</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mt-0 mb-2">Xác minh thông tin</h3>
                            <p class="text-text-muted-light dark:text-text-muted-dark mb-0">Nhân viên CSKH sẽ tiếp nhận
                                và xác minh tình trạng hoa trong thời gian sớm nhất (không quá 1 giờ làm việc).</p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex-shrink-0 mr-4">
                            <div
                                class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">
                                3</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mt-0 mb-2">Phương án giải quyết</h3>
                            <p class="text-text-muted-light dark:text-text-muted-dark mb-0">Nếu lỗi thuộc về TBS, chúng
                                tôi sẽ đề xuất các phương án: Giao lại sản phẩm mới, Hoàn trả một phần tiền hoặc Hoàn
                                trả toàn bộ tiền tùy theo mức độ ảnh hưởng.</p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex-shrink-0 mr-4">
                            <div
                                class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">
                                4</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mt-0 mb-2">Thực hiện hoàn tiền (nếu có)</h3>
                            <p class="text-text-muted-light dark:text-text-muted-dark mb-0">Thời gian hoàn tiền sẽ từ
                                1-3 ngày làm việc (đối với chuyển khoản ngân hàng) hoặc tùy thuộc vào thời gian xử lý
                                của cổng thanh toán điện tử.</p>
                        </div>
                    </div>
                </div>
                <div
                    class="mt-12 bg-pink-50 dark:bg-gray-900 border border-pink-100 dark:border-gray-800 p-8 text-center rounded-lg">
                    <h3 class="text-2xl font-display mb-4">Cần Hỗ Trợ Nhanh?</h3>
                    <p class="mb-6 text-text-muted-light dark:text-text-muted-dark">Đừng ngần ngại liên hệ với bộ phận
                        CSKH của chúng tôi để được giải quyết ngay lập tức.</p>
                    <div class="flex justify-center gap-4">
                        <a class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary hover:bg-pink-700 transition-colors"
                            href="tel:1900xxxx">
                            <span class="material-icons mr-2 text-sm">phone</span> Hotline: 1900 xxxx
                        </a>
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