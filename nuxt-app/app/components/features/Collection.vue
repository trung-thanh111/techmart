<template>
  <section class="bg-white shadow-md rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4">
    <div class="mb-4 md:mb-8">
      <div class="flex justify-between items-center space-x-2">
        <div class="head-section-product">
          <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-xl md:text-3xl font-bold">{{ $t("home.collection") }}</h2>
          <div class="h-1 max-w-48 my-1 md:my-2 relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
          </div>
        </div>
        <div class="group flex items-center space-x-2">
          <a href="#" class="relative inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00b88c] font-medium text-sm md:text-base after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-gradient-to-r after:from-[#007c8b] after:to-[#00b88c] after:transition-all after:duration-300 group-hover:after:w-full">{{ $t("home.discoverNow") }}</a>
        </div>
      </div>
      <p class="text-gray-600 md:mt-2 mt-1 max-w-xl md:text-sm text-xs">{{ $t("home.collectionDesc") }}</p>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="grid gap-4">
        <a href="#" class="block">
          <NuxtImg class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image.jpg" alt="Collection image 1" loading="lazy" />
        </a>
        <a href="#" class="block">
          <NuxtImg class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-1.jpg" alt="Collection image 2" loading="lazy" />
        </a>
        <a href="#" class="block">
          <NuxtImg class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-2.jpg" alt="Collection image 3" loading="lazy" />
        </a>
      </div>
      <div class="grid gap-4">
        <a href="#" class="block">
          <NuxtImg class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-3.jpg" alt="Collection image 4" loading="lazy" />
        </a>
        <a href="#" class="block">
          <NuxtImg class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-4.jpg" alt="Collection image 5" loading="lazy" />
        </a>
        <a href="#" class="block">
          <NuxtImg class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-5.jpg" alt="Collection image 6" loading="lazy" />
        </a>
      </div>
      <div class="grid gap-4">
        <a href="#" class="block">
          <NuxtImg class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-6.jpg" alt="Collection image 7" loading="lazy" />
        </a>
        <a href="#" class="block">
          <NuxtImg class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-7.jpg" alt="Collection image 8" loading="lazy" />
        </a>
        <a href="#" class="block">
          <NuxtImg class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-8.jpg" alt="Collection image 9" loading="lazy" />
        </a>
      </div>
      <div class="grid gap-4">
        <a href="#" class="block">
          <NuxtImg class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-9.jpg" alt="Collection image 10" loading="lazy" />
        </a>
        <a href="#" class="block">
          <NuxtImg class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-10.jpg" alt="Collection image 11" loading="lazy" />
        </a>
        <a href="#" class="block">
          <NuxtImg class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-11.jpg" alt="Collection image 12" loading="lazy" />
        </a>
      </div>
    </div>
  </section>
</template>

<script setup></script>
