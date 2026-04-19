<script setup>
import { ref } from 'vue';
import Header_Client from '@/pages/Includes/Layouts/Header_Client.vue';
import Footer_Client from '@/pages/Includes/Layouts/Footer_Client.vue';
</script>
<template>
    <!DOCTYPE html>
<html lang="vi"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Đổi Trả &amp; Hoàn Tiền - TBS</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&amp;family=Inter:wght@300;400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#d63384",
                        "background-light": "#ffffff",
                        "background-dark": "#121212",
                        "surface-light": "#fcf5f8",
                        "surface-dark": "#1e1e1e",
                        "text-light": "#333333",
                        "text-dark": "#e0e0e0",
                        "text-muted-light": "#666666",
                        "text-muted-dark": "#a0a0a0",
                    },
                    fontFamily: {
                        display: ["Playfair Display", "serif"],
                        sans: ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        md: "0.5rem",
                        lg: "1rem",
                    },
                },
            },
        };
    </script>
<style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-display { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark transition-colors duration-300">
<header class="bg-background-light dark:bg-background-dark border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex justify-between items-center h-20">
<div class="flex items-center space-x-8">
<a class="flex-shrink-0 flex items-center" href="#">
<span class="text-3xl font-display font-bold text-green-700 tracking-tighter">TBS</span>
</a>
<nav class="hidden md:flex space-x-8">
<a class="text-text-light dark:text-text-dark hover:text-primary dark:hover:text-primary px-3 py-2 text-sm font-medium transition-colors" href="#">Cửa hàng</a>
</nav>
</div>
<div class="flex items-center space-x-6">
<button class="text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary transition-colors">
<span class="material-icons">search</span>
</button>
<button class="text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary transition-colors relative">
<span class="material-icons">shopping_bag</span>
<span class="absolute -top-1 -right-2 bg-primary text-white text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center">2</span>
</button>
<div class="hidden md:flex items-center space-x-4 border-l border-gray-300 dark:border-gray-700 pl-6 ml-2">
<span class="text-sm font-medium text-text-light dark:text-text-dark">Hi, Thành Bùi</span>
<a class="text-sm text-text-muted-light dark:text-text-muted-dark hover:text-primary dark:hover:text-primary transition-colors" href="#">Đăng xuất</a>
</div>
</div>
</div>
</div>
</header>
<section class="bg-surface-light dark:bg-surface-dark py-20 px-4 text-center">
<div class="max-w-3xl mx-auto">
<h2 class="text-sm font-bold tracking-widest text-primary uppercase mb-4">Chính Sách Hỗ Trợ</h2>
<h1 class="text-5xl md:text-6xl font-display font-bold text-text-light dark:text-text-dark mb-6 italic">Đổi Trả &amp; Hoàn Tiền</h1>
<p class="text-lg text-text-muted-light dark:text-text-muted-dark leading-relaxed">
                TBS luôn mong muốn mang đến những sản phẩm hoa tươi chất lượng nhất. Tuy nhiên, nếu có bất kỳ sự cố nào xảy ra, chúng tôi luôn sẵn sàng hỗ trợ bạn theo chính sách dưới đây.
            </p>
</div>
</section>
<main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
<div class="prose prose-lg dark:prose-invert max-w-none prose-headings:font-display prose-headings:font-semibold prose-a:text-primary">
<h2 class="text-2xl mb-6 border-b pb-2 border-gray-200 dark:border-gray-800">1. Trường hợp được áp dụng đổi trả</h2>
<p>Chúng tôi chấp nhận đổi trả sản phẩm hoặc hoàn tiền trong các trường hợp sau:</p>
<ul class="list-disc pl-6 mb-8 space-y-2 text-text-muted-light dark:text-text-muted-dark">
<li><strong class="text-text-light dark:text-text-dark">Hoa không đúng với mẫu mã:</strong> Sản phẩm thực tế khác biệt quá lớn so với hình ảnh trên website (không tính sự khác biệt nhỏ do tính chất mùa vụ của hoa).</li>
<li><strong class="text-text-light dark:text-text-dark">Hoa bị dập nát, héo úa:</strong> Hoa bị hư hỏng nghiêm trọng trong quá trình vận chuyển trước khi đến tay người nhận.</li>
<li><strong class="text-text-light dark:text-text-dark">Giao sai sản phẩm:</strong> Khách hàng nhận được sản phẩm không đúng với đơn đặt hàng.</li>
<li><strong class="text-text-light dark:text-text-dark">Lỗi phụ kiện:</strong> Giỏ, bình, hộp đựng bị nứt vỡ hoặc thiệp ghi sai thông điệp do lỗi của TBS.</li>
</ul>
<h2 class="text-2xl mb-6 border-b pb-2 border-gray-200 dark:border-gray-800 mt-12">2. Trường hợp KHÔNG được đổi trả</h2>
<p>Chúng tôi xin phép từ chối đổi trả trong các trường hợp sau:</p>
<ul class="list-disc pl-6 mb-8 space-y-2 text-text-muted-light dark:text-text-muted-dark">
<li>Người nhận không hài lòng với loại hoa, màu sắc nhưng đúng với đơn đặt hàng ban đầu.</li>
<li>Hoa héo do khách hàng không bảo quản đúng cách sau khi nhận.</li>
<li>Khách hàng cung cấp sai thông tin giao hàng dẫn đến việc giao muộn làm hoa kém tươi.</li>
<li>Người nhận từ chối nhận hoa.</li>
</ul>
<h2 class="text-2xl mb-6 border-b pb-2 border-gray-200 dark:border-gray-800 mt-12">3. Thời gian yêu cầu đổi trả</h2>
<div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg mb-8 border border-gray-100 dark:border-gray-800">
<p class="mb-0 text-text-light dark:text-text-dark">Quý khách vui lòng thông báo cho TBS về sự cố trong vòng <strong class="text-primary text-xl">02 giờ</strong> kể từ thời điểm nhận hoa thành công.</p>
<p class="text-sm mt-2 text-text-muted-light dark:text-text-muted-dark">Do đặc thù của sản phẩm hoa tươi là mặt hàng mau hỏng, việc thông báo sớm giúp chúng tôi có thể xử lý và xác minh tình trạng hoa một cách chính xác nhất.</p>
</div>
<h2 class="text-2xl mb-6 border-b pb-2 border-gray-200 dark:border-gray-800 mt-12">4. Quy trình xử lý đổi trả / hoàn tiền</h2>
<div class="space-y-8 mb-8">
<div class="flex">
<div class="flex-shrink-0 mr-4">
<div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">1</div>
</div>
<div>
<h3 class="text-xl font-semibold mt-0 mb-2">Liên hệ phản ánh</h3>
<p class="text-text-muted-light dark:text-text-muted-dark mb-0">Quý khách liên hệ qua Hotline hoặc Zalo của TBS, cung cấp mã đơn hàng và hình ảnh/video chụp rõ tình trạng sản phẩm ngay lúc nhận.</p>
</div>
</div>
<div class="flex">
<div class="flex-shrink-0 mr-4">
<div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">2</div>
</div>
<div>
<h3 class="text-xl font-semibold mt-0 mb-2">Xác minh thông tin</h3>
<p class="text-text-muted-light dark:text-text-muted-dark mb-0">Nhân viên CSKH sẽ tiếp nhận và xác minh tình trạng hoa trong thời gian sớm nhất (không quá 1 giờ làm việc).</p>
</div>
</div>
<div class="flex">
<div class="flex-shrink-0 mr-4">
<div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">3</div>
</div>
<div>
<h3 class="text-xl font-semibold mt-0 mb-2">Phương án giải quyết</h3>
<p class="text-text-muted-light dark:text-text-muted-dark mb-0">Nếu lỗi thuộc về TBS, chúng tôi sẽ đề xuất các phương án: Giao lại sản phẩm mới, Hoàn trả một phần tiền hoặc Hoàn trả toàn bộ tiền tùy theo mức độ ảnh hưởng.</p>
</div>
</div>
<div class="flex">
<div class="flex-shrink-0 mr-4">
<div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">4</div>
</div>
<div>
<h3 class="text-xl font-semibold mt-0 mb-2">Thực hiện hoàn tiền (nếu có)</h3>
<p class="text-text-muted-light dark:text-text-muted-dark mb-0">Thời gian hoàn tiền sẽ từ 1-3 ngày làm việc (đối với chuyển khoản ngân hàng) hoặc tùy thuộc vào thời gian xử lý của cổng thanh toán điện tử.</p>
</div>
</div>
</div>
<div class="mt-12 bg-pink-50 dark:bg-gray-900 border border-pink-100 dark:border-gray-800 p-8 text-center rounded-lg">
<h3 class="text-2xl font-display mb-4">Cần Hỗ Trợ Nhanh?</h3>
<p class="mb-6 text-text-muted-light dark:text-text-muted-dark">Đừng ngần ngại liên hệ với bộ phận CSKH của chúng tôi để được giải quyết ngay lập tức.</p>
<div class="flex justify-center gap-4">
<a class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary hover:bg-pink-700 transition-colors" href="tel:1900xxxx">
<span class="material-icons mr-2 text-sm">phone</span> Hotline: 1900 xxxx
                    </a>
</div>
</div>
</div>
</main>
<footer class="bg-surface-light dark:bg-surface-dark border-t border-gray-200 dark:border-gray-800 pt-16 pb-8">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
<div class="col-span-1 md:col-span-1">
<a class="flex-shrink-0 flex items-center mb-4" href="#">
<span class="text-3xl font-display font-bold text-green-700 tracking-tighter">TBS</span>
</a>
<p class="text-sm text-text-muted-light dark:text-text-muted-dark leading-relaxed">
                        Nơi hội tụ những nhành hoa tươi thắm và tinh tế nhất, mang tâm tình gửi gắm vào từng nhành hoa nghệ thuật.
                    </p>
</div>
<div>
<h4 class="text-sm font-bold uppercase tracking-wider text-text-light dark:text-text-dark mb-4">Sản Phẩm</h4>
<ul class="space-y-3">
<li><a class="text-sm text-text-muted-light dark:text-text-muted-dark hover:text-primary dark:hover:text-primary transition-colors" href="#">Hoa Bó Mới Về</a></li>
<li><a class="text-sm text-text-muted-light dark:text-text-muted-dark hover:text-primary dark:hover:text-primary transition-colors" href="#">Hoa Khai Trương</a></li>
<li><a class="text-sm text-text-muted-light dark:text-text-muted-dark hover:text-primary dark:hover:text-primary transition-colors" href="#">Vòng Hoa Chia Buồn</a></li>
<li><a class="text-sm text-text-muted-light dark:text-text-muted-dark hover:text-primary dark:hover:text-primary transition-colors" href="#">Dịch Vụ Điện Hoa</a></li>
</ul>
</div>
<div>
<h4 class="text-sm font-bold uppercase tracking-wider text-text-light dark:text-text-dark mb-4">Hỗ Trợ</h4>
<ul class="space-y-3">
<li><a class="text-sm text-text-muted-light dark:text-text-muted-dark hover:text-primary dark:hover:text-primary transition-colors" href="#">Chính Sách Giao Hoa</a></li>
<li><a class="text-sm text-text-muted-light dark:text-text-muted-dark hover:text-primary dark:hover:text-primary transition-colors" href="#">Đổi Trả &amp; Hoàn Tiền</a></li>
<li><a class="text-sm text-text-muted-light dark:text-text-muted-dark hover:text-primary dark:hover:text-primary transition-colors" href="#">Hướng Dẫn Đặt Hàng</a></li>
<li><a class="text-sm text-text-muted-light dark:text-text-muted-dark hover:text-primary dark:hover:text-primary transition-colors" href="#">Liên Hệ Góp Ý</a></li>
</ul>
</div>
<div>
<h4 class="text-sm font-bold uppercase tracking-wider text-text-light dark:text-text-dark mb-4">Đăng Ký Nhận Tin</h4>
<p class="text-sm text-text-muted-light dark:text-text-muted-dark mb-4">Để nhận ưu đãi đặc biệt và các mẫu hoa mới nhất hàng tuần.</p>
<form class="flex border-b border-gray-300 dark:border-gray-700 pb-2">
<input class="w-full bg-transparent border-none p-0 text-sm focus:ring-0 text-text-light dark:text-text-dark placeholder-gray-400" placeholder="Email của bạn..." type="email"/>
<button class="text-primary hover:text-pink-700 transition-colors" type="submit">
<span class="material-icons text-lg">send</span>
</button>
</form>
</div>
</div>
<div class="border-t border-gray-200 dark:border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
<p class="text-xs text-text-muted-light dark:text-text-muted-dark uppercase mb-4 md:mb-0">
                    © 2026 TBS FLORA STORE. LAN TỎA YÊU THƯƠNG BẰNG HOA TƯƠI.
                </p>
<div class="flex space-x-6 text-xs text-text-muted-light dark:text-text-muted-dark uppercase">
<a class="hover:text-primary dark:hover:text-primary transition-colors" href="#">Chính Sách Bảo Mật</a>
<a class="hover:text-primary dark:hover:text-primary transition-colors" href="#">Điều Khoản Phục Vụ</a>
</div>
</div>
</div>
</footer>

</body></html>
</template>
<style scoped>
</style>