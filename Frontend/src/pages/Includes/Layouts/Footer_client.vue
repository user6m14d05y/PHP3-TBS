<script setup>
import { ref } from 'vue';
import axios from "axios";
import Swal from 'sweetalert2';

const email = ref('');
const isSupportOpen = ref(false);
const activeSupportTab = ref('options');
const chatbotMessage = ref('');
const chatbotLoading = ref(false);
const chatbotMessages = ref([
  {
    role: 'bot',
    content: 'Xin chào, mình là trợ lý TBS Flora. Bạn cần tư vấn mẫu hoa, đặt hàng hay chính sách giao hoa?',
    products: []
  },
]);

const quickSuggestions = ref([
  'Tư vấn mẫu hoa',
  'Hoa dưới 300k',
  'Chính sách giao hoa',
  'Hướng dẫn đặt hàng'
]);

const clickSuggestion = async (suggest) => {
  chatbotMessage.value = suggest;
  await sendChatbotMessage();
};

const formatPrice = (value) => {
  if (!value) return '';
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
};

const parseMarkdown = (text) => {
  if (!text) return '';
  let escaped = text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
  
  // Replace bold **text** with styled bold HTML
  escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-gray-900">$1</strong>');
  
  // Replace bullet points starting with * or - with cute lists
  escaped = escaped.replace(/(?:^|\n)\s*[\*\-]\s*(.*?)(?=\n|$)/g, '<div class="pl-2.5 py-0.5 flex items-start gap-1"><span class="text-pink-500">•</span> <span>$1</span></div>');
  
  // Replace line breaks with HTML line breaks
  escaped = escaped.replace(/\n/g, '<br>');
  
  return escaped;
};

const toggleSupport = () => {
  isSupportOpen.value = !isSupportOpen.value;
};

const openChatbot = () => {
  activeSupportTab.value = 'chatbot';
};

const sendChatbotMessage = async () => {
  const message = chatbotMessage.value.trim();

  if (!message || chatbotLoading.value) return;

  chatbotMessages.value.push({ role: 'user', content: message, products: [] });
  chatbotMessage.value = '';
  chatbotLoading.value = true;

  try {
    const response = await axios.post('http://localhost:8888/api/chatbot', { message });
    chatbotMessages.value.push({
      role: 'bot',
      content: response.data.reply || 'Mình chưa trả lời được câu này, bạn thử hỏi lại giúp mình nhé.',
      products: response.data.products || []
    });
  } catch (error) {
    console.error('Lỗi chatbot:', error);
    chatbotMessages.value.push({
      role: 'bot',
      content: 'Chatbot đang bận, bạn vui lòng thử lại sau hoặc chọn Chat trực tuyến nhé.',
      products: []
    });
  } finally {
    chatbotLoading.value = false;
  }
};

const SubmitContact = async () => {

  if (email.value === "") {
    // error.value = "Email của bạn đang để trống";
    alert("abcdef");
    return;
  }

  try {
    const reponse = await axios.post("http://localhost:8888/api/SubmitContact", {
      email: email.value
    });
    if (reponse.data.status == "success") {
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
          <p class="text-gray-500 text-sm font-light leading-relaxed mb-6">Nơi hội tụ những nhành hoa tươi thắm và tinh
            tế nhất, mang tâm tình gửi gắm vào từng nhành hoa nghệ thuật.</p>
        </div>
        <div>
          <h3
            class="text-sm font-bold uppercase tracking-widest mb-6 text-gray-900 border-b border-pink-100 pb-2 inline-block">
            Sản Phẩm</h3>
          <ul class="space-y-3 text-gray-500 text-sm">
            <li><router-link to="/product" class="hover:text-pink-600 transition">Hoa Bó Mới Về</router-link></li>
            <li><router-link to="/product" class="hover:text-pink-600 transition">Hoa Khai Trương</router-link></li>
            <li><router-link to="/product" class="hover:text-pink-600 transition">Vòng Hoa Chia Buồn</router-link></li>
            <li><router-link to="/product" class="hover:text-pink-600 transition">Dịch Vụ Điện Hoa</router-link></li>
          </ul>
        </div>
        <div>
          <h3
            class="text-sm font-bold uppercase tracking-widest mb-6 text-gray-900 border-b border-pink-100 pb-2 inline-block">
            Hỗ Trợ</h3>
          <ul class="space-y-3 text-gray-500 text-sm">
            <li><router-link to="/delivery-policy" class="hover:text-pink-600 transition">Chính Sách Giao
                Hoa</router-link></li>
            <li><router-link to="/refund-policy" class="hover:text-pink-600 transition">Đổi Trả & Hoàn
                Tiền</router-link></li>
            <li><router-link to="/ordering" class="hover:text-pink-600 transition">Hướng Dẫn Đặt Hàng</router-link></li>
            <li><router-link to="/contact" class="hover:text-pink-600 transition">Liên Hệ Góp Ý</router-link></li>
          </ul>
        </div>
        <div>
          <h3
            class="text-sm font-bold uppercase tracking-widest mb-6 text-gray-900 border-b border-pink-100 pb-2 inline-block">
            Đăng Ký Nhận Tin</h3>
          <p class="text-gray-500 text-sm font-light mb-6">Để nhận ưu đãi đặc biệt và các mẫu hoa mới nhất hàng tuần.
          </p>
          <form @submit.prevent="SubmitContact"
            class="flex border-b border-pink-200 focus-within:border-pink-500 transition pb-2">
            <input v-model="email" type="email" placeholder="Email của bạn..."
              class="bg-transparent border-none outline-none text-sm w-full font-light text-gray-700 placeholder-gray-400">
            <button type="submit" class="text-pink-600 hover:text-pink-800 font-bold"><i
                class="fa-solid fa-paper-plane"></i></button>
          </form>
        </div>
      </div>
      <div
        class="border-t border-pink-50 pt-8 flex flex-col md:flex-row justify-between items-center text-gray-400 text-[11px] uppercase tracking-widest">
        <p>© 2026 TBS Flora Store. Lan tỏa yêu thương bằng hoa tươi.</p>
        <div class="flex space-x-6 mt-4 md:mt-0">
          <a href="#" class="hover:text-pink-600 transition">Chính Sách Bảo Mật</a>
          <a href="#" class="hover:text-pink-600 transition">Điều Khoản Phục Vụ</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Floating Contact Widget -->
  <div class="fixed bottom-6 right-6 z-[9999] flex flex-col items-end gap-4 font-sans">
    <!-- Phone Call Button -->
    <a href="tel:0987654321" class="group flex items-center gap-3 relative cursor-pointer select-none">
      <span
        class="absolute right-14 bg-red-500 text-white font-bold text-xs px-3 py-1.5 rounded-full shadow-lg opacity-0 translate-x-4 pointer-events-none group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 whitespace-nowrap z-[10000]">
        Hotline: 0987.654.321
      </span>
      <div
        class="w-12 h-12 flex items-center justify-center bg-gradient-to-r from-red-500 to-orange-500 text-white rounded-full shadow-lg group-hover:scale-110 transition-all duration-300 animate-phone-pulse relative">
        <span class="absolute inset-0 rounded-full bg-red-500 animate-ping opacity-75"></span>
        <i class="fa-solid fa-phone text-lg animate-phone-ring relative z-10"></i>
      </div>
    </a>

    <!-- Zalo Chat Button -->
    <a href="https://zalo.me/0911616211" target="_blank" rel="noopener noreferrer"
      class="group flex items-center gap-3 relative cursor-pointer select-none">
      <span
        class="absolute right-14 bg-blue-600 text-white font-bold text-xs px-3 py-1.5 rounded-full shadow-lg opacity-0 translate-x-4 pointer-events-none group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 whitespace-nowrap z-[10000]">
        Chat Zalo
      </span>
      <div
        class="w-12 h-12 flex items-center justify-center bg-[#0068ff] text-white rounded-full shadow-lg group-hover:scale-110 transition-all duration-300 relative">
        <span class="absolute inset-0 rounded-full bg-[#0068ff] animate-ping opacity-40"></span>
        <span class="relative z-10 text-[13px] font-black tracking-tight">Zalo</span>
      </div>
    </a>

    <!-- Facebook Messenger Button -->
    <a href="https://m.me/05.thanh" target="_blank" rel="noopener noreferrer"
      class="group flex items-center gap-3 relative cursor-pointer select-none">
      <span
        class="absolute right-14 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold text-xs px-3 py-1.5 rounded-full shadow-lg opacity-0 translate-x-4 pointer-events-none group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 whitespace-nowrap z-[10000]">
        Chat Messenger
      </span>
      <div
        class="w-12 h-12 flex items-center justify-center bg-gradient-to-tr from-[#0066ff] to-[#ff5c8a] text-white rounded-full shadow-lg group-hover:scale-110 transition-all duration-300 relative">
        <span class="absolute inset-0 rounded-full bg-[#0084ff] animate-ping opacity-40"></span>
        <i class="fa-brands fa-facebook-messenger text-2xl relative z-10"></i>
      </div>
    </a>

    <!-- Main Floating Support Button -->
    <div class="relative flex flex-col items-end gap-3">
      <Transition name="support-popup">
        <div v-if="isSupportOpen" class="w-[calc(100vw-3rem)] max-w-sm overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-pink-100">
          <div class="bg-gradient-to-r from-pink-500 to-rose-500 px-5 py-4 text-white">
            <div class="flex items-center justify-between gap-3">
              <div>
                <p class="text-sm font-bold">TBS Flora hỗ trợ bạn</p>
                <p class="mt-1 text-xs text-white/80">{{ activeSupportTab === 'chatbot' ? 'Chat với trợ lý AI' : 'Chọn kênh tư vấn phù hợp' }}</p>
              </div>
              <div class="flex items-center gap-2">
                <button v-if="activeSupportTab === 'chatbot'" @click="activeSupportTab = 'options'" class="flex h-8 w-8 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25" aria-label="Quay lại lựa chọn hỗ trợ">
                  <i class="fa-solid fa-chevron-left text-sm"></i>
                </button>
                <button @click="toggleSupport" class="flex h-8 w-8 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25" aria-label="Đóng hỗ trợ">
                  <i class="fa-solid fa-xmark"></i>
                </button>
              </div>
            </div>
          </div>

          <div v-if="activeSupportTab === 'options'" class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2">
            <button @click="openChatbot" type="button" class="group rounded-2xl border border-pink-100 bg-pink-50 p-4 text-left transition hover:-translate-y-0.5 hover:border-pink-200 hover:bg-pink-100 hover:shadow-lg hover:shadow-pink-100">
              <span class="mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-white text-pink-600 shadow-sm transition group-hover:bg-pink-600 group-hover:text-white">
                <i class="fa-solid fa-robot text-lg"></i>
              </span>
              <span class="block text-sm font-bold text-gray-900">Chat bot</span>
              <span class="mt-1 block text-xs leading-relaxed text-gray-500">Tư vấn nhanh sản phẩm, chính sách và đặt hoa.</span>
            </button>

            <a href="https://m.me/05.thanh" target="_blank" rel="noopener noreferrer" class="group rounded-2xl border border-blue-100 bg-blue-50 p-4 text-left transition hover:-translate-y-0.5 hover:border-blue-200 hover:bg-blue-100 hover:shadow-lg hover:shadow-blue-100">
              <span class="mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-white text-blue-600 shadow-sm transition group-hover:bg-blue-600 group-hover:text-white">
                <i class="fa-brands fa-facebook-messenger text-xl"></i>
              </span>
              <span class="block text-sm font-bold text-gray-900">Chat trực tuyến</span>
              <span class="mt-1 block text-xs leading-relaxed text-gray-500">Kết nối nhân viên tư vấn như Messenger.</span>
            </a>
          </div>

          <div v-else class="flex h-[420px] flex-col bg-pink-50/40">
            <div class="flex-1 space-y-3 overflow-y-auto p-4">
              <div v-for="(message, index) in chatbotMessages" :key="index" class="flex flex-col gap-1.5" :class="message.role === 'user' ? 'items-end' : 'items-start'">
                <div class="flex w-full" :class="message.role === 'user' ? 'justify-end' : 'justify-start'">
                  <div 
                    class="max-w-[82%] rounded-2xl px-4 py-2 text-sm leading-relaxed shadow-sm" 
                    :class="message.role === 'user' ? 'rounded-br-md bg-pink-600 text-white' : 'rounded-bl-md bg-white text-gray-700'"
                  >
                    <span v-if="message.role === 'user'">{{ message.content }}</span>
                    <span v-else v-html="parseMarkdown(message.content)"></span>
                  </div>
                </div>
                <!-- Carousel sản phẩm gợi ý thực tế -->
                <div v-if="message.products && message.products.length > 0" class="w-full max-w-[90%] overflow-x-auto pb-2 -mt-1">
                  <div class="flex gap-2.5 px-1 py-1">
                    <div v-for="prod in message.products" :key="prod.id" class="flex-shrink-0 w-28 rounded-xl border border-pink-100 bg-white overflow-hidden shadow-xs hover:shadow-md transition duration-200 flex flex-col">
                      <div class="w-full h-20 bg-pink-50 flex items-center justify-center overflow-hidden relative">
                        <img :src="'http://localhost:8888/images/' + prod.img" class="w-full h-full object-cover" :alt="prod.name" @error="(e) => { e.target.src = 'http://localhost:8888/images/default.jpg' }">
                      </div>
                      <div class="p-1.5 flex-1 flex flex-col justify-between">
                        <div>
                          <p class="text-[10px] font-bold text-gray-800 line-clamp-2 leading-tight" :title="prod.name">{{ prod.name }}</p>
                          <div class="mt-1 flex flex-col">
                            <span class="text-[11px] font-black text-pink-600 leading-none">{{ formatPrice(prod.sale_price || prod.price) }}</span>
                            <span v-if="prod.sale_price" class="text-[8px] text-gray-400 line-through leading-none mt-0.5">{{ formatPrice(prod.price) }}</span>
                          </div>
                        </div>
                        <router-link :to="'/product-detail/' + prod.id" class="mt-1.5 block text-center text-[9px] font-bold py-1 bg-pink-50 text-pink-600 rounded-md hover:bg-pink-600 hover:text-white transition duration-200">
                          Xem chi tiết
                        </router-link>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="chatbotLoading" class="flex justify-start">
                <div class="rounded-2xl rounded-bl-md bg-white px-4 py-2 text-sm text-gray-500 shadow-sm">
                  <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                </div>
              </div>
            </div>

            <!-- Nút gợi ý nhanh phổ biến -->
            <div class="flex flex-wrap gap-1.5 px-4 pb-2">
              <button v-for="suggest in quickSuggestions" :key="suggest" @click="clickSuggestion(suggest)" type="button" class="text-[11px] bg-white hover:bg-pink-600 hover:text-white text-pink-600 px-2.5 py-1 rounded-full transition-all border border-pink-200/60 shadow-xs active:scale-95 duration-200">
                {{ suggest }}
              </button>
            </div>

            <form @submit.prevent="sendChatbotMessage" class="flex items-center gap-2 border-t border-pink-100 bg-white p-3">
              <input v-model="chatbotMessage" type="text" placeholder="Nhập câu hỏi của bạn..." class="min-w-0 flex-1 rounded-full border border-pink-100 px-4 py-2.5 text-sm outline-none transition focus:border-pink-400 focus:ring-2 focus:ring-pink-100" :disabled="chatbotLoading">
              <button type="submit" class="flex h-10 w-10 items-center justify-center rounded-full bg-pink-600 text-white transition hover:bg-pink-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="chatbotLoading || !chatbotMessage.trim()" aria-label="Gửi tin nhắn chatbot">
                <i class="fa-solid fa-paper-plane text-sm"></i>
              </button>
            </form>
          </div>
        </div>
      </Transition>

      <button @click="toggleSupport" type="button" class="group flex items-center gap-3 relative cursor-pointer select-none" aria-label="Mở hỗ trợ trực tuyến">
        <span
          class="absolute right-16 bg-pink-600 text-white font-bold text-xs px-3 py-1.5 rounded-full shadow-lg opacity-0 translate-x-4 pointer-events-none group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 whitespace-nowrap z-[10000]">
          Hỗ trợ trực tuyến
        </span>
        <span
          class="w-14 h-14 flex items-center justify-center bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-full shadow-2xl hover:scale-110 transition-all duration-300 relative border border-pink-200">
          <span class="absolute -inset-1 rounded-full bg-pink-500 animate-pulse opacity-30"></span>
          <i :class="isSupportOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-comments'" class="text-2xl relative z-10"></i>
        </span>
      </button>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;700;900&display=swap');

.font-serif {
  font-family: 'Playfair Display', serif;
}

.font-sans {
  font-family: 'Inter', sans-serif;
}

/* Custom keyframes for premium contact widget animations */
@keyframes phone-ring {
  0% {
    transform: rotate(0) scale(1);
  }

  10% {
    transform: rotate(-25deg) scale(1.1);
  }

  20% {
    transform: rotate(25deg) scale(1.1);
  }

  30% {
    transform: rotate(-25deg) scale(1.1);
  }

  40% {
    transform: rotate(25deg) scale(1.1);
  }

  50% {
    transform: rotate(0) scale(1);
  }

  100% {
    transform: rotate(0) scale(1);
  }
}

@keyframes phone-pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6);
  }

  70% {
    box-shadow: 0 0 0 15px rgba(239, 68, 68, 0);
  }

  100% {
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
  }
}

.animate-phone-ring {
  animation: phone-ring 1.8s infinite ease-in-out;
}

.animate-phone-pulse {
  animation: phone-pulse 2s infinite ease-in-out;
}

.support-popup-enter-active,
.support-popup-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.support-popup-enter-from,
.support-popup-leave-to {
  opacity: 0;
  transform: translateY(12px) scale(0.96);
}

/* Custom scrollbar for horizontal product list */
.overflow-x-auto::-webkit-scrollbar {
  height: 4px;
}
.overflow-x-auto::-webkit-scrollbar-track {
  background: #fdf2f2;
}
.overflow-x-auto::-webkit-scrollbar-thumb {
  background-color: #fbcfe8;
  border-radius: 4px;
}
.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background-color: #f472b6;
}
</style>