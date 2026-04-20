 <script setup>
import { ref } from 'vue';
import axios  from "axios";
import Swal from 'sweetalert2';

const email = ref('');

const SubmitContact = async () => {

  if(email.value === ""){
    // error.value = "Email của bạn đang để trống";
    alert ("abcdef");
    return;
  }

  try {
    const reponse = await axios.post("http://localhost:8888/api/SubmitContact", {
      email: email.value
    }); 
    if (reponse.data.status == "success"){
     alert(reponse.data.message);
     email.value = "";
    }
  } catch (error) {
        alert("loi gui email: " + error);
        console.error('Lỗi đăng ký:', error);
    };
};

</script>    

<template>
    <!-- Footer Simple -->
    <footer class="bg-[#fff9f9] pt-16 pb-8 text-gray-900 border-t border-pink-100 font-sans">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
          <div class="col-span-1 md:col-span-1">
            <img src="../../../../public/favicon.ico" class="w-25 h-25" alt="Logo TBS">
            <p class="text-gray-500 text-sm font-light leading-relaxed mb-6">Nơi hội tụ những nhành hoa tươi thắm và tinh tế nhất, mang tâm tình gửi gắm vào từng nhành hoa nghệ thuật.</p>
          </div>
          <div>
            <h3 class="text-sm font-bold uppercase tracking-widest mb-6 text-gray-900 border-b border-pink-100 pb-2 inline-block">Sản Phẩm</h3>
            <ul class="space-y-3 text-gray-500 text-sm">
              <li><router-link to="/product" class="hover:text-pink-600 transition">Hoa Bó Mới Về</router-link></li>
              <li><router-link to="/product" class="hover:text-pink-600 transition">Hoa Khai Trương</router-link></li>
              <li><router-link to="/product" class="hover:text-pink-600 transition">Vòng Hoa Chia Buồn</router-link></li>
              <li><router-link to="/product" class="hover:text-pink-600 transition">Dịch Vụ Điện Hoa</router-link></li>
            </ul>
          </div>
          <div>
            <h3 class="text-sm font-bold uppercase tracking-widest mb-6 text-gray-900 border-b border-pink-100 pb-2 inline-block">Hỗ Trợ</h3>
            <ul class="space-y-3 text-gray-500 text-sm">
              <li><router-link to="/delivery-policy" class="hover:text-pink-600 transition">Chính Sách Giao Hoa</router-link></li>
              <li><router-link to="/refund-policy" class="hover:text-pink-600 transition">Đổi Trả & Hoàn Tiền</router-link></li>
              <li><router-link to="/ordering" class="hover:text-pink-600 transition">Hướng Dẫn Đặt Hàng</router-link></li>
              <li><router-link to="/contact" class="hover:text-pink-600 transition">Liên Hệ Góp Ý</router-link></li>
            </ul>
          </div>
          <div>
            <h3 class="text-sm font-bold uppercase tracking-widest mb-6 text-gray-900 border-b border-pink-100 pb-2 inline-block">Đăng Ký Nhận Tin</h3>
            <p class="text-gray-500 text-sm font-light mb-6">Để nhận ưu đãi đặc biệt và các mẫu hoa mới nhất hàng tuần.</p>
            <form @submit.prevent="SubmitContact" class="flex border-b border-pink-200 focus-within:border-pink-500 transition pb-2">
              <input v-model="email" type="email" placeholder="Email của bạn..." class="bg-transparent border-none outline-none text-sm w-full font-light text-gray-700 placeholder-gray-400">
              <button type="submit" class="text-pink-600 hover:text-pink-800 font-bold"><i class="fa-solid fa-paper-plane"></i></button>
            </form>
          </div>
        </div>
        <div class="border-t border-pink-50 pt-8 flex flex-col md:flex-row justify-between items-center text-gray-400 text-[11px] uppercase tracking-widest">
          <p>© 2026 TBS Flora Store. Lan tỏa yêu thương bằng hoa tươi.</p>
          <div class="flex space-x-6 mt-4 md:mt-0">
            <a href="#" class="hover:text-pink-600 transition">Chính Sách Bảo Mật</a>
            <a href="#" class="hover:text-pink-600 transition">Điều Khoản Phục Vụ</a>
          </div>
        </div>
      </div>
    </footer>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;700;900&display=swap');
.font-serif { font-family: 'Playfair Display', serif; }
.font-sans { font-family: 'Inter', sans-serif; }
</style>
 