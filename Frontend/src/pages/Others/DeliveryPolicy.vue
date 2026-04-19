<script setup>
import { ref } from 'vue';
import Header_Client from '@/pages/Includes/Layouts/Header_Client.vue';
import Footer_Client from '@/pages/Includes/Layouts/Footer_Client.vue';
</script>
<template>
<div class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark antialiased min-h-screen flex flex-col transition-colors duration-300">
<header class="bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-800 sticky top-0 z-50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex justify-between items-center h-20">
<div class="flex-shrink-0 flex items-center">
<a class="text-2xl font-bold text-slate-800 dark:text-white tracking-tighter" href="#">
<span class="text-emerald-700 dark:text-emerald-500">T</span><span class="text-amber-500">B</span><span class="text-primary">S</span>
</a>
</div>
<nav class="hidden md:flex space-x-8">
<a class="text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary px-3 py-2 text-sm font-medium transition-colors" href="#">Cửa hàng</a>
<a class="text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary px-3 py-2 text-sm font-medium transition-colors" href="#">Giới thiệu</a>
<a class="text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary px-3 py-2 text-sm font-medium transition-colors" href="#">Liên hệ</a>
</nav>
<div class="flex items-center space-x-6">
<button class="text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors">
<span class="material-icons">search</span>
</button>
<a class="relative text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">
<span class="material-icons">shopping_bag</span>
<span class="absolute -top-1 -right-2 bg-primary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">0</span>
</a>
<div class="hidden sm:flex items-center space-x-4 border-l border-gray-200 dark:border-slate-700 pl-6">
<span class="text-sm text-gray-600 dark:text-gray-300">Đăng nhập</span>
</div>
</div>
</div>
</div>
</header>
<div class="bg-surface-light dark:bg-slate-800 py-16 md:py-24 text-center">
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
<span class="text-primary font-semibold tracking-wider uppercase text-sm mb-4 block">Thông Tin Khách Hàng</span>
<h1 class="text-4xl md:text-5xl font-display font-bold text-heading-light dark:text-heading-dark mb-6 italic">Chính Sách Giao Hoa</h1>
<p class="text-lg text-text-light dark:text-text-dark">
                Chúng tôi cam kết mang đến những bó hoa tươi thắm nhất, nguyên vẹn và đúng hẹn để mọi khoảnh khắc của bạn đều trọn vẹn.
            </p>
</div>
</div>
<main class="flex-grow">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
<div class="prose prose-lg prose-pink dark:prose-invert max-w-none">
<div class="mb-16">
<div class="flex items-center mb-6">
<div class="w-12 h-12 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center text-primary mr-4">
<span class="material-icons">schedule</span>
</div>
<h2 class="text-2xl font-display font-semibold text-heading-light dark:text-heading-dark m-0">1. Thời Gian Giao Hàng</h2>
</div>
<p>TBS Flora cung cấp các khung giờ giao hàng linh hoạt để phù hợp với nhu cầu của quý khách:</p>
<ul>
<li><strong>Giao hàng tiêu chuẩn:</strong> Từ 8:00 đến 20:00 các ngày trong tuần. Đơn hàng sẽ được giao trong vòng 2-4 tiếng kể từ khi xác nhận.</li>
<li><strong>Giao hàng hẹn giờ:</strong> Quý khách có thể chọn khung giờ cụ thể (ví dụ: 9:00 - 11:00). Phụ phí có thể được áp dụng tùy vào khung giờ và địa điểm.</li>
<li><strong>Giao hàng hỏa tốc:</strong> Trong vòng 1-2 tiếng (chỉ áp dụng tại khu vực trung tâm thành phố).</li>
</ul>
<p class="text-sm text-gray-500 dark:text-gray-400 italic mt-2">* Lưu ý: Trong các dịp Lễ, Tết (Valentine, 8/3, 20/10...), thời gian giao hàng có thể chênh lệch do lượng đơn hàng tăng cao. Mong quý khách thông cảm và đặt hàng sớm.</p>
</div>
<div class="mb-16">
<div class="flex items-center mb-6">
<div class="w-12 h-12 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center text-primary mr-4">
<span class="material-icons">local_shipping</span>
</div>
<h2 class="text-2xl font-display font-semibold text-heading-light dark:text-heading-dark m-0">2. Phí Vận Chuyển</h2>
</div>
<p>Phí giao hàng được tính dựa trên khoảng cách từ cửa hàng đến địa chỉ nhận:</p>
<div class="overflow-x-auto mt-6">
<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
<thead class="bg-gray-50 dark:bg-slate-800">
<tr>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Khu vực</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Khoảng cách</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Phí giao hàng</th>
</tr>
</thead>
<tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-gray-700">
<tr>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">Trung tâm TP</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Dưới 5km</td>
<td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-primary">Miễn phí</td>
</tr>
<tr>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">Nội thành</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">5km - 10km</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">30.000 VNĐ</td>
</tr>
<tr>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">Ngoại thành</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Trên 10km</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">50.000 VNĐ - 100.000 VNĐ</td>
</tr>
</tbody>
</table>
</div>
</div>
<div class="mb-16">
<div class="flex items-center mb-6">
<div class="w-12 h-12 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center text-primary mr-4">
<span class="material-icons">ac_unit</span>
</div>
<h2 class="text-2xl font-display font-semibold text-heading-light dark:text-heading-dark m-0">3. Quy Trình Bảo Quản Khi Vận Chuyển</h2>
</div>
<p>Để đảm bảo hoa đến tay người nhận với tình trạng hoàn hảo nhất, chúng tôi áp dụng quy trình đóng gói và vận chuyển nghiêm ngặt:</p>
<ul>
<li><strong>Cấp nước liên tục:</strong> Gốc hoa luôn được bọc trong bông tẩm nước hoặc ống tuýp giữ ẩm chuyên dụng trong suốt quá trình vận chuyển.</li>
<li><strong>Che chắn cẩn thận:</strong> Bó hoa/lẵng hoa được cố định chắc chắn trong túi giấy hoặc hộp carton thiết kế riêng, bảo vệ hoa khỏi gió, nắng và va đập.</li>
<li><strong>Phương tiện chuyên dụng:</strong> Đối với các đơn hàng lớn, hoa lan hồ điệp hoặc kệ hoa chúc mừng, chúng tôi sử dụng xe tải có mái che để đảm bảo chất lượng.</li>
</ul>
</div>
<div class="bg-pink-50 dark:bg-slate-800/50 p-8 rounded-2xl border border-pink-100 dark:border-slate-700">
<div class="flex items-center mb-4">
<div class="text-primary mr-3">
<span class="material-icons text-3xl">verified</span>
</div>
<h2 class="text-2xl font-display font-semibold text-heading-light dark:text-heading-dark m-0">4. Cam Kết Giao Hoa Tươi</h2>
</div>
<p class="mb-0">
                        TBS Flora cam kết 100% hoa được giao đúng mẫu mã (hoặc tương tự 90% tùy thuộc vào mùa hoa), tươi mới và không dập nát. 
                        Nếu quý khách không hài lòng với chất lượng hoa khi nhận, vui lòng phản hồi ngay trong vòng <strong>2 giờ</strong> kèm theo hình ảnh. Chúng tôi sẽ tiến hành đổi trả hoặc hoàn tiền 100% theo chính sách bảo hành.
                    </p>
</div>
</div>
</div>
</main>
<footer class="bg-surface-light dark:bg-slate-900 border-t border-gray-100 dark:border-slate-800 pt-16 pb-8">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
<div class="col-span-1 md:col-span-1">
<a class="text-2xl font-bold text-slate-800 dark:text-white tracking-tighter block mb-6" href="#">
<span class="text-emerald-700 dark:text-emerald-500">T</span><span class="text-amber-500">B</span><span class="text-primary">S</span>
</a>
<p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                        Nơi hội tụ những nhành hoa tươi thắm và tinh tế nhất, mang tâm tình gửi gắm vào từng nhành hoa nghệ thuật.
                    </p>
</div>
<div>
<h3 class="text-sm font-bold text-heading-light dark:text-heading-dark uppercase tracking-wider mb-6">Sản Phẩm</h3>
<ul class="space-y-4">
<li><a class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Hoa Bó Mới Về</a></li>
<li><a class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Hoa Khai Trương</a></li>
<li><a class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Vòng Hoa Chia Buồn</a></li>
<li><a class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Dịch Vụ Điện Hoa</a></li>
</ul>
</div>
<div>
<h3 class="text-sm font-bold text-heading-light dark:text-heading-dark uppercase tracking-wider mb-6">Hỗ Trợ</h3>
<ul class="space-y-4">
<li><a class="text-sm text-primary font-medium" href="#">Chính Sách Giao Hoa</a></li>
<li><a class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Đổi Trả &amp; Hoàn Tiền</a></li>
<li><a class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Hướng Dẫn Đặt Hàng</a></li>
<li><a class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Liên Hệ Góp Ý</a></li>
</ul>
</div>
<div>
<h3 class="text-sm font-bold text-heading-light dark:text-heading-dark uppercase tracking-wider mb-6">Đăng Ký Nhận Tin</h3>
<p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Để nhận ưu đãi đặc biệt và các mẫu hoa mới nhất hàng tuần.</p>
<form class="flex items-center border-b border-gray-300 dark:border-gray-600 pb-2">
<input class="bg-transparent border-none focus:ring-0 text-sm w-full text-gray-700 dark:text-gray-300 placeholder-gray-400 dark:placeholder-gray-500 px-0" placeholder="Email của bạn..." type="email"/>
<button class="text-primary hover:text-pink-600 transition-colors" type="submit">
<span class="material-icons">send</span>
</button>
</form>
</div>
</div>
<div class="border-t border-gray-200 dark:border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center">
<p class="text-xs text-gray-400 dark:text-gray-500 mb-4 md:mb-0">
                    © 2026 TBS FLORA STORE. LAN TỎA YÊU THƯƠNG BẰNG HOA TƯƠI.
                </p>
<div class="flex space-x-6">
<a class="text-xs text-gray-400 dark:text-gray-500 hover:text-primary dark:hover:text-primary transition-colors" href="#">CHÍNH SÁCH BẢO MẬT</a>
<a class="text-xs text-gray-400 dark:text-gray-500 hover:text-primary dark:hover:text-primary transition-colors" href="#">ĐIỀU KHOẢN PHỤC VỤ</a>
</div>
</div>
</div>
</footer>
</template>
<style scoped>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        h1, h2, h3, h4, h5, h6, .font-display {
            font-family: 'Playfair Display', serif;
        }
</style>