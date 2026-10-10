<template>
  <div class="main-content max-w-7xl mx-auto">
    <DataState :loading="productDetailLoading" :error="productDetailError" :data="productDetail" :show-empty-image="false">
      <template v-if="productDetail">
        <Breadcrumb :crumbs="breadcrumbs" />
        <section class="bg-white shadow-md rounded-md">
          <div class="container">
            <div class="pb-3 xl:flex xl:pb-4 xl:space-x-2">
              <div class="flex-1 bg-white border-b lg:flex xl:border-b-0">
                <div class="slide lg:w-[392px] relative">
                  <div class="sticky-product">
                    <div class="gallery-product pb-0 pt-4 md:py-4 px-3 xl:px-0 xl:pl-4">
                      <div class="relative">
                        <div class="relative mb-4">
                          <div v-if="discountPercent > 0" class="absolute top-2 left-2 z-10 bg-red-500 text-white font-bold text-xs md:text-sm px-2 py-1 rounded-md shadow-lg">-{{ discountPercent }}%</div>
                          <div class="absolute top-2 right-2 z-10 flex flex-col items-center gap-2">
                            <div v-tooltip.top="$t('header.compareTooltip')">
                              <Checkbox v-model="isCompareChecked" inputId="compare-checkbox" :binary="true" class="compare-checkbox-large" />
                            </div>
                            <button @click="handleWishlistToggle" :data-product-variant-id="selectedVariant?.id" class="p-2 rounded-full bg-white/80 hover:bg-red-100 transition-colors shadow-md relative group/heart">
                              <svg v-if="isCurrentVariantInWishlist" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-4 h-4 transition-all fill-red-500 group-hover/heart:opacity-0 group-hover/heart:scale-0">
                                <path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z" />
                              </svg>
                              <i v-if="isCurrentVariantInWishlist" class="fa-solid fa-heart-crack w-4 h-4 text-red-500 absolute inset-0 m-auto transition-all opacity-0 scale-0 group-hover/heart:opacity-100 group-hover/heart:scale-100"></i>
                              <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-4 h-4 transition-colors fill-gray-400 hover:fill-red-500">
                                <path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8l0-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5l0 3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20-.1-.1s0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5l0 3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2l0-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z" />
                              </svg>
                            </button>
                          </div>

                          <Splide ref="mainSliderRef" :options="productImageMainOptions" class="mb-4">
                            <SplideSlide v-for="(image, index) in images" :key="index">
                              <div class="cursor-pointer" @click="showLightbox(index)">
                                <NuxtImg :src="image" :alt="productDetail.name" class="w-full h-auto max-h-[400px] md:max-h-[500px] lg:max-h-[600px] aspect-[4/5] object-contain rounded mx-auto" loading="lazy" @error="handleImageError" />
                              </div>
                            </SplideSlide>
                          </Splide>

                          <VueEasyLightbox :visible="lightboxVisible" :imgs="images" :index="lightboxIndex" @hide="lightboxVisible = false" />
                        </div>

                        <Splide ref="thumbSliderRef" :options="productImageThumbnailOptions" class="product-thumbnail-slider">
                          <SplideSlide v-for="(image, index) in images" :key="index">
                            <NuxtImg :src="image" :alt="`${productDetail.name} - ${index + 1}`" class="object-contain w-full h-full aspect-square rounded cursor-pointer border border-gray-300 hover:border-blue-500" loading="lazy" @error="handleImageError" />
                          </SplideSlide>
                        </Splide>
                      </div>
                    </div>

                    <div class="lg:mb-3 p-3">
                      <div class="flex items-center md:justify-between justify-end">
                        <div class="flex items-center justify-between">
                          <span class="caption font-medium text-sm text-gray-500 mr-[18px] md:block hidden">{{ $t("action.share") }}</span>
                          <div class="flex items-center space-x-[12px]">
                            <a aria-label="message-share" class="transition duration-200 ease-in-out hover:opacity-70" href="https://www.facebook.com/dialog/send?app_id=420292729776844&amp;display=popup&amp;link=https://techmart.vn/den-led-ban-hoc-cam-ung-6w-rd-rl-38plus-p-221223003160&amp;redirect_uri=https://techmart.vn/den-led-ban-hoc-cam-ung-6w-rd-rl-38plus-p-221223003160"><NuxtImg src="/images/social/messenger.png" alt="facebook message" class="object-cover w-5 h-5" loading="lazy" /></a>
                            <a aria-label="facebook-share" class="transition duration-200 ease-in-out hover:opacity-70" href="https://www.facebook.com/sharer/sharer.php?u=https://techmart.vn/den-led-ban-hoc-cam-ung-6w-rd-rl-38plus-p-221223003160">
                              <NuxtImg src="/images/social/facebook.png" alt="facebook share" class="object-cover w-5 h-5" loading="lazy" />
                            </a>

                            <a aria-label="printer-share" class="transition duration-200 ease-in-out hover:opacity-70" href="http://pinterest.com/pin/create/button/?url=https://techmart.vn/den-led-ban-hoc-cam-ung-6w-rd-rl-38plus-p-221223003160">
                              <NuxtImg src="/images/social/zalo.png" alt="sharing printer" class="object-cover w-5 h-5" loading="lazy" />
                            </a>
                            <div class="relative">
                              <div class="w-5 h-5 transition duration-200 ease-in-out hover:opacity-70">
                                <span>
                                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="20" fill="none" viewBox="0 0 25 24">
                                    <rect width="24" height="24" x=".5" fill="#D8DCE7" rx="12"></rect>
                                    <path stroke="#545866" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12.156 8.03l1.532-1.532a3.404 3.404 0 1 1 4.814 4.816l-2.188 2.189a3.403 3.403 0 0 1-4.814 0"></path>
                                    <path stroke="#545866" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12.844 15.97l-1.532 1.532a3.406 3.406 0 0 1-4.815 0 3.406 3.406 0 0 1 .001-4.816l2.188-2.189a3.404 3.404 0 0 1 4.814 0"></path>
                                  </svg>
                                </span>
                              </div>
                              <input id="input-copy" type="hidden" />
                              <div class="absolute top-[0%] bg-green-300 text-white duration-300 py-[4px] px-[12px] w-max left-[160%] hidden">{{ $t("common.copiedLink") }}</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="p-3 lg:px-4">
                      <div class="rounded-lg border border-gray-300 overflow-hidden">
                        <h4 class="text-gray-600 body-text font-medium px-4 py-2 bg-gray-200">{{ $t("header.promotionTitle") }}</h4>
                        <div class="max-w-none py-3 px-3 lg:px-4 promotion-content space-y-3">
                          <template v-if="applicablePromotions.length">
                            <div v-for="promotion in applicablePromotions" :key="promotion.id" class="rounded-md border border-sky-100 bg-sky-50/40 p-3 space-y-2">
                              <div class="flex flex-wrap justify-between gap-3">
                                <div>
                                  <p class="text-base font-semibold text-sky-700 mb-1">{{ promotion.name }}</p>
                                  <p class="text-sm text-gray-600">
                                    {{ $t("header.discountText") }} {{ formatPromotionDiscount(promotion) }}
                                    <span v-if="getPromotionMaxDiscount(promotion)"> ({{ $t("header.maxDiscount", { amount: formatCurrency(getPromotionMaxDiscount(promotion)) }) }})</span>
                                  </p>
                                  <p v-if="promotionWindow(promotion)" class="text-xs text-gray-500">{{ $t("header.timeLabel") }} {{ promotionWindow(promotion) }}</p>
                                </div>
                                <span class="inline-flex items-center text-xs font-semibold uppercase tracking-wide bg-sky-500 text-white rounded-full px-3 py-1">{{ $t("common.priority") }} #{{ promotion.priority ?? 0 }}</span>
                              </div>
                              <div v-if="promotion.description" class="text-sm text-gray-600 leading-relaxed prose max-w-none" v-html="promotion.description"></div>
                              <div v-if="promotion.applicableTargets?.length" class="text-xs text-gray-500 flex flex-wrap items-center gap-2">
                                <span class="font-semibold text-gray-600">{{ $t("header.appliesTo") }}:</span>
                                <span v-for="target in promotion.applicableTargets" :key="`${promotion.id}-${target.id || target.productVariantId || target.productId || target.productCatalogueId}`" class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-white text-gray-600 border border-gray-200">
                                  <span v-if="target.isGift" class="text-[10px] font-semibold text-pink-600 uppercase">{{ $t("common.gift") }}</span>
                                  <span>{{ resolvePromotionTargetLabel(target) }}</span>
                                </span>
                              </div>
                            </div>
                          </template>
                          <p v-else class="text-sm text-gray-500 m-0">{{ $t("header.noPromotion") }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="flex-1">
                  <div class="p-2 mb-3 bg-white lg:px-4 lg:pb-1 lg:mb-0">
                    <div class="mb-2 lg:flex lg:flex-row-reverse lg:items-start lg:mb-4">
                      <div class="lg:flex-1 lg:mr-3">
                        <h1 class="mb-1 text-xl leading-[24px] font-medium text-gray-700 uppercase font-display">
                          {{ productDetail.name }}
                          <span v-if="selectedVariant?.name" class="normal-case">| {{ selectedVariant.name }}</span>
                        </h1>
                        <div class="text-sm text-gray-500 my-1">
                          <span>{{ $t("field.category") }}:</span>
                          <NuxtLink v-for="catalogue in productDetail.productCatalogues as any[]" :key="catalogue.id" :to="`${PRODUCTCATEGORY}/${catalogue.slug}`" class="ml-2 font-semibold text-sky-500 hover:underline">
                            {{ catalogue.name }}
                          </NuxtLink>
                          <template v-if="(productDetail as any).brand">
                            &nbsp;|&nbsp;
                            <span>{{ $t("field.brand") }}:</span>
                            <NuxtLink :to="`/brand/${(productDetail as any).brand.slug}`" class="ml-2 font-semibold text-sky-500 hover:underline">{{ (productDetail as any).brand.name }}</NuxtLink>
                          </template>
                        </div>
                        <div v-if="selectedVariant" class="text-xs font-medium text-sky-500 md:text-sm">{{ $t("field.sku") }}: {{ selectedVariant.sku }}</div>
                      </div>
                    </div>
                    <div class="mb-4 lg:rounded lg:bg-gray-100 lg:px-4 lg:py-3">
                      <div>
                        <div class="flex flex-wrap items-center mb-1">
                          <strong class="mr-4 font-bold text-red-500 text-xl">{{ displayPrice }}&nbsp;₫</strong>
                          <span v-if="productDetail.priceOrigin" class="mr-4 text-gray-400 line-through body-text text-sm">{{ new Intl.NumberFormat("vi-VN").format(parseFloat(productDetail.priceOrigin.toString())) }}&nbsp;₫</span>
                        </div>
                      </div>
                      <NuxtLink :to="TOEXCHANGEPOLICY" class="inline-flex items-center transition-colors duration-200 ease-in-out text-sky-500 hover:text-sky-600">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" fill="none" viewBox="0 0 20 21">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15z"></path>
                            <path fill="currentColor" d="M10 15.5a.937.937 0 1 0 0-1.875.937.937 0 0 0 0 1.875z"></path>
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 11.75v-.625a2.187 2.187 0 1 0-2.188-2.188"></path>
                          </svg>
                        </span>
                        <span class="ml-1 font-medium caption text-sm text-sky-500">{{ $t("header.easyExchange") }}</span>
                      </NuxtLink>
                    </div>
                    <div v-if="Object.keys(attributeGroups).length > 0">
                      <div v-for="(attributes, attrName) in attributeGroups" :key="attrName" class="mb-3">
                        <div>
                          <div class="mb-2 caption text-sm">
                            <span class="text-gray-600">{{ attrName }}:</span>
                            <span class="font-medium ml-2 text-xs text-sky-500">{{ attributes.find((a) => a.id === selectedAttributes[attrName])?.name || "" }}</span>
                          </div>
                          <div class="flex flex-wrap gap-2">
                            <label v-for="attr in attributes" :key="attr.id" class="relative cursor-pointer">
                              <input type="radio" :name="`attr-${attrName}`" :value="attr.id" :checked="selectedAttributes[attrName] === attr.id" @change="handleSelectAttribute(attrName, attr.id)" class="sr-only peer" />
                              <div class="flex items-center flex-wrap justify-center px-2 min-w-[80px] h-9 bg-white text-gray-700 font-semibold rounded peer-checked:border-sky-500 peer-checked:bg-sky-50 peer-checked:text-sky-600 hover:bg-sky-100 hover:border-sky-500 border border-gray-300 transition-colors duration-200 text-sm">
                                {{ attr.name }}
                              </div>
                              <div class="rounded-tr-sm absolute top-0 right-0 w-5 h-5 bg-gradient-to-tr from-sky-600 to-cyan-500 peer-checked:flex hidden" style="clip-path: polygon(110% 0, 0 0, 110% 110%)">
                                <svg class="absolute top-[1px] right-[1px] w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                              </div>
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="px-4 pb-3 bg-white">
                    <div class="relative">
                      <div class="flex items-center gap-2 mb-2">
                        <span class="text-gray-600 text-sm">{{ $t("field.quantity") }}</span>
                        <span class="font-medium text-sky-500 text-sm">{{ displayStock }}</span>
                      </div>
                      <div class="flex items-center mt-2 border border-gray-300 rounded overflow-hidden w-max h-[38px]">
                        <button @click="quantity = Math.max(1, quantity - 1)" title="quantity-decrease" class="px-3 flex items-center justify-center text-gray-500 hover:text-gray-700">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 18 18">
                            <path d="M3.75 9H14.25"></path>
                          </svg>
                        </button>
                        <input v-model.number="quantity" type="number" :min="1" :max="displayStock" @input="handleQuantityInput" class="w-12 text-center border-x border-gray-200 text-gray-700 text-sm focus:outline-none" inputmode="numeric" />
                        <button @click="quantity = Math.min(displayStock, quantity + 1)" title="quantity-increase" class="px-3 flex items-center justify-center text-gray-500 hover:text-gray-700">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 18 18">
                            <path d="M9 3.75V14.25"></path>
                            <path d="M3.75 9H14.25"></path>
                          </svg>
                        </button>
                      </div>
                    </div>
                    <div class="grid grid-cols-2 mb-2 gap-x-4 mt-5">
                      <Button :label="$t('cart.addToCart')" icon="pi pi-shopping-cart" size="large" @click="handleAddToCart" :loading="isAddingToCart" class="border-0 !flex items-center justify-center !bg-gradient-to-r !from-sky-500 !to-sky-600 hover:!from-sky-600 hover:!to-sky-700 text-white font-semibold py-4 px-4 rounded-sm transition-all duration-300 ease-in-out transform hover:scale-98 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-1 uppercase text-sm md:text-base leading-none shadow-md hover:shadow-md" />
                      <Button :label="$t('cart.buyNow')" icon="pi pi-bolt" severity="danger" size="large" @click="handleBuyNow" :loading="isBuyingNow" class="flex items-center justify-center bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-semibold py-4 px-4 rounded-sm transition-all duration-300 ease-in-out transform hover:scale-98 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 uppercase text-sm md:text-base leading-none shadow-md hover:shadow-md" />
                    </div>
                    <div class="font-medium text-gray-500 text-xs description">
                      <NuxtLink to="#" class="font-bold underline cursor-pointer text-sky-400 hover:text-sky-600">{{ $t("header.loginRequired") }}</NuxtLink>
                      {{ $t("header.memberBenefits") }}
                    </div>
                  </div>
                  <div class="px-4 md:px-[24px]" v-if="Object.keys(attributeGroups).length > 0">
                    <div class="py-2 md:py-3 border-t border-gray-300">
                      <div class="mb-1 space-y-[4px] text-sm">
                        <div v-for="([attrName, attributes], index) in Object.entries(attributeGroups).slice(0, 2)" :key="attrName">
                          <div class="flex caption text-gray-600">
                            <div class="w-[120px] mr-2 md:mr-3 xl:mr-4">{{ attrName }}</div>
                            <div class="flex-1">{{ attributes.map((a) => a.name).join(", ") }}</div>
                          </div>
                        </div>
                      </div>
                      <div class="btn-md text-strong-blue-400 inline-flex items-center cursor-pointer duration-200 mt-2">
                        <a @click.prevent="scrollToTab('specification')" href="#tabs-information" class="text-xs font-medium flex items-center space-x-[6px] text-sky-500">{{ $t("action.viewDetail") }}</a>
                        <div class="ml-[4px] flex-shrink-0">
                          <svg class="text-sky-500" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                            <g clip-path="url(#clip0_8633_16495)">
                              <path d="M2.54054 0.159839L2.2107 0.487403C2.10769 0.590493 2.05094 0.727648 2.05094 0.874234C2.05094 1.02074 2.10769 1.15806 2.2107 1.26115L5.94748 4.99776L2.20655 8.73869C2.10354 8.84162 2.04687 8.97893 2.04687 9.12544C2.04687 9.27195 2.10354 9.40934 2.20655 9.51235L2.53436 9.84C2.74753 10.0533 3.09477 10.0533 3.30794 9.84L7.77798 5.38598C7.88091 5.28305 7.95343 5.1459 7.95343 4.99809L7.95343 4.99638C7.95343 4.8498 7.88082 4.71264 7.77798 4.60971L3.32006 0.159839C3.21713 0.0567483 3.07583 0.000163033 2.92932 -3.85729e-08C2.78273 -3.21654e-08 2.64338 0.0567484 2.54054 0.159839Z" fill="currentColor"></path>
                            </g>
                            <defs>
                              <clipPath id="clip0_8633_16495">
                                <rect width="10" height="10" fill="white" transform="translate(0 10) rotate(-90)"></rect>
                              </clipPath>
                            </defs>
                          </svg>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-if="productDetail.description" class="px-4 lg:px-[24px]">
                    <div class="py-2 md:py-3 border-t border-gray-300 text-sm">
                      <div class="prose max-w-none caption summary-content line-clamp-5 text-gray-600">
                        <div v-html="productDetail.description"></div>
                      </div>
                      <div class="btn-md text-strong-blue-400 inline-flex items-center cursor-pointer hover:text-strong-blue-500 duration-200 mt-2">
                        <a @click.prevent="scrollToTab('summary')" href="#tabs-information" class="text-xs font-medium flex items-center space-x-[6px] text-sky-500">{{ $t("action.viewDetail") }}</a>
                        <div class="ml-[4px] flex-shrink-0">
                          <svg class="text-sky-500" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                            <g clip-path="url(#clip0_8633_16495)">
                              <path d="M2.54054 0.159839L2.2107 0.487403C2.10769 0.590493 2.05094 0.727648 2.05094 0.874234C2.05094 1.02074 2.10769 1.15806 2.2107 1.26115L5.94748 4.99776L2.20655 8.73869C2.10354 8.84162 2.04687 8.97893 2.04687 9.12544C2.04687 9.27195 2.10354 9.40934 2.20655 9.51235L2.53436 9.84C2.74753 10.0533 3.09477 10.0533 3.30794 9.84L7.77798 5.38598C7.88091 5.28305 7.95343 5.1459 7.95343 4.99809L7.95343 4.99638C7.95343 4.8498 7.88082 4.71264 7.77798 4.60971L3.32006 0.159839C3.21713 0.0567483 3.07583 0.000163033 2.92932 -3.85729e-08C2.78273 -3.21654e-08 2.64338 0.0567484 2.54054 0.159839Z" fill="currentColor"></path>
                            </g>
                            <defs>
                              <clipPath id="clip0_8633_16495">
                                <rect width="10" height="10" fill="white" transform="translate(0 10) rotate(-90)"></rect>
                              </clipPath>
                            </defs>
                          </svg>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="xl:w-[352px] sticky top-[10px] h-full lg:block hidden mr-3">
                <div class="space-y-[8px]">
                  <div class="py-[16px] bg-white rounded-b-[4px]">
                    <div class="relative flex items-center mb-2">
                      <div class="relative flex items-center mb-2">
                        <div class="relative bg-gradient-to-r from-[#f59e0b] via-[#f97316] to-[#eab308] border border-white z-10 w-[34px] h-[34px] rounded-full overflow-hidden flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <g clip-path="url(#clip0_9148_135645)">
                              <path d="M2.5 17.5H17.5" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                              <path d="M12.5 6.66667C12.5 7.32971 12.7634 7.96559 13.2322 8.43443C13.7011 8.90328 14.337 9.16667 15 9.16667C15.663 9.16667 16.2989 8.90328 16.7678 8.43443C17.2366 7.96559 17.5 7.32971 17.5 6.66667V5.83333H2.5L4.16667 2.5H15.8333L17.5 5.83333M2.5 5.83333V6.66667C2.5 7.32971 2.76339 7.96559 3.23223 8.43443C3.70107 8.90328 4.33696 9.16667 5 9.16667C5.66304 9.16667 6.29893 8.90328 6.76777 8.43443C7.23661 7.96559 7.5 7.32971 7.5 6.66667V5.83333H2.5ZM7.5 6.66667C7.5 7.32971 7.76339 7.96559 8.23223 8.43443C8.70107 8.90328 9.33696 9.16667 10 9.16667C10.663 9.16667 11.2989 8.90328 11.7678 8.43443C12.2366 7.96559 12.5 7.32971 12.5 6.66667V5.83333L7.5 6.66667Z" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                              <path d="M4.16797 17.5013V9.04297" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                              <path d="M15.832 17.5013V9.04297" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                              <path d="M7.5 17.5V14.1667C7.5 13.7246 7.6756 13.3007 7.98816 12.9882C8.30072 12.6756 8.72464 12.5 9.16667 12.5H10.8333C11.2754 12.5 11.6993 12.6756 12.0118 12.9882C12.3244 13.3007 12.5 13.7246 12.5 14.1667V17.5" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </g>
                            <defs>
                              <clipPath id="clip0_9148_135645">
                                <rect width="20" height="20" fill="white"></rect>
                              </clipPath>
                            </defs>
                          </svg>
                        </div>
                        <div class="relative py-[2px] pl-[14px] pr-2 bg-gradient-to-r from-[#f59e0b] via-[#f97316] to-[#eab308] caption text-white text-sm font-medium min-w-[216px] transform translate-x-[-10px]">{{ $t("header.distributionSystem") }}</div>
                      </div>
                    </div>
                    <div v-if="selectedVariant && agenciesForVariant.length > 0" class="mt-2 text-xs">
                      <div class="text-gray-600 caption">
                        {{ $t("header.storesCount", { count: agenciesForVariant.length }) }}
                      </div>
                      <div class="mt-2 overflow-hidden border border-gray-200 rounded-lg">
                        <div class="h-full overflow-y-auto scrollbar-hide address-agents">
                          <div class="w-full h-full">
                            <div v-for="(agency, index) in agenciesForVariant" :key="agency.id" :class="['flex items-center space-x-[6px] text-xs p-2', index % 2 === 0 ? 'bg-white' : 'bg-gray-200']">
                              <div class="flex items-center space-x-[2px]">
                                <div class="flex-shrink-0">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none">
                                    <path d="M11.0204 9.42932L10.9572 9.31253C10.7803 8.92721 8.60618 8.34341 8.42922 8.33175L8.29018 8.3434C8.02469 8.39007 7.73399 8.61193 7.16522 9.0673C7.05148 9.16072 6.89973 9.18409 6.7607 9.11397C6.01496 8.72865 5.10488 7.95801 4.64982 7.49098C4.15686 6.98894 3.56279 6.15989 3.28471 5.49431C3.23418 5.36587 3.28471 5.22578 3.38584 5.13237C4.03045 4.59522 4.30853 4.33843 4.33383 4.05816C4.34644 3.8947 3.96732 1.82801 3.57539 1.62952L3.46165 1.55947C3.20888 1.40766 2.82965 1.18579 2.41254 1.26757C2.3114 1.29094 2.21028 1.32596 2.12184 1.37263C1.84385 1.53626 1.14863 1.97998 0.832631 2.55205C0.643067 2.91399 0.554551 6.19502 3.20894 8.96228C5.83805 11.7062 9.08652 11.8229 9.56684 11.7295H9.57945L9.61736 11.7179C10.2747 11.496 10.8308 10.9239 11.0457 10.6787C11.4375 10.2467 11.1722 9.72123 11.0204 9.42932Z" fill="#0180AD"></path>
                                  </svg>
                                </div>
                                <div class="flex items-center text-[#0180AD]">
                                  <NuxtLink :to="`tel:${agency.phone}`" class="description !leading-[140%] font-bold">{{ agency.phone }}</NuxtLink>
                                </div>
                              </div>
                              <div class="w-[1px] h-[11px] bg-gray-300"></div>
                              <div class="flex-1">
                                <NuxtLink v-if="agency.slug" :to="`${TOAGENCYSYSTEM}/${agency.slug}`" class="text-gray-800 duration-150 line-clamp-1 description hover:text-[#0180AD]">{{ agency.address }}</NuxtLink>
                                <span v-else class="text-gray-800 line-clamp-1 description">{{ agency.address }}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-else-if="selectedVariant" class="mt-2 text-sm">
                      <div class="text-gray-500 caption italic">{{ $t("header.noStores") }}</div>
                    </div>
                  </div>
                  <div class="px-3 py-4 bg-white rounded-b">
                    <div class="relative flex items-center mb-2">
                      <div class="relative shadow-lg border border-white z-10 w-[34px] h-[34px] bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] rounded-full overflow-hidden flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                          <g clip-path="url(#clip0_9148_135645)">
                            <path d="M2.5 17.5H17.5" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M12.5 6.66667C12.5 7.32971 12.7634 7.96559 13.2322 8.43443C13.7011 8.90328 14.337 9.16667 15 9.16667C15.663 9.16667 16.2989 8.90328 16.7678 8.43443C17.2366 7.96559 17.5 7.32971 17.5 6.66667V5.83333H2.5L4.16667 2.5H15.8333L17.5 5.83333M2.5 5.83333V6.66667C2.5 7.32971 2.76339 7.96559 3.23223 8.43443C3.70107 8.90328 4.33696 9.16667 5 9.16667C5.66304 9.16667 6.29893 8.90328 6.76777 8.43443C7.23661 7.96559 7.5 7.32971 7.5 6.66667V5.83333H2.5ZM7.5 6.66667C7.5 7.32971 7.76339 7.96559 8.23223 8.43443C8.70107 8.90328 9.33696 9.16667 10 9.16667C10.663 9.16667 11.2989 8.90328 11.7678 8.43443C12.2366 7.96559 12.5 7.32971 12.5 6.66667V5.83333L7.5 6.66667Z" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M4.16797 17.5013V9.04297" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M15.832 17.5013V9.04297" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.5 17.5V14.1667C7.5 13.7246 7.6756 13.3007 7.98816 12.9882C8.30072 12.6756 8.72464 12.5 9.16667 12.5H10.8333C11.2754 12.5 11.6993 12.6756 12.0118 12.9882C12.3244 13.3007 12.5 13.7246 12.5 14.1667V17.5" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                          </g>
                          <defs>
                            <clipPath id="clip0_9148_135645">
                              <rect width="20" height="20" fill="white"></rect>
                            </clipPath>
                          </defs>
                        </svg>
                      </div>
                      <div class="relative bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-xs text-white font-medium min-w-[160px] transform translate-x-[-10px] translate-y-[1px] pl-[14px] py-1">{{ $t("header.distributionAgent") }}</div>
                    </div>
                    <div class="flex items-center mb-1">
                      <span class="mr-3 text-green-600">
                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <g clip-path="url(#clip0_7612_110764)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.27363 14.122C1.71852 14.0284 1.20199 13.7645 0.798711 13.3612C0.287305 12.8498 0 12.1562 0 11.433V9.77113C0 9.04789 0.287305 8.35426 0.798711 7.84285C1.31016 7.33145 2.00379 7.0441 2.72703 7.0441H2.90852C3.19867 3.38176 6.26281 0.5 10 0.5C13.7372 0.5 16.8013 3.38176 17.0915 7.0441H17.273C17.9963 7.0441 18.6899 7.33145 19.2013 7.84285C19.7127 8.35426 20 9.04789 20 9.77113V11.433C20 12.1562 19.7127 12.8498 19.2013 13.3612C18.6899 13.8727 17.9963 14.16 17.273 14.16H16.4683C16.1345 14.16 15.864 13.8895 15.864 13.5557V7.61398C15.864 4.37539 13.2386 1.75 10 1.75C6.76141 1.75 4.13605 4.37539 4.13605 7.61398V13.5557C4.13605 13.8894 3.86547 14.16 3.53176 14.16H3.53168C3.62063 14.9986 3.99391 15.7862 4.5957 16.388C5.2902 17.0825 6.23215 17.4727 7.21434 17.4727H7.29738C7.40082 17.1224 7.59055 16.8 7.85348 16.537C8.26734 16.1232 8.82871 15.8906 9.41406 15.8906H10.5859C11.8048 15.8906 12.793 16.8787 12.793 18.0977C12.793 19.3166 11.8048 20.3047 10.5859 20.3047H9.41406C8.41227 20.3047 7.56633 19.6372 7.2968 18.7227H7.21434C5.90062 18.7227 4.64074 18.2008 3.71184 17.2719C2.86641 16.4264 2.35816 15.3069 2.27363 14.122ZM11.543 18.0977C11.543 17.5691 11.1145 17.1406 10.5859 17.1406H9.41406C9.16023 17.1406 8.91684 17.2414 8.73734 17.4209C8.55785 17.6004 8.45703 17.8438 8.45703 18.0977C8.45703 18.6262 8.88551 19.0547 9.41406 19.0547H10.5859C11.1145 19.0547 11.543 18.6262 11.543 18.0977ZM2.88605 8.2941H2.72703C2.33527 8.2941 1.95961 8.44973 1.68262 8.72672C1.40562 9.00371 1.25 9.37941 1.25 9.77113V11.433C1.25 11.8247 1.40562 12.2004 1.68262 12.4774C1.95961 12.7544 2.33527 12.91 2.72703 12.91H2.88605V8.2941ZM17.114 8.2941V12.91H17.273C17.6647 12.91 18.0404 12.7544 18.3174 12.4774C18.5944 12.2004 18.75 11.8247 18.75 11.433V9.77113C18.75 9.37941 18.5944 9.00371 18.3174 8.72672C18.0404 8.44973 17.6647 8.2941 17.273 8.2941H17.114Z" fill="#4E7C0E"></path>
                          </g>
                          <defs>
                            <clipPath id="clip0_7612_110764">
                              <rect width="20" height="20" fill="white" transform="translate(0 0.5)"></rect>
                            </clipPath>
                          </defs>
                        </svg>
                      </span>
                      <span class="text-gray-600 caption text-sm">{{ $t("header.freeConsultation") }}</span>
                    </div>
                    <div class="flex items-center mb-1">
                      <span class="mr-3 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="21" fill="none" viewBox="0 0 20 21">
                          <path fill="currentColor" d="M4.45 5.778v1.389l.11-.111a.614.614 0 0 1 .884 0 .623.623 0 0 1 0 .888L4.257 9.111a.65.65 0 0 1-.442.195.586.586 0 0 1-.441-.195L2.186 7.917a.623.623 0 0 1 0-.89.614.614 0 0 1 .884 0l.11.112V5.778C3.18 3.972 4.644 2.5 6.438 2.5h1.38c.332 0 .608.278.608.611a.615.615 0 0 1-.607.611h-1.38c-1.078 0-1.989.917-1.989 2.056zm13.39 7.278l-1.186-1.167a.614.614 0 0 0-.884 0l-1.187 1.194a.623.623 0 0 0 0 .89c.248.25.635.25.883 0l.11-.112v1.361a2.045 2.045 0 0 1-2.042 2.056h-1.38a.615.615 0 0 0-.608.61c0 .334.276.612.607.612h1.38c1.795 0 3.258-1.472 3.258-3.278v-1.389l.111.111a.65.65 0 0 0 .442.195.587.587 0 0 0 .441-.195.626.626 0 0 0 .056-.888zm-9.166-.612a.615.615 0 0 0 .608-.61.615.615 0 0 0-.608-.612H2.656a.615.615 0 0 0-.608.611v6.056c0 .333.276.611.608.611h6.018a.615.615 0 0 0 .608-.611v-3.417a.615.615 0 0 0-.608-.61.615.615 0 0 0-.607.61v2.778H3.291v-4.806h5.383zm9.222-3.222V5.806a.615.615 0 0 0-.608-.612.615.615 0 0 0-.607.612v2.777h-4.804V3.778h5.384a.615.615 0 0 0 .607-.611.615.615 0 0 0-.607-.611H11.27a.615.615 0 0 0-.608.61v6.056c0 .334.276.611.608.611h6.018a.615.615 0 0 0 .608-.61z"></path>
                        </svg>
                      </span>
                      <span class="text-gray-600 caption text-sm">{{ $t("header.easyReturn") }}</span>
                    </div>
                    <div class="flex items-center mb-2.5">
                      <span class="mr-3 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="21" fill="none" viewBox="0 0 20 21">
                          <path fill="currentColor" stroke="currentColor" stroke-width=".75" d="M10.136 2.603L10 2.5l-.136.103C6.85 4.902 3.214 5.737 3.177 5.745L3 5.784v.184C3 9.76 3 17.659 9.973 18.5h.054C17 17.66 17 9.766 17 5.968v-.184l-.177-.04c-.036-.007-3.672-.842-6.687-3.141zM10 18.042c-6.452-.797-6.544-7.918-6.544-11.896C4.219 5.948 7.334 5.063 10 3.07c2.683 1.994 5.786 2.879 6.544 3.077 0 3.978-.092 11.1-6.544 11.898v-.002z"></path>
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 10.583L9.8 13.5l3.2-5"></path>
                        </svg>
                      </span>
                      <span class="text-gray-600 caption text-sm">{{ $t("header.warrantyYears") }}</span>
                    </div>
                    <NuxtLink :to="TOPOLICY" class="text-sm flex items-center space-x-[6px] text-sky-500">
                      <span class="underline">{{ $t("action.viewPolicy") }}</span>
                      <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="7" height="10" fill="none" viewBox="0 0 7 10">
                          <path fill="currentColor" d="M1.04.16L.71.487a.543.543 0 0 0-.16.387c0 .147.058.284.16.387l3.737 3.737-3.74 3.74a.543.543 0 0 0-.16.387c0 .147.057.284.16.387l.327.328c.214.213.56.213.774 0l4.47-4.454a.56.56 0 0 0 .175-.388v-.002a.559.559 0 0 0-.175-.386L1.82.16a.553.553 0 0 0-.78 0z"></path>
                        </svg>
                      </span>
                    </NuxtLink>
                  </div>
                  <NuxtLink :to="HOTLINE" class="block w-full h-[158px]"><NuxtImg src="/images/contact/contact-advice.webp" alt="contact" loading="lazy" class="object-cover w-full h-full" /></NuxtLink>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- info product  -->
        <section id="tabs-information" class="bg-white product-description max-md:pt-3 mt-5 p-3 shadow-md">
          <div class="container pb-1 sticky top-0 z-40 tabs">
            <div class="relative max-w-full overflow-x-auto scrollbar-hide hiddenScroll tabs-information-scroll">
              <div class="flex w-full items-center space-x-[2px]">
                <div id="tab-info-summary" class="flex-1 flex-shrink-0 p-2 md:p-3 font-semibold text-center whitespace-normal duration-75 cursor-pointer tabs_item text-gray-700 bg-white hover:bg-gradient-to-r hover:from-sky-500 hover:to-sky-600 hover:text-white active-tab" @click="activeTab = 'summary'">
                  <span class="inline-block md:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                  </span>
                  <span class="hidden md:inline">{{ $t("header.productDescription") }}</span>
                </div>
                <div id="tab-info-specification" class="flex-1 flex-shrink-0 p-2 md:p-3 font-semibold text-center whitespace-normal duration-75 cursor-pointer tabs_item text-gray-700 bg-white hover:bg-gradient-to-r hover:from-sky-500 hover:to-sky-600 hover:text-white" @click="activeTab = 'specification'">
                  <span class="inline-block md:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path d="M9.75 3v1.5M14.25 3v1.5M3.75 7.5h16.5M6 10.5v8.25a.75.75 0 00.75.75h10.5a.75.75 0 00.75-.75V10.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </span>
                  <span class="hidden md:inline">{{ $t("header.specifications") }}</span>
                </div>
                <div id="tab-info-document" class="flex-1 flex-shrink-0 p-2 md:p-3 font-semibold text-center whitespace-normal duration-75 cursor-pointer tabs_item text-gray-700 bg-white hover:bg-gradient-to-r hover:from-sky-500 hover:to-sky-600 hover:text-white" @click="activeTab = 'faq'">
                  <span class="inline-block md:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 512 512">
                      <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3l58.3 0c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24l0-13.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1l-58.3 0c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z" />
                    </svg>
                  </span>
                  <span class="hidden md:inline">{{ $t("nav.faq") }}</span>
                </div>
                <div id="tab-info-rating" class="flex-1 flex-shrink-0 p-2 md:p-3 font-semibold text-center whitespace-normal duration-75 cursor-pointer tabs_item text-gray-700 bg-white hover:bg-gradient-to-r hover:from-sky-500 hover:to-sky-600 hover:text-white" @click="activeTab = 'rating'">
                  <span class="inline-block md:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.386 2.46a1 1 0 00-.364 1.118l1.286 3.966c.3.921-.755 1.688-1.54 1.118L10 14.347l-3.386 2.46c-.784.57-1.838-.197-1.539-1.118l1.286-3.966a1 1 0 00-.364-1.118L2.611 9.393c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.966z" />
                    </svg>
                  </span>
                  <span class="hidden md:inline">{{ $t("product.rating") }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="container">
            <div class="text-gray-600 text-base" v-if="activeTab === 'summary'">
              <div class="max-w-[720px] w-full mx-auto py-4">
                <div class="p-2 lg:p-0 content-block">
                  <div class="contentContainer relative text-gray-600 text-base" :class="isDescriptionExpanded ? '' : 'overflow-hidden max-h-[653px]'">
                    <div v-if="!isDescriptionExpanded" class="overflow-blur-content absolute bottom-0 left-0 w-full h-full max-h-20 transparent-NuxtImg bg-gradient-to-t from-white"></div>
                    <div class="prose markup-custom list-striped">
                      <div class="prose max-w-none" v-if="productDetail.description" v-html="productDetail.description"></div>
                      <div v-else class="my-5 text-center text-gray-500">{{ $t("header.noDescription") }}</div>
                    </div>
                    <div v-if="(productDetail as any).productTags && (productDetail as any).productTags.length > 0" class="flex flex-col gap-1 mt-5 justify-center items-start w-full bg-white dark:bg-transparent px-4 py-4 shadow-xl rounded-lg">
                      <p class="font-medium text-xl text-gray-500 mb-2">{{ $t("common.tags") }}</p>
                      <div class="flex flex-wrap gap-2">
                        <NuxtLink v-for="tag in (productDetail as any).productTags as any[]" :key="tag.id" :to="`/product/tag/${tag.slug}`" class="px-3 py-1 text-[12px] bg-[#d9dfe3] max-w-max rounded font-semibold text-[#7281a3] hover:bg-sky-100 hover:text-sky-600 transition-colors">#{{ tag.name }}</NuxtLink>
                      </div>
                    </div>
                  </div>
                  <div class="flex justify-center mt-6 sm:mt-8">
                    <div class="relative z-10">
                      <button type="button" @click="isDescriptionExpanded = !isDescriptionExpanded" class="toggleBtn w-full flex items-center justify-center bg-gradient-to-r from-sky-700 to-sky-600 hover:from-sky-600 hover:to-sky-500 text-white rounded-xs transition-all duration-300 ease-in-out transform focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-1 text-sm leading-none shadow-md hover:shadow-md font-semibold py-[12px] px-[24px]">
                        <span class="whitespace-nowrap">{{ isDescriptionExpanded ? $t("action.collapse") : $t("action.expand") }}</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="text-gray-600 text-base" v-if="activeTab === 'specification'">
              <div class="max-w-[1018px] w-full mx-auto py-4">
                <div class="px-4 bg-white">
                  <div class="prose specification-table">
                    <div class="specifications">
                      <div class="px-4 md:px-[24px]" v-if="Object.keys(attributeGroups).length > 0">
                        <div class="py-2 md:py-3">
                          <div class="mb-1 space-y-[4px]">
                            <div v-for="(attributes, attrName) in attributeGroups" :key="attrName">
                              <div class="flex caption text-gray-600">
                                <div class="w-[120px] mr-2 md:mr-3 xl:mr-4">{{ attrName }}</div>
                                <div class="flex-1">{{ attributes.map((a) => a.name).join(", ") }}</div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div v-else class="my-5 text-center text-gray-500">{{ $t("header.noSpecFound") }}</div>
                      &nbsp;
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="text-gray-600 text-base" v-if="activeTab === 'faq'">
              <div v-if="(productDetail as any).faqs.length" class="w-full py-2 sm:py-4">
                <div class="mx-auto lg:w-[40vw] w-full px-0 sm:px-2 md:px-6">
                  <template v-for="(faq, id) in (productDetail as any).faqs" :key="id">
                    <div class="bg-white rounded-md border border-gray-100 hover:border-cyan-500 shadow transition hover:shadow-lg overflow-hidden mb-2">
                      <h2>
                        <button type="button" class="flex items-center justify-between w-full px-4 py-3 text-left text-base font-semibold text-gray-800 hover:text-cyan-600 transition" :aria-expanded="openFaq === id" @click="openFaq === id ? (openFaq = null) : (openFaq = id)">
                          <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 flex items-center justify-center bg-cyan-100 text-cyan-600 font-bold rounded-md text-sm">
                              {{ (id as number) + 1 }}
                            </div>
                            <span class="line-clamp-2">{{ faq.question }}</span>
                          </div>
                          <svg class="w-4 h-4 shrink-0 transition-transform duration-300" :class="{ 'rotate-180': openFaq === id }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                          </svg>
                        </button>
                      </h2>
                      <div v-show="openFaq === id" class="px-4 pb-4 text-sm text-gray-600 transition-all duration-300" v-html="faq.answer"></div>
                    </div>
                  </template>
                </div>
              </div>
              <div v-else class="my-5 text-center text-gray-500">{{ $t("common.noData") }}</div>
            </div>
            <div class="text-gray-600 text-base" v-if="activeTab === 'rating'">
              <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white rounded-md shadow-md p-4 mb-6">
                  <div class="space-y-4">
                    <h2 class="text-2xl font-bold text-gray-700">{{ $t("product.customerReviewsTitle") }}</h2>
                    <p class="text-gray-600 text-sm">{{ $t("product.customerReviewsDescription") }}</p>
                    <p class="text-gray-500 text-xs">Mọi đánh giá từ khách hàng đều giúp chúng tôi hoàn thiện chất lượng sản phẩm & dịch vụ tốt hơn.</p>
                  </div>
                  <div>
                    <div class="flex items-center justify-between mb-4">
                      <div>
                        <p class="text-sm text-gray-500">
                          {{ $t("product.basedOn") }}
                          <span class="text-sky-500 font-semibold">{{ reviewSummary?.total_reviews ?? 0 }}</span>
                          {{ $t("product.reviews") }}
                        </p>
                        <p class="text-3xl font-bold text-gray-900">{{ reviewSummary?.average_rating ? reviewSummary.average_rating.toFixed(1) : "5.0" }}</p>
                      </div>
                      <div class="flex items-center space-x-1">
                        <!-- Stars -->
                        <template v-for="star in 5" :key="star">
                          <svg :class="star <= Math.round(reviewSummary?.average_rating ?? 5) ? 'text-yellow-400' : 'text-gray-300'" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97 4.184.012c.97.003 1.371 1.24.588 1.81l-3.396 2.53 1.27 3.996c.286.899-.755 1.637-1.54 1.092L10 13.187l-3.343 2.148c-.784.545-1.825-.193-1.54-1.092l1.27-3.996-3.396-2.53c-.783-.57-.382-1.807.588-1.81l4.184-.012 1.286-3.97z" />
                          </svg>
                        </template>
                      </div>
                    </div>

                    <div class="space-y-2">
                      <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="flex items-center gap-3">
                        <span class="text-sm w-4 font-medium text-gray-900">{{ star }}</span>
                        <div class="flex-1 h-3 bg-gray-200 rounded-full overflow-hidden">
                          <div
                            :class="[
                              star === 5 ? 'bg-gradient-to-r from-green-400 to-green-600' : '',
                              star === 4 ? 'bg-gradient-to-r from-lime-400 to-lime-600' : '',
                              star === 3 ? 'bg-gradient-to-r from-yellow-400 to-yellow-500' : '',
                              star === 2 ? 'bg-gradient-to-r from-orange-400 to-orange-500' : '',
                              star === 1 ? 'bg-gradient-to-r from-red-400 to-red-500' : '',
                              'h-full rounded-full transition-all duration-500',
                            ]"
                            :style="{ width: `${reviewSummary?.star_percentages?.[star] ?? 0}%` }"
                          ></div>
                        </div>
                        <span class="text-xs w-12 text-right text-gray-500">{{ reviewSummary?.star_percentages?.[star] ?? 0 }}%</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- RIGHT: Ratings & Review Form -->
                <div class="max-w-2xl mx-auto mb-8">
                  <h3 class="text-xl font-bold text-gray-700 mb-3 text-center">Gửi đánh giá của bạn</h3>

                  <!-- Rating Star Selector -->
                  <div class="flex items-center justify-center space-x-2 mb-4">
                    <span class="text-sm text-gray-600 mr-2">Mức độ hài lòng:</span>
                    <button
                      v-for="s in 5"
                      :key="s"
                      type="button"
                      @click="reviewRating = s"
                      class="text-2xl transition transform hover:scale-110 focus:outline-none"
                    >
                      <i :class="[s <= reviewRating ? 'fa-solid fa-star text-yellow-400' : 'fa-regular fa-star text-gray-300']"></i>
                    </button>
                    <span class="text-sm font-semibold text-gray-700 ml-2">({{ reviewRating }} sao)</span>
                  </div>

                  <!-- Form fields for guest -->
                  <div v-if="!authStore.isAuthenticated" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                    <input
                      v-model="reviewAuthorName"
                      type="text"
                      placeholder="Họ và tên của bạn"
                      class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:border-sky-500"
                    />
                    <input
                      v-model="reviewAuthorEmail"
                      type="email"
                      placeholder="Email nhận thông báo (tùy chọn)"
                      class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:border-sky-500"
                    />
                  </div>

                  <!-- Textarea -->
                  <form @submit.prevent="handleSendReview" class="rounded-md bg-gray-50 border border-gray-200 overflow-hidden shadow-sm">
                    <textarea
                      v-model="reviewComment"
                      class="w-full p-4 text-gray-700 placeholder-gray-400 focus:outline-none resize-none bg-white text-sm"
                      rows="3"
                      placeholder="Hãy chia sẻ trải nghiệm của bạn về sản phẩm này (chất lượng, thiết kế, hiệu năng...)..."
                      required
                    ></textarea>

                    <div v-if="reviewSuccessMsg" class="px-4 py-2 bg-green-50 text-green-700 text-sm border-t border-green-200">
                      {{ reviewSuccessMsg }}
                    </div>
                    <div v-if="reviewErrorMsg" class="px-4 py-2 bg-red-50 text-red-600 text-sm border-t border-red-200">
                      {{ reviewErrorMsg }}
                    </div>

                    <div class="flex items-center justify-between bg-gray-100 px-4 py-2">
                      <span class="text-xs text-gray-500">Đánh giá công khai</span>
                      <button
                        type="submit"
                        :disabled="reviewSubmitting"
                        class="flex items-center justify-center px-4 py-2 text-white text-sm font-medium rounded-md bg-sky-500 hover:bg-sky-600 disabled:opacity-50 transition"
                      >
                        <span v-if="reviewSubmitting">Đang gửi...</span>
                        <span v-else>Gửi đánh giá</span>
                      </button>
                    </div>
                  </form>
                </div>

                <!-- COMMENT LIST -->
                <div class="max-w-2xl mx-auto space-y-4">
                  <h4 class="text-lg font-bold text-gray-700 mb-4">
                    Tất cả đánh giá ({{ reviewSummary?.total_reviews ?? 0 }})
                  </h4>

                  <div v-if="reviewLoading && reviewList.length === 0" class="text-center py-8 text-gray-500">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-500 mb-2"></div>
                    <p class="text-sm">Đang tải đánh giá...</p>
                  </div>

                  <div v-else-if="reviewList.length === 0" class="text-center py-8 bg-white border border-gray-200 rounded-lg p-6">
                    <i class="fa-regular fa-comment-dots text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 text-sm">Chưa có đánh giá nào cho sản phẩm này.</p>
                    <p class="text-xs text-gray-400 mt-1">Hãy là người đầu tiên chia sẻ cảm nghĩ về sản phẩm!</p>
                  </div>

                  <!-- Comment Card -->
                  <div
                    v-for="item in reviewList"
                    :key="item.id"
                    class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm hover:border-gray-300 transition"
                  >
                    <div class="flex items-center justify-between mb-2">
                      <div class="flex items-center space-x-3">
                        <NuxtImg
                          class="w-8 h-8 rounded-full object-cover"
                          :src="item.user_avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(item.user_name)}&background=0284c7&color=fff`"
                          alt="avatar"
                        />
                        <div>
                          <p class="text-sm font-semibold text-gray-800">{{ item.user_name }}</p>
                          <p class="text-xs text-gray-400">{{ item.created_at_human || item.created_at }}</p>
                        </div>
                      </div>
                      <div class="flex items-center space-x-1">
                        <template v-for="star in 5" :key="star">
                          <svg :class="star <= item.rating ? 'text-yellow-400' : 'text-gray-300'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97 4.184.012c.97.003 1.371 1.24.588 1.81l-3.396 2.53 1.27 3.996c.286.899-.755 1.637-1.54 1.092L10 13.187l-3.343 2.148c-.784.545-1.825-.193-1.54-1.092l1.27-3.996-3.396-2.53c-.783-.57-.382-1.807.588-1.81l4.184-.012 1.286-3.97z" />
                          </svg>
                        </template>
                      </div>
                    </div>
                    <p class="text-gray-700 text-sm mb-3 whitespace-pre-line">{{ item.comment }}</p>
                    <div class="flex items-center justify-between text-gray-500 text-xs">
                      <button
                        type="button"
                        @click="likeReview(item.id)"
                        class="flex items-center space-x-1.5 hover:text-sky-600 transition"
                      >
                        <i class="fa-regular fa-thumbs-up"></i>
                        <span>{{ item.likes_count || 0 }} hữu ích</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- <div class="flex justify-center mt-6 sm:mt-8">
              <div class="relative z-10">
                <button type="button" class="toggleBtn w-full flex items-center justify-center bg-gradient-to-r from-sky-700 to-sky-600 hover:from-sky-600 hover:to-sky-500 text-white rounded-xs transition-all duration-300 ease-in-out transform focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-1 text-sm leading-none shadow-md hover:shadow-md font-semibold py-[12px] px-[24px]">
                  <span class="whitespace-nowrap">Xem thêm</span>
                </button>
              </div>
            </div> -->
            </div>
          </div>
        </section>
        <section v-if="relatedProducts.length > 0" class="product-accsessory bg-white shadow-md rounded-md py-3 md:py-6 px-3 md:px-6 md:mb-8 mb-4 mt-5">
          <div class="mb-4 md:mb-8">
            <div class="flex justify-between items-center space-x-2">
              <div class="head-section-product">
                <h2 class="text-transparent bg-clip-text bg-gradient-to-r from-[rgba(43,57,144,0.91)] via-[#0097cc] to-[#8dc63f] text-xl md:text-3xl font-bold">{{ $t("product.youMightLike") }}</h2>
                <div class="h-1 max-w-48 my-1 md:my-2 relative">
                  <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
                </div>
              </div>
            </div>
            <p class="text-gray-600 md:mt-2 mt-1 max-w-xl md:text-sm text-xs">{{ $t("header.lightingDescription") }}</p>
          </div>
          <div class="col-span-5 min-w-0">
            <Splide :options="productRelatedOptions(relatedProducts.length)" id="product-related-slide">
              <SplideSlide v-for="product in relatedProducts" :key="product.id">
                <ProductCard :product="product" />
              </SplideSlide>
            </Splide>
          </div>
        </section>
      </template>
    </DataState>
  </div>
</template>
<script setup lang="ts">
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import ProductCard from "~/components//features/ProductCard.vue";
import DataState from "~/components/ui/DataState.vue";
import { Splide, SplideSlide } from "@splidejs/vue-splide";
import { useProduct } from "~/composables/product/useProduct";
import { useProductSlider } from "~/composables/useProductSlider";
import { resolveAlbum } from "~/utils/helpers";
import type { IProduct } from "~/interfaces/product/product.interface";
import { useAuthStore } from "~/stores/auth/auth";
import { useWishlist } from "~/composables/product/useWishlist";
import { useCompare } from "~/composables/product/useCompare";
import { useCart } from "~/composables/cart/useCart";
import { useProductReview } from "~/composables/product/useProductReview";
import type { IPromotion, IPromotionTarget } from "~/interfaces/promotion/promotion.interface";
import { IMAGEDEFAULT, TOEXCHANGEPOLICY, TOAGENCYSYSTEM, HOTLINE, TOCART } from "~/utils/constants";
import { useRoute } from "vue-router";
import { computed, nextTick, onMounted, ref, watch } from "vue";
import { navigateTo } from "#app";
import { definePageMeta } from "#imports";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const route = useRoute();
type PromotionWithTargets = IPromotion & { applicableTargets: IPromotionTarget[] };

const { productDetail, productDetailLoading, productDetailError, trackView, fetchById } = useProduct();
const { productImageMainOptions, productImageThumbnailOptions, productRelatedOptions } = useProductSlider();
const { toggleWishlist, isVariantInWishlist, checkVariantWishlistStatus, fetchWishlist, getWishlistVariantIdsByProductId } = useWishlist();
const { addToCompare, removeFromCompare, isInCompare } = useCompare();
const { addItem, isAddingToCart, isBuyingNow } = useCart();
const authStore = useAuthStore();

// Reviews composable & state
const {
  reviews: reviewList,
  summary: reviewSummary,
  loading: reviewLoading,
  submitting: reviewSubmitting,
  fetchReviews,
  submitReview,
  likeReview,
} = useProductReview();

const reviewRating = ref(5);
const reviewComment = ref("");
const reviewAuthorName = ref("");
const reviewAuthorEmail = ref("");
const reviewSuccessMsg = ref("");
const reviewErrorMsg = ref("");

const handleSendReview = async () => {
  if (!productDetail.value?.id) return;
  if (!reviewComment.value.trim()) {
    reviewErrorMsg.value = "Vui lòng nhập nội dung đánh giá của bạn.";
    return;
  }
  reviewErrorMsg.value = "";
  reviewSuccessMsg.value = "";

  const res = await submitReview({
    product_id: productDetail.value.id,
    rating: reviewRating.value,
    comment: reviewComment.value.trim(),
    user_name: reviewAuthorName.value.trim() || undefined,
    user_email: reviewAuthorEmail.value.trim() || undefined,
  });

  if (res.success) {
    reviewComment.value = "";
    reviewSuccessMsg.value = "Cảm ơn bạn! Đánh giá của bạn đã được ghi nhận.";
    setTimeout(() => {
      reviewSuccessMsg.value = "";
    }, 5000);
  } else {
    reviewErrorMsg.value = res.message || "Gửi đánh giá không thành công.";
  }
};

// State
const activeTab = ref("summary");
const selectedAttributes = ref<Record<string, number>>({});
const selectedVariant = ref<any>(null);
const openFaq = ref<number | null>(null);
const quantity = ref(1);
const isDescriptionExpanded = ref(false);
const currencyFormatter = new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" });
// Compare checkbox state - sync với store
const isCompareChecked = computed({
  get: () => {
    if (!selectedVariant.value?.id) return false;
    return isInCompare(selectedVariant.value.id);
  },
  set: (value: boolean) => {
    if (!selectedVariant.value?.id || !productDetail.value) return;
    if (value) {
      addToCompare(productDetail.value, selectedVariant.value);
    } else {
      removeFromCompare(selectedVariant.value.id);
    }
  },
});

// Refs cho Splide sliders
const mainSliderRef = ref<any>(null);
const thumbSliderRef = ref<any>(null);
const lightboxVisible = ref(false);
const lightboxIndex = ref(0);

// Hiển thị lightbox khi click vào ảnh
const showLightbox = (index: number) => {
  lightboxIndex.value = index;
  lightboxVisible.value = true;
};

const handleImageError = (event: Event) => {
  const target = event.target as HTMLImageElement;
  if (target && target.src !== IMAGEDEFAULT) {
    target.src = IMAGEDEFAULT;
  }
};

// Computed
const breadcrumbs = computed(() => [
  { label: t("page.shop"), href: TOSHOP },
  { label: productDetail.value?.name || "...", href: route.fullPath },
]);

const images = computed(() => {
  const list = resolveAlbum(productDetail.value?.album);
  return list.length ? list : [IMAGEDEFAULT];
});

const hashString = (str: string): number => {
  let hash = 0;
  for (let i = 0; i < str.length; i++) {
    hash = (hash << 5) - hash + str.charCodeAt(i);
    hash |= 0;
  }
  return Math.abs(hash) || 1;
};

const getNormalizedAttributeData = (variant: any): Record<string, { id: number; name: string }> => {
  if (!variant?.attributeData) return {};
  let raw = variant.attributeData;
  if (typeof raw === "string") {
    try {
      raw = JSON.parse(raw);
      if (typeof raw === "string") raw = JSON.parse(raw);
    } catch {
      return {};
    }
  }
  if (!raw || typeof raw !== "object") return {};

  const result: Record<string, { id: number; name: string }> = {};

  Object.entries(raw).forEach(([key, val]: [string, any]) => {
    if (!val) return;
    let groupName = key;
    let id = typeof val === "object" ? (val.id ?? val.attribute_id ?? Number(key)) : Number(key) || 0;
    let name = typeof val === "object" ? (val.name ?? val.value ?? val.label ?? "") : String(val);

    if (/^\d+$/.test(groupName)) {
      if (typeof val === "string") {
        name = val;
        id = Number(key);
      }
      groupName = "Thuộc tính";
    }

    if (name) {
      result[groupName] = {
        id: id || hashString(name),
        name,
      };
    }
  });

  return result;
};

// Nhóm các thuộc tính từ variants
const attributeGroups = computed(() => {
  if (!productDetail.value?.productVariants?.length) return {};

  const groups: Record<string, { id: number; name: string }[]> = {};

  (productDetail.value.productVariants as any[]).forEach((variant: any) => {
    const attrData = getNormalizedAttributeData(variant);
    Object.entries(attrData).forEach(([attrName, attrValue]) => {
      if (!groups[attrName]) {
        groups[attrName] = [];
      }
      const exists = groups[attrName].find((item) => item.id === attrValue.id || item.name === attrValue.name);
      if (!exists) {
        groups[attrName].push(attrValue);
      }
    });
  });

  return groups;
});

// Tìm variant phù hợp với attributes đã chọn
const findMatchingVariant = () => {
  if (!productDetail.value?.productVariants?.length) return null;

  return (productDetail.value.productVariants as any[]).find((variant: any) => {
    const attrData = getNormalizedAttributeData(variant);
    if (!Object.keys(attrData).length) return false;

    return Object.entries(selectedAttributes.value).every(([attrName, attrId]) => {
      return attrData[attrName]?.id === attrId;
    });
  });
};

const handleSelectAttribute = (attributeName: string, attributeId: number) => {
  selectedAttributes.value[attributeName] = attributeId;
  const matched = findMatchingVariant();
  if (matched) {
    selectedVariant.value = matched;
  } else {
    const fallback = (productDetail.value?.productVariants as any[])?.find((v: any) => {
      const data = getNormalizedAttributeData(v);
      return data[attributeName]?.id === attributeId;
    });
    if (fallback) {
      selectedVariant.value = fallback;
      const data = getNormalizedAttributeData(fallback);
      Object.entries(data).forEach(([name, val]) => {
        selectedAttributes.value[name] = val.id;
      });
    }
  }
};

const displayPrice = computed(() => {
  if (selectedVariant.value?.price) {
    return new Intl.NumberFormat("vi-VN").format(selectedVariant.value.price);
  }
  if (productDetail.value?.priceOrigin) {
    return new Intl.NumberFormat("vi-VN").format(parseFloat(productDetail.value.priceOrigin.toString()));
  }
  return "0";
});

const displayStock = computed(() => {
  if (selectedVariant.value?.stock !== undefined) {
    return selectedVariant.value.stock;
  }
  return 0;
});

const productCatalogueIds = computed<number[]>(() => {
  const catalogues = (productDetail.value as any)?.productCatalogues;
  if (!Array.isArray(catalogues)) return [];
  return catalogues.map((catalogue: any) => (typeof catalogue === "number" ? catalogue : catalogue?.id)).filter((id): id is number => typeof id === "number");
});

const applicablePromotions = computed<PromotionWithTargets[]>(() => {
  const rawPromotions = (productDetail.value as any)?.promotions;
  let promoList: any[] = [];
  if (Array.isArray(rawPromotions)) {
    promoList = rawPromotions;
  } else if (rawPromotions && typeof rawPromotions === "object") {
    promoList = [
      ...(Array.isArray(rawPromotions.product) ? rawPromotions.product : []),
      ...Object.values(rawPromotions.variants || {}).flat(),
      ...Object.values(rawPromotions.catalogues || {}).flat(),
    ];
  }
  if (!promoList.length) return [];
  const productId = productDetail.value?.id;
  const variantId = selectedVariant.value?.id;

  const uniquePromos = Array.from(new Map(promoList.map((p) => [p.id, p])).values());

  return uniquePromos
    .map((promotion: any) => {
      const targets = (promotion.targets ?? promotion.promotionTargets ?? []).filter((target: any) => {
        const tType = target.targetType || target.target_type;
        const tId = Number(target.targetId || target.target_id || target.productVariantId || target.productId || target.productCatalogueId);
        if (tType === "product_variant" || target.productVariantId) {
          return variantId && tId === variantId;
        }
        if (tType === "product" || target.productId) {
          return productId && tId === productId;
        }
        if (tType === "product_catalogue" || target.productCatalogueId) {
          return productCatalogueIds.value.includes(tId);
        }
        return false;
      });
      return { ...promotion, applicableTargets: targets };
    })
    .filter((item): item is PromotionWithTargets => Boolean(item));
});

const formatCurrency = (value?: number | string | null) => {
  if (value === undefined || value === null || value === "") return "";
  const num = typeof value === "number" ? value : Number(value);
  if (isNaN(num) || !isFinite(num)) return "";
  return currencyFormatter.format(num);
};

const getPromotionMaxDiscount = (promotion: any) => {
  const action = promotion.actions?.[0] || promotion.promotionActions?.[0];
  const type = action?.actionType || promotion.discountType;
  if (type === "percent") {
    const maxVal = action?.maxDiscount ?? promotion.maxDiscountValue;
    return maxVal ? Number(maxVal) : null;
  }
  return null;
};

const formatPromotionDiscount = (promotion: any) => {
  const action = promotion.actions?.[0] || promotion.promotionActions?.[0];
  const type = action?.actionType || promotion.discountType || (promotion.type === "percent" ? "percent" : "fixed");
  const rawValue = action?.actionValue ?? promotion.discountValue ?? 0;
  const value = typeof rawValue === "number" ? rawValue : Number(rawValue || 0);

  if (type === "percent") {
    return `${value}%`;
  }
  return formatCurrency(value);
};

const promotionWindow = (promotion: IPromotion) => {
  const formatDate = (input?: string | null) => {
    if (!input) return null;
    const parsed = new Date(input);
    if (Number.isNaN(parsed.getTime())) return null;
    return parsed.toLocaleDateString("vi-VN");
  };
  const start = formatDate(promotion.startDate);
  const end = formatDate(promotion.endDate);
  if (start && end) return `${start} - ${end}`;
  if (start) return `Từ ${start}`;
  if (end) return `Đến ${end}`;
  return "Không giới hạn";
};

const resolvePromotionTargetLabel = (target: IPromotionTarget) => {
  if ((target as any).productVariant?.name) return (target as any).productVariant.name;
  if ((target as any).product?.name) return (target as any).product.name;
  if ((target as any).productCatalogue?.name) return (target as any).productCatalogue.name;
  if (target.productVariantId) return `Phiên bản #${target.productVariantId}`;
  if (target.productId) return `Sản phẩm #${target.productId}`;
  if (target.productCatalogueId) return `Danh mục #${target.productCatalogueId}`;
  return "Sản phẩm";
};

const agenciesForVariant = computed(() => {
  if (!selectedVariant.value?.agencies) return [];
  return selectedVariant.value.agencies;
});

const discountPercent = computed(() => {
  if (!productDetail.value) return 0;

  const originalPrice = parseFloat(productDetail.value.priceOrigin?.toString().replace(/[^0-9.-]+/g, "") || "0");
  const rawSalePrice = selectedVariant.value?.price ?? productDetail.value.priceOrigin ?? 0;
  const salePrice = parseFloat(rawSalePrice.toString().replace(/[^0-9.-]+/g, "") || "0");

  if (isNaN(originalPrice) || isNaN(salePrice) || originalPrice <= 0 || salePrice >= originalPrice) return 0;

  const discount = ((originalPrice - salePrice) / originalPrice) * 100;
  return isNaN(discount) ? 0 : Math.round(discount);
});

// related products
const relatedProducts = computed(() => {
  if (!productDetail.value?.relateds || !Array.isArray(productDetail.value.relateds)) return [];
  const items = productDetail.value.relateds;
  if (items.length === 0) return [];
  if (typeof items[0] === "object" && items[0] !== null && "id" in items[0]) {
    return items as IProduct[];
  }
  return [];
});

// Wishlist
const isCurrentVariantInWishlist = computed(() => {
  if (!selectedVariant.value?.id) return false;
  return isVariantInWishlist(selectedVariant.value.id);
});

const handleWishlistToggle = async () => {
  if (!selectedVariant.value?.id) return;
  await toggleWishlist(selectedVariant.value.id);
};

// Lấy tất cả variant ids có trong wishlist cho product hiện tại
const wishlistVariantIds = computed(() => {
  if (!productDetail.value?.id) return [];
  return getWishlistVariantIdsByProductId(productDetail.value.id);
});

const isAttributeOptionInWishlist = (attrName: string, attrId: number): boolean => {
  if (!productDetail.value?.productVariants?.length || wishlistVariantIds.value.length === 0) return false;

  return (productDetail.value.productVariants as any[]).some((variant: any) => {
    if (!wishlistVariantIds.value.includes(variant.id)) return false;

    const data = getNormalizedAttributeData(variant);
    return data[attrName]?.id === attrId;
  });
};

watch(quantity, (newVal) => {
  if (newVal < 1) quantity.value = 1;
  if (displayStock.value > 0 && newVal > displayStock.value) {
    quantity.value = displayStock.value;
  }
});

const handleQuantityInput = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const value = parseInt(target.value, 10);
  if (isNaN(value) || value < 1) {
    quantity.value = 1;
  } else if (displayStock.value > 0 && value > displayStock.value) {
    quantity.value = displayStock.value;
  } else {
    quantity.value = value;
  }
};

const scrollToTab = (tabName: string) => {
  activeTab.value = tabName;
  nextTick(() => {
    const element = document.getElementById("tabs-information");
    if (element) {
      element.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  });
};

const handleAddToCart = async () => {
  if (!selectedVariant.value && !productDetail.value) return;
  await addItem(productDetail.value!, selectedVariant.value, quantity.value, false);
};

const handleBuyNow = async () => {
  if (!selectedVariant.value && !productDetail.value) return;
  const success = await addItem(productDetail.value!, selectedVariant.value, quantity.value, true);
  if (success) {
    await navigateTo(TOCART);
  }
};

// Load data khi component mount
onMounted(async () => {
  const slug = route.params.slug as string;
  if (slug) {
    await fetchById(slug);

    // Load wishlist nếu đã đăng nhập
    if (authStore.isAuthenticated) {
      await fetchWishlist();
    }

    // Chọn variant đầu tiên mặc định
    if (productDetail.value?.productVariants?.length) {
      const firstVariant = (productDetail.value.productVariants as any[])[0];
      const data = getNormalizedAttributeData(firstVariant);
      Object.entries(data).forEach(([attrName, attr]) => {
        selectedAttributes.value[attrName] = attr.id;
      });
      selectedVariant.value = firstVariant;
    }

    // Fetch reviews
    if (productDetail.value?.id) {
      await fetchReviews(productDetail.value.id);
    }

    // Sync sliders
    await nextTick();
    if (mainSliderRef.value?.splide && thumbSliderRef.value?.splide) {
      mainSliderRef.value.splide.sync(thumbSliderRef.value.splide);
    }
  }
});

// check wishlist status
watch(selectedVariant, async (newVariant) => {
  if (newVariant?.id && authStore.isAuthenticated) {
    await checkVariantWishlistStatus(newVariant.id);
  }
});

definePageMeta({
  layout: "client",
});
</script>
<style scoped>
.active-tab {
  color: #fff !important;
}
.product-thumbnail-slider.splide__slide.is-visible.is-active {
  border: 1px solid #5786d0 !important;
  border-radius: 0.375rem !important;
}
</style>
