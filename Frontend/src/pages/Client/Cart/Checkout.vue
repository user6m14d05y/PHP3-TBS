<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import http, { getErrorMessage } from '@/utils/http';
import { formatVND } from '@/utils/format';
import { defaultImageUrl } from '@/utils/api';
import { useAuthStore } from '@/stores/auth';
import { useCartStore } from '@/stores/cart';
import { useGeocode } from '@/composables/useGeocode';
import Header_client from '@/pages/Includes/Layouts/Header_client.vue';
import Footer_client from '@/pages/Includes/Layouts/Footer_client.vue';

const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();
const { geocoding, geocodeError, geocodeAddress } = useGeocode();

// --- Trạng thái địa chỉ ---
const addresses = ref([]);
const selectedAddressId = ref(null);
const addressOption = ref('new');
const newForm = ref({
  recipient_name: '',
  phone: '',
  email: '',
  address_line: '',
  ward: '',
  district: '',
  city: ''
});
const savingAddress = ref(false);
const addressLoading = ref(false);

// --- Vận chuyển ---
const shops = ref([]);
const selectedShopId = ref(null);
const delivery = ref(null); // { shop, distance_km, delivery_radius_km, can_deliver, shipping_fees }
const deliveryChecking = ref(false);
const shippingMethod = ref('standard');

// --- Thanh toán ---
const paymentMethod = ref('cod');
const note = ref('');

// --- Coupon ---
const couponCode = ref('');
const appliedCoupon = ref(null); // { coupon, discount_amount, total_after_discount }
const couponApplying = ref(false);

// --- Submit ---
const submitting = ref(false);

const cartItems = computed(() => cartStore.items);
const subtotal = computed(() => cartStore.subtotal);
const discountAmount = computed(() => Number(appliedCoupon.value?.discount_amount || 0));
const shippingFee = computed(() => {
  const fees = delivery.value?.shipping_fees;
  if (!fees) return 0;
  return Number(fees[shippingMethod.value] ?? 0);
});
const totalAmount = computed(() => Math.max(subtotal.value - discountAmount.value, 0) + shippingFee.value);

const selectedAddress = computed(() =>
  addresses.value.find((a) => a.id === selectedAddressId.value) || null
);

const canSubmit = computed(() => {
  return !!selectedAddress.value
    && !!selectedAddress.value.latitude
    && !!delivery.value?.can_deliver
    && cartItems.value.length > 0
    && !submitting.value;
});

onMounted(async () => {
  if (!authStore.user) {
    router.replace({ name: 'login', query: { redirect: '/checkout' } });
    return;
  }

  try {
    await cartStore.fetchCart(true);
    if (cartStore.items.length === 0) {
      await Swal.fire({
        icon: 'warning',
        title: 'Giỏ hàng trống',
        text: 'Vui lòng thêm sản phẩm vào giỏ hàng trước khi thanh toán.',
        confirmButtonColor: '#db2777'
      });
      router.replace('/cart');
      return;
    }
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Không thể tải giỏ hàng', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
    return;
  }

  await Promise.all([loadAddresses(), loadShops()]);
});

const loadAddresses = async () => {
  addressLoading.value = true;
  try {
    const res = await http.get('/api/addresses');
    addresses.value = res.data.data || [];
    const defaultAddr = addresses.value.find((a) => a.is_default) || addresses.value[0];
    if (defaultAddr) {
      selectedAddressId.value = defaultAddr.id;
      addressOption.value = 'saved';
    }
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Không thể tải địa chỉ', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
  } finally {
    addressLoading.value = false;
  }
};

const loadShops = async () => {
  try {
    const res = await http.get('/api/shops');
    shops.value = (res.data.data || []).filter((s) => s.is_active);
    if (shops.value.length > 0) {
      selectedShopId.value = shops.value[0].id;
    }
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Không thể tải cửa hàng', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
  }
};

const runDeliveryCheck = async () => {
  const addr = selectedAddress.value;
  if (!addr || !selectedShopId.value || addr.latitude == null || addr.longitude == null) {
    delivery.value = null;
    return;
  }

  deliveryChecking.value = true;
  try {
    const res = await http.post('/api/shops/delivery-check', {
      shop_id: selectedShopId.value,
      latitude: addr.latitude,
      longitude: addr.longitude
    });
    delivery.value = res.data.data;
  } catch (error) {
    delivery.value = null;
    Swal.fire({ icon: 'error', title: 'Kiểm tra giao hàng thất bại', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
  } finally {
    deliveryChecking.value = false;
  }
};

// Khi chọn địa chỉ đã lưu: nếu thiếu tọa độ → tự geocode và cập nhật
watch(selectedAddressId, async (id) => {
  if (!id) return;
  const addr = addresses.value.find((a) => a.id === id);
  if (!addr) return;

  if (addr.latitude == null || addr.longitude == null) {
    const coords = await geocodeAddress(addr);
    if (coords) {
      try {
        const res = await http.patch(`/api/addresses/${id}`, coords);
        const idx = addresses.value.findIndex((a) => a.id === id);
        if (idx !== -1) {
          addresses.value[idx] = res.data.data;
        }
      } catch (error) {
        Swal.fire({ icon: 'error', title: 'Cập nhật tọa độ thất bại', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
      }
    }
  }

  await runDeliveryCheck();
});

const saveNewAddress = async () => {
  const form = newForm.value;
  if (!form.recipient_name || !form.phone || !form.address_line || !form.city) {
    Swal.fire({ icon: 'warning', title: 'Thiếu thông tin', text: 'Vui lòng nhập đầy đủ họ tên, số điện thoại, địa chỉ và tỉnh/thành phố.', confirmButtonColor: '#db2777' });
    return;
  }

  savingAddress.value = true;
  try {
    const coords = await geocodeAddress(form);
    if (!coords) {
      Swal.fire({ icon: 'error', title: 'Không tìm thấy tọa độ', text: geocodeError.value, confirmButtonColor: '#db2777' });
      return;
    }

    const res = await http.post('/api/addresses', { ...form, ...coords, is_default: addresses.value.length === 0 });
    const created = res.data.data;
    addresses.value.push(created);
    selectedAddressId.value = created.id;
    addressOption.value = 'saved';
    newForm.value = { recipient_name: '', phone: '', email: '', address_line: '', ward: '', district: '', city: '' };
    Swal.fire({ toast: true, icon: 'success', title: 'Đã lưu địa chỉ', position: 'top-end', showConfirmButton: false, timer: 2000 });
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Lưu địa chỉ thất bại', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
  } finally {
    savingAddress.value = false;
  }
};

const applyCoupon = async () => {
  const code = couponCode.value.trim();
  if (!code) return;

  couponApplying.value = true;
  try {
    const res = await http.post('/api/coupons/apply', { coupon_code: code });
    appliedCoupon.value = res.data.data;
    Swal.fire({ toast: true, icon: 'success', title: 'Voucher hợp lệ', position: 'top-end', showConfirmButton: false, timer: 2000 });
  } catch (error) {
    appliedCoupon.value = null;
    Swal.fire({ icon: 'error', title: 'Voucher không hợp lệ', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
  } finally {
    couponApplying.value = false;
  }
};

const submitOrder = async () => {
  if (!canSubmit.value) return;

  submitting.value = true;
  try {
    const res = await http.post('/api/checkout', {
      shop_id: selectedShopId.value,
      user_address_id: selectedAddressId.value,
      coupon_code: appliedCoupon.value?.coupon?.code || null,
      payment_method: paymentMethod.value,
      shipping_method: shippingMethod.value,
      note: note.value || null
    });

    cartStore.reset();
    const orderCode = res.data?.data?.order_code;
    router.push({ name: 'order-success', query: orderCode ? { order: orderCode } : {} });
  } catch (error) {
    // Coupon hết hạn giữa lúc apply và đặt hàng
    if (error.response?.data?.errors?.coupon_code) {
      appliedCoupon.value = null;
    }
    Swal.fire({ icon: 'error', title: 'Đặt hàng thất bại', text: getErrorMessage(error), confirmButtonColor: '#db2777' });
  } finally {
    submitting.value = false;
  }
};
</script>
<template>

    <Header_client />
    <div
        class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark min-h-screen flex flex-col transition-colors duration-300">
        <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="mb-8 text-center sm:text-left">
                <h1 class="text-3xl md:text-4xl font-display font-semibold mb-2">Thanh Toán</h1>
                <p class="text-text-light-muted dark:text-text-dark-muted">Vui lòng hoàn tất thông tin bên dưới để đặt
                    hàng.</p>
            </div>
            <div class="flex flex-col lg:flex-row gap-10">
                <div class="w-full lg:w-3/5 space-y-8">
                    <section
                        class="bg-surface-light dark:bg-surface-dark rounded-lg border border-border-light dark:border-border-dark p-6 sm:p-8 shadow-sm transition-colors duration-300">
                        <h2
                            class="text-xl font-display font-semibold mb-6 flex items-center gap-2 border-b border-border-light dark:border-border-dark pb-3">
                            <span class="material-symbols-outlined text-primary">local_shipping</span>
                            Thông Tin Giao Hàng
                        </h2>
                        <div class="flex p-1 bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark rounded-lg mb-6">
                            <button @click="addressOption = 'saved'" type="button" :disabled="addresses.length === 0"
                                :class="{'bg-primary text-white shadow': addressOption === 'saved', 'text-text-light-muted dark:text-text-dark-muted hover:text-text-light dark:hover:text-text-dark': addressOption !== 'saved', 'opacity-50 cursor-not-allowed': addresses.length === 0}"
                                class="flex-1 py-4 rounded-md text-sm font-medium transition-all">
                                Địa chỉ đã lưu
                            </button>
                            <button @click="addressOption = 'new'" type="button"
                                :class="{'bg-primary text-white shadow': addressOption === 'new', 'text-text-light-muted dark:text-text-dark-muted hover:text-text-light dark:hover:text-text-dark': addressOption !== 'new'}"
                                class="flex-1 py-4 rounded-md text-sm font-medium transition-all">
                                Địa chỉ mới
                            </button>
                        </div>

                        <!-- Form Địa Chỉ Mới -->
                        <form v-if="addressOption === 'new'" @submit.prevent="saveNewAddress" class="space-y-5 animate-fade-in">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="fullName">Họ và Tên <span
                                            class="text-primary">*</span></label>
                                    <input v-model="newForm.recipient_name"
                                        class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary transition-colors duration-300 shadow-sm"
                                        id="fullName" name="fullName" placeholder="Nhập họ và tên" required=""
                                        type="text" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="phone">Số Điện Thoại <span
                                            class="text-primary">*</span></label>
                                    <input v-model="newForm.phone"
                                        class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary transition-colors duration-300 shadow-sm"
                                        id="phone" name="phone" placeholder="Nhập số điện thoại" required=""
                                        type="tel" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" for="address">Địa Chỉ Nhận Hàng <span
                                        class="text-primary">*</span></label>
                                <input v-model="newForm.address_line"
                                    class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary transition-colors duration-300 shadow-sm"
                                    id="address" name="address" placeholder="Số nhà, tên đường, phường/xã..."
                                    required="" type="text" />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="ward">Phường / Xã</label>
                                    <input v-model="newForm.ward"
                                        class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary transition-colors duration-300 shadow-sm"
                                        id="ward" name="ward" placeholder="Phường/xã (tùy chọn)" type="text" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="district">Quận / Huyện</label>
                                    <input v-model="newForm.district"
                                        class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary transition-colors duration-300 shadow-sm"
                                        id="district" name="district" placeholder="Quận/huyện (tùy chọn)" type="text" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" for="city">Tỉnh / Thành Phố <span
                                        class="text-primary">*</span></label>
                                <input v-model="newForm.city"
                                    class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary transition-colors duration-300 shadow-sm"
                                    id="city" name="city" placeholder="Nhập tỉnh/thành phố" required="" type="text" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" for="notes">Ghi Chú Đơn Hàng (Tùy
                                    chọn)</label>
                                <textarea v-model="note"
                                    class="w-full rounded border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary transition-colors duration-300 shadow-sm"
                                    id="notes" name="notes"
                                    placeholder="Ghi chú thêm về thời gian giao hàng, lời nhắn..." rows="3"></textarea>
                            </div>
                            <button type="submit" :disabled="savingAddress || geocoding"
                                class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-3 px-6 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ geocoding ? 'Đang tìm tọa độ...' : (savingAddress ? 'Đang lưu...' : 'Lưu địa chỉ này') }}
                            </button>
                            <p v-if="geocodeError" class="text-sm text-red-500">{{ geocodeError }}</p>
                        </form>

                        <!-- Danh Sách Địa Chỉ Đã Lưu -->
                        <div v-else class="space-y-4 animate-fade-in">
                            <p v-if="addressLoading" class="text-sm text-text-light-muted dark:text-text-dark-muted">Đang tải địa chỉ...</p>
                            <p v-else-if="addresses.length === 0" class="text-sm text-text-light-muted dark:text-text-dark-muted">Chưa có địa chỉ đã lưu.</p>
                            <label v-for="addr in addresses" :key="addr.id"
                                class="flex items-start p-4 border border-border-light dark:border-border-dark rounded-lg cursor-pointer hover:border-primary dark:hover:border-primary transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input v-model="selectedAddressId" class="mt-1 text-primary focus:ring-primary" name="savedAddress" type="radio" :value="addr.id" />
                                <div class="ml-3 flex-grow">
                                    <div class="flex justify-between">
                                        <span class="block font-medium text-text-light dark:text-text-dark">{{ addr.recipient_name }} - {{ addr.phone }}</span>
                                        <span v-if="addr.is_default" class="text-xs bg-primary/10 text-primary px-2 py-1 rounded">Mặc định</span>
                                    </div>
                                    <span class="block text-sm text-text-light-muted dark:text-text-dark-muted mt-1">
                                        {{ addr.formatted_address || [addr.address_line, addr.ward, addr.district, addr.city].filter(Boolean).join(', ') }}
                                    </span>
                                    <span v-if="geocoding && selectedAddressId === addr.id" class="block text-xs text-amber-500 mt-1">Đang tìm tọa độ cho địa chỉ này...</span>
                                </div>
                            </label>
                        </div>
                    </section>
                    <section
                        class="bg-surface-light dark:bg-surface-dark rounded-lg border border-border-light dark:border-border-dark p-6 sm:p-8 shadow-sm transition-colors duration-300">
                        <h2
                            class="text-xl font-display font-semibold mb-6 flex items-center gap-2 border-b border-border-light dark:border-border-dark pb-3">
                            <span class="material-symbols-outlined text-primary">airport_shuttle</span>
                            Phương Thức Vận Chuyển
                        </h2>
                        <div v-if="!delivery" class="text-sm text-text-light-muted dark:text-text-dark-muted mb-4">
                            Chọn địa chỉ giao hàng để tính phí vận chuyển.
                        </div>
                        <div v-else class="mb-4 text-sm space-y-1">
                            <p class="text-text-light-muted dark:text-text-dark-muted">
                                Khoảng cách: <strong>{{ delivery.distance_km }} km</strong> (bán kính hỗ trợ {{ delivery.delivery_radius_km }} km)
                            </p>
                            <p v-if="delivery.can_deliver" class="text-green-600 dark:text-green-400 font-medium">✓ Địa chỉ của bạn nằm trong khu vực giao hàng</p>
                            <p v-else class="text-red-500 font-medium">✗ Địa chỉ của bạn quá xa, không hỗ trợ giao hàng</p>
                        </div>
                        <div class="space-y-4">
                            <label
                                class="flex items-center p-4 border border-border-light dark:border-border-dark rounded-lg cursor-pointer hover:border-primary dark:hover:border-primary transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input v-model="shippingMethod" class="text-primary focus:ring-primary" name="shippingMethod"
                                    type="radio" value="standard" />
                                <div class="ml-4 w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-primary">local_shipping</span>
                                </div>
                                <div class="ml-4 flex-grow flex justify-between items-center">
                                    <div>
                                        <span class="block font-medium text-text-light dark:text-text-dark">Giao Hàng
                                            Tiêu Chuẩn</span>
                                        <span class="block text-sm text-text-light-muted dark:text-text-dark-muted">Nhận
                                            hàng trong 2-3 ngày làm việc</span>
                                    </div>
                                    <span class="font-medium text-text-light dark:text-text-dark">{{ delivery ? formatVND(delivery.shipping_fees?.standard) : '—' }}</span>
                                </div>
                            </label>
                            <label
                                class="flex items-center p-4 border border-border-light dark:border-border-dark rounded-lg cursor-pointer hover:border-primary dark:hover:border-primary transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input v-model="shippingMethod" class="text-primary focus:ring-primary" name="shippingMethod" type="radio"
                                    value="express" />
                                <div class="ml-4 w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-primary">airport_shuttle</span>
                                </div>
                                <div class="ml-4 flex-grow flex justify-between items-center">
                                    <div>
                                        <span class="block font-medium text-text-light dark:text-text-dark">Giao Hàng
                                            Hỏa Tốc</span>
                                        <span class="block text-sm text-text-light-muted dark:text-text-dark-muted">Nhận
                                            hàng trong vòng 2 giờ (chỉ áp dụng nội thành)</span>
                                    </div>
                                    <span class="font-medium text-text-light dark:text-text-dark">{{ delivery ? formatVND(delivery.shipping_fees?.express) : '—' }}</span>
                                </div>
                            </label>
                        </div>
                    </section>
                    <section
                        class="bg-surface-light dark:bg-surface-dark rounded-lg border border-border-light dark:border-border-dark p-6 sm:p-8 shadow-sm transition-colors duration-300">
                        <h2
                            class="text-xl font-display font-semibold mb-6 flex items-center gap-2 border-b border-border-light dark:border-border-dark pb-3">
                            <span class="material-symbols-outlined text-primary">payment</span>
                            Phương Thức Thanh Toán
                        </h2>
                        <div class="space-y-4">
                            <div
                                class="border border-border-light dark:border-border-dark rounded-lg overflow-hidden transition-colors has-[:checked]:border-primary">
                                <label
                                    class="flex items-center p-4 cursor-pointer hover:bg-background-light dark:hover:bg-background-dark transition-colors has-[:checked]:bg-primary/5">
                                    <input v-model="paymentMethod" class="text-primary focus:ring-primary" name="paymentMethod"
                                        type="radio" value="cod" />
                                    <div class="ml-4 w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-blue-50 text-blue-500 dark:bg-blue-500/10 dark:text-blue-400">
                                        <span class="material-symbols-outlined">payments</span>
                                    </div>
                                    <span class="ml-3 font-medium text-text-light dark:text-text-dark flex-grow">Thanh
                                        toán khi nhận hàng (COD)</span>
                                </label>
                                <div
                                    class="px-4 pb-4 pt-0 ml-[4.5rem] text-sm text-text-light-muted dark:text-text-dark-muted">
                                    Quý khách sẽ thanh toán bằng tiền mặt khi nhân viên giao hàng giao hoa đến nơi.
                                </div>
                            </div>
                            <div
                                class="border border-border-light dark:border-border-dark rounded-lg overflow-hidden transition-colors has-[:checked]:border-primary">
                                <label
                                    class="flex items-center p-4 cursor-pointer hover:bg-background-light dark:hover:bg-background-dark transition-colors has-[:checked]:bg-primary/5">
                                    <input v-model="paymentMethod" class="text-primary focus:ring-primary peer" name="paymentMethod"
                                        type="radio" value="bank" />
                                    <div class="ml-4 w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-green-50 text-green-500 dark:bg-green-500/10 dark:text-green-400">
                                        <span class="material-symbols-outlined">account_balance</span>
                                    </div>
                                    <span class="ml-3 font-medium text-text-light dark:text-text-dark flex-grow">Chuyển
                                        khoản ngân hàng</span>
                                </label>
                            </div>
                            <div
                                class="border border-border-light dark:border-border-dark rounded-lg overflow-hidden transition-colors opacity-60 cursor-not-allowed">
                                <label class="flex items-center p-4">
                                    <input disabled class="text-primary focus:ring-primary" name="paymentMethod" type="radio" value="momo" />
                                    <div class="ml-4 w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-[#a50064]/10 dark:bg-[#a50064]/20 p-1">
                                        <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-MoMo-Square.png" alt="Momo" class="w-full h-full object-contain" />
                                    </div>
                                    <span class="ml-3 font-medium text-text-light dark:text-text-dark flex-grow">Thanh
                                        toán qua Ví Momo</span>
                                    <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded ml-2">Sắp ra mắt</span>
                                </label>
                            </div>
                            <div
                                class="border border-border-light dark:border-border-dark rounded-lg overflow-hidden transition-colors opacity-60 cursor-not-allowed">
                                <label class="flex items-center p-4">
                                    <input disabled class="text-primary focus:ring-primary" name="paymentMethod" type="radio" value="zalo" />
                                    <div class="ml-4 w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-[#005BA6]/10 dark:bg-[#005BA6]/20 p-1">
                                        <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-ZaloPay-Square.png" alt="ZaloPay" class="w-full h-full object-contain rounded-md" />
                                    </div>
                                    <span class="ml-3 font-medium text-text-light dark:text-text-dark flex-grow">Thanh
                                        toán qua ZaloPay</span>
                                    <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded ml-2">Sắp ra mắt</span>
                                </label>
                            </div>
                            <div
                                class="border border-border-light dark:border-border-dark rounded-lg overflow-hidden transition-colors opacity-60 cursor-not-allowed">
                                <label class="flex items-center p-4">
                                    <input disabled class="text-primary focus:ring-primary" name="paymentMethod" type="radio" value="vnpay" />
                                    <div class="ml-4 w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 border border-border-light dark:border-border-dark p-1 bg-white">
                                        <img src="https://vinadesign.vn/uploads/images/2023/05/vnpay-logo-vinadesign-25-12-57-55.jpg" alt="VNPay" class="w-full h-full object-contain" />
                                    </div>
                                    <span class="ml-3 font-medium text-text-light dark:text-text-dark flex-grow">Thanh
                                        toán qua VNPay</span>
                                    <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded ml-2">Sắp ra mắt</span>
                                </label>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="w-full lg:w-2/5">
                    <div
                        class="bg-primary/5 dark:bg-primary/10 rounded-lg p-6 sm:p-8 sticky top-24 border border-primary/20 dark:border-primary/30">
                        <h2
                            class="text-xl font-display font-semibold mb-6 border-b border-primary/20 dark:border-primary/30 pb-3 text-text-light dark:text-text-dark">
                            Tóm Tắt Đơn Hàng</h2>
                        <div class="space-y-4 mb-6 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                            <div v-for="item in cartItems" :key="item.id" class="flex items-start gap-4">
                                <div
                                    class="w-20 h-20 flex-shrink-0 bg-surface-light dark:bg-surface-dark rounded border border-border-light dark:border-border-dark overflow-hidden relative">
                                    <img alt="Sản phẩm" class="w-full h-full object-cover" :src="defaultImageUrl" />
                                    <span
                                        class="absolute -top-2 -right-2 bg-text-light dark:bg-text-dark text-surface-light dark:text-surface-dark text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full">{{ item.quantity }}</span>
                                </div>
                                <div class="flex-grow">
                                    <h3
                                        class="font-medium text-text-light dark:text-text-dark text-sm sm:text-base leading-tight mb-1">
                                        {{ item.product_name }}</h3>
                                    <p v-if="item.size_name" class="text-xs text-text-light-muted dark:text-text-dark-muted mb-2">Size: {{ item.size_name }}</p>
                                    <p class="font-semibold text-text-light dark:text-text-dark">{{ formatVND(item.line_total) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-6 flex gap-2 border-b border-primary/20 dark:border-primary/30 pb-6">
                            <input v-model="couponCode"
                                class="flex-grow rounded border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark text-text-light dark:text-text-dark focus:ring-primary focus:border-primary shadow-sm text-sm"
                                placeholder="Mã giảm giá" type="text" />
                            <button @click="applyCoupon" :disabled="couponApplying"
                                class="bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-light dark:text-text-dark px-4 py-2 rounded hover:bg-background-light dark:hover:bg-background-dark transition-colors font-medium text-sm disabled:opacity-50"
                                type="button">Áp Dụng</button>
                        </div>
                        <div class="space-y-3 mb-6 text-sm">
                            <div class="flex justify-between text-text-light-muted dark:text-text-dark-muted">
                                <span>Tạm tính</span>
                                <span>{{ formatVND(subtotal) }}</span>
                            </div>
                            <div v-if="discountAmount > 0" class="flex justify-between text-green-600 dark:text-green-400">
                                <span>Giảm giá ({{ appliedCoupon?.coupon?.code }})</span>
                                <span>-{{ formatVND(discountAmount) }}</span>
                            </div>
                            <div class="flex justify-between text-text-light-muted dark:text-text-dark-muted">
                                <span>Phí vận chuyển</span>
                                <span>{{ shippingFee > 0 ? formatVND(shippingFee) : '—' }}</span>
                            </div>
                        </div>
                        <div
                            class="flex justify-between items-center border-t border-primary/20 dark:border-primary/30 pt-4 mb-8">
                            <span class="font-display font-semibold text-lg text-text-light dark:text-text-dark">Tổng
                                Cộng</span>
                            <span class="font-display font-bold text-2xl text-primary">{{ formatVND(totalAmount) }}</span>
                        </div>
                        <button @click="submitOrder" :disabled="!canSubmit"
                            class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-4 px-6 rounded-lg transition-colors flex justify-center items-center gap-2 shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                            type="submit">
                            <span class="material-symbols-outlined text-xl">lock</span>
                            {{ submitting ? 'Đang đặt hàng...' : 'Thanh Toán An Toàn' }}
                        </button>
                        <p v-if="!canSubmit && cartItems.length > 0" class="text-center text-xs text-amber-600 mt-3">
                            Cần chọn địa chỉ hợp lệ và nằm trong khu vực giao hàng để tiếp tục.
                        </p>
                        <p class="text-center text-xs text-text-light-muted dark:text-text-dark-muted mt-4">Thông tin
                            của bạn được bảo mật tuyệt đối.</p>
                    </div>
                </div>
            </div>
        </main>
        <Footer_client />
    </div>
</template>
<style scoped>

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background-color: #E5E7EB;
                border-radius: 20px;
            }

            .dark .custom-scrollbar::-webkit-scrollbar-thumb {
                background-color: #374151;
            }
            input{
                padding: 13px;
                outline-color: #D14D72;
            }
            select{
                padding: 13px;
                outline-color: #D14D72;
            }
            textarea{
                padding: 13px;
                outline-color: #D14D72;
            }
        </style>
