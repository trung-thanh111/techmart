<template>
  <div>
    <div id="modalCreateAddress" data-modal-id="modalCreateAddress" class="modal fixed inset-0 z-50 bg-black/20 backdrop-blur-sm hidden">
      <div class="w-full h-full flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Thêm địa chỉ mới</h3>
            <button class="btn-close-modal w-4 h-4 text-gray-500 hover:text-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 384 512">
                <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
              </svg>
            </button>
          </div>
          <form class="space-y-4" @submit.prevent="$emit('create')">
            <div>
              <label for="full_name_new" class="block text-sm font-medium text-gray-700 mb-2">
                Họ và tên
                <span class="text-red-500">*</span>
              </label>
              <input id="full_name_new" name="full_name" type="text" required placeholder="Nguyễn Văn A" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4 gap-2">
              <div>
                <label for="email_new" class="block text-sm font-medium text-gray-700 mb-2">
                  Email
                  <span class="text-red-500">*</span>
                </label>
                <input id="email_new" name="email" type="email" required placeholder="email@domain.com" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              </div>
              <div>
                <label for="phone_new" class="block text-sm font-medium text-gray-700 mb-2">
                  Số điện thoại
                  <span class="text-red-500">*</span>
                </label>
                <input id="phone_new" name="phone" type="tel" required placeholder="0123 456 789" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              </div>
            </div>
            <div>
              <label for="address_new" class="block text-sm font-medium text-gray-700 mb-2">
                Địa chỉ nhận hàng
                <span class="text-red-500">*</span>
              </label>
              <textarea id="address_new" name="address" rows="2" required placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500"></textarea>
            </div>
            <div>
              <label for="note_new" class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
              <textarea id="note_new" name="note" type="text" rows="3" placeholder="Ví dụ: Giao giờ hành chính" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            </div>
            <div class="flex justify-end gap-2">
              <button type="button" class="btn-close-modal text-sm font-semibold px-4 py-2 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-600">Hủy</button>
              <button type="submit" class="text-sm font-semibold py-2 px-4 border border-transparent rounded shadow-sm text-white bg-teal-500 hover:bg-teal-600">Thêm mới</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Update Address Modal -->
    <div id="modalUpdateAddress" data-modal-id="modalUpdateAddress" class="modal fixed inset-0 z-50 bg-black/20 backdrop-blur-sm hidden">
      <div class="w-full h-full flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Cập nhật địa chỉ</h3>
            <button class="btn-close-modal w-4 h-4 text-gray-500 hover:text-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 384 512">
                <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
              </svg>
            </button>
          </div>
          <form class="space-y-4" @submit.prevent="$emit('update')">
            <div>
              <label for="full_name_update" class="block text-sm font-medium text-gray-700 mb-2">
                Họ và tên
                <span class="text-red-500">*</span>
              </label>
              <input id="full_name_update" name="full_name" type="text" required placeholder="Nguyễn Văn A" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4 gap-2">
              <div>
                <label for="email_update" class="block text-sm font-medium text-gray-700 mb-2">
                  Email
                  <span class="text-red-500">*</span>
                </label>
                <input id="email_update" name="email" type="email" required placeholder="email@domain.com" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              </div>
              <div>
                <label for="phone_update" class="block text-sm font-medium text-gray-700 mb-2">
                  Số điện thoại
                  <span class="text-red-500">*</span>
                </label>
                <input id="phone_update" name="phone" type="tel" required placeholder="0123 456 789" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              </div>
            </div>
            <div>
              <label for="address_update" class="block text-sm font-medium text-gray-700 mb-2">
                Địa chỉ nhận hàng
                <span class="text-red-500">*</span>
              </label>
              <textarea id="address_update" name="address" rows="2" required placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500"></textarea>
            </div>
            <div>
              <label for="note_update" class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
              <textarea id="note_update" name="note" type="text" rows="3" placeholder="Ví dụ: Giao giờ hành chính" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            </div>
            <div class="flex justify-end gap-2">
              <button type="button" class="btn-close-modal text-sm font-semibold px-4 py-2 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-600">Hủy</button>
              <button type="submit" class="text-sm font-semibold py-2 px-4 border border-transparent rounded shadow-sm text-white bg-teal-500 hover:bg-teal-600">Cập nhật</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Address Modal -->
    <div id="modalDeleteAddress" data-modal-id="modalDeleteAddress" class="modal fixed inset-0 z-50 bg-black/20 backdrop-blur-sm hidden">
      <div class="w-full h-full flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
          <div class="flex justify-end items-center mb-4">
            <button class="btn-close-modal w-4 h-4 text-gray-500 hover:text-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 384 512">
                <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
              </svg>
            </button>
          </div>
          <button type="button" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
          <div class="flex justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 3h6a2 2 0 012 2v0H7v0a2 2 0 012-2z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-center mb-2 text-gray-800">Bạn có chắc chắn?</h3>
          <p class="text-sm text-gray-500 text-center mb-6">Bạn có chắc muốn xoá bản ghi đang chọn này không?</p>
          <div class="flex justify-center gap-2">
            <button type="button" class="btn-close-modal text-sm font-semibold px-4 py-2 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-600">Hủy</button>
            <button type="button" class="text-white bg-red-500 hover:bg-red-600 text-sm font-semibold py-2 px-4 border border-transparent rounded shadow-sm" @click="$emit('delete')">Xác nhận</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Props can be added later for passing data to modals (e.g., address to update)
defineEmits<{
  (e: "create"): void;
  (e: "update"): void;
  (e: "delete"): void;
}>();
</script>
