<template>
  <div class="min-h-screen bg-gradient-to-br from-rose-50 via-white to-pink-50 flex items-center justify-center px-4 py-12">
    <div class="max-w-2xl w-full text-center">
      <div class="mb-8">
        <h1 class="text-8xl md:text-9xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-rose-500 leading-none">
          404
        </h1>
        <div class="mt-4 inline-flex items-center justify-center w-20 h-20 rounded-full bg-pink-100 text-pink-500">
          <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 8v5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            <path d="M12 16h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
          </svg>
        </div>
      </div>

      <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
        Không tìm thấy trang
      </h2>
      <p class="text-gray-600 text-lg mb-8">
        Đường dẫn bạn vừa nhập không tồn tại hoặc đã được thay đổi. Vui lòng quay lại trang chủ để tiếp tục mua sắm.
      </p>

      <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <RouterLink
          to="/"
          class="w-full sm:w-auto inline-flex items-center justify-center rounded-full bg-pink-500 px-8 py-3 text-white font-semibold shadow-lg shadow-pink-200 transition hover:bg-pink-600"
        >
          Về trang chủ
        </RouterLink>
        <RouterLink
          to="/product"
          class="w-full sm:w-auto inline-flex items-center justify-center rounded-full border border-pink-200 bg-white px-8 py-3 text-pink-600 font-semibold transition hover:bg-pink-50"
        >
          Xem sản phẩm
        </RouterLink>
      </div>
    </div>
  </div>
</template>
