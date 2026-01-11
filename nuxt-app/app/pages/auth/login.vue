<template>
  <div class="main-content rounded-lg h-screen overflow-hidden">
    <div class="relative w-full h-full">
      <NuxtImg src="/client/images/bg-auth.png" alt="image-background" class="w-full h-full object-cover" loading="lazy" />
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="flex flex-col lg:flex-row max-w-4xl w-full mx-auto overflow-hidden lg:h-[560px]">
          <div class="relative lg:w-1/2 hidden lg:block h-full">
            <NuxtImg src="/client/images/showroom/image-showroom.jpg" alt="background đăng nhập" class="absolute inset-0 w-full h-full object-cover z-0" loading="lazy" />
            <div class="absolute inset-0 bg-sky-200/80 opacity-90 z-10"></div>
            <div class="relative z-40 flex flex-col p-6 lg:p-8 h-full">
              <NuxtImg src="/shared/images/logo/logo-website.png" alt="Logo" class="w-40 h-8 mb-6" loading="lazy" />
              <div class="flex-grow"></div>
              <i class="fas fa-quote-left text-4xl text-teal-500 mb-2"></i>
              <div class="overflow-blur-content absolute bottom-0 left-0 w-full h-full max-h-40 transparent-img bg-gradient-to-t from-gray-500"></div>
              <Splide :options="authOptions" class="w-full">
                <SplideSlide v-for="(item, index) in loginItems" :key="index">
                  <div class="italic text-lg text-white text-center min-h-[60px] flex items-center justify-center">"{{ item.title }}"</div>
                </SplideSlide>
              </Splide>
            </div>
          </div>
          <div class="flex flex-col justify-center p-6 lg:p-8 w-full lg:w-1/2 bg-white max-w-xs sm:max-w-sm md:max-w-md mx-auto">
            <h2 class="text-xl font-semibold text-cyan-600 mb-1 text-center">{{ $t("auth.welcomeBack") }}</h2>
            <p class="text-sm text-gray-500 mb-6 text-center">{{ $t("auth.loginDescription") }}</p>

            <form @submit.prevent="handleLogin" class="space-y-6">
              <!-- field -->
              <div class="relative">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                  {{ $t("auth.form.email") }}:
                  <span class="text-red-500">*</span>
                </label>
                <Field v-slot="{ field, errorMessage, meta }" name="email" :validate-on-input="true">
                  <input v-bind="field" id="email" type="email" :placeholder="$t('auth.form.emailPlaceholder')" :class="['w-full border rounded px-3 py-2 text-sm focus:outline-none pr-10 transition-colors', errorMessage && meta.touched ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-sky-500']" />
                  <small v-if="errorMessage && meta.touched" class="error-message">
                    {{ $t(errorMessage) }}
                  </small>
                </Field>
              </div>

              <div class="relative">
                <div class="flex justify-between items-center mb-2">
                  <label for="password" class="block text-sm font-medium text-gray-700">
                    {{ $t("auth.form.password") }}:
                    <span class="text-red-500">*</span>
                  </label>
                  <NuxtLink :to="TOFORGOT" class="text-right font-normal text-xs text-gray-600 hover:underline hover:text-sky-500" tabindex="-1">{{ $t("auth.form.forgot") }}</NuxtLink>
                </div>
                <Field v-slot="{ field, errorMessage, meta }" name="password" :validate-on-input="true">
                  <div class="relative">
                    <input v-bind="field" id="password" :type="showPassword ? 'text' : 'password'" :placeholder="$t('auth.form.passwordPlaceholder')" :class="['w-full border rounded px-3 py-2 text-sm focus:outline-none pr-10 transition-colors', errorMessage && meta.touched ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-sky-500']" />
                    <button type="button" class="absolute right-3 top-2 text-sm text-gray-500 hover:text-gray-700" @click="showPassword = !showPassword" tabindex="-1">
                      <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="w-4 h-4"></i>
                    </button>
                  </div>
                  <small v-if="errorMessage && meta.touched" class="error-message">
                    {{ $t(errorMessage) }}
                  </small>
                </Field>
              </div>
              <div class="flex items-center">
                <Field v-slot="{ field }" name="rememberMe" type="checkbox" :value="true" :unchecked-value="false">
                  <input v-bind="field" id="rememberMe" :value="true" type="checkbox" class="h-4 w-4 text-teal-600 border-gray-300 rounded" />
                </Field>
                <label for="rememberMe" class="ml-2 block text-sm text-gray-700">{{ $t("auth.form.remember") }}</label>
              </div>
              <Button
                type="submit"
                :label="$t('admin.common.form.submit')"
                :loading="isLoading"
                unstyled
                class="w-full text-sm font-semibold flex justify-center py-2 px-4 rounded shadow-sm text-white bg-teal-500 hover:bg-teal-600 focus:outline-none mt-5 disabled:opacity-50"
                :pt="{
                  loadingicon: 'w-3 h-3 mr-2 mt-1',
                }"
              />
              <div class="text-center mt-4">
                <p class="text-xs text-gray-600 mb-4">{{ $t("auth.orLoginWith") }}</p>
                <div class="flex justify-center gap-2">
                  <a href="#" tabindex="-1" class="bg-blue-600 text-white w-8 h-8 flex items-center justify-center rounded-full hover:bg-blue-700"><i class="fab fa-facebook-f"></i></a>
                  <a href="#" tabindex="-1" class="bg-red-600 text-white w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-700"><i class="fab fa-google"></i></a>
                  <a href="#" tabindex="-1" class="bg-gray-800 text-white w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-900"><i class="fab fa-github"></i></a>
                  <a href="#" tabindex="-1" class="bg-sky-500 text-white w-8 h-8 flex items-center justify-center rounded-full hover:bg-sky-600"><i class="fab fa-twitter"></i></a>
                </div>
              </div>
            </form>

            <p class="mt-6 text-center text-sm text-gray-600">
              {{ $t("auth.noAccount") }}
              <NuxtLink :to="TOREGISTER" class="text-cyan-600 hover:underline">{{ $t("auth.register.title") }}</NuxtLink>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import { TOREGISTER, TOFORGOT } from "~/utils/constants";
import { useLogin } from "~/composables/auth/useLogin";
import { useAuthSlider } from "~/composables/useAuthSlider";

// States
const showPassword = ref(false);
const { authOptions, loginItems } = useAuthSlider();

const { handleLogin, isLoading } = useLogin();

definePageMeta({
  middleware: "auth-redirect",
  layout: "auth",
});
</script>

<style>
.error-message {
  color: red;
  font-size: 12px;
  font-weight: 400;
}

.transition-colors {
  transition-property: color, background-color, border-color;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
</style>
