<template>
  <div class="relative w-full" ref="containerRef">
    <!-- Display Input Trigger -->
    <div
      @click="toggleDropdown"
      :class="[
        isDark
          ? 'bg-[#0f172a] border-gray-600 text-white hover:border-gray-500'
          : 'bg-white border-gray-300 text-gray-900 hover:border-gray-400',
        isOpen ? 'ring-4 ring-blue-500/20 border-blue-500' : '',
        disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
      ]"
      class="w-full flex items-center justify-between px-3 py-2 border rounded-lg text-sm transition-all select-none"
    >
      <div class="flex items-center space-x-2.5 overflow-hidden">
        <i class="fa-regular fa-calendar-days text-blue-500 text-base shrink-0"></i>
        <span v-if="displayValue" class="font-medium text-sm truncate">
          {{ displayValue }}
        </span>
        <span v-else :class="isDark ? 'text-gray-500' : 'text-gray-400'" class="truncate">
          {{ placeholder || 'Chọn thời gian' }}
        </span>
      </div>

      <div class="flex items-center space-x-1.5 shrink-0 ml-2">
        <button
          v-if="modelValue && !disabled"
          @click.stop="clearValue"
          type="button"
          class="p-1 rounded-full text-gray-400 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
          title="Xóa lựa chọn"
        >
          <i class="fa-solid fa-xmark text-xs"></i>
        </button>
        <i
          class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
          :class="isOpen ? 'transform rotate-180 text-blue-500' : ''"
        ></i>
      </div>
    </div>

    <!-- Calendar & Time Picker Dropdown -->
    <transition
      enter-active-class="transition duration-150 ease-out"
      :enter-from-class="isDropUp ? 'transform scale-95 opacity-0 translate-y-1' : 'transform scale-95 opacity-0 -translate-y-1'"
      enter-to-class="transform scale-100 opacity-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="transform scale-100 opacity-100 translate-y-0"
      :leave-to-class="isDropUp ? 'transform scale-95 opacity-0 translate-y-1' : 'transform scale-95 opacity-0 -translate-y-1'"
    >
      <div
        v-if="isOpen"
        :class="[
          isDark
            ? 'bg-slate-900 border-gray-700 text-white shadow-2xl shadow-black/80'
            : 'bg-white border-gray-200 text-gray-900 shadow-2xl shadow-gray-500/20',
          isDropUp ? 'bottom-full mb-2' : 'top-full mt-2',
          isAlignRight ? 'right-0' : 'left-0'
        ]"
        class="absolute z-50 w-72 sm:w-80 p-3.5 border rounded-xl"
      >
        <!-- Calendar Header (Month / Year Navigation) -->
        <div class="flex items-center justify-between mb-3 pb-2 border-b" :class="isDark ? 'border-gray-800' : 'border-gray-100'">
          <button
            @click.stop="prevMonth"
            type="button"
            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400 transition-colors"
            title="Tháng trước"
          >
            <i class="fa-solid fa-chevron-left text-xs"></i>
          </button>

          <span class="text-xs font-bold uppercase tracking-wider">
            Tháng {{ currentMonth + 1 }}, {{ currentYear }}
          </span>

          <button
            @click.stop="nextMonth"
            type="button"
            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400 transition-colors"
            title="Tháng sau"
          >
            <i class="fa-solid fa-chevron-right text-xs"></i>
          </button>
        </div>

        <!-- Day of Week Headers -->
        <div class="grid grid-cols-7 gap-1 text-center mb-1">
          <span
            v-for="d in ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']"
            :key="d"
            class="text-[11px] font-semibold text-gray-400 uppercase py-1"
          >
            {{ d }}
          </span>
        </div>

        <!-- Calendar Days Grid -->
        <div class="grid grid-cols-7 gap-1 text-center mb-3">
          <button
            v-for="(day, idx) in calendarDays"
            :key="idx"
            type="button"
            @click.stop="selectDate(day)"
            :disabled="day.disabled"
            :class="[
              day.isCurrentMonth
                ? isDark
                  ? 'text-gray-200 hover:bg-gray-800'
                  : 'text-gray-800 hover:bg-gray-100'
                : 'text-gray-400/30 pointer-events-none',
              day.isSelected
                ? '!bg-blue-600 !text-white font-bold shadow-md shadow-blue-500/30'
                : '',
              day.isToday && !day.isSelected
                ? 'border border-blue-500 font-bold text-blue-500'
                : ''
            ]"
            class="h-8 w-8 mx-auto flex items-center justify-center rounded-lg text-xs transition-colors"
          >
            {{ day.date }}
          </button>
        </div>

        <!-- Time Picker Section -->
        <div
          class="pt-3 border-t flex items-center justify-between gap-2"
          :class="isDark ? 'border-gray-800' : 'border-gray-100'"
        >
          <div class="flex items-center space-x-1.5 text-xs text-gray-400">
            <i class="fa-regular fa-clock text-blue-500"></i>
            <span class="font-medium">Giờ:</span>
          </div>

          <div class="flex items-center space-x-1.5">
            <!-- Hours Select -->
            <select
              v-model="selectedHour"
              @change="updateTime"
              :class="isDark ? 'bg-gray-800 border-gray-700 text-white' : 'bg-gray-50 border-gray-200 text-gray-800'"
              class="px-2 py-1 text-xs border rounded-md font-mono focus:outline-none focus:border-blue-500 cursor-pointer"
            >
              <option v-for="h in 24" :key="h - 1" :value="padZero(h - 1)">
                {{ padZero(h - 1) }}
              </option>
            </select>

            <span class="font-bold text-gray-400">:</span>

            <!-- Minutes Select -->
            <select
              v-model="selectedMinute"
              @change="updateTime"
              :class="isDark ? 'bg-gray-800 border-gray-700 text-white' : 'bg-gray-50 border-gray-200 text-gray-800'"
              class="px-2 py-1 text-xs border rounded-md font-mono focus:outline-none focus:border-blue-500 cursor-pointer"
            >
              <option v-for="m in 60" :key="m - 1" :value="padZero(m - 1)">
                {{ padZero(m - 1) }}
              </option>
            </select>
          </div>
        </div>

        <!-- Bottom Actions -->
        <div class="mt-3 pt-2.5 border-t flex items-center justify-between" :class="isDark ? 'border-gray-800' : 'border-gray-100'">
          <button
            type="button"
            @click.stop="setNow"
            class="text-xs text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 font-semibold"
          >
            Bây giờ
          </button>

          <button
            type="button"
            @click.stop="isOpen = false"
            class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md shadow-sm transition-colors"
          >
            Xong
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Bắt đầu'
  },
  isDark: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  position: {
    type: String,
    default: 'auto' // 'auto' | 'top' | 'bottom'
  },
  align: {
    type: String,
    default: 'auto' // 'auto' | 'left' | 'right'
  }
});

const emit = defineEmits(['update:modelValue']);

const containerRef = ref(null);
const isOpen = ref(false);

const detectedDropUp = ref(false);
const detectedAlignRight = ref(false);

const isDropUp = computed(() => {
  if (props.position === 'top') return true;
  if (props.position === 'bottom') return false;
  return detectedDropUp.value;
});

const isAlignRight = computed(() => {
  if (props.align === 'right') return true;
  if (props.align === 'left') return false;
  return detectedAlignRight.value;
});

const today = new Date();
const currentYear = ref(today.getFullYear());
const currentMonth = ref(today.getMonth());
const selectedYear = ref(today.getFullYear());
const selectedMonth = ref(today.getMonth());
const selectedDay = ref(today.getDate());
const selectedHour = ref('00');
const selectedMinute = ref('00');

const padZero = (n) => String(n).padStart(2, '0');

// Parse modelValue into state
const parseModelValue = (val) => {
  if (!val) {
    const now = new Date();
    selectedHour.value = padZero(now.getHours());
    selectedMinute.value = padZero(now.getMinutes());
    return;
  }
  const d = new Date(val);
  if (!isNaN(d.getTime())) {
    currentYear.value = d.getFullYear();
    currentMonth.value = d.getMonth();
    selectedYear.value = d.getFullYear();
    selectedMonth.value = d.getMonth();
    selectedDay.value = d.getDate();
    selectedHour.value = padZero(d.getHours());
    selectedMinute.value = padZero(d.getMinutes());
  }
};

watch(
  () => props.modelValue,
  (newVal) => {
    parseModelValue(newVal);
  },
  { immediate: true }
);

// Formatted display value
const displayValue = computed(() => {
  if (!props.modelValue) return '';
  const d = new Date(props.modelValue);
  if (isNaN(d.getTime())) return props.modelValue;
  const day = padZero(d.getDate());
  const month = padZero(d.getMonth() + 1);
  const year = d.getFullYear();
  const hours = padZero(d.getHours());
  const minutes = padZero(d.getMinutes());
  return `${hours}:${minutes} - ${day}/${month}/${year}`;
});

// Generate calendar grid
const calendarDays = computed(() => {
  const year = currentYear.value;
  const month = currentMonth.value;

  const firstDayOfMonth = new Date(year, month, 1).getDay(); // 0 is Sunday
  // Convert Sunday (0) to 7 for Monday-based week
  const startDay = firstDayOfMonth === 0 ? 6 : firstDayOfMonth - 1;

  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const daysInPrevMonth = new Date(year, month, 0).getDate();

  const days = [];

  // Trailing days from previous month
  for (let i = startDay - 1; i >= 0; i--) {
    days.push({
      date: daysInPrevMonth - i,
      month: month - 1,
      year: month === 0 ? year - 1 : year,
      isCurrentMonth: false,
      isSelected: false,
      isToday: false
    });
  }

  // Days in current month
  const now = new Date();
  for (let i = 1; i <= daysInMonth; i++) {
    const isToday =
      now.getFullYear() === year && now.getMonth() === month && now.getDate() === i;
    const isSelected =
      !!props.modelValue &&
      selectedYear.value === year &&
      selectedMonth.value === month &&
      selectedDay.value === i;

    days.push({
      date: i,
      month,
      year,
      isCurrentMonth: true,
      isSelected,
      isToday
    });
  }

  // Pad to end of the week
  const totalDaysSoFar = days.length;
  const remainder = totalDaysSoFar % 7;
  const daysToAdd = remainder === 0 ? 0 : 7 - remainder;
  for (let i = 1; i <= daysToAdd; i++) {
    days.push({
      date: i,
      month: month + 1,
      year: month === 11 ? year + 1 : year,
      isCurrentMonth: false,
      isSelected: false,
      isToday: false
    });
  }

  return days;
});

const prevMonth = () => {
  if (currentMonth.value === 0) {
    currentMonth.value = 11;
    currentYear.value--;
  } else {
    currentMonth.value--;
  }
};

const nextMonth = () => {
  if (currentMonth.value === 11) {
    currentMonth.value = 0;
    currentYear.value++;
  } else {
    currentMonth.value++;
  }
};

const emitUpdate = () => {
  const y = selectedYear.value;
  const m = padZero(selectedMonth.value + 1);
  const d = padZero(selectedDay.value);
  const hh = selectedHour.value;
  const mm = selectedMinute.value;
  emit('update:modelValue', `${y}-${m}-${d}T${hh}:${mm}`);
};

const selectDate = (day) => {
  selectedYear.value = day.year;
  selectedMonth.value = day.month;
  selectedDay.value = day.date;
  emitUpdate();
};

const updateTime = () => {
  if (!props.modelValue) {
    selectedYear.value = currentYear.value;
    selectedMonth.value = currentMonth.value;
    selectedDay.value = today.getDate();
  }
  emitUpdate();
};

const setNow = () => {
  const now = new Date();
  currentYear.value = now.getFullYear();
  currentMonth.value = now.getMonth();
  selectedYear.value = now.getFullYear();
  selectedMonth.value = now.getMonth();
  selectedDay.value = now.getDate();
  selectedHour.value = padZero(now.getHours());
  selectedMinute.value = padZero(now.getMinutes());
  emitUpdate();
};

const clearValue = () => {
  emit('update:modelValue', '');
};

const toggleDropdown = () => {
  if (props.disabled) return;
  if (!isOpen.value && containerRef.value) {
    const rect = containerRef.value.getBoundingClientRect();
    const spaceBelow = window.innerHeight - rect.bottom;
    
    // Auto detect: If space below is limited, open upward
    detectedDropUp.value = spaceBelow < 360;
    detectedAlignRight.value = window.innerWidth - rect.left < 330 || rect.left > window.innerWidth / 2;
  }
  isOpen.value = !isOpen.value;
};

// Click outside handler
const handleClickOutside = (event) => {
  if (containerRef.value && !containerRef.value.contains(event.target)) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
