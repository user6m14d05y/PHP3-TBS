<script setup>
import Footer_client from '@/pages/Includes/Layouts/Footer_client.vue';
import Header_client from '@/pages/Includes/Layouts/Header_client.vue';
import SlidebarProduct_client from '@/pages/Includes/Layouts/SlidebarProduct_client.vue';
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { setPageSeo } from '@/utils/seo';
import { apiUrl, imageUrl } from '@/utils/api';

const route = useRoute();
const products = ref([]);
const categories = ref([]);
const categoryItems = ref([]);
const selectedCategoryId = ref(null);
const selectedCategoryItemId = ref(null);
const openedCategoryId = ref(null);
const selectedPriceRanges = ref([]);
const sortOption = ref('default');

const currentPage = ref(1);
const perPage = ref(12);
const defaultProductListSeo = {
  title: 'Fresh flower shop | TBS Flower Shop',
  description: 'Explore fresh flower collections by category, price range, and gifting occasion.',
};

const fetchCategories = async () => {
  try {
    const response = await axios.get(apiUrl('/api/category'));
    categories.value = response.data.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const fetchCategoryItems = async () => {
  try {
    const response = await axios.get(apiUrl('/api/category-item'));
    categoryItems.value = response.data.data;
  } catch (error) {
    console.error('Error fetching category items:', error);
  }
};

const fetchProducts = async () => {
  try {
    const response = await axios.get(apiUrl('/api/product?limit=100'));
    products.value = response.data.data;
  } catch (error) {
    console.error('Error fetching products:', error);
  }
};

// Danh sách Mức giá
const priceRanges = ref([
  { label: 'Dưới 500.000đ', min: 0, max: 500000 },
  { label: '500.000đ - 1.000.000đ', min: 500000, max: 1000000 },
  { label: 'Trên 1.000.000đ', min: 1000000, max: 99999999 }
]);

const filteredProducts = computed(() => {
  let filtered = [...products.value];

  if (selectedCategoryItemId.value) {
    filtered = filtered.filter(product => product.category_item_id === selectedCategoryItemId.value);
  } else if (selectedCategoryId.value) {
    filtered = filtered.filter(product => product.category_id === selectedCategoryId.value);
  }

  if (selectedPriceRanges.value.length > 0) {
    filtered = filtered.filter(p => {
      const price = getMinPrice(p);
      return selectedPriceRanges.value.some(range => price >= range.min && price <= range.max);
    });
  }

  return filtered.sort((a, b) => {
    if (sortOption.value === 'price-asc') return getMinPrice(a) - getMinPrice(b);
    if (sortOption.value === 'price-desc') return getMinPrice(b) - getMinPrice(a);
    if (sortOption.value === 'oldest') return new Date(a.created_at) - new Date(b.created_at);

    return new Date(b.created_at) - new Date(a.created_at) || b.id - a.id;
  });
});

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  const end = start + perPage.value;
  return filteredProducts.value.slice(start, end);
});

const lastPage = computed(() => {
  return Math.ceil(filteredProducts.value.length / perPage.value) || 1;
});

const selectedSeoSource = computed(() => {
  if (selectedCategoryItemId.value) {
    return categoryItems.value.find((item) => Number(item.id) === Number(selectedCategoryItemId.value)) || null;
  }

  if (selectedCategoryId.value) {
    return categories.value.find((category) => Number(category.id) === Number(selectedCategoryId.value)) || null;
  }

  return null;
});

const selectedSeoTitle = computed(() => {
  return selectedSeoSource.value?.seo_title || selectedSeoSource.value?.name || '';
});

const selectedSeoContent = computed(() => {
  return selectedSeoSource.value?.seo_content || '';
});

const updateProductListSeo = () => {
  const source = selectedSeoSource.value;

  setPageSeo({
    title: source?.seo_title || (source?.name ? `${source.name} | TBS Flower Shop` : defaultProductListSeo.title),
    description: source?.meta_description || source?.seo_content || defaultProductListSeo.description,
    path: '/product',
    image: '/favicon.ico',
  });
};

const changePage = (page) => {
  if (page < 1 || page > lastPage.value || page === currentPage.value) return;
  currentPage.value = page;
  window.scrollTo({ top: 300, behavior: 'smooth' });
};

watch([selectedCategoryId, selectedCategoryItemId, selectedPriceRanges, sortOption], () => {
  currentPage.value = 1;
}, { deep: true });

watch([selectedCategoryId, selectedCategoryItemId, categories, categoryItems], () => {
  updateProductListSeo();
}, { deep: true });

const getMinPrice = (price) => {
  if (!price.variants?.length) return 0;
  const prices = price.variants.map(v => Number(v.sale_price || v.price));
  return Math.min(...prices);
}

const getMaxPrice = (price) => {
  if (!price.variants?.length) return 0;
  const prices = price.variants.map(v => Number(v.sale_price || v.price));
  return Math.max(...prices);
}

const getBestVariant = (product) => {
  if (!product.variants?.length) return null;

  return [...product.variants]
    .filter((variant) => Number(variant.price) > 0)
    .sort((a, b) => Number(a.sale_price || a.price) - Number(b.sale_price || b.price))[0] || null;
};

const getDiscountPercent = (variant) => {
  const price = Number(variant?.price);
  const salePrice = Number(variant?.sale_price);

  if (!price || !salePrice || salePrice >= price) return 0;

  return Math.round(((price - salePrice) / price) * 100);
};

const formatVND = (price) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
}

const getCategoryName = (product) => {
  return product.categoryItem?.name || product.category_item?.name || product.category?.name || 'Chưa phân loại';
}

const isNewProduct = (product) => {
  if (!product.created_at) return false;

  const createdAt = new Date(product.created_at);
  const sevenDaysAgo = new Date();
  sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);

  return createdAt >= sevenDaysAgo;
}

const getCategoryItems = (categoryId) => {
  return categoryItems.value.filter(item => item.category_id === categoryId);
};

const toggleCategory = (categoryId) => {
  openedCategoryId.value = openedCategoryId.value === categoryId ? null : categoryId;
  selectedCategoryId.value = categoryId;
  selectedCategoryItemId.value = null;
};

const selectCategoryItem = (categoryId, categoryItemId) => {
  selectedCategoryId.value = categoryId;
  selectedCategoryItemId.value = categoryItemId;
};

const clearFilters = () => {
  selectedCategoryId.value = null;
  selectedCategoryItemId.value = null;
  openedCategoryId.value = null;
  selectedPriceRanges.value = [];
  sortOption.value = 'default';
};

updateProductListSeo();

onMounted(async () => {
  await fetchCategories();
  if (route.query.category) {
    selectedCategoryId.value = Number(route.query.category);
    openedCategoryId.value = Number(route.query.category);
  }
  await fetchCategoryItems();
  await fetchProducts();
  updateProductListSeo();
});

</script>

<template>
  <div class="min-h-screen bg-white flex flex-col font-sans text-gray-900">
    <Header_client />

    <!-- Hero Section for Products -->
    <div class="bg-pink-100 border-b border-gray-100 py-24">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-xs uppercase tracking-[0.4em] mb-4 block font-medium text-pink-600">Tuyển tập hoa mới
          nhất</span>
        <h1 class="text-5xl md:text-7xl font-serif font-bold text-white mb-6 italic leading-tight">Cửa hàng hoa</h1>
        <p class="text-lg md:text-xl font-light text-gray-600 max-w-2xl mx-auto leading-relaxed">Khám phá các mẫu hoa tươi theo dịp, được thiết kế tinh tế từ nguyên liệu chọn lọc mỗi ngày.</p>
      </div>
    </div>

    <section v-if="selectedSeoContent" class="border-b border-pink-50 bg-white py-10">
      <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <h2 class="mb-3 font-serif text-2xl font-bold text-gray-900">{{ selectedSeoTitle }}</h2>
        <p class="whitespace-pre-line text-sm leading-7 text-gray-600">{{ selectedSeoContent }}</p>
      </div>
    </section>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full flex flex-col md:flex-row gap-16">

      <!-- SIDEBAR -->
      <aside class="w-full md:w-80 shrink-0">
        <div class="sticky top-28 bg-white shadow-xl rounded-2xl p-8 border border-gray-100 space-y-7 transition-all duration-300">

          <div class="pb-6 border-b border-gray-100">
            <div class="flex items-center justify-between gap-4">
              <div>
                <h2 class="text-2xl font-serif font-bold text-gray-900 mb-2">Bộ Lọc</h2>
                <p class="text-xs text-gray-400 font-light italic">Lọc theo sở thích của bạn</p>
              </div>
              <button @click="clearFilters" class="btn text-[12px] font-medium text-gray-600 uppercase tracking-widest hover:text-pink-700">
                Xóa lọc
              </button>
            </div>
          </div>

          <!-- Lọc Danh Mục -->
          <div>
            <h3 class="text-[10px] font-bold text-gray-400 mb-6 uppercase tracking-[0.3em]">Danh Mục Hoa</h3>
            <div class="space-y-3">
              <div v-for="cat in categories" :key="cat.id">
                <button @click="toggleCategory(cat.id)"
                  class="flex items-center justify-between group cursor-pointer w-full text-left py-1">
                  <span
                    class="text-sm font-medium transition-colors uppercase tracking-[0.1em]"
                    :class="selectedCategoryId === cat.id ? 'text-pink-600' : 'text-gray-500 group-hover:text-pink-600'">
                    {{ cat.name }}
                  </span>
                  <i
                    class="fa-solid fa-chevron-down text-[10px] transition-all"
                    :class="openedCategoryId === cat.id ? 'rotate-180 text-pink-600' : 'text-gray-300 group-hover:text-pink-600'"></i>
                </button>

                <div class="grid transition-all duration-300 ease-in-out"
                  :style="{ gridTemplateRows: openedCategoryId === cat.id ? '1fr' : '0fr' }">
                  <div class="overflow-hidden">
                    <div class="mt-2 ml-4 pl-4 border-l border-pink-100 space-y-1.5 pb-2">
                      <button
                        v-for="item in getCategoryItems(cat.id)"
                        :key="item.id"
                        @click="selectCategoryItem(cat.id, item.id)"
                        class="block w-full text-left text-xs font-semibold uppercase tracking-wider transition-all duration-200 py-1.5 hover:pl-2"
                        :class="selectedCategoryItemId === item.id ? 'text-pink-600 font-bold' : 'text-gray-400 hover:text-pink-600'">
                        {{ item.name }}
                      </button>

                      <p v-if="getCategoryItems(cat.id).length === 0" class="text-xs text-gray-400 italic py-1">
                        Chưa có danh mục con
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Mức giá -->
          <div>
            <h3 class="text-[10px] font-bold text-gray-400 mb-6 uppercase tracking-[0.3em]">Lọc Theo Giá</h3>
            <div class="space-y-6">
              <label v-for="(range, index) in priceRanges" :key="index"
                class="flex items-center group cursor-pointer relative pl-9">
                <input type="checkbox" :value="range" v-model="selectedPriceRanges"
                  class="absolute left-0 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-900 border-gray-300 rounded-none cursor-pointer focus:ring-black transition-colors">
                <span
                  class="text-sm font-medium text-gray-500 group-hover:text-pink-600 transition-colors uppercase tracking-widest text-xs">{{
                  range.label }}</span>
              </label>
            </div>
          </div>
        </div>
      </aside>

      <!-- DANH SÁCH -->
      <section class="flex-1 flex flex-col justify-between">
        <div>
          <div class="flex justify-between items-end mb-12 pb-6 border-b border-gray-50">
            <div>
              <p class="text-sm text-gray-400 font-light italic">
                <span v-if="filteredProducts.length > 0">
                  Hiển thị <span class="font-bold text-pink-600">{{ (currentPage - 1) * perPage + 1 }}</span>
                  -
                  <span class="font-bold text-pink-600">{{ Math.min(currentPage * perPage, filteredProducts.length) }}</span>
                  trong tổng <span class="font-bold text-pink-600">{{ filteredProducts.length }}</span> hoa mẫu tinh tế
                </span>
                <span v-else>Không có hoa mẫu nào được hiển thị</span>
              </p>
            </div>
            <div class="flex items-center gap-6">
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Sắp xếp:</span>
              <select v-model="sortOption"
                class="text-[11px] font-bold border-b border-gray-200 pb-1 bg-transparent hover: outline-none cursor-pointer transition-colors uppercase tracking-widest">
                <option value="default">Mặc định</option>
                <option value="price-asc">Giá thấp đến cao</option>
                <option value="price-desc">Giá cao xuống thấp</option>
                <option value="newest">Mới nhất</option>
                <option value="oldest">Cũ nhất</option>
              </select>
            </div>
          </div>

          <div v-if="filteredProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
              <div v-for="product in paginatedProducts" :key="product.id" class="group cursor-pointer flex flex-col">
                <router-link :to="'/product/' + product.slug" class="relative h-80 mb-4 overflow-hidden bg-gray-100 block">
                  <span v-if="isNewProduct(product)"
                    class="absolute top-4 right-4 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full border-2 border-white bg-pink-600 text-white text-[10px] font-bold uppercase tracking-wider shadow-lg shadow-pink-200">
                    New
                  </span>
                  <span v-if="getDiscountPercent(getBestVariant(product))"
                    class="absolute left-4 top-4 z-10 rounded-full bg-emerald-600 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white shadow-lg">
                    -{{ getDiscountPercent(getBestVariant(product)) }}%
                  </span>
                  <img :src="imageUrl(product.thumbnail)" :alt="product.image_alt || product.name"
                    class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-700 ease-in-out"
                    loading="lazy"
                    decoding="async">
                  <div
                    class="absolute bottom-4 left-0 right-0 flex justify-center opacity-0 group-hover:opacity-100 transition duration-300 z-20">
                    <button @click.stop.prevent
                      class="bg-white px-6 py-3 text-sm font-medium shadow-lg hover:bg-pink-600 hover:text-white transition w-10/12 uppercase tracking-widest font-bold">
                      Thêm Vào Giỏ
                    </button>
                  </div>
                </router-link>
                <div>
                  <span class="text-xs text-gray-500 uppercase tracking-wider mb-1 block">{{ getCategoryName(product) }}</span>
                  <router-link :to="'/product/' + product.slug">
                    <h3 class="text-base font-medium text-gray-900 group-hover:text-pink-600 transition-colors duration-300">{{ product.name }}</h3>
                  </router-link>
                  <div class="mt-1 flex flex-wrap items-center gap-2">
                    <p class="text-sm font-bold text-gray-900">{{ formatVND(getMinPrice(product)) }}</p>
                    <span v-if="getDiscountPercent(getBestVariant(product))" class="text-xs font-semibold text-emerald-600">
                      Giảm {{ getDiscountPercent(getBestVariant(product)) }}%
                    </span>
                  </div>
                </div>
              </div>
          </div>

          <div v-else class="bg-gray-50 p-24 flex flex-col items-center justify-center text-center">
            <i class="fa-solid fa-box-open text-gray-200 text-6xl mb-10 font-light"></i>
            <h3 class="text-3xl font-serif text-gray-900 mb-4 italic">Chưa tìm thấy hoa mẫu phù hợp</h3>
            <button @click="clearFilters"
              class="mt-4 text-xs font-bold border-b border-black pb-1 hover:text-pink-600 hover:border-pink-600 transition uppercase tracking-widest cursor-pointer">Xóa
              bộ lọc</button>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="filteredProducts.length > 0" class="flex flex-col sm:flex-row justify-between items-center gap-6 mt-16 pt-8 border-t border-gray-100">
          <p class="text-xs text-gray-400 font-light uppercase tracking-widest">
            Trang <span class="font-semibold text-pink-600">{{ currentPage }}</span> / <span class="font-semibold text-gray-600">{{ lastPage }}</span>
          </p>

          <div class="flex items-center gap-2">
            <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
              class="px-4 py-2.5 rounded-lg border text-[10px] font-bold uppercase tracking-widest disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-200 cursor-pointer"
              :class="currentPage === 1 
                ? 'border-gray-100 text-gray-300' 
                : 'border-gray-200 text-gray-700 hover:border-pink-600 hover:text-pink-600'">
              Trước
            </button>

            <button v-for="page in lastPage" :key="page" @click="changePage(page)"
              class="w-10 h-10 rounded-lg border text-[10px] font-bold transition-all duration-200 cursor-pointer"
              :class="page === currentPage
                ? 'bg-pink-600 border-pink-600 text-white shadow-md shadow-pink-100'
                : 'border-gray-200 text-gray-700 hover:border-pink-600 hover:text-pink-600'">
              {{ page }}
            </button>

            <button @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage"
              class="px-4 py-2.5 rounded-lg border text-[10px] font-bold uppercase tracking-widest disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-200 cursor-pointer"
              :class="currentPage === lastPage 
                ? 'border-gray-100 text-gray-300' 
                : 'border-gray-200 text-gray-700 hover:border-pink-600 hover:text-pink-600'">
              Sau
            </button>
          </div>
        </div>
      </section>
    </main>

    <Footer_client />
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;600;700&display=swap');

.font-serif {
  font-family: 'Playfair Display', serif;
}

.font-sans {
  font-family: 'Inter', sans-serif;
}
</style>
