<script setup>
import HeaderAdmin from '../../Includes/Layouts/Header_Admin.vue';
import NavbarAdmin from '../../Includes/Layouts/Navbar_Admin.vue';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import { apiUrl, imageUrl } from '@/utils/api';

const router = useRouter();

const isDark = ref(false);
const isSidebarOpen = ref(true);
const products = ref([]);
const currentPage = ref(1);
const perPage = ref(12);
const totalProducts = ref(0);
const lastPage = ref(1);
const isLoading = ref(false);

const fetchProducts = async (page = currentPage.value) => {
  isLoading.value = true;
  try {
    const response = await axios.get(apiUrl('/api/product'), {
      params: {
        page,
        limit: perPage.value,
        include_inactive: 1,
      },
    });

    products.value = response.data.data || [];
    currentPage.value = response.data.current_page || 1;
    perPage.value = response.data.per_page || perPage.value;
    totalProducts.value = response.data.total || 0;
    lastPage.value = response.data.last_page || 1;
  } catch (error) {
    console.error('Product list load failed:', error);
    Swal.fire({
      icon: 'error',
      title: 'Không tải được sản phẩm',
      text: 'Vui lòng kiểm tra backend API.',
      confirmButtonColor: '#db2777',
    });
  } finally {
    isLoading.value = false;
  }
};

const changePage = (page) => {
  if (page < 1 || page > lastPage.value || page === currentPage.value) return;
  fetchProducts(page);
};

const editProduct = (product) => {
  router.push({ name: 'admin-product-edit', params: { id: product.id } });
};

const createProduct = () => {
  router.push({ name: 'admin-product-add' });
};

const deleteProduct = async (id) => {
  const result = await Swal.fire({
    title: 'Xóa sản phẩm này?',
    text: 'Tất cả biến thể và ảnh sẽ bị xóa theo.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonText: 'Hủy',
    confirmButtonText: 'Xóa',
  });

  if (!result.isConfirmed) return;

  try {
    await axios.delete(apiUrl(`/api/product/${id}`));
    await fetchProducts(currentPage.value);
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Đã xóa sản phẩm',
      showConfirmButton: false,
      timer: 2200,
    });
  } catch (error) {
    console.error('Product delete failed:', error);
    Swal.fire({
      icon: 'error',
      title: 'Không xóa được sản phẩm',
      text: error.response?.data?.message || 'Vui lòng thử lại.',
      confirmButtonColor: '#db2777',
    });
  }
};

const formatPrice = (price) => {
  if (!price) return '-';
  return Number(price).toLocaleString('vi-VN') + 'đ';
};

const getPriceRange = (variants = []) => {
  if (!variants.length) return 'Chưa có giá';

  const prices = variants.map((variant) => Number(variant.sale_price || variant.price)).filter((price) => price > 0);
  if (!prices.length) return 'Chưa có giá';

  const minPrice = Math.min(...prices);
  const maxPrice = Math.max(...prices);

  return minPrice === maxPrice ? formatPrice(minPrice) : `${formatPrice(minPrice)} - ${formatPrice(maxPrice)}`;
};

const toggleTheme = () => {
  isDark.value = !isDark.value;
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

onMounted(() => {
  isDark.value = localStorage.getItem('theme') === 'dark';
  fetchProducts();
});
</script>

<template>
  <div class="antialiased font-sans transition-colors duration-300">
    <div
      :class="isDark ? 'bg-[#0f172a] text-gray-100' : 'bg-gray-50 text-gray-900'"
      class="flex h-screen overflow-hidden"
    >
      <NavbarAdmin :isDark="isDark" :isSidebarOpen="isSidebarOpen" />

      <div class="relative flex flex-1 flex-col overflow-hidden">
        <HeaderAdmin
          :isDark="isDark"
          @toggle-sidebar="isSidebarOpen = !isSidebarOpen"
          @toggle-theme="toggleTheme"
        />

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
          <div
            :class="isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-100'"
            class="w-full rounded-xl border p-6 shadow-sm transition-colors duration-300 md:p-8"
          >
            <div
              :class="isDark ? 'border-gray-700' : 'border-gray-200'"
              class="mb-8 flex flex-col items-start justify-between gap-4 border-b pb-6 sm:flex-row sm:items-center"
            >
              <div>
                <h2 :class="isDark ? 'text-white' : 'text-gray-900'" class="mb-2 text-3xl font-serif font-bold">
                  Sản phẩm
                </h2>
                <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-sm font-light">
                  Quản lý sản phẩm, biến thể, ảnh và dữ liệu SEO.
                </p>
              </div>
              <button
                class="flex shrink-0 items-center rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-pink-700"
                @click="createProduct"
              >
                <i class="fa-solid fa-plus mr-2 text-lg"></i>
                Thêm sản phẩm
              </button>
            </div>

            <div
              :class="isDark ? 'border-gray-700' : 'border-gray-200'"
              class="overflow-x-auto rounded-lg border"
            >
              <table class="w-full border-collapse text-left">
                <thead>
                  <tr
                    :class="isDark ? 'bg-gray-800/50 text-gray-400 border-gray-700' : 'bg-gray-50 text-gray-600 border-gray-200'"
                    class="border-b text-xs uppercase tracking-wider"
                  >
                    <th class="w-12 px-4 py-4 font-semibold">STT</th>
                    <th class="w-20 px-4 py-4 font-semibold">Ảnh</th>
                    <th class="px-4 py-4 font-semibold">Tên sản phẩm</th>
                    <th class="px-4 py-4 font-semibold">SEO</th>
                    <th class="px-4 py-4 font-semibold">Danh mục</th>
                    <th class="px-4 py-4 font-semibold">Khoảng giá</th>
                    <th class="px-4 py-4 font-semibold text-center">Trạng thái</th>
                    <th class="px-4 py-4 text-right font-semibold">Thao tác</th>
                  </tr>
                </thead>

                <tbody :class="isDark ? 'divide-gray-700' : 'divide-gray-200'" class="divide-y">
                  <tr v-if="isLoading">
                    <td colspan="8" class="py-16 text-center text-gray-400">
                      <i class="fa-solid fa-spinner fa-spin mb-3 block text-3xl"></i>
                      Đang tải sản phẩm...
                    </td>
                  </tr>

                  <tr v-else-if="products.length === 0">
                    <td colspan="8" class="py-16 text-center text-gray-400">
                      <i class="fa-solid fa-box-open mb-3 block text-4xl opacity-40"></i>
                      Chưa có sản phẩm nào.
                    </td>
                  </tr>

                  <tr
                    v-for="(product, index) in products"
                    v-else
                    :key="product.id"
                    :class="isDark ? 'hover:bg-gray-800/30' : 'hover:bg-gray-50'"
                    class="transition-colors"
                  >
                    <td class="px-4 py-4 text-sm" :class="isDark ? 'text-gray-400' : 'text-gray-500'">
                      #{{ (currentPage - 1) * perPage + index + 1 }}
                    </td>

                    <td class="px-4 py-4">
                      <router-link
                        :to="'/product/' + product.slug"
                        :class="isDark ? 'border-gray-700' : 'border-gray-200'"
                        class="block h-14 w-14 overflow-hidden rounded-lg border transition-all duration-300 hover:scale-105 hover:border-pink-500"
                      >
                        <img
                          v-if="product.thumbnail"
                          :src="imageUrl(product.thumbnail)"
                          :alt="product.image_alt || product.name"
                          class="h-full w-full object-cover"
                          loading="lazy"
                          decoding="async"
                        >
                        <div v-else class="flex h-full w-full items-center justify-center bg-gray-100">
                          <i class="fa-regular fa-image text-xl text-gray-400"></i>
                        </div>
                      </router-link>
                    </td>

                    <td class="px-4 py-4">
                      <router-link :to="'/product/' + product.slug" class="group/name">
                        <p
                          :class="isDark ? 'text-white' : 'text-gray-900'"
                          class="text-sm font-semibold transition-colors group-hover/name:text-pink-600"
                        >
                          {{ product.name }}
                        </p>
                      </router-link>
                      <p :class="isDark ? 'text-gray-500' : 'text-gray-400'" class="mt-0.5 text-xs">
                        /product/{{ product.slug }}
                      </p>
                    </td>

                    <td class="px-4 py-4 text-sm">
                      <span
                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                        :class="product.seo_title && product.meta_description ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                      >
                        {{ product.seo_title && product.meta_description ? 'Đủ SEO' : 'Thiếu SEO' }}
                      </span>
                    </td>

                    <td class="px-4 py-4 text-sm" :class="isDark ? 'text-gray-300' : 'text-gray-700'">
                      <span class="font-medium">{{ product.category?.name || 'None' }}</span>
                      <span v-if="product.categoryItem || product.category_item" :class="isDark ? 'text-gray-500' : 'text-gray-400'" class="block text-xs">
                        {{ (product.categoryItem || product.category_item).name }}
                      </span>
                    </td>

                    <td class="px-4 py-4 text-sm font-medium text-emerald-500">
                      {{ getPriceRange(product.variants) }}
                    </td>

                    <td class="px-4 py-4 text-center">
                      <span
                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                        :class="product.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
                      >
                        {{ product.is_active ? 'Hiển thị' : 'Ẩn' }}
                      </span>
                    </td>

                    <td class="space-x-3 whitespace-nowrap px-4 py-4 text-right">
                      <button class="text-blue-500 transition-colors hover:text-blue-700" @click="editProduct(product)">
                        <i class="fa-regular fa-pen-to-square text-lg"></i>
                      </button>
                      <button class="text-red-500 transition-colors hover:text-red-700" @click="deleteProduct(product.id)">
                        <i class="fa-regular fa-trash-can text-lg"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-if="totalProducts > 0" class="mt-6 flex flex-col items-center justify-between gap-4 sm:flex-row">
              <p :class="isDark ? 'text-gray-400' : 'text-gray-500'" class="text-sm">
                Hiển thị
                <span class="font-semibold">{{ (currentPage - 1) * perPage + 1 }}</span>
                -
                <span class="font-semibold">{{ Math.min(currentPage * perPage, totalProducts) }}</span>
                trong tổng
                <span class="font-semibold">{{ totalProducts }}</span>
                sản phẩm
              </p>

              <div class="flex items-center gap-2">
                <button
                  :disabled="currentPage === 1"
                  :class="isDark ? 'border-gray-700 text-gray-300 hover:bg-gray-800' : 'border-gray-200 text-gray-700 hover:bg-gray-50'"
                  class="rounded-lg border px-3 py-2 text-sm font-medium transition-colors disabled:cursor-not-allowed disabled:opacity-40"
                  @click="changePage(currentPage - 1)"
                >
                  Trước
                </button>

                <button
                  v-for="page in lastPage"
                  :key="page"
                  :class="page === currentPage ? 'bg-pink-600 border-pink-600 text-white' : isDark ? 'border-gray-700 text-gray-300 hover:bg-gray-800' : 'border-gray-200 text-gray-700 hover:bg-gray-50'"
                  class="h-9 w-9 rounded-lg border text-sm font-semibold transition-colors"
                  @click="changePage(page)"
                >
                  {{ page }}
                </button>

                <button
                  :disabled="currentPage === lastPage"
                  :class="isDark ? 'border-gray-700 text-gray-300 hover:bg-gray-800' : 'border-gray-200 text-gray-700 hover:bg-gray-50'"
                  class="rounded-lg border px-3 py-2 text-sm font-medium transition-colors disabled:cursor-not-allowed disabled:opacity-40"
                  @click="changePage(currentPage + 1)"
                >
                  Sau
                </button>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap');

.font-serif {
  font-family: 'Playfair Display', serif;
}

.font-sans {
  font-family: 'Inter', sans-serif;
}
</style>
