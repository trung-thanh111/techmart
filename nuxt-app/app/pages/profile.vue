<template>
  <div class="main-content max-w-7xl mx-auto">
    <Breadcrumb
      :crumbs="[
        { label: 'Quản lý tài khoản', href: '/profile' },
        { label: 'Thông tin', href: '' },
      ]"
    />
    <ProfileHeader />
    <section class="flex bg-white shadow-md rounded-lg">
      <Sidebar />

      <main class="flex-1 p-2 md:p-4 lg:p-8 mx-auto">
        <div class="max-w-4xl">
          <h1 class="text-xl md:text-2xl font-semibold text-gray-900 mb-1">Thông tin tài khoản</h1>
          <p class="text-sm text-gray-500 mb-5 md:mb-10">Cập nhật hồ sơ và chi tiết cá nhân của bạn tại đây</p>

          <form @submit.prevent="" class="md:mb-10 mb-5 space-y-4 md:space-y-6" enctype="multipart/form-data">
            <h2 class="text-sm font-semibold text-gray-700">Hồ sơ</h2>
            <div class="flex justify-end items-center gap-4 mb:mb-6 mb-3">
              <div class="flex flex-col items-end">
                <input type="file" ref="fileInput" @change="handleFileChange" class="hidden" />
                <button type="button" @click="$refs.fileInput.click()" class="flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">⬆️ Tải ảnh lên</button>
                <p class="text-xs text-gray-500 mt-1 text-right">Ảnh nên có kích thước tối thiểu 512×512.</p>
              </div>
              <img :src="'/shared/images/defaults/avatar.jpg'" :alt="'Avatar ' + payload?.name" class="w-16 h-16 rounded-full object-cover" />
            </div>

            <!-- Họ tên -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-700 mb-1">Họ</label>
                <input v-model="payload.firstName" type="text" placeholder="Nguyễn" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              </div>
              <div>
                <label class="block text-sm text-gray-700 mb-1">Tên</label>
                <input v-model="payload.lastName" type="text" placeholder="Văn A" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              </div>
            </div>

            <div>
              <label class="block text-sm text-gray-700 mb-1">Tên hiển thị</label>
              <input v-model="payload.name" type="text" placeholder="Nguyễn A" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            </div>

            <div>
              <label class="block text-sm text-gray-700 mb-1">Email</label>
              <input v-model="payload.email" type="email" placeholder="example@email.com" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:border-sky-500 bg-gray-50" readonly />
              <p class="text-xs text-gray-500 mt-1">Hãy sử dụng email chính xác để nhận thông báo đơn hàng.</p>
            </div>

            <div>
              <label class="block text-sm text-gray-700 mb-1">Số điện thoại</label>
              <input v-model="payload.phone" type="tel" placeholder="(+84) 123 456 789" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            </div>

            <!-- Giới tính & Ngày sinh -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-700 mb-1">Giới tính</label>
                <select v-model="payload.gender" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:border-sky-500">
                  <option value="">Chọn giới tính</option>
                  <option value="male">Nam</option>
                  <option value="female">Nữ</option>
                  <option value="other">Khác</option>
                </select>
              </div>
              <div>
                <label class="block text-sm text-gray-700 mb-1">Ngày sinh</label>
                <input v-model="payload.birthday" type="date" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              </div>
            </div>

            <div>
              <label class="block text-sm text-gray-700 mb-1">Địa chỉ mặc định</label>
              <input v-model="payload.address" type="text" placeholder="123 Đường ABC, Quận 1, TP.HCM" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            </div>

            <div class="mt-6 flex justify-end">
              <button type="submit" class="md:px-4 md:py-2 px-3 py-1.5 md:text-base text-sm bg-sky-600 text-white rounded-md hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:opacity-50">Xác nhận</button>
            </div>
          </form>
        </div>
      </main>
    </section>
  </div>
</template>

<script setup>
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";
import Sidebar from "~/components/client/navigation/SidebarProfile.vue";
import ProfileHeader from "~/components/client/navigation/ProfileHeader.vue";
import ButtonViewMore from "~/components/client/ui/Button.vue";

const payload = {
  firstName: "",
  lastName: "",
  name: "",
  email: "",
  phone: "",
  gender: "",
  birthday: "",
  address: "",
};

const handleFileChange = () => {};
</script>

<style scoped></style>
