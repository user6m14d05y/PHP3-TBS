<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import http, { getErrorMessage } from '@/utils/http';
import { useGeocode } from '@/composables/useGeocode';
import { useAuthStore } from '@/stores/auth';
import Header_client from '@/pages/Includes/Layouts/Header_client.vue';
import Footer_client from '@/pages/Includes/Layouts/Footer_client.vue';

const router = useRouter();
const authStore = useAuthStore();
const { geocoding, geocodeError, geocodeAddress } = useGeocode();

const addresses = ref([]);
const loading = ref(true);

const showForm = ref(false);
const editingId = ref(null);
const form = ref({
  label: '',
  recipient_name: '',
  phone: '',
  email: '',
  address_line: '',
  ward: '',
  district: '',
  city: '',
  is_default: false
});
const saving = ref(false);
const deletingId = ref(null);
const defaultingId = ref(null);

const isEditing = computed(() => editingId.value !== null);

onMounted(async () => {
  if (!authStore.user) {
    router.replace({ name: 'login', query: { redirect: '/profile/address' } });
    return;
  }
  await fetchAddresses();
});

const fetchAddresses = async () => {
  loading.value = true;
  try {
    const res = await http.get('/api/addresses');
    addresses.value = res.data.data || [];
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Không thể tải địa chỉ', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
  } finally {
    loading.value = false;
  }
};

const openCreate = () => {
  editingId.value = null;
  form.value = {
    label: '',
    recipient_name: '',
    phone: '',
    email: '',
    address_line: '',
    ward: '',
    district: '',
    city: '',
    is_default: addresses.value.length === 0
  };
  geocodeError.value = '';
  showForm.value = true;
};

const openEdit = (addr) => {
  editingId.value = addr.id;
  form.value = {
    label: addr.label || '',
    recipient_name: addr.recipient_name,
    phone: addr.phone,
    email: addr.email || '',
    address_line: addr.address_line,
    ward: addr.ward || '',
    district: addr.district || '',
    city: addr.city || '',
    is_default: !!addr.is_default
  };
  geocodeError.value = '';
  showForm.value = true;
};

const closeForm = () => {
  showForm.value = false;
  editingId.value = null;
};

const saveAddress = async () => {
  if (!form.value.recipient_name || !form.value.phone || !form.value.address_line || !form.value.city) {
    Swal.fire({ icon: 'warning', title: 'Thiếu thông tin', text: 'Vui lòng nhập đầy đủ họ tên, số điện thoại, địa chỉ và tỉnh/thành phố.', confirmButtonColor: '#db2777' });
    return;
  }

  saving.value = true;
  try {
    // Geocode để lấy tọa độ GPS (backend cần để tính khoảng cách giao hàng)
    const coords = await geocodeAddress(form.value);
    if (!coords) {
      Swal.fire({ icon: 'error', title: 'Không tìm thấy tọa độ', text: geocodeError.value, confirmButtonColor: '#db2777' });
      return;
    }

    const payload = { ...form.value, ...coords };

    if (isEditing.value) {
      const res = await http.patch(`/api/addresses/${editingId.value}`, payload);
      const idx = addresses.value.findIndex((a) => a.id === editingId.value);
      if (idx !== -1) addresses.value[idx] = res.data.data;
      Swal.fire({ toast: true, icon: 'success', title: 'Đã cập nhật địa chỉ', position: 'top-end', showConfirmButton: false, timer: 2000 });
    } else {
      const res = await http.post('/api/addresses', payload);
      addresses.value.push(res.data.data);
      Swal.fire({ toast: true, icon: 'success', title: 'Đã thêm địa chỉ', position: 'top-end', showConfirmButton: false, timer: 2000 });
    }

    closeForm();
    await fetchAddresses();
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Lưu địa chỉ thất bại', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
  } finally {
    saving.value = false;
  }
};

const deleteAddress = async (addr) => {
  const result = await Swal.fire({
    icon: 'warning',
    title: 'Xóa địa chỉ?',
    text: `Bạn chắc chắn muốn xóa địa chỉ "${addr.address_line}"?`,
    showCancelButton: true,
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Giữ lại',
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280'
  });

  if (!result.isConfirmed) return;

  deletingId.value = addr.id;
  try {
    await http.delete(`/api/addresses/${addr.id}`);
    Swal.fire({ toast: true, icon: 'success', title: 'Đã xóa địa chỉ', position: 'top-end', showConfirmButton: false, timer: 2000 });
    await fetchAddresses();
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Xóa thất bại', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
  } finally {
    deletingId.value = null;
  }
};

const makeDefault = async (addr) => {
  if (addr.is_default) return;

  defaultingId.value = addr.id;
  try {
    await http.post(`/api/addresses/${addr.id}/default`);
    Swal.fire({ toast: true, icon: 'success', title: 'Đã đặt làm mặc định', position: 'top-end', showConfirmButton: false, timer: 2000 });
    await fetchAddresses();
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Cập nhật thất bại', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
  } finally {
    defaultingId.value = null;
  }
};
</script>
<template>
    <Header_client />
    <main class="flex-grow bg-surface-light dark:bg-surface-dark py-12 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
                <div>
                    <h1 class="text-4xl font-display font-bold text-text-main-light dark:text-text-main-dark mb-2">Sổ Địa Chỉ</h1>
                    <p class="text-text-muted-light dark:text-text-muted-dark">Quản lý địa chỉ giao hàng của bạn.</p>
                </div>
                <button @click="openCreate"
                    class="bg-primary hover:bg-primary-dark text-white font-semibold px-5 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">add</span>
                    Thêm địa chỉ
                </button>
            </div>

            <!-- Form tạo/sửa -->
            <div v-if="showForm"
                class="bg-background-light dark:bg-background-dark rounded-lg shadow-sm border border-border-light dark:border-border-dark p-6 mb-8">
                <h2 class="text-xl font-display font-semibold mb-5">{{ isEditing ? 'Sửa địa chỉ' : 'Thêm địa chỉ mới' }}</h2>
                <form @submit.prevent="saveAddress" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Họ và Tên <span class="text-red-500">*</span></label>
                            <input v-model="form.recipient_name" required type="text" placeholder="Nhập họ và tên"
                                class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Số Điện Thoại <span class="text-red-500">*</span></label>
                            <input v-model="form.phone" required type="tel" placeholder="Nhập số điện thoại"
                                class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary shadow-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Địa chỉ (số nhà, tên đường) <span class="text-red-500">*</span></label>
                        <input v-model="form.address_line" required type="text" placeholder="Số nhà, tên đường, phường/xã..."
                            class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary shadow-sm" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Phường / Xã</label>
                            <input v-model="form.ward" type="text" placeholder="Phường/xã"
                                class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Quận / Huyện</label>
                            <input v-model="form.district" type="text" placeholder="Quận/huyện"
                                class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Tỉnh / Thành phố <span class="text-red-500">*</span></label>
                            <input v-model="form.city" required type="text" placeholder="Tỉnh/thành phố"
                                class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary shadow-sm" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nhãn (tùy chọn)</label>
                            <input v-model="form.label" type="text" placeholder="VD: Nhà riêng, Công ty..."
                                class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Email (tùy chọn)</label>
                            <input v-model="form.email" type="email" placeholder="Email nhận thông báo"
                                class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary shadow-sm" />
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="form.is_default" type="checkbox" class="text-primary focus:ring-primary" />
                        Đặt làm địa chỉ mặc định
                    </label>
                    <p v-if="geocodeError" class="text-sm text-red-500">{{ geocodeError }}</p>
                    <div class="flex gap-3">
                        <button type="submit" :disabled="saving || geocoding"
                            class="bg-primary hover:bg-primary-dark text-white font-semibold px-6 py-2.5 rounded-lg transition-colors disabled:opacity-50">
                            {{ geocoding ? 'Đang tìm tọa độ...' : (saving ? 'Đang lưu...' : 'Lưu địa chỉ') }}
                        </button>
                        <button type="button" @click="closeForm"
                            class="border border-border-light dark:border-border-dark px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-background-light dark:hover:bg-background-dark transition-colors">
                            Hủy
                        </button>
                    </div>
                </form>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="text-center py-16">
                <p class="text-text-muted-light dark:text-text-muted-dark">Đang tải địa chỉ...</p>
            </div>

            <!-- Empty -->
            <div v-else-if="addresses.length === 0" class="text-center py-16">
                <i class="fa-solid fa-location-dot text-gray-200 text-7xl mb-6"></i>
                <h2 class="font-display text-2xl font-bold text-gray-900 dark:text-white mb-3">Chưa có địa chỉ nào</h2>
                <p class="text-text-muted-light dark:text-text-muted-dark">Thêm địa chỉ giao hàng đầu tiên của bạn.</p>
            </div>

            <!-- List -->
            <div v-else class="space-y-4">
                <div v-for="addr in addresses" :key="addr.id"
                    class="bg-background-light dark:bg-background-dark rounded-lg shadow-sm border border-border-light dark:border-border-dark p-5 flex flex-col sm:flex-row justify-between gap-4"
                    :class="{ 'border-primary': addr.is_default }">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span v-if="addr.label" class="text-xs bg-gray-100 dark:bg-gray-700 text-text-muted-light dark:text-text-muted-dark px-2 py-1 rounded">{{ addr.label }}</span>
                            <span v-if="addr.is_default" class="text-xs bg-primary/10 text-primary px-2 py-1 rounded font-semibold">Mặc định</span>
                            <span v-if="addr.latitude && addr.longitude"
                                class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">Có tọa độ GPS</span>
                            <span v-else class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded">Chưa có tọa độ</span>
                        </div>
                        <p class="font-semibold text-sm">{{ addr.recipient_name }} - {{ addr.phone }}</p>
                        <p class="text-sm text-text-muted-light dark:text-text-muted-dark mt-1">
                            {{ addr.formatted_address || [addr.address_line, addr.ward, addr.district, addr.city].filter(Boolean).join(', ') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0 flex-wrap">
                        <button v-if="!addr.is_default" @click="makeDefault(addr)" :disabled="defaultingId === addr.id"
                            class="text-xs font-semibold border border-border-light dark:border-border-dark px-3 py-1.5 rounded hover:bg-primary/10 transition-colors disabled:opacity-50">
                            Đặt mặc định
                        </button>
                        <button @click="openEdit(addr)"
                            class="text-xs font-semibold border border-border-light dark:border-border-dark px-3 py-1.5 rounded hover:bg-primary/10 transition-colors">
                            Sửa
                        </button>
                        <button @click="deleteAddress(addr)" :disabled="deletingId === addr.id"
                            class="text-xs font-semibold border border-red-200 text-red-500 px-3 py-1.5 rounded hover:bg-red-50 transition-colors disabled:opacity-50">
                            {{ deletingId === addr.id ? 'Đang xóa...' : 'Xóa' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <Footer_client />
</template>

<style scoped>
</style>
