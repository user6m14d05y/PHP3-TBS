<script setup>
import Header_Client from '@/pages/Includes/Layouts/Header_Client.vue';
import Footer_Client from '@/pages/Includes/Layouts/Footer_Client.vue';
</script>
<template>
    <Header_Client />
        <header
            class="bg-background-light dark:bg-background-dark border-b border-gray-100 dark:border-gray-800 sticky top-0 z-50 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <div class="flex items-center space-x-8">
                        <a class="flex-shrink-0 flex items-center" href="#">
                            <img alt="TBS Flora Store Logo" class="h-8 w-auto"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAkkoVa04S3-0-tmH5Dr3MJytxh2jBFONuPm2dw3sey-Zfd3CWMpM8QNM5xPAI5_TpJsxkCfoUdR8YnRzzW_x8WdsdKMNBPastzwGOW1LuHw26cqrnZMS1i0vkzbU-Xt5n30G6cYOrdEwGMNV93JwaPK0Gl7_dMbb8ByF2u99hD3c6F_caLMdaGbMzADf7yyiHsSe52-ha2EXgq5swRNbjQ0a01Lbl-Frj4BxzMgST0RW0bnzWaRxVF4QATUaYHlp9XyQFEEpye-Rk" />
                        </a>
                        <nav class="hidden md:flex space-x-8">
                            <a class="text-sm font-medium text-heading-light dark:text-heading-dark hover:text-primary transition-colors"
                                href="#">Cửa hàng</a>
                        </nav>
                    </div>
                    <div class="flex items-center space-x-6">
                        <button class="text-gray-400 hover:text-primary transition-colors">
                            <span class="material-icons">search</span>
                        </button>
                        <button class="text-gray-400 hover:text-primary transition-colors relative">
                            <span class="material-icons">shopping_bag</span>
                            <span
                                class="absolute -top-1 -right-2 bg-primary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">2</span>
                        </button>
                        <div
                            class="hidden md:flex items-center space-x-4 border-l border-gray-200 dark:border-gray-700 pl-6 ml-2">
                            <span class="text-sm font-medium text-heading-light dark:text-heading-dark">Hi, Thành
                                Bùi</span>
                            <a class="text-sm text-gray-500 hover:text-primary transition-colors" href="#">Đăng xuất</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div
            class="bg-primary-light dark:bg-gray-900 py-16 md:py-24 text-center border-b border-pink-100 dark:border-gray-800 transition-colors duration-300">
            <div class="max-w-3xl mx-auto px-4">
                <p class="text-xs font-bold tracking-widest text-primary uppercase mb-4">Pháp lý</p>
                <h1
                    class="font-display text-4xl md:text-5xl lg:text-6xl text-heading-light dark:text-heading-dark mb-6 italic">
                    Chính sách &amp; Điều khoản</h1>
                <p class="text-base md:text-lg text-gray-600 dark:text-gray-400">Những thông tin quan trọng về quyền lợi
                    và trách nhiệm khi sử dụng dịch vụ của TBS Flora Store.</p>
            </div>
        </div>
        <main class="flex-grow py-16 md:py-24">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8 md:p-12 lg:p-16 transition-colors duration-300">
                    <article class="prose prose-slate dark:prose-invert max-w-none">
                        <h2 class="text-heading-light dark:text-heading-dark border-b pb-2 mb-6" id="privacy-policy">
                            Chính sách bảo mật</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Cập nhật lần cuối: Ngày 01 tháng 01 năm 2026
                        </p>
                        <p>Chào mừng bạn đến với TBS Flora Store. Chúng tôi tôn trọng và cam kết bảo vệ dữ liệu cá nhân
                            của bạn. Chính sách bảo mật này sẽ thông báo cho bạn về cách chúng tôi quản lý dữ liệu cá
                            nhân của bạn khi bạn truy cập trang web của chúng tôi và cho bạn biết về quyền riêng tư của
                            bạn cũng như cách luật pháp bảo vệ bạn.</p>
                        <h3 class="text-heading-light dark:text-heading-dark">1. Dữ liệu chúng tôi thu thập</h3>
                        <p>Chúng tôi có thể thu thập, sử dụng, lưu trữ và chuyển giao các loại dữ liệu cá nhân khác nhau
                            về bạn mà chúng tôi đã nhóm lại với nhau như sau:</p>
                        <ul>
                            <li><strong>Dữ liệu danh tính</strong> bao gồm tên, họ, tên người dùng hoặc mã định danh
                                tương tự, chức danh.</li>
                            <li><strong>Dữ liệu liên hệ</strong> bao gồm địa chỉ thanh toán, địa chỉ giao hàng, địa chỉ
                                email và số điện thoại.</li>
                            <li><strong>Dữ liệu tài chính</strong> bao gồm chi tiết tài khoản ngân hàng và thẻ thanh
                                toán.</li>
                            <li><strong>Dữ liệu giao dịch</strong> bao gồm thông tin chi tiết về các khoản thanh toán
                                đến và đi từ bạn cũng như các thông tin chi tiết khác về các sản phẩm và dịch vụ bạn đã
                                mua từ chúng tôi.</li>
                            <li><strong>Dữ liệu kỹ thuật</strong> bao gồm địa chỉ giao thức internet (IP), dữ liệu đăng
                                nhập của bạn, loại và phiên bản trình duyệt, cài đặt múi giờ và vị trí, các loại và
                                phiên bản trình cắm của trình duyệt, hệ điều hành và nền tảng cũng như công nghệ khác
                                trên các thiết bị bạn sử dụng để truy cập trang web này.</li>
                        </ul>
                        <h3 class="text-heading-light dark:text-heading-dark">2. Cách chúng tôi sử dụng dữ liệu của bạn
                        </h3>
                        <p>Chúng tôi sẽ chỉ sử dụng dữ liệu cá nhân của bạn khi luật pháp cho phép. Thông thường nhất,
                            chúng tôi sẽ sử dụng dữ liệu cá nhân của bạn trong các trường hợp sau:</p>
                        <ul>
                            <li>Khi chúng tôi cần thực hiện hợp đồng mà chúng tôi sắp ký kết hoặc đã ký kết với bạn.
                            </li>
                            <li>Khi cần thiết cho lợi ích hợp pháp của chúng tôi (hoặc lợi ích của bên thứ ba) và lợi
                                ích cũng như quyền cơ bản của bạn không vượt qua các lợi ích đó.</li>
                            <li>Khi chúng tôi cần tuân thủ một nghĩa vụ pháp lý.</li>
                        </ul>
                        <h3 class="text-heading-light dark:text-heading-dark">3. Bảo mật dữ liệu</h3>
                        <p>Chúng tôi đã áp dụng các biện pháp bảo mật thích hợp để ngăn chặn việc dữ liệu cá nhân của
                            bạn vô tình bị mất, bị sử dụng hoặc truy cập theo cách trái phép, bị thay đổi hoặc tiết lộ.
                            Ngoài ra, chúng tôi giới hạn quyền truy cập vào dữ liệu cá nhân của bạn cho những nhân viên,
                            đại lý, nhà thầu và các bên thứ ba khác có nhu cầu kinh doanh cần biết.</p>
                    </article>
                    <hr class="my-16 border-gray-200 dark:border-gray-800" />
                    <article class="prose prose-slate dark:prose-invert max-w-none">
                        <h2 class="text-heading-light dark:text-heading-dark border-b pb-2 mb-6" id="terms-of-service">
                            Điều khoản dịch vụ</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Cập nhật lần cuối: Ngày 01 tháng 01 năm 2026
                        </p>
                        <p>Vui lòng đọc kỹ các Điều khoản Dịch vụ này trước khi truy cập hoặc sử dụng trang web của
                            chúng tôi. Bằng cách truy cập hoặc sử dụng bất kỳ phần nào của trang web, bạn đồng ý chịu sự
                            ràng buộc của các Điều khoản Dịch vụ này.</p>
                        <h3 class="text-heading-light dark:text-heading-dark">1. Điều kiện cửa hàng trực tuyến</h3>
                        <p>Bằng cách đồng ý với các Điều khoản Dịch vụ này, bạn tuyên bố rằng bạn ít nhất ở độ tuổi
                            thành niên tại tiểu bang hoặc tỉnh nơi bạn cư trú, hoặc bạn ở độ tuổi thành niên tại tiểu
                            bang hoặc tỉnh nơi bạn cư trú và bạn đã cho chúng tôi sự đồng ý để cho phép bất kỳ người phụ
                            thuộc chưa thành niên nào của bạn sử dụng trang web này.</p>
                        <p>Bạn không được sử dụng các sản phẩm của chúng tôi cho bất kỳ mục đích bất hợp pháp hoặc trái
                            phép nào và khi sử dụng Dịch vụ, bạn không được vi phạm bất kỳ luật nào trong khu vực tài
                            phán của bạn (bao gồm nhưng không giới hạn ở luật bản quyền).</p>
                        <h3 class="text-heading-light dark:text-heading-dark">2. Điều kiện chung</h3>
                        <p>Chúng tôi có quyền từ chối dịch vụ cho bất kỳ ai vì bất kỳ lý do gì vào bất kỳ lúc nào.</p>
                        <p>Bạn hiểu rằng nội dung của bạn (không bao gồm thông tin thẻ tín dụng), có thể được chuyển
                            giao không được mã hóa và liên quan đến (a) việc truyền qua các mạng khác nhau; và (b) các
                            thay đổi để phù hợp và thích ứng với các yêu cầu kỹ thuật của việc kết nối các mạng hoặc
                            thiết bị.</p>
                        <h3 class="text-heading-light dark:text-heading-dark">3. Sửa đổi dịch vụ và giá cả</h3>
                        <p>Giá cho các sản phẩm của chúng tôi có thể thay đổi mà không cần thông báo trước.</p>
                        <p>Chúng tôi bảo lưu quyền tại bất kỳ thời điểm nào sửa đổi hoặc chấm dứt Dịch vụ (hoặc bất kỳ
                            phần hoặc nội dung nào trong đó) mà không cần thông báo trước vào bất kỳ lúc nào.</p>
                        <p>Chúng tôi sẽ không chịu trách nhiệm với bạn hoặc với bất kỳ bên thứ ba nào về bất kỳ sự sửa
                            đổi, thay đổi giá, đình chỉ hoặc chấm dứt Dịch vụ nào.</p>
                        <h3 class="text-heading-light dark:text-heading-dark">4. Sản phẩm hoặc Dịch vụ</h3>
                        <p>Một số sản phẩm hoặc dịch vụ nhất định có thể chỉ khả dụng trực tuyến thông qua trang web.
                            Những sản phẩm hoặc dịch vụ này có thể có số lượng hạn chế và chỉ có thể trả lại hoặc đổi
                            theo Chính sách hoàn trả của chúng tôi.</p>
                        <p>Chúng tôi đã nỗ lực hết sức để hiển thị chính xác nhất có thể màu sắc và hình ảnh của các sản
                            phẩm xuất hiện tại cửa hàng. Chúng tôi không thể đảm bảo rằng màn hình máy tính của bạn hiển
                            thị bất kỳ màu sắc nào sẽ chính xác.</p>
                    </article>
                </div>
            </div>
        </main>
        <Footer_Client />

</template>

<style scoped>
.prose h2 {
    color: var(--tw-prose-headings);
    font-family: 'Playfair Display', serif;
    font-weight: 600;
    margin-top: 2em;
    margin-bottom: 1em;
    font-size: 1.5em;
}

.prose h3 {
    color: var(--tw-prose-headings);
    font-weight: 600;
    margin-top: 1.5em;
    margin-bottom: 0.5em;
    font-size: 1.25em;
}

.prose p {
    margin-bottom: 1.25em;
    line-height: 1.75;
}

.prose ul {
    margin-top: 1em;
    margin-bottom: 1em;
    padding-left: 1.625em;
    list-style-type: disc;
}

.prose li {
    margin-top: 0.5em;
    margin-bottom: 0.5em;
}
</style>