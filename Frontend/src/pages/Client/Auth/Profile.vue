<script setup>
</script>
<template>
    <!DOCTYPE html>
<html lang="vi"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Hồ Sơ Cá Nhân - TBS</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&amp;family=Inter:wght@300;400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>
<script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#d81b60", // Deep pink from the reference
                        "primary-light": "#fce4ec",
                        "primary-dark": "#ad1457",
                        "background-light": "#ffffff",
                        "background-dark": "#121212",
                        "surface-light": "#fdf8f9", // Very light pinkish surface
                        "surface-dark": "#1e1e1e",
                        "text-main-light": "#333333",
                        "text-main-dark": "#f5f5f5",
                        "text-muted-light": "#666666",
                        "text-muted-dark": "#aaaaaa",
                        "border-light": "#eaeaea",
                        "border-dark": "#333333"
                    },
                    fontFamily: {
                        display: ["Playfair Display", "serif"],
                        sans: ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        md: "0.5rem",
                        lg: "1rem",
                        full: "9999px"
                    },
                },
            },
        };
    </script>
<style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6, .font-display {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main-light dark:text-text-main-dark antialiased flex flex-col min-h-screen">
<header class="sticky top-0 z-50 bg-background-light dark:bg-background-dark border-b border-border-light dark:border-border-dark">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex justify-between h-20 items-center">
<div class="flex items-center space-x-8">
<a class="flex-shrink-0 flex items-center" href="#">
<span class="text-3xl font-display font-bold text-primary tracking-tighter">TBS</span>
</a>
<nav class="hidden md:flex space-x-8">
<a class="text-sm font-medium text-text-muted-light dark:text-text-muted-dark hover:text-primary dark:hover:text-primary transition-colors" href="#">Cửa hàng</a>
</nav>
</div>
<div class="flex items-center space-x-6">
<button class="text-text-muted-light dark:text-text-muted-dark hover:text-primary dark:hover:text-primary transition-colors">
<span class="material-icons-outlined">search</span>
</button>
<button class="relative text-text-muted-light dark:text-text-muted-dark hover:text-primary dark:hover:text-primary transition-colors">
<span class="material-icons-outlined">shopping_bag</span>
<span class="absolute -top-1 -right-1 bg-primary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">2</span>
</button>
<div class="hidden sm:flex items-center space-x-4 border-l border-border-light dark:border-border-dark pl-6">
<span class="text-sm font-medium">Hi, Thành Bùi</span>
<a class="text-sm text-text-muted-light dark:text-text-muted-dark hover:text-primary dark:hover:text-primary transition-colors" href="#">Đăng xuất</a>
</div>
</div>
</div>
</div>
</header>
<main class="flex-grow bg-surface-light dark:bg-surface-dark py-12">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="mb-10 text-center md:text-left">
<h1 class="text-4xl font-display font-bold text-text-main-light dark:text-text-main-dark mb-2">Hồ Sơ Của Bạn</h1>
<p class="text-text-muted-light dark:text-text-muted-dark">Quản lý thông tin cá nhân và xem lịch sử mua hàng.</p>
</div>
<div class="flex flex-col lg:flex-row gap-8">
<div class="w-full lg:w-1/3">
<div class="bg-background-light dark:bg-background-dark rounded-lg shadow-sm border border-border-light dark:border-border-dark p-6 sticky top-28">
<div class="flex flex-col items-center text-center pb-6 border-b border-border-light dark:border-border-dark">
<div class="w-24 h-24 rounded-full bg-primary-light text-primary flex items-center justify-center text-3xl font-display font-bold mb-4">
                                TB
                            </div>
<h2 class="text-xl font-bold mb-1">Thành Bùi</h2>
<p class="text-sm text-text-muted-light dark:text-text-muted-dark">Thành viên từ Tháng 5, 2023</p>
</div>
<div class="py-6 space-y-4">
<div class="flex items-start">
<span class="material-icons-outlined text-primary mr-3 text-xl">email</span>
<div>
<p class="text-xs text-text-muted-light dark:text-text-muted-dark uppercase tracking-wider font-medium mb-0.5">Email</p>
<p class="text-sm">thanh.bui@example.com</p>
</div>
</div>
<div class="flex items-start">
<span class="material-icons-outlined text-primary mr-3 text-xl">phone</span>
<div>
<p class="text-xs text-text-muted-light dark:text-text-muted-dark uppercase tracking-wider font-medium mb-0.5">Số điện thoại</p>
<p class="text-sm">0987 654 321</p>
</div>
</div>
<div class="flex items-start">
<span class="material-icons-outlined text-primary mr-3 text-xl">location_on</span>
<div>
<p class="text-xs text-text-muted-light dark:text-text-muted-dark uppercase tracking-wider font-medium mb-0.5">Địa chỉ mặc định</p>
<p class="text-sm leading-relaxed">123 Đường Hoa Hồng, Phường 4, Quận 5,<br/>TP. Hồ Chí Minh</p>
</div>
</div>
</div>
<div class="pt-4">
<button class="w-full py-2.5 px-4 border border-primary text-primary hover:bg-primary hover:text-white dark:hover:bg-primary dark:hover:text-white rounded-md transition-colors text-sm font-medium flex items-center justify-center">
<span class="material-icons-outlined text-sm mr-2">edit</span>
                                Chỉnh sửa thông tin
                            </button>
</div>
</div>
</div>
<div class="w-full lg:w-2/3">
<div class="bg-background-light dark:bg-background-dark rounded-lg shadow-sm border border-border-light dark:border-border-dark p-6 md:p-8">
<div class="flex justify-between items-center mb-6 pb-4 border-b border-border-light dark:border-border-dark">
<h3 class="text-2xl font-display font-semibold">Đơn Hàng Gần Đây</h3>
<a class="text-sm text-primary hover:underline font-medium" href="#">Xem tất cả</a>
</div>
<div class="space-y-6">
<div class="border border-border-light dark:border-border-dark rounded-md p-4 hover:border-primary dark:hover:border-primary transition-colors">
<div class="flex flex-col sm:flex-row justify-between sm:items-center mb-4 pb-4 border-b border-border-light dark:border-border-dark gap-2">
<div>
<p class="text-xs text-text-muted-light dark:text-text-muted-dark mb-1">Mã đơn hàng</p>
<p class="font-medium text-sm">#ORD-2023-8901</p>
</div>
<div>
<p class="text-xs text-text-muted-light dark:text-text-muted-dark mb-1">Ngày đặt</p>
<p class="font-medium text-sm">15/10/2023</p>
</div>
<div>
<p class="text-xs text-text-muted-light dark:text-text-muted-dark mb-1">Tổng tiền</p>
<p class="font-medium text-sm text-primary">1.250.000 ₫</p>
</div>
<div class="text-right">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                            Đã giao hàng
                                        </span>
</div>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center space-x-4">
<div class="w-16 h-16 bg-surface-light dark:bg-surface-dark rounded border border-border-light dark:border-border-dark overflow-hidden flex-shrink-0">
<img alt="Hoa Bó" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCHVpDyAS693Rs6wqgQ5pxgSZP-zMoKBERsdADvQELpmLWuTuFy07WpL75Jd1Cqx9CPSfl-53OtK0WhwOH7-Zv5f-tDkvb-Si91xXbfXkPeaFGGHtf7RwWgmzkd1ynPSt3WOj2pBpilAN33ual0XRw--xiecRBioZX0Zvk_Vc_nmXJRhM9smioOsboMoPy6Uj9pAsaTZMybvph53TS0RBxbEkbD66DYHJZyi2cBdDwpgNIcT8Zm94exFySJibSE0uVmcTMg52Xvugs"/>
</div>
<div>
<p class="font-medium text-sm line-clamp-1">Hoa Bó Mùa Yêu Thương</p>
<p class="text-xs text-text-muted-light dark:text-text-muted-dark mt-1">Số lượng: 1</p>
</div>
</div>
<button class="text-sm font-medium text-primary hover:underline whitespace-nowrap ml-4">
                                        Chi tiết
                                    </button>
</div>
</div>
<div class="border border-border-light dark:border-border-dark rounded-md p-4 hover:border-primary dark:hover:border-primary transition-colors">
<div class="flex flex-col sm:flex-row justify-between sm:items-center mb-4 pb-4 border-b border-border-light dark:border-border-dark gap-2">
<div>
<p class="text-xs text-text-muted-light dark:text-text-muted-dark mb-1">Mã đơn hàng</p>
<p class="font-medium text-sm">#ORD-2023-7542</p>
</div>
<div>
<p class="text-xs text-text-muted-light dark:text-text-muted-dark mb-1">Ngày đặt</p>
<p class="font-medium text-sm">28/08/2023</p>
</div>
<div>
<p class="text-xs text-text-muted-light dark:text-text-muted-dark mb-1">Tổng tiền</p>
<p class="font-medium text-sm text-primary">850.000 ₫</p>
</div>
<div class="text-right">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                            Đã giao hàng
                                        </span>
</div>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center space-x-4">
<div class="w-16 h-16 bg-surface-light dark:bg-surface-dark rounded border border-border-light dark:border-border-dark overflow-hidden flex-shrink-0">
<img alt="Lẵng Hoa" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuARLL048FXhJ7BfAtnAYdEJFoId4533ELNbRHIS11gltNrF4Jz2HBKEb4zp75i6_EPTo4n-dByadlsEF7tin1VM7LteghACblcu8sGQeH0UiRbiqVjhTecf5-GsMnVFaikL_gLruMRM_D5Huk3orfkM2JQTHQrdmchzZsTBEbh1IRaZCF53SK6YWQyeu_psmX6GCAjCDFyp1BzLZy4RZdGd9fHQ32Nt3foGXRYgzND10I9SwtmnUqBAyK8SNvyCzhJrOklYh4INpxk"/>
</div>
<div>
<p class="font-medium text-sm line-clamp-1">Lẵng Hoa Khai Trương Tinh Tế</p>
<p class="text-xs text-text-muted-light dark:text-text-muted-dark mt-1">Số lượng: 1</p>
</div>
</div>
<button class="text-sm font-medium text-primary hover:underline whitespace-nowrap ml-4">
                                        Chi tiết
                                    </button>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</main>
<footer class="bg-surface-light dark:bg-surface-dark pt-16 pb-8 border-t border-border-light dark:border-border-dark">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
<div class="col-span-1">
<a class="inline-block mb-6" href="#">
<span class="text-3xl font-display font-bold text-primary tracking-tighter">TBS</span>
</a>
<p class="text-sm text-text-muted-light dark:text-text-muted-dark leading-relaxed">
                        Nơi hội tụ những nhành hoa tươi thắm và tinh tế nhất, mang tâm tình gửi gắm vào từng nhành hoa nghệ thuật.
                    </p>
</div>
<div>
<h4 class="text-sm font-bold uppercase tracking-wider mb-6">Sản Phẩm</h4>
<ul class="space-y-4 text-sm text-text-muted-light dark:text-text-muted-dark">
<li><a class="hover:text-primary transition-colors" href="#">Hoa Bó Mới Về</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Hoa Khai Trương</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Vòng Hoa Chia Buồn</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Dịch Vụ Điện Hoa</a></li>
</ul>
</div>
<div>
<h4 class="text-sm font-bold uppercase tracking-wider mb-6">Hỗ Trợ</h4>
<ul class="space-y-4 text-sm text-text-muted-light dark:text-text-muted-dark">
<li><a class="hover:text-primary transition-colors" href="#">Chính Sách Giao Hoa</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Đổi Trả &amp; Hoàn Tiền</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Hướng Dẫn Đặt Hàng</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Liên Hệ Góp Ý</a></li>
</ul>
</div>
<div>
<h4 class="text-sm font-bold uppercase tracking-wider mb-6">Đăng Ký Nhận Tin</h4>
<p class="text-sm text-text-muted-light dark:text-text-muted-dark mb-4">Để nhận ưu đãi đặc biệt và các mẫu hoa mới nhất hàng tuần.</p>
<form class="relative">
<input class="w-full bg-transparent border-b border-border-light dark:border-border-dark py-2 text-sm focus:outline-none focus:border-primary dark:focus:border-primary transition-colors" placeholder="Email của bạn..." type="email"/>
<button class="absolute right-0 top-1/2 -translate-y-1/2 text-primary hover:text-primary-dark transition-colors" type="submit">
<span class="material-icons-outlined text-xl">send</span>
</button>
</form>
</div>
</div>
<div class="flex flex-col md:flex-row justify-between items-center pt-8 border-t border-border-light dark:border-border-dark text-xs text-text-muted-light dark:text-text-muted-dark">
<p>© 2026 TBS FLORA STORE. LAN TỎA YÊU THƯƠNG BẰNG HOA TƯƠI.</p>
<div class="flex space-x-6 mt-4 md:mt-0">
<a class="hover:text-primary transition-colors uppercase tracking-wider" href="#">Chính sách bảo mật</a>
<a class="hover:text-primary transition-colors uppercase tracking-wider" href="#">Điều khoản phục vụ</a>
</div>
</div>
</div>
</footer>

</body></html>
</template>
<style scoped>
</style>