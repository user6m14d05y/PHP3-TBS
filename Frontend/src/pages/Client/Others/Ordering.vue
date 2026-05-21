<script setup>
import Header_Client from '@/pages/Includes/Layouts/Header_Client.vue';
import Footer_Client from '@/pages/Includes/Layouts/Footer_Client.vue';
</script>

<template>
    <Header_Client />
    <div class="min-h-screen bg-[#fbf7f8] text-gray-900">
        <section class="bg-gradient-to-r from-pink-50 via-rose-50 to-pink-100 py-16 md:py-20 text-center border-b border-pink-100">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <span class="text-pink-600 text-xs font-semibold tracking-[0.3em] uppercase mb-4 block">Hỗ trợ khách hàng</span>
                <h1 class="text-4xl md:text-6xl font-bold mb-5 text-gray-900">Hướng dẫn đặt hàng</h1>
                <p class="text-base md:text-lg text-gray-600 leading-relaxed">
                    Chỉ với vài thao tác đơn giản, những bó hoa tươi thắm sẽ được giao đến tận tay người thương của bạn.
                </p>
            </div>
        </section>

        <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <div class="space-y-10 md:space-y-14">
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <img alt="Chọn mẫu hoa" class="rounded-2xl w-full h-72 md:h-80 object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDPGihmDk9vaiqyYTIxw4thr8xkH-flGwdDnbXOtrzt1z9J84Z2HofBoHoC4C1iVEXgUsuXFr6RkgzBpjMahkMOtB_-gCwijnxBEADaBk4AXG2Fs6B6Tb75THfvaRoujtBphiLxroIEq0uVFYWbLaosEliN92rZcw8Ty4weueAqMGfYLF0ZR8Y9Yrj83rXcfKFGvANY1Ymn-OyiKQVo2s40K31in9mGY49NjVSBSt2Gh5MlGgSnWg1RRAPzha4GMMtqacOq-caMZws" />
                    <div>
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-pink-100 text-pink-600 font-bold text-xl mb-4">1</div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Chọn mẫu hoa</h2>
                        <p class="text-gray-600 leading-relaxed">
                            Duyệt qua bộ sưu tập hoa trong phần cửa hàng. Bạn có thể dùng bộ lọc theo chủ đề hoặc loại hoa để nhanh chóng tìm được thiết kế ưng ý.
                        </p>
                    </div>
                </section>

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <img alt="Thêm vào giỏ hàng" class="rounded-2xl w-full h-72 md:h-80 object-cover md:order-2" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAx4GYmL8rnsiSkD_id7w_3Ziodsm9LRkHe9NTCPFVjyL1esrSkv62hVO0hNVZuT9XnnCMxUpiODxHyT1nNxkEOQMsU3boHPh0hePciM3oOILsX7z7CIqhM4hL6rEZJwjCUamVR34mJmfb-I_vbjjLqt1fbSRLOYFxrzcSqoHY5nr6-xH4Ofd6WBsojg89sSd0O_cp__GtGU7anLLVCgyQyJn4oFcekoIZv0SrQEqh-a1U97G4Dk4ACOe6henfdHNsW0ldcPyCFOek" />
                    <div>
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-pink-100 text-pink-600 font-bold text-xl mb-4">2</div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Thêm vào giỏ</h2>
                        <p class="text-gray-600 leading-relaxed">
                            Khi đã chọn được mẫu hoa, hãy xem chi tiết sản phẩm, chọn kích cỡ hoặc phụ kiện nếu có, sau đó nhấn nút thêm vào giỏ.
                        </p>
                    </div>
                </section>

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <img alt="Nhập thông tin giao hàng" class="rounded-2xl w-full h-72 md:h-80 object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDnCMWHdXMDb-cG_2EIoxLuRSpWfil71W5bKe2uKS4PJc21sgcSVIDUMIxGIEBwuNJ3S2f1Cr3l0IRor2cG6_w7yn0uKX9mfbDyBzyeN9uKw0rQOqHWRhS_XFIGcvxRalJVICC2CJ71-QBmrP96X4hzP5xhpqdKwZE8Xtg4T7gfW9QeeMbUxMDPukyuunDkj4gICTZbqf6BQV2f31tqqI5MLHY6nVKYdfes1GWR-ZTSYpGl-ysknOar07tBcjyM9dTWBeqahPeNEeI" />
                    <div>
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-pink-100 text-pink-600 font-bold text-xl mb-4">3</div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Nhập thông tin</h2>
                        <p class="text-gray-600 leading-relaxed">
                            Điền đầy đủ thông tin người gửi, người nhận, địa chỉ giao hàng, thời gian giao mong muốn và lời nhắn trên thiệp.
                        </p>
                    </div>
                </section>

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <img alt="Thanh toán" class="rounded-2xl w-full h-72 md:h-80 object-cover md:order-2" src="https://lh3.googleusercontent.com/aida-public/AB6AXuClp-txaJbedx-8BSqMcZj8b863APjY3oFqVmGz-1X84uC5wXrA-cPwErSsevydydudQWXt_PSRnTn2SlNr-PZmZWL8Vk-g0wYLy9Dzf7At59nTlgGz_Ku2Usf6qENSk8LjXj3VTn14pdVMoJH6Hw4ad_BItDOimoaP1RwtUaY9LdgcEflCRCn8-TpWUIQGr4Nc6WhZJVqidbgirkU1sywl4ei3MSL8UCAnqk5k-DDv4DTOegPWyCL__VoO8J__3Mf3ZUJsrNiqFo8" />
                    <div>
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-pink-100 text-pink-600 font-bold text-xl mb-4">4</div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Thanh toán</h2>
                        <p class="text-gray-600 leading-relaxed">
                            Chọn phương thức thanh toán phù hợp. Sau khi thanh toán thành công, bạn sẽ nhận được thông tin xác nhận đơn hàng.
                        </p>
                    </div>
                </section>
            </div>

            <div class="mt-12 text-center">
                <router-link :to="{ name: 'product' }" class="inline-flex items-center justify-center px-8 py-4 rounded-full text-white bg-pink-600 hover:bg-pink-700 font-semibold transition-colors shadow-lg shadow-pink-100">
                    Bắt đầu chọn hoa ngay
                </router-link>
            </div>
        </main>
    </div>
    <Footer_Client />
</template>
