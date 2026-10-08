<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  coupon: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close']);
const router = useRouter();

const isCopied = ref(false);
let copyTimeout = null;

const formatPlainVND = (value) => {
  return `${new Intl.NumberFormat('vi-VN').format(Number(value) || 0)} đ`;
};

const formatCouponDate = (dateStr) => {
  if (!dateStr) return 'Vô thời hạn';
  const d = new Date(dateStr);
  if (Number.isNaN(d.getTime())) return 'Vô thời hạn';
  return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')}/${d.getFullYear()}`;
};

const formatFullCouponDateTime = (dateStr) => {
  if (!dateStr) return 'Vô thời hạn';
  const d = new Date(dateStr);
  if (Number.isNaN(d.getTime())) return 'Vô thời hạn';
  const time = `${d.getHours().toString().padStart(2, '0')}:${d.getMinutes().toString().padStart(2, '0')}`;
  const date = `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')}/${d.getFullYear()}`;
  return `${time} ngày ${date}`;
};

const getCouponDiscountLabel = (c) => {
  if (!c) return '';
  if (c.discount_type === 'percentage') {
    return `GIẢM ${parseFloat(c.discount_value)}%`;
  }
  return `GIẢM ${formatPlainVND(c.discount_value)}`;
};

const parseCouponDate = (dateStr) => {
  if (!dateStr) return null;
  const date = new Date(dateStr);
  return Number.isNaN(date.getTime()) ? null : date;
};

const isCouponExpired = (c) => {
  if (!c) return false;
  const expiry = parseCouponDate(c.expires_at);
  return !!expiry && expiry < new Date();
};

const isCouponUnavailable = (c) => {
  if (!c) return false;
  const hasUsageLimit = c.usage_limit !== null && c.usage_limit !== undefined;
  const usageLimitReached = hasUsageLimit && Number(c.used_count || 0) >= Number(c.usage_limit);
  return c.is_active === false || usageLimitReached;
};

const isCouponNearExpiry = (c) => {
  if (!c) return false;
  const expiry = parseCouponDate(c.expires_at);
  if (!expiry || isCouponExpired(c)) return false;
  const now = new Date();
  const sevenDays = 7 * 24 * 60 * 60 * 1000;
  return expiry.getTime() - now.getTime() <= sevenDays;
};

const getCouponStatusLabel = (c) => {
  if (!c) return '';
  if (isCouponExpired(c)) return 'Đã hết hạn';
  if (isCouponUnavailable(c)) return 'Tạm hết lượt dùng';
  if (isCouponNearExpiry(c)) return 'Sắp hết hạn';
  return 'Sẵn sàng sử dụng';
};

const canUseCoupon = (c) => {
  if (!c) return false;
  return !isCouponExpired(c) && !isCouponUnavailable(c);
};

const closeModal = () => {
  emit('close');
};

const copyCode = async () => {
  if (!props.coupon?.code) return;

  try {
    if (!navigator.clipboard?.writeText) {
      throw new Error('Clipboard API is not available');
    }

    await navigator.clipboard.writeText(props.coupon.code);
    isCopied.value = true;

    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Đã sao chép mã voucher',
      text: props.coupon.code,
      showConfirmButton: false,
      timer: 2000,
    });

    if (copyTimeout) clearTimeout(copyTimeout);
    copyTimeout = setTimeout(() => {
      isCopied.value = false;
    }, 3000);
  } catch {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Không thể sao chép',
      text: 'Vui lòng sao chép mã thủ công.',
      showConfirmButton: false,
      timer: 2500,
    });
  }
};

const useCouponNow = () => {
  copyCode();
  closeModal();
  router.push('/product');
};

const handleKeydown = (e) => {
  if (e.key === 'Escape' && props.show) {
    closeModal();
  }
};

watch(
  () => props.show,
  (val) => {
    if (val) {
      document.body.style.overflow = 'hidden';
      isCopied.value = false;
    } else {
      document.body.style.overflow = '';
    }
  }
);

onMounted(() => {
  window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
  document.body.style.overflow = '';
  if (copyTimeout) clearTimeout(copyTimeout);
});
</script>

<template>
  <Transition name="modal-fade">
    <div
      v-if="show && coupon"
      class="fixed inset-0 z-[9999] flex items-center justify-center p-3 sm:p-5 bg-black/60 backdrop-blur-xs"
      @click.self="closeModal"
      role="dialog"
      aria-modal="true"
      aria-labelledby="modal-coupon-title"
    >
      <div
        class="relative flex w-full max-w-lg flex-col bg-white border border-[#eaded2] shadow-2xl transition-all duration-300 max-h-[88vh] overflow-hidden"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-[#eaded2] bg-[#fffaf6] px-5 py-3.5">
          <div class="flex items-center gap-2.5">
            <span class="inline-flex h-7 w-7 items-center justify-center bg-pink-100 text-pink-700">
              <i class="fa-solid fa-ticket text-xs"></i>
            </span>
            <div>
              <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#7b8a80]">Ưu đãi TBS Flora</p>
              <h3 id="modal-coupon-title" class="font-serif text-base font-bold text-[#17251d]">
                Chi Tiết Mã Giảm Giá
              </h3>
            </div>
          </div>

          <button
            @click="closeModal"
            type="button"
            class="flex h-8 w-8 items-center justify-center border border-[#eaded2] bg-white text-[#5f6b62] transition hover:bg-pink-50 hover:text-pink-700 hover:border-pink-300"
            aria-label="Đóng cửa sổ"
          >
            <i class="fa-solid fa-xmark text-sm"></i>
          </button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
          <!-- Discount Hero Banner -->
          <div class="border border-[#eaded2] bg-[#fffaf6] p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
              <div>
                <span
                  :class="[
                    'inline-block px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider border',
                    isCouponExpired(coupon) || isCouponUnavailable(coupon)
                      ? 'bg-gray-100 text-gray-600 border-gray-300'
                      : isCouponNearExpiry(coupon)
                        ? 'bg-amber-50 text-amber-700 border-amber-300'
                        : 'bg-emerald-50 text-emerald-700 border-emerald-300'
                  ]"
                >
                  {{ getCouponStatusLabel(coupon) }}
                </span>
                <p class="mt-1.5 text-2xl font-black tracking-tight text-pink-700 sm:text-3xl">
                  {{ getCouponDiscountLabel(coupon) }}
                </p>
                <p v-if="coupon.discount_type === 'percentage' && Number(coupon.max_discount_amount) > 0" class="mt-0.5 text-xs font-semibold text-[#5f6b62]">
                  Giảm tối đa {{ formatPlainVND(coupon.max_discount_amount) }}
                </p>
              </div>

              <!-- Coupon Code Box -->
              <div class="flex flex-col items-start sm:items-end">
                <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#7b8a80]">Mã ưu đãi</span>
                <div class="mt-1 flex items-center border border-dashed border-pink-400 bg-pink-50 px-3.5 py-2 font-mono text-base font-black tracking-[0.1em] text-pink-800 select-all">
                  {{ coupon.code }}
                </div>
              </div>
            </div>
          </div>

          <!-- Coupon Name & Description -->
          <div class="space-y-2">
            <h4 class="font-serif text-xl font-bold text-[#17251d]">
              {{ coupon.name || 'Mã giảm giá hấp dẫn TBS Flora' }}
            </h4>
            <p class="text-sm leading-relaxed text-[#5f6b62]">
              {{ coupon.description || 'Áp dụng cho mọi đơn hàng hoa tươi đủ điều kiện trên toàn hệ thống TBS Flora Store.' }}
            </p>
          </div>

          <!-- Details & Conditions Table -->
          <div class="border border-[#eaded2]">
            <div class="bg-[#fcf7f2] px-4 py-2.5 border-b border-[#eaded2]">
              <span class="text-xs font-bold uppercase tracking-[0.18em] text-[#17251d]">
                Điều Kiện & Chi Tiết Áp Dụng
              </span>
            </div>
            <div class="divide-y divide-[#eaded2] text-sm">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 gap-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-[#7b8a80]">Giá trị đơn tối thiểu</span>
                <span class="font-bold text-[#17251d]">
                  {{ coupon.min_order_amount ? formatPlainVND(coupon.min_order_amount) : 'Không giới hạn (Mọi giá trị đơn)' }}
                </span>
              </div>

              <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 gap-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-[#7b8a80]">Mức giảm tối đa</span>
                <span class="font-bold text-[#17251d]">
                  {{ coupon.max_discount_amount ? formatPlainVND(coupon.max_discount_amount) : 'Theo giá trị giảm trực tiếp' }}
                </span>
              </div>

              <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 gap-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-[#7b8a80]">Thời hạn áp dụng</span>
                <span class="font-bold text-[#17251d]">
                  {{ formatFullCouponDateTime(coupon.expires_at) }}
                </span>
              </div>

              <div v-if="coupon.usage_limit" class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 gap-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-[#7b8a80]">Lượt sử dụng</span>
                <span class="font-bold text-[#17251d]">
                  Đã dùng {{ coupon.used_count || 0 }} / {{ coupon.usage_limit }} lượt
                </span>
              </div>

              <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 gap-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-[#7b8a80]">Phạm vi áp dụng</span>
                <span class="font-bold text-[#17251d]">
                  Đặt hoa online qua website TBS Flora
                </span>
              </div>
            </div>
          </div>

          <!-- Usage Instructions -->
          <div class="border-l-2 border-pink-500 bg-[#fffaf6] p-4 text-xs leading-relaxed text-[#5f6b62]">
            <p class="font-bold text-[#17251d] mb-1">Cách sử dụng mã giảm giá:</p>
            <p>1. Nhấn nút <strong>"Sao chép mã"</strong> bên dưới.</p>
            <p>2. Chọn hoa tươi yêu thích và tiến hành thanh toán tại Giỏ hàng.</p>
            <p>3. Dán mã giảm giá vào ô <strong>"Mã giảm giá / Voucher"</strong> tại bước Thanh toán để nhận ưu đãi.</p>
          </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="border-t border-[#eaded2] bg-[#fffaf6] p-4 sm:p-5 flex flex-col sm:flex-row gap-3 sm:justify-end">
          <button
            @click="closeModal"
            type="button"
            class="inline-flex min-h-11 items-center justify-center border border-[#eaded2] bg-white px-5 py-2.5 text-xs font-bold uppercase tracking-[0.16em] text-[#5f6b62] transition hover:bg-gray-100 hover:text-black"
          >
            Đóng
          </button>

          <button
            @click="copyCode"
            type="button"
            :disabled="!canUseCoupon(coupon)"
            :class="[
              'inline-flex min-h-11 items-center justify-center gap-2 border px-5 py-2.5 text-xs font-bold uppercase tracking-[0.16em] transition',
              !canUseCoupon(coupon)
                ? 'cursor-not-allowed bg-gray-200 text-gray-500 border-gray-300'
                : isCopied
                  ? 'bg-emerald-700 text-white border-emerald-700'
                  : 'border-pink-600 bg-white text-pink-700 hover:bg-pink-50'
            ]"
          >
            <i :class="isCopied ? 'fa-solid fa-check' : 'fa-regular fa-copy'" class="text-sm"></i>
            <span>{{ isCopied ? 'ĐÃ SAO CHÉP' : 'SAO CHÉP MÃ' }}</span>
          </button>

          <button
            v-if="canUseCoupon(coupon)"
            @click="useCouponNow"
            type="button"
            class="inline-flex min-h-11 items-center justify-center gap-2 bg-pink-700 px-6 py-2.5 text-xs font-black uppercase tracking-[0.16em] text-white transition hover:bg-pink-800"
          >
            <i class="fa-solid fa-bag-shopping text-sm"></i>
            <span>DÙNG MÃ NGAY</span>
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.25s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
