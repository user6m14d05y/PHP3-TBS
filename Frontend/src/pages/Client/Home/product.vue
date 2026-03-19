<script setup>
import Footer_client from '@/pages/Includes/Layouts/Footer_client.vue';
import Header_client from '@/pages/Includes/Layouts/Header_client.vue';
import { ref, computed } from 'vue';

// Dummy data cho sản phẩm (Bạn có thể fetch API về sau)
const products = ref([
  { id: 1, name: 'Áo Khoác Blazer Classic', price: 1250000, image: 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=600', category: 'Áo Khoác', isNew: true },
  { id: 2, name: 'Đầm Lụa Midi Mùa Thu', price: 850000, image: 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=600', category: 'Váy & Đầm', isNew: false },
  { id: 3, name: 'Sơ Mi Cotton Trắng Basic', price: 450000, image: 'https://images.unsplash.com/photo-1582142306909-195724d33ffc?w=600', category: 'Áo Sơ Mi', isNew: true },
  { id: 4, name: 'Quần Âu Thanh Lịch Nam', price: 650000, image: 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=600', category: 'Quần', isNew: false },
  { id: 5, name: 'Áo Phông Polo Sang Trọng', price: 350000, image: 'https://images.unsplash.com/photo-1581655353564-df123a1eb820?w=600', category: 'Áo Phông', isNew: true },
  { id: 6, name: 'Túi Xách Da Minimal', price: 1550000, image: 'https://images.unsplash.com/photo-1584916201218-f4242ceb4809?w=600', category: 'Phụ Kiện', isNew: false },
  { id: 7, name: 'Chân Váy Xếp Li Xinh Xắn', price: 420000, image: 'https://images.unsplash.com/photo-1582142306909-195724d33ffc?w=600', category: 'Váy & Đầm', isNew: false },
  { id: 8, name: 'Áo Khoác Biker Gắn Đinh', price: 2100000, image: 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=600', category: 'Áo Khoác', isNew: true },
]);

// Danh sách Danh mục
const categories = ref(['Áo Khoác', 'Váy & Đầm', 'Áo Sơ Mi', 'Quần', 'Áo Phông', 'Phụ Kiện']);
const selectedCategories = ref([]);

// Danh sách Mức giá
const priceRanges = ref([
    { label: 'Dưới 500.000đ', min: 0, max: 500000 },
    { label: '500.000đ - 1.000.000đ', min: 500000, max: 1000000 },
    { label: 'Trên 1.000.000đ', min: 1000000, max: 9999999999 }
]);
const selectedPriceRanges = ref([]);

// Lựa chọn Sắp xếp
const sortOption = ref('default'); 

// Logic: Tính toán danh sách sản phẩm hiển thị dựa trên Lọc và Sắp xếp
const filteredProducts = computed(() => {
    let result = [...products.value];

    // Lọc theo danh mục
    if (selectedCategories.value.length > 0) {
        result = result.filter(p => selectedCategories.value.includes(p.category));
    }

    // Lọc theo giá
    if (selectedPriceRanges.value.length > 0) {
        result = result.filter(p => {
            return selectedPriceRanges.value.some(range => p.price >= range.min && p.price <= range.max);
        });
    }

    // Sắp xếp
    if (sortOption.value === 'price-asc') {
        result.sort((a, b) => a.price - b.price); // Thấp đến cao
    } else if (sortOption.value === 'price-desc') {
        result.sort((a, b) => b.price - a.price); // Cao xuống thấp
    } else if (sortOption.value === 'newest') {
        result.sort((a, b) => b.isNew - a.isNew); // Mới nhất lên đầu
    }

    return result;
});

// Định dạng tiền tệ
const formatCurrency = (val) => {
    return val.toLocaleString('vi-VN') + ' ₫';
}
</script>

<template>
  <div class="min-h-screen bg-[#f8fafc] flex flex-col font-sans">
    <Header_client />

    <!-- Breadcrumb & Banner -->
    <div class="bg-white border-b border-gray-200 pt-10 pb-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 tracking-tight">Sản Phẩm</h1>
        <p class="mt-3 text-sm md:text-base text-gray-500">Khám phá phong cách thời trang tuyệt vời với những ưu đãi tốt nhất dành cho bạn.</p>
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex flex-col md:flex-row gap-8 lg:gap-12">
      
      <!-- CỘT BÊN TRÁI: LỌC SẢN PHẨM (SIDEBAR) -->
      <aside class="w-full md:w-64 shrink-0 transition-all">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm sticky top-24">
          <!-- Header Bộ lọc -->
          <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
             <i class="fa-solid fa-filter text-blue-600"></i>
             <h2 class="text-lg font-bold text-gray-900 font-serif">Bộ Lọc</h2>
          </div>

          <!-- Component Lọc: Danh Mục -->
          <div class="mb-8">
            <h3 class="font-bold text-gray-900 mb-4 uppercase text-xs tracking-widest text-[#64748b]">Lọc Danh Mục</h3>
            <div class="space-y-3">
              <label v-for="cat in categories" :key="cat" class="flex items-center group cursor-pointer relative pl-7">
                <input type="checkbox" :value="cat" v-model="selectedCategories" 
                  class="absolute left-0 top-1/2 -translate-y-1/2 w-4 h-4 text-blue-600 border-gray-300 rounded cursor-pointer focus:ring-blue-500 transition-colors">
                <span class="text-sm font-medium text-gray-600 group-hover:text-blue-600 transition-colors">{{ cat }}</span>
              </label>
            </div>
          </div>

          <!-- Component Lọc: Mức giá -->
          <div>
            <h3 class="font-bold text-gray-900 mb-4 uppercase text-xs tracking-widest text-[#64748b]">Lọc Theo Giá</h3>
            <div class="space-y-3">
              <label v-for="(range, index) in priceRanges" :key="index" class="flex items-center group cursor-pointer relative pl-7">
                <input type="checkbox" :value="range" v-model="selectedPriceRanges"
                  class="absolute left-0 top-1/2 -translate-y-1/2 w-4 h-4 text-blue-600 border-gray-300 rounded cursor-pointer focus:ring-blue-500 transition-colors">
                <span class="text-sm font-medium text-gray-600 group-hover:text-blue-600 transition-colors">{{ range.label }}</span>
              </label>
            </div>
          </div>
          
        </div>
      </aside>

      <!-- CỘT BÊN PHẢI: DANH SÁCH SẢN PHẨM -->
      <section class="flex-1">
        
        <!-- Toolbar (Sort & Display options) -->
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm mb-8 flex flex-col sm:flex-row sm:justify-between items-start sm:items-center gap-4">
            <p class="text-sm text-gray-500 font-medium">
                Đang hiển thị <span class="font-bold text-blue-600">{{ filteredProducts.length }}</span> sản phẩm
            </p>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Sắp xếp:</span>
                <div class="relative w-full sm:w-56">
                    <select v-model="sortOption" class="w-full text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 px-4 py-2 bg-gray-50 hover:bg-white transition-all appearance-none cursor-pointer outline-none text-gray-700 font-medium z-10 relative">
                        <option value="default">Cơ bản</option>
                        <option value="newest">Sản phẩm Mới nhất</option>
                        <option value="price-asc">Giá: Từ Thấp đến Cao</option>
                        <option value="price-desc">Giá: Từ Cao xuống Thấp</option>
                    </select>
                    <!-- Icon mũi tên xuống cho select -->
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none z-20"></i>
                </div>
            </div>
        </div>

        <!-- Grid Products -->
        <div v-if="filteredProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <div v-for="product in filteredProducts" :key="product.id" class="group cursor-pointer">
                
                <!-- Khu vực Hình ảnh -->
                <div class="relative aspect-[4/5] overflow-hidden bg-gray-50 flex items-center justify-center">
                    <img :src="product.image" :alt="product.name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out" />
                    
                    <!-- Nhãn "Mới" -->
                    <span v-if="product.isNew" class="absolute top-4 left-4 bg-red-500/90 backdrop-blur-sm text-white text-[10px] uppercase font-extrabold px-3 py-1.5 rounded-full shadow-md z-10 transition-transform hover:scale-105">Mới</span>
                    
                    <!-- Nút Thêm Vào Giỏ (Hiện ra khi rê chuột) -->
                    <div class="absolute inset-x-0 bottom-0 p-4 opacity-0 group-hover:opacity-100 translate-y-6 group-hover:translate-y-0 transition-all duration-300 ease-in-out flex justify-center z-10">
                        <button class="bg-white px-6 py-3 border border-gray-200 text-sm font-medium shadow-lg hover:bg-black hover:text-white transition w-10/12">
                            Thêm Vào Giỏ
                        </button>
                    </div>
                </div>
                
                <!-- Thông tin Sản phẩm -->
                <div class="p-5 flex flex-col flex-grow bg-white z-20 relative">
                    <span class="text-[11px] text-blue-500 font-bold mb-2 tracking-widest uppercase">{{ product.category }}</span>
                    <h3 class="text-gray-900 font-serif font-bold text-lg mb-3 leading-snug group-hover:text-blue-600 transition-colors line-clamp-2 cursor-pointer">
                        {{ product.name }}
                    </h3>
                    <div class="mt-auto flex items-end justify-between pt-2">
                        <span class="text-xl font-bold text-gray-900 tracking-tight">{{ formatCurrency(product.price) }}</span>
                        <!-- Nút Yêu thích tim nhỏ -->
                        <button class="text-gray-300 hover:text-red-500 transition-colors" title="Thêm vào yêu thích">
                            <i class="fa-solid fa-heart text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Box hiển thị khi không tìm thấy kết quả -->
        <div v-else class="bg-white rounded-2xl border border-gray-100 p-16 flex flex-col items-center justify-center text-center shadow-sm">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 text-gray-300 text-4xl shadow-inner">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <h3 class="text-2xl font-serif font-bold text-gray-900 mb-3">Chưa tìm thấy sản phẩm</h3>
            <p class="text-gray-500 max-w-md text-sm leading-relaxed mb-8">Xin lỗi, không có sản phẩm nào phù hợp với bộ lọc bạn đang chọn. Hãy thử thay đổi mức giá hoặc chọn danh mục khác nhé.</p>
            <button @click="selectedCategories = []; selectedPriceRanges = []; sortOption = 'default'" class="px-8 py-3 bg-gray-900 text-white font-medium text-sm rounded-xl hover:bg-blue-600 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300 flex items-center gap-2">
                <i class="fa-solid fa-rotate-right"></i> Xóa tất cả bộ lọc
            </button>
        </div>

      </section>
    </main>

    <Footer_client class="mt-auto" />
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap');

.font-serif {
  font-family: 'Playfair Display', serif;
}
.font-sans {
  font-family: 'Inter', sans-serif;
}
</style>