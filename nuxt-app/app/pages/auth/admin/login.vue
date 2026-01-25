<template>
  <div class="main-content rounded-lg h-screen overflow-hidden">
    <div class="relative w-full h-full">
      <NuxtImg src="/client/images/bg-auth.png" alt="image-background" class="w-full h-full object-cover" loading="lazy" />
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="flex flex-col lg:flex-row max-w-4xl w-full mx-auto overflow-hidden lg:h-[560px] shadow-2xl rounded-lg">
          <div class="relative lg:w-1/2 hidden lg:block h-full">
            <NuxtImg src="/client/images/showroom/image-showroom.jpg" alt="background đăng nhập" class="absolute inset-0 w-full h-full object-cover z-0" loading="lazy" />
            <div class="absolute inset-0 bg-teal-950/70 z-10"></div>
            <div class="relative top-10 z-20 flex flex-col p-12 text-white text-center">
              <NuxtImg src="/shared/images/logo/logo-website.png" alt="Logo" class="w-48 h-auto mb-8 mx-auto filter drop-shadow-md pt-5" loading="lazy" />
            </div>
          </div>

          <div class="flex flex-col justify-center p-8 w-full lg:w-1/2 bg-white relative z-20">
            <h2 class="text-2xl font-bold text-teal-700 mb-2 text-center">Đăng nhập Quản trị</h2>
            <p class="text-sm text-gray-500 mb-8 text-center">Vui lòng nhập thông tin tài khoản của bạn</p>
            <form @submit.prevent="handleLogin" class="space-y-6">
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
            </form>

            <div class="mt-8 text-center">
              <span class="text-xs text-gray-400 border-t border-gray-200 pt-2 px-4">TechMart Admin Panel</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import { useAdminLogin } from "~/composables/auth/useAdminLogin";

const { handleLogin, isLoading } = useAdminLogin();
const showPassword = ref(false);

definePageMeta({
  middleware: "guest",
  layout: "auth",
});
</script>
