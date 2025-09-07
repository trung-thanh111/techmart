<template>
  <div class="relative rounded-lg overflow-hidden mx-auto mb-5">
    <img src="/client/images/slide/banner-category-1.jpg" alt="Cover" class="w-full h-48 md:h-64 object-cover blur-[0.5px]" loading="lazy" />
    <div class="absolute bottom-0 left-0 w-full flex items-center px-4 md:px-6 pb-3 md:pb-4 bg-[#eef2f6] shadow-md">
      <div class="relative">
        <img :src="user?.image?.url || '/shared/images/defaults/avatar.jpg'" :alt="'Avatar ' + user?.name" class="w-16 h-16 md:w-20 md:h-20 rounded-full border-2 md:border-4 border-sky-500 -mt-12 md:-mt-15 shadow-lg" loading="lazy" />
      </div>
      <div class="flex-1 ml-3 md:ml-4 -mt-8 md:-mt-9 h-16 md:h-20">
        <h2 class="text-lg md:text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] my-1">{{ user?.name }}</h2>
        <div class="flex items-center flex-wrap gap-1 md:gap-2">
          <span :class="`inline-flex items-center rounded-md bg-${user?.userCatalogueId === 1 ? 'green' : 'red'}-100 px-2 py-1 text-xs font-medium text-${user?.userCatalogueId === 1 ? 'green' : 'red'}-500 ring-1 ring-inset`">
            <svg class="md:w-3 md:h-3 w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 512 512">
              <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm113-303L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
            </svg>
            {{ user?.userCatalogueId === 1 ? "Khách hàng" : "Quản trị viên" }}
          </span>
          <span class="text-xs text-gray-500">từ ngày</span>
          <a href="#" class="text-sky-500 text-xs hover:underline flex items-center">{{ user?.createdAt }}</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// import { useAuthStore } from "@/store/modules/authStore";
// import { formatDateVN } from "@/utils/formatters";
// import { useUser } from "@/composables/useUser";
// import { watch } from "vue";

// const authStore = useAuthStore();
// const { user, getUser } = useUser(); // ban đầu user là null

// const loadUserData = async (userId) => {
//   if (!userId) return;
//   await getUser(userId); // sau khi getUser thành công, user sẽ có giá trị -> đổ data - biến user
// };

// // Watch authStore.user?.id để tự động load user khi id có giá trị
// watch(
//   () => authStore.user?.id,
//   (id) => {
//     if (id) loadUserData(id);
//   },
//   { immediate: true }
// );
</script>
