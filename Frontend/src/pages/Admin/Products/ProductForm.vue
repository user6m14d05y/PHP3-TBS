<script setup>
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import { apiUrl, imageUrl } from '@/utils/api';

const props = defineProps({
  mode: {
    type: String,
    default: 'create',
  },
  productId: {
    type: [String, Number],
    default: null,
  },
  isDark: {
    type: Boolean,
    default: false,
  },
});

const router = useRouter();
const maxImageSize = 10 * 1024 * 1024;

const categories = ref([]);
const categoryItems = ref([]);
const sizes = ref([]);
const isLoading = ref(false);
const isSaving = ref(false);
const fieldErrors = ref({});

const form = ref({
  name: '',
  slug: '',
  description: '',
  seo_title: '',
  meta_description: '',
  focus_keyword: '',
  image_alt: '',
  category_id: '',
  category_item_id: '',
  is_active: true,
  thumbnail: null,
  thumbnailPreview: null,
  gallery: [],
  galleryPreviews: [],
  existingImages: [],
  variants: [{ size_id: '', price: '', discount_percent: '', stock: 0, sku: '' }],
});

const isEditMode = computed(() => props.mode === 'edit');
const titleCount = computed(() => form.value.seo_title.length);
const descriptionCount = computed(() => form.value.meta_description.length);
const normalizedSlug = computed(() => normalizeSlug(form.value.slug || form.value.name));
const slugPreview = computed(() => normalizedSlug.value.slice(0, 120));
const hasValidationErrors = computed(() => Object.keys(fieldErrors.value).length > 0);

const fieldClass = computed(() =>
  props.isDark
    ? 'bg-[#0f172a] border-gray-600 text-white placeholder-gray-500'
    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400',
);

const panelClass = computed(() =>
  props.isDark ? 'bg-[#1e293b] border-gray-700' : 'bg-white border-gray-100',
);

const mutedTextClass = computed(() => (props.isDark ? 'text-gray-400' : 'text-gray-500'));
const labelClass = computed(() => (props.isDark ? 'text-gray-300' : 'text-gray-700'));
const validationTextClass = computed(() => (props.isDark ? 'text-red-300' : 'text-red-600'));
const actionBarClass = computed(() =>
  props.isDark ? 'bg-[#1e293b]/95 border-gray-700' : 'bg-white/95 border-pink-100',
);

const normalizeSlug = (value) => {
  return String(value || '')
    .toLowerCase()
    .replace(/\u0111/g, 'd')
    .replace(/\u0110/g, 'd')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .replace(/-{2,}/g, '-');
};

const getFieldError = (field) => fieldErrors.value[field] || '';

const getVariantError = (index, field) => getFieldError(`variants.${index}.${field}`);

const validationClass = (field) => [
  fieldClass.value,
  getFieldError(field)
    ? '!border-red-500 focus:!border-red-500 focus:!ring-red-500/20'
    : 'focus:border-pink-500 focus:ring-pink-500/15',
];

const variantValidationClass = (index, field) => [
  fieldClass.value,
  getVariantError(index, field)
    ? '!border-red-500 focus:!border-red-500 focus:!ring-red-500/20'
    : 'focus:border-pink-500 focus:ring-pink-500/15',
];

const setFieldError = (field, message) => {
  fieldErrors.value = {
    ...fieldErrors.value,
    [field]: message,
  };
};

const clearFieldError = (field) => {
  if (!fieldErrors.value[field]) return;

  const nextErrors = { ...fieldErrors.value };
  delete nextErrors[field];
  fieldErrors.value = nextErrors;
};

const clearVariantError = (index, field) => {
  clearFieldError(`variants.${index}.${field}`);
};

const clearVariantErrors = () => {
  fieldErrors.value = Object.fromEntries(
    Object.entries(fieldErrors.value).filter(([field]) => !field.startsWith('variants.')),
  );
};

const scrollToFirstError = async () => {
  await nextTick();

  const firstError = document.querySelector('[data-has-error="true"]');
  firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
};

const fetchCategories = async () => {
  const response = await axios.get(apiUrl('/api/category'));
  categories.value = response.data.data || [];
};

const fetchCategoryItems = async (categoryId = form.value.category_id, resetSelection = true) => {
  if (!categoryId) {
    categoryItems.value = [];
    form.value.category_item_id = '';
    return;
  }

  const response = await axios.get(apiUrl('/api/category-item'), {
    params: { category_id: categoryId },
  });
  categoryItems.value = response.data.data || [];

  if (resetSelection) {
    form.value.category_item_id = '';
  }
};

const handleCategoryChange = () => {
  clearFieldError('category_id');
  fetchCategoryItems(form.value.category_id);
};

const fetchSizes = async () => {
  const response = await axios.get(apiUrl('/api/size'));
  sizes.value = response.data.data || [];
};

const fetchProduct = async () => {
  if (!isEditMode.value || !props.productId) return;

  isLoading.value = true;
  try {
    const response = await axios.get(apiUrl(`/api/product/${props.productId}`));
    const product = response.data.data;

    form.value = {
      name: product.name || '',
      slug: product.slug || '',
      description: product.description || '',
      seo_title: product.seo_title || '',
      meta_description: product.meta_description || '',
      focus_keyword: product.focus_keyword || '',
      image_alt: product.image_alt || product.name || '',
      category_id: product.category_id || '',
      category_item_id: product.category_item_id || '',
      is_active: Boolean(product.is_active),
      thumbnail: null,
      thumbnailPreview: product.thumbnail ? imageUrl(product.thumbnail) : null,
      gallery: [],
      galleryPreviews: [],
      existingImages: product.images || [],
      variants: product.variants?.length
        ? product.variants.map((variant) => ({
            size_id: variant.size_id || '',
            price: variant.price || '',
            discount_percent: calculateDiscountPercent(variant.price, variant.sale_price),
            stock: variant.stock || 0,
            sku: variant.sku || '',
          }))
        : [{ size_id: '', price: '', discount_percent: '', stock: 0, sku: '' }],
    };

    await fetchCategoryItems(product.category_id, false);
  } catch (error) {
    console.error('Product load failed:', error);
    Swal.fire({
      icon: 'error',
      title: 'Không tải được sản phẩm',
      text: 'Vui lòng kiểm tra lại API hoặc sản phẩm đã bị xóa.',
      confirmButtonColor: '#db2777',
    });
  } finally {
    isLoading.value = false;
  }
};

const validateImageFile = (file, field = 'thumbnail') => {
  if (!file) return true;

  if (!file.type.startsWith('image/')) {
    setFieldError(field, 'Vui lòng chọn đúng định dạng ảnh.');
    Swal.fire({ icon: 'warning', title: 'File không hợp lệ', text: 'Vui lòng chọn file ảnh.' });
    return false;
  }

  if (file.size > maxImageSize) {
    setFieldError(field, 'Mỗi ảnh tối đa 10MB. Vui lòng nén ảnh trước khi tải lên.');
    Swal.fire({
      icon: 'warning',
      title: 'Ảnh quá lớn',
      text: 'Mỗi ảnh tối đa 10MB. Vui lòng nén ảnh trước khi tải lên.',
      confirmButtonColor: '#db2777',
    });
    return false;
  }

  clearFieldError(field);
  return true;
};

const handleThumbnail = (event) => {
  const file = event.target.files?.[0];
  if (!validateImageFile(file, 'thumbnail')) {
    event.target.value = '';
    return;
  }

  form.value.thumbnail = file;
  form.value.thumbnailPreview = URL.createObjectURL(file);
};

const handleGallery = (event) => {
  const files = Array.from(event.target.files || []);
  const validFiles = files.filter((file) => validateImageFile(file, 'gallery'));

  validFiles.forEach((file) => {
    form.value.gallery.push(file);
    form.value.galleryPreviews.push(URL.createObjectURL(file));
  });

  event.target.value = '';
};

const removeNewGalleryImage = (index) => {
  form.value.gallery.splice(index, 1);
  form.value.galleryPreviews.splice(index, 1);
};

const removeExistingImage = async (image, index) => {
  const result = await Swal.fire({
    title: 'Xóa ảnh này?',
    text: 'Ảnh sẽ bị xóa khỏi sản phẩm.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonText: 'Hủy',
    confirmButtonText: 'Xóa',
  });

  if (!result.isConfirmed) return;

  await axios.delete(apiUrl(`/api/product/image/${image.id}`));
  form.value.existingImages.splice(index, 1);
};

const addVariant = () => {
  form.value.variants.push({ size_id: '', price: '', discount_percent: '', stock: 0, sku: '' });
};

const toggleProductVisibility = () => {
  form.value.is_active = !form.value.is_active;
};

const removeVariant = (index) => {
  if (form.value.variants.length <= 1) return;
  form.value.variants.splice(index, 1);
  clearVariantErrors();
};

const formatCurrency = (value) => {
  if (!Number(value)) return '';

  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
  }).format(Number(value));
};

const normalizeDiscountPercent = (value) => {
  if (value === '' || value === null || value === undefined) return null;

  const percent = Number(value);
  if (!Number.isFinite(percent) || percent <= 0) return null;

  return Math.min(percent, 99);
};

const calculateDiscountPercent = (price, salePrice) => {
  const basePrice = Number(price);
  const discountedPrice = Number(salePrice);

  if (!basePrice || !discountedPrice || discountedPrice >= basePrice) return '';

  return Math.round(((basePrice - discountedPrice) / basePrice) * 100);
};

const calculateSalePrice = (price, discountPercent) => {
  const basePrice = Number(price);
  const percent = normalizeDiscountPercent(discountPercent);

  if (!basePrice || !percent) return null;

  return Math.round(basePrice * (100 - percent) / 100);
};

const primaryVariant = computed(() =>
  form.value.variants.find((variant) => Number(variant.price) > 0) || form.value.variants[0] || {},
);

const previewSalePrice = computed(() =>
  calculateSalePrice(primaryVariant.value.price, primaryVariant.value.discount_percent),
);

const previewOriginalPrice = computed(() =>
  primaryVariant.value.price ? formatCurrency(primaryVariant.value.price) : '',
);

const previewFinalPrice = computed(() =>
  previewSalePrice.value ? formatCurrency(previewSalePrice.value) : previewOriginalPrice.value,
);

const normalizeVariantForPayload = (variant) => {
  const discountPercent = normalizeDiscountPercent(variant.discount_percent);
  const salePrice = calculateSalePrice(variant.price, variant.discount_percent);

  return {
    size_id: variant.size_id || null,
    price: variant.price,
    sale_price: salePrice,
    discount_percent: discountPercent,
    stock: variant.stock === '' ? 0 : variant.stock,
    sku: variant.sku ? String(variant.sku).trim() : null,
  };
};

const buildFormData = () => {
  const payload = new FormData();
  const slug = slugPreview.value;

  payload.append('name', form.value.name.trim());
  payload.append('slug', slug);
  payload.append('description', form.value.description || '');
  payload.append('seo_title', form.value.seo_title || '');
  payload.append('meta_description', form.value.meta_description || '');
  payload.append('focus_keyword', form.value.focus_keyword || '');
  payload.append('image_alt', form.value.image_alt || form.value.name.trim());
  payload.append('is_active', form.value.is_active ? 1 : 0);

  if (form.value.category_id) {
    payload.append('category_id', form.value.category_id);
  }

  if (form.value.category_item_id) {
    payload.append('category_item_id', form.value.category_item_id);
  }

  if (form.value.thumbnail) {
    payload.append('thumbnail', form.value.thumbnail);
  }

  form.value.gallery.forEach((file) => {
    payload.append('gallery[]', file);
  });

  payload.append('variants', JSON.stringify(form.value.variants.map(normalizeVariantForPayload)));

  return payload;
};

const validateForm = () => {
  const errors = {};

  if (!form.value.name.trim()) {
    errors.name = 'Vui lòng nhập tên sản phẩm.';
  }

  if (!normalizedSlug.value) {
    errors.slug = 'Vui lòng nhập slug hoặc tên sản phẩm để tạo slug.';
  } else if (normalizedSlug.value.length > 120) {
    errors.slug = 'Slug tối đa 120 ký tự. Vui lòng rút gọn slug.';
  }

  if (form.value.seo_title.length > 70) {
    errors.seo_title = 'SEO title tối đa 70 ký tự.';
  }

  if (form.value.focus_keyword.length > 120) {
    errors.focus_keyword = 'Focus keyword tối đa 120 ký tự.';
  }

  if (form.value.meta_description.length > 170) {
    errors.meta_description = 'Meta description tối đa 170 ký tự.';
  }

  if (form.value.image_alt.length > 160) {
    errors.image_alt = 'Alt ảnh chính tối đa 160 ký tự.';
  }

  if (!form.value.thumbnail && !form.value.thumbnailPreview) {
    errors.thumbnail = 'Vui lòng chọn ảnh đại diện sản phẩm.';
  }

  form.value.variants.forEach((variant, index) => {
    const price = Number(variant.price);
    const stock = Number(variant.stock);

    if (!price || price <= 0) {
      errors[`variants.${index}.price`] = 'Giá bán phải lớn hơn 0.';
    }

    if (variant.discount_percent !== '' && variant.discount_percent !== null && variant.discount_percent !== undefined) {
      const percent = Number(variant.discount_percent);

      if (!Number.isFinite(percent) || percent < 0 || percent > 99) {
        errors[`variants.${index}.discount_percent`] = 'Giảm giá phải nằm trong khoảng 0 - 99%.';
      }
    }

    if (variant.stock !== '' && (!Number.isFinite(stock) || stock < 0)) {
      errors[`variants.${index}.stock`] = 'Tồn kho không được nhỏ hơn 0.';
    }
  });

  fieldErrors.value = errors;

  if (Object.keys(errors).length) {
    scrollToFirstError();
    return false;
  }

  return true;
};

const applyServerErrors = (errors = {}) => {
  fieldErrors.value = Object.fromEntries(
    Object.entries(errors).map(([field, messages]) => [
      field,
      Array.isArray(messages) ? messages[0] : String(messages),
    ]),
  );
  scrollToFirstError();
};

const submitProduct = async () => {
  if (!validateForm()) return;

  isSaving.value = true;
  try {
    const endpoint = isEditMode.value
      ? apiUrl(`/api/product/update/${props.productId}`)
      : apiUrl('/api/product');

    await axios.post(endpoint, buildFormData(), {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    await Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: isEditMode.value ? 'Cập nhật sản phẩm thành công' : 'Thêm sản phẩm thành công',
      showConfirmButton: false,
      timer: 2200,
    });

    router.push({ name: 'admin-product' });
  } catch (error) {
    console.error('Product save failed:', error);

    if (error.response?.data?.errors) {
      applyServerErrors(error.response.data.errors);
      return;
    }

    Swal.fire({
      icon: 'error',
      title: 'Không lưu được sản phẩm',
      text: error.response?.data?.message || 'Vui lòng kiểm tra dữ liệu và thử lại.',
      confirmButtonColor: '#db2777',
    });
  } finally {
    isSaving.value = false;
  }
};

watch(
  () => form.value.name,
  (name) => {
    clearFieldError('name');

    if (!isEditMode.value && !form.value.slug) {
      form.value.slug = normalizeSlug(name).slice(0, 120);
      clearFieldError('slug');
    }

    if (!form.value.image_alt) {
      form.value.image_alt = name;
    }
  },
);

watch(
  () => form.value.slug,
  () => {
    clearFieldError('slug');
  },
);

onMounted(async () => {
  isLoading.value = true;
  try {
    await Promise.all([fetchCategories(), fetchSizes()]);
    await fetchProduct();
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <form @submit.prevent="submitProduct" class="space-y-6 pb-24">
    <div v-if="isLoading" :class="panelClass" class="rounded-lg border p-8 text-center">
      <i class="fa-solid fa-spinner fa-spin mr-2"></i>
      Đang tải dữ liệu sản phẩm...
    </div>

    <template v-else>
      <button
        type="submit"
        :disabled="isSaving"
        class="fixed bottom-6 right-6 z-40 hidden rounded-full bg-pink-600 px-5 py-3 text-sm font-semibold text-white shadow-2xl shadow-pink-500/25 transition hover:bg-pink-700 disabled:cursor-not-allowed disabled:opacity-60 lg:inline-flex lg:items-center lg:gap-2"
      >
        <i v-if="isSaving" class="fa-solid fa-spinner fa-spin"></i>
        <i v-else class="fa-solid fa-floppy-disk"></i>
        {{ isEditMode ? 'Cập nhật' : 'Lưu sản phẩm' }}
      </button>

      <div
        v-if="hasValidationErrors"
        class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
      >
        Vui lòng kiểm tra các trường đang báo lỗi bên dưới.
      </div>

      <div class="grid gap-6 lg:grid-cols-12 lg:items-start">
        <section :class="panelClass" class="rounded-lg border p-6 shadow-sm lg:col-span-8">
          <div class="mb-5 flex items-start justify-between gap-4">
          <div>
            <h2 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-xl font-bold">
              Thông tin sản phẩm
            </h2>
            <p :class="mutedTextClass" class="mt-1 text-sm">
              Tên, danh mục và mô tả sản phẩm.
            </p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div class="md:col-span-2" :data-has-error="Boolean(getFieldError('name'))">
            <label :class="labelClass" class="mb-1.5 block text-sm font-semibold">Tên sản phẩm *</label>
            <input
              v-model="form.name"
              type="text"
              placeholder="VD: Bó hoa hồng đỏ"
              :class="validationClass('name')"
              @input="clearFieldError('name')"
              class="w-full rounded-lg border px-4 py-2.5 text-sm outline-none transition focus:ring-4 focus:ring-pink-500/15"
            >
            <p v-if="getFieldError('name')" :class="validationTextClass" class="mt-1 text-xs font-medium">
              {{ getFieldError('name') }}
            </p>
          </div>

          <div :data-has-error="Boolean(getFieldError('category_id'))">
            <label :class="labelClass" class="mb-1.5 block text-sm font-semibold">Danh mục cha</label>
            <select
              v-model="form.category_id"
              :class="validationClass('category_id')"
              class="w-full rounded-lg border px-4 py-2.5 text-sm outline-none transition focus:ring-4 focus:ring-pink-500/15"
              @change="handleCategoryChange"
            >
              <option value="">-- Chọn danh mục --</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
            <p v-if="getFieldError('category_id')" :class="validationTextClass" class="mt-1 text-xs font-medium">
              {{ getFieldError('category_id') }}
            </p>
          </div>

          <div>
            <label :class="labelClass" class="mb-1.5 block text-sm font-semibold">Danh mục con</label>
            <select
              v-model="form.category_item_id"
              :disabled="categoryItems.length === 0"
              :class="fieldClass"
              class="w-full rounded-lg border px-4 py-2.5 text-sm outline-none transition focus:ring-4 focus:ring-pink-500/15 disabled:opacity-50"
            >
              <option value="">-- Chọn danh mục con --</option>
              <option v-for="item in categoryItems" :key="item.id" :value="item.id">
                {{ item.name }}
              </option>
            </select>
          </div>

          <div class="md:col-span-2">
            <label :class="labelClass" class="mb-1.5 block text-sm font-semibold">Mô tả sản phẩm</label>
            <textarea
              v-model="form.description"
              rows="5"
              placeholder="Mô tả chi tiết về kiểu hoa, dịp sử dụng, kích thước và cách bảo quản..."
              :class="fieldClass"
              class="w-full resize-y rounded-lg border px-4 py-2.5 text-sm outline-none transition focus:ring-4 focus:ring-pink-500/15"
            ></textarea>
          </div>
        </div>
      </section>

        <section :class="panelClass" class="rounded-lg border p-6 shadow-sm lg:col-span-8">
        <div class="mb-5">
          <h2 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-xl font-bold">
            SEO sản phẩm
          </h2>
          <p :class="mutedTextClass" class="mt-1 text-sm">
            Mỗi sản phẩm có title, meta description, slug và alt ảnh riêng.
          </p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div class="md:col-span-2" :data-has-error="Boolean(getFieldError('slug'))">
            <label :class="labelClass" class="mb-1.5 block text-sm font-semibold">Slug URL *</label>
            <input
              v-model="form.slug"
              type="text"
              placeholder="bo-hoa-hong-do"
              :class="validationClass('slug')"
              @input="clearFieldError('slug')"
              class="w-full rounded-lg border px-4 py-2.5 text-sm outline-none transition focus:ring-4 focus:ring-pink-500/15"
            >
            <p v-if="getFieldError('slug')" :class="validationTextClass" class="mt-1 text-xs font-medium">
              {{ getFieldError('slug') }}
            </p>
            <p :class="mutedTextClass" class="mt-1 text-xs">
              Preview: /product/{{ slugPreview || 'slug-san-pham' }} - {{ slugPreview.length }}/120 ký tự
            </p>
          </div>

          <div :data-has-error="Boolean(getFieldError('seo_title'))">
            <label :class="labelClass" class="mb-1.5 block text-sm font-semibold">SEO title</label>
            <input
              v-model="form.seo_title"
              maxlength="70"
              type="text"
              placeholder="Bó hoa hồng đỏ giao nhanh tại TP.HCM"
              :class="validationClass('seo_title')"
              @input="clearFieldError('seo_title')"
              class="w-full rounded-lg border px-4 py-2.5 text-sm outline-none transition focus:ring-4 focus:ring-pink-500/15"
            >
            <p v-if="getFieldError('seo_title')" :class="validationTextClass" class="mt-1 text-xs font-medium">
              {{ getFieldError('seo_title') }}
            </p>
            <p :class="titleCount > 60 ? 'text-amber-500' : mutedTextClass" class="mt-1 text-xs">
              {{ titleCount }}/70 ký tự
            </p>
          </div>

          <div :data-has-error="Boolean(getFieldError('focus_keyword'))">
            <label :class="labelClass" class="mb-1.5 block text-sm font-semibold">Focus keyword</label>
            <input
              v-model="form.focus_keyword"
              maxlength="120"
              type="text"
              placeholder="bó hoa hồng đỏ"
              :class="validationClass('focus_keyword')"
              @input="clearFieldError('focus_keyword')"
              class="w-full rounded-lg border px-4 py-2.5 text-sm outline-none transition focus:ring-4 focus:ring-pink-500/15"
            >
            <p v-if="getFieldError('focus_keyword')" :class="validationTextClass" class="mt-1 text-xs font-medium">
              {{ getFieldError('focus_keyword') }}
            </p>
          </div>

          <div class="md:col-span-2" :data-has-error="Boolean(getFieldError('meta_description'))">
            <label :class="labelClass" class="mb-1.5 block text-sm font-semibold">Meta description</label>
            <textarea
              v-model="form.meta_description"
              maxlength="170"
              rows="3"
              placeholder="Đặt bó hoa hồng đỏ tươi, thiết kế tinh tế, giao hoa nhanh trong ngày và chụp ảnh xác nhận trước khi giao."
              :class="validationClass('meta_description')"
              @input="clearFieldError('meta_description')"
              class="w-full resize-none rounded-lg border px-4 py-2.5 text-sm outline-none transition focus:ring-4 focus:ring-pink-500/15"
            ></textarea>
            <p v-if="getFieldError('meta_description')" :class="validationTextClass" class="mt-1 text-xs font-medium">
              {{ getFieldError('meta_description') }}
            </p>
            <p :class="descriptionCount > 155 ? 'text-amber-500' : mutedTextClass" class="mt-1 text-xs">
              {{ descriptionCount }}/170 ký tự
            </p>
          </div>
        </div>
      </section>

        <section :class="panelClass" class="rounded-lg border p-6 shadow-sm lg:top-6 lg:col-span-4 lg:col-start-9 lg:row-span-3 lg:row-start-1">
        <div class="mb-5">
          <h2 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-xl font-bold">Ảnh và hiển thị</h2>
          <p :class="mutedTextClass" class="mt-1 text-sm">Quản lý trạng thái bán hàng, ảnh đại diện và gallery.</p>
        </div>

        <div class="space-y-5">
          <div
            :class="isDark ? 'border-gray-700 bg-[#0f172a]' : 'border-gray-100 bg-white'"
            class="overflow-hidden rounded-lg border"
          >
            <div class="aspect-[4/3] bg-gray-100">
              <img
                v-if="form.thumbnailPreview"
                :src="form.thumbnailPreview"
                :alt="form.image_alt || form.name"
                class="h-full w-full object-cover"
              >
              <div v-else class="flex h-full w-full items-center justify-center text-gray-400">
                <i class="fa-regular fa-image text-3xl"></i>
              </div>
            </div>
            <div class="space-y-2 p-4">
              <div class="flex items-center justify-between gap-3">
                <span
                  :class="form.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'"
                  class="rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide"
                >
                  {{ form.is_active ? 'Đang hiển thị' : 'Đang ẩn' }}
                </span>
                <span :class="mutedTextClass" class="text-xs">Preview</span>
              </div>
              <h3 :class="isDark ? 'text-white' : 'text-gray-900'" class="line-clamp-2 text-base font-semibold">
                {{ form.name || 'Tên sản phẩm' }}
              </h3>
              <div class="flex items-center gap-2">
                <span v-if="previewFinalPrice" class="text-sm font-bold text-pink-600">{{ previewFinalPrice }}</span>
                <span v-else :class="mutedTextClass" class="text-sm">Chưa nhập giá</span>
                <span v-if="previewSalePrice && previewOriginalPrice" :class="mutedTextClass" class="text-xs line-through">
                  {{ previewOriginalPrice }}
                </span>
              </div>
              <p :class="mutedTextClass" class="break-all text-xs">
                /product/{{ slugPreview || 'slug-san-pham' }}
              </p>
            </div>
          </div>

          <div :data-has-error="Boolean(getFieldError('image_alt'))">
            <label :class="labelClass" class="mb-1.5 block text-sm font-semibold">Alt ảnh chính</label>
            <input
              v-model="form.image_alt"
              maxlength="160"
              type="text"
              placeholder="Bó hoa hồng đỏ tươi giao trong ngày"
              :class="validationClass('image_alt')"
              @input="clearFieldError('image_alt')"
              class="w-full rounded-lg border px-4 py-2.5 text-sm outline-none transition focus:ring-4 focus:ring-pink-500/15"
            >
            <p v-if="getFieldError('image_alt')" :class="validationTextClass" class="mt-1 text-xs font-medium">
              {{ getFieldError('image_alt') }}
            </p>
          </div>

          <div class="grid gap-4 sm:grid-cols-[120px_1fr] lg:grid-cols-1 xl:grid-cols-[120px_1fr]" :data-has-error="Boolean(getFieldError('thumbnail'))">
            <div v-if="form.thumbnailPreview" class="h-28 w-28 overflow-hidden rounded-lg border border-pink-200 bg-pink-50">
              <img :src="form.thumbnailPreview" :alt="form.image_alt || form.name" class="h-full w-full object-cover">
            </div>
            <div v-else class="flex h-28 w-28 items-center justify-center rounded-lg border border-dashed border-gray-300 text-gray-400">
              <i class="fa-regular fa-image text-2xl"></i>
            </div>

            <label
              :class="[
                isDark ? 'border-gray-600 bg-[#0f172a]' : 'border-gray-300 bg-gray-50',
                getFieldError('thumbnail') ? '!border-red-500' : '',
              ]"
              class="flex min-h-28 cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed p-5 text-center transition hover:border-pink-400"
            >
              <input type="file" accept="image/*" class="hidden" @change="handleThumbnail">
              <i class="fa-solid fa-cloud-arrow-up mb-2 text-2xl text-pink-500"></i>
              <span :class="mutedTextClass" class="text-sm">Chọn ảnh đại diện</span>
            </label>
          </div>

          <p v-if="getFieldError('thumbnail')" :class="validationTextClass" class="text-xs font-medium">
            {{ getFieldError('thumbnail') }}
          </p>

          <div>
            <label :class="labelClass" class="mb-2 block text-sm font-semibold">Gallery</label>
            <div v-if="form.existingImages.length || form.galleryPreviews.length" class="mb-4 flex flex-wrap gap-3">
              <div
                v-for="(image, index) in form.existingImages"
                :key="image.id"
                class="group relative h-20 w-20 overflow-hidden rounded-lg border border-gray-200"
              >
                <img :src="imageUrl(image.image_path)" :alt="form.image_alt || form.name" class="h-full w-full object-cover">
                <button type="button" class="absolute inset-0 hidden bg-red-500/70 text-white group-hover:block" @click="removeExistingImage(image, index)">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </div>

              <div
                v-for="(image, index) in form.galleryPreviews"
                :key="image"
                class="group relative h-20 w-20 overflow-hidden rounded-lg border border-pink-200"
              >
                <img :src="image" :alt="form.image_alt || form.name" class="h-full w-full object-cover">
                <button type="button" class="absolute inset-0 hidden bg-red-500/70 text-white group-hover:block" @click="removeNewGalleryImage(index)">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </div>
            </div>

            <label
              :class="isDark ? 'border-gray-600 bg-[#0f172a]' : 'border-gray-300 bg-gray-50'"
              class="flex cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed p-5 text-center transition hover:border-pink-400"
            >
              <input type="file" accept="image/*" multiple class="hidden" @change="handleGallery">
              <i class="fa-solid fa-images mb-2 text-2xl text-pink-500"></i>
              <span :class="mutedTextClass" class="text-sm">Thêm ảnh gallery</span>
            </label>
          </div>
        </div>
        <button
          type="button"
          role="switch"
          :aria-checked="String(form.is_active)"
          :class="isDark ? 'border-gray-700 bg-[#0f172a]' : 'border-pink-100 bg-pink-50/60'"
          class="mt-5 flex w-full cursor-pointer items-center justify-between gap-4 rounded-lg border p-4 text-left transition hover:border-pink-300 focus:outline-none focus:ring-4 focus:ring-pink-500/15"
          @click="toggleProductVisibility"
        >
          <span class="min-w-0">
            <span :class="labelClass" class="block text-sm font-semibold">Hiển thị sản phẩm</span>
            <span :class="mutedTextClass" class="mt-1 block text-xs">
              {{ form.is_active ? 'Sản phẩm đang được hiển thị trên website.' : 'Sản phẩm đang được ẩn khỏi website.' }}
            </span>
          </span>
          <span class="flex shrink-0 items-center gap-2">
            <span
              :class="form.is_active ? 'bg-pink-600' : (isDark ? 'bg-gray-700' : 'bg-gray-300')"
              class="relative block h-8 w-16 rounded-full p-1 transition-colors"
            >
              <span
                :class="form.is_active ? 'translate-x-8' : 'translate-x-0'"
                class="block h-6 w-6 rounded-full bg-white shadow transition-transform"
              ></span>
            </span>
            <span
              :class="form.is_active ? 'text-emerald-500' : mutedTextClass"
              class="w-8 text-right text-xs font-bold"
            >
              {{ form.is_active ? 'ON' : 'OFF' }}
            </span>
          </span>
        </button>
      </section>

        <section :class="panelClass" class="rounded-lg border p-6 shadow-sm lg:col-span-8">
        <div class="mb-5 flex items-center justify-between gap-4">
          <div>
            <h2 :class="isDark ? 'text-white' : 'text-gray-900'" class="text-xl font-bold">Biến thể và giá</h2>
            <p :class="mutedTextClass" class="mt-1 text-sm">Mỗi biến thể cần có giá bán; giảm giá nhập theo phần trăm.</p>
          </div>
          <button type="button" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600" @click="addVariant">
            <i class="fa-solid fa-plus mr-1"></i>
            Thêm biến thể
          </button>
        </div>

        <div class="space-y-3">
          <div
            v-for="(variant, index) in form.variants"
            :key="index"
            :class="isDark ? 'bg-gray-800/50 border-gray-700' : 'bg-gray-50 border-gray-200'"
            class="grid gap-3 rounded-lg border p-3 md:grid-cols-12"
          >
            <div class="md:col-span-3">
              <label class="mb-1 block text-xs font-semibold text-gray-400">Kích thước</label>
              <select v-model="variant.size_id" :class="fieldClass" class="w-full rounded-lg border px-3 py-2 text-sm outline-none">
                <option value="">-- Size --</option>
                <option v-for="size in sizes" :key="size.id" :value="size.id">{{ size.name }}</option>
              </select>
            </div>

            <div class="md:col-span-2" :data-has-error="Boolean(getVariantError(index, 'price'))">
              <label class="mb-1 block text-xs font-semibold text-gray-400">Giá *</label>
              <input
                v-model="variant.price"
                type="number"
                min="0"
                :class="variantValidationClass(index, 'price')"
                class="w-full rounded-lg border px-3 py-2 text-sm outline-none"
                @input="clearVariantError(index, 'price')"
              >
              <p v-if="getVariantError(index, 'price')" :class="validationTextClass" class="mt-1 text-[11px] font-medium">
                {{ getVariantError(index, 'price') }}
              </p>
            </div>

            <div class="md:col-span-2" :data-has-error="Boolean(getVariantError(index, 'discount_percent'))">
              <label class="mb-1 block text-xs font-semibold text-gray-400">Giảm (%)</label>
              <input
                v-model="variant.discount_percent"
                type="number"
                min="0"
                max="99"
                :class="variantValidationClass(index, 'discount_percent')"
                class="w-full rounded-lg border px-3 py-2 text-sm outline-none"
                @input="clearVariantError(index, 'discount_percent')"
              >
              <p v-if="getVariantError(index, 'discount_percent')" :class="validationTextClass" class="mt-1 text-[11px] font-medium">
                {{ getVariantError(index, 'discount_percent') }}
              </p>
              <p v-if="calculateSalePrice(variant.price, variant.discount_percent)" class="mt-1 text-[11px] text-pink-500">
                Sau giảm: {{ formatCurrency(calculateSalePrice(variant.price, variant.discount_percent)) }}
              </p>
            </div>

            <div class="md:col-span-2" :data-has-error="Boolean(getVariantError(index, 'stock'))">
              <label class="mb-1 block text-xs font-semibold text-gray-400">Tồn kho</label>
              <input
                v-model="variant.stock"
                type="number"
                min="0"
                :class="variantValidationClass(index, 'stock')"
                class="w-full rounded-lg border px-3 py-2 text-sm outline-none"
                @input="clearVariantError(index, 'stock')"
              >
              <p v-if="getVariantError(index, 'stock')" :class="validationTextClass" class="mt-1 text-[11px] font-medium">
                {{ getVariantError(index, 'stock') }}
              </p>
            </div>

            <div class="md:col-span-2">
              <label class="mb-1 block text-xs font-semibold text-gray-400">SKU</label>
              <input v-model="variant.sku" type="text" :class="fieldClass" class="w-full rounded-lg border px-3 py-2 text-sm outline-none">
            </div>

            <div class="flex items-end justify-end md:col-span-1">
              <button
                type="button"
                :disabled="form.variants.length === 1"
                class="rounded-lg px-3 py-2 text-red-500 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-30"
                @click="removeVariant(index)"
              >
                <i class="fa-solid fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </section>

      </div>

     
    </template>
  </form>
</template>
