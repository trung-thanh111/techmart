<!-- <template>
  <div class="main-content rounded-lg flex items-center justify-center">
    <div class="relative w-full h-[400px] sm:h-[500px] md:h-[600px] lg:min-h-screen">
      <img :src="bgImage" alt="" loading="lazy" class="absolute inset-0 w-full h-full object-cover" />
      <div class="absolute inset-0 flex flex-col justify-center items-center px-2">
        <img :src="errorImage" alt="Error" loading="lazy" class="object-contain mb-2 max-w-[250px] md:max-w-[350px] lg:max-w-[500px] w-full" />
        <div class="text-center mb-4">
          <h1 class="text-2xl font-bold text-red-500 mb-2">{{ error?.statusCode || 'Error' }}</h1>
          <p class="text-gray-600">{{ error?.message || 'Có lỗi xảy ra' }}</p>
        </div>
        <div class="flex gap-2">
          <button 
            @click="handleError"
            class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white font-normal px-4 py-1.5 rounded transition-colors text-xs md:text-sm"
          >
            Thử lại
          </button>
          <NuxtLink to="/" class="inline-flex items-center gap-2 bg-teal-500 hover:bg-teal-600 text-white font-normal px-4 py-1.5 rounded transition-colors text-xs md:text-sm">
            Về trang chủ
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import bgImage from "/client/images/bg-auth.png";
import errorImage from "/client/images/404-error.png";

defineProps({
  error: Object
})

const handleError = () => {
  clearError({ redirect: '/' })
}
</script>  -->