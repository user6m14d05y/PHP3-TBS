<script setup>
import Header_Client from '@/pages/Includes/Layouts/Header_Client.vue';
import Footer_Client from '@/pages/Includes/Layouts/Footer_Client.vue';
</script>
<template>
    <Header_Client />
    <div class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark font-sans antialiased">
        <section class="bg-pink-light dark:bg-pink-dark py-20 text-center transition-colors duration-300">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <p class="text-primary text-sm font-bold tracking-widest uppercase mb-4">Hỗ trợ khách hàng</p>
                <h1 class="text-5xl md:text-6xl font-display font-bold italic mb-6 text-gray-900 dark:text-white">Hướng
                    Dẫn Đặt Hàng</h1>
                <p class="text-lg text-text-muted-light dark:text-text-muted-dark leading-relaxed">
                    Chỉ với vài thao tác đơn giản, những bó hoa tươi thắm sẽ được giao đến tận tay người thương của bạn.
                    Khám phá các bước đặt hoa trực tuyến dễ dàng tại TBS Flora Store.
                </p>
            </div>
        </section>
        <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="space-y-24">
                <div class="flex flex-col md:flex-row items-center gap-12">
                    <div class="w-full md:w-1/2">
                        <img alt="Chọn mẫu hoa" class="rounded-2xl shadow-lg w-full h-80 object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDPGihmDk9vaiqyYTIxw4thr8xkH-flGwdDnbXOtrzt1z9J84Z2HofBoHoC4C1iVEXgUsuXFr6RkgzBpjMahkMOtB_-gCwijnxBEADaBk4AXG2Fs6B6Tb75THfvaRoujtBphiLxroIEq0uVFYWbLaosEliN92rZcw8Ty4weueAqMGfYLF0ZR8Y9Yrj83rXcfKFGvANY1Ymn-OyiKQVo2s40K31in9mGY49NjVSBSt2Gh5MlGgSnWg1RRAPzha4GMMtqacOq-caMZws" />
                    </div>
                    <div class="w-full md:w-1/2 space-y-4">
                        <div
                            class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary/10 text-primary font-bold text-xl mb-2">
                            1</div>
                        <h2 class="text-3xl font-display font-semibold text-gray-900 dark:text-white">Chọn Mẫu Hoa</h2>
                        <p class="text-text-muted-light dark:text-text-muted-dark leading-relaxed">
                            Duyệt qua bộ sưu tập hoa đa dạng của chúng tôi trong phần "Cửa hàng". Bạn có thể sử dụng các
                            bộ lọc theo chủ đề (Hoa Tình Yêu, Hoa Sinh Nhật, Hoa Khai Trương...) hoặc theo loại hoa để
                            nhanh chóng tìm được thiết kế ưng ý nhất.
                        </p>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row-reverse items-center gap-12">
                    <div class="w-full md:w-1/2">
                        <img alt="Thêm vào giỏ hàng" class="rounded-2xl shadow-lg w-full h-80 object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAx4GYmL8rnsiSkD_id7w_3Ziodsm9LRkHe9NTCPFVjyL1esrSkv62hVO0hNVZuT9XnnCMxUpiODxHyT1nNxkEOQMsU3boHPh0hePciM3oOILsX7z7CIqhM4hL6rEZJwjCUamVR34mJmfb-I_vbjjLqt1fbSRLOYFxrzcSqoHY5nr6-xH4Ofd6WBsojg89sSd0O_cp__GtGU7anLLVCgyQyJn4oFcekoIZv0SrQEqh-a1U97G4Dk4ACOe6henfdHNsW0ldcPyCFOek" />
                    </div>
                    <div class="w-full md:w-1/2 space-y-4">
                        <div
                            class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary/10 text-primary font-bold text-xl mb-2">
                            2</div>
                        <h2 class="text-3xl font-display font-semibold text-gray-900 dark:text-white">Thêm Vào Giỏ</h2>
                        <p class="text-text-muted-light dark:text-text-muted-dark leading-relaxed">
                            Khi đã chọn được mẫu hoa, hãy nhấp vào sản phẩm để xem chi tiết. Tại đây, bạn có thể chọn
                            kích cỡ, thêm thiệp chúc mừng hoặc phụ kiện đi kèm. Sau đó, nhấn nút "Thêm Vào Giỏ" để tiếp
                            tục quá trình đặt hàng.
                        </p>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row items-center gap-12">
                    <div class="w-full md:w-1/2">
                        <img alt="Nhập thông tin giao hàng" class="rounded-2xl shadow-lg w-full h-80 object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDnCMWHdXMDb-cG_2EIoxLuRSpWfil71W5bKe2uKS4PJc21sgcSVIDUMIxGIEBwuNJ3S2f1Cr3l0IRor2cG6_w7yn0uKX9mfbDyBzyeN9uKw0rQOqHWRhS_XFIGcvxRalJVICC2CJ71-QBmrP96X4hzP5xhpqdKwZE8Xtg4T7gfW9QeeMbUxMDPukyuunDkj4gICTZbqf6BQV2f31tqqI5MLHY6nVKYdfes1GWR-ZTSYpGl-ysknOar07tBcjyM9dTWBeqahPeNEeI" />
                    </div>
                    <div class="w-full md:w-1/2 space-y-4">
                        <div
                            class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary/10 text-primary font-bold text-xl mb-2">
                            3</div>
                        <h2 class="text-3xl font-display font-semibold text-gray-900 dark:text-white">Nhập Thông Tin
                        </h2>
                        <p class="text-text-muted-light dark:text-text-muted-dark leading-relaxed">
                            Tiến hành thanh toán bằng cách nhấp vào biểu tượng giỏ hàng. Điền đầy đủ thông tin người
                            gửi, thông tin người nhận (tên, số điện thoại, địa chỉ) và chọn thời gian giao hàng mong
                            muốn. Đừng quên để lại lời nhắn yêu thương trên thiệp nhé!
                        </p>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row-reverse items-center gap-12">
                    <div class="w-full md:w-1/2">
                        <img alt="Thanh toán" class="rounded-2xl shadow-lg w-full h-80 object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuClp-txaJbedx-8BSqMcZj8b863APjY3oFqVmGz-1X84uC5wXrA-cPwErSsevydydudQWXt_PSRnTn2SlNr-PZmZWL8Vk-g0wYLy9Dzf7At59nTlgGz_Ku2Usf6qENSk8LjXj3VTn14pdVMoJH6Hw4ad_BItDOimoaP1RwtUaY9LdgcEflCRCn8-TpWUIQGr4Nc6WhZJVqidbgirkU1sywl4ei3MSL8UCAnqk5k-DDv4DTOegPWyCL__VoO8J__3Mf3ZUJsrNiqFo8" />
                    </div>
                    <div class="w-full md:w-1/2 space-y-4">
                        <div
                            class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary/10 text-primary font-bold text-xl mb-2">
                            4</div>
                        <h2 class="text-3xl font-display font-semibold text-gray-900 dark:text-white">Thanh Toán</h2>
                        <p class="text-text-muted-light dark:text-text-muted-dark leading-relaxed">
                            Chọn phương thức thanh toán phù hợp (chuyển khoản ngân hàng, thẻ tín dụng/ghi nợ, hoặc ví
                            điện tử). Sau khi thanh toán thành công, bạn sẽ nhận được email xác nhận đơn hàng cùng với
                            mã vận đơn để theo dõi quá trình giao hoa.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-20 text-center">
                <a class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-medium rounded-md text-white bg-primary hover:bg-primary/90 transition-colors shadow-sm"
                    href="#">
                    Bắt đầu chọn hoa ngay
                </a>
            </div>
        </main>
    </div>
    <Footer_Client />
</template>

<style scoped></style>