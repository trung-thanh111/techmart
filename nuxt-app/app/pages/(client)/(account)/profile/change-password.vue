<template>
  <div class="w-full">
    <h2 class="text-xl md:text-2xl font-semibold text-gray-600 mb-1">{{ $t("client.changePassword.title") }}</h2>
    <p class="text-xs text-gray-500 md:mb-10">{{ $t("client.changePassword.subtitle") }}</p>
    <form class="space-y-4 md:space-y-6" @submit.prevent.stop="onSubmit">
      <div class="grid grid-cols-1 lg:grid-cols-10 gap-4">
        <div class="lg:col-span-6 space-y-4">
          <div class="relative">
            <label for="passwordOld" class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t("client.changePassword.currentPassword") }}:
              <span class="text-red-500">*</span>
            </label>
            <Field v-slot="{ field, errorMessage, meta }" name="passwordOld" :validate-on-input="true">
              <div class="relative">
                <input v-bind="field" id="passwordOld" :type="showPasswordOld ? 'text' : 'password'" :placeholder="$t('client.changePassword.currentPasswordPlaceholder')" :class="['w-full border rounded px-3 py-2 text-sm focus:outline-none pr-10 transition-colors', errorMessage && meta.touched ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-sky-500']" />
                <button type="button" class="absolute right-3 top-2 text-sm text-gray-500 hover:text-gray-700" @click="showPasswordOld = !showPasswordOld" tabindex="-1">
                  <i :class="showPasswordOld ? 'fas fa-eye-slash' : 'fas fa-eye'" class="w-4 h-4"></i>
                </button>
              </div>
              <small v-if="errorMessage && meta.touched" class="error-message">
                {{ errorMessage }}
              </small>
            </Field>
          </div>
          <div class="relative">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t("client.changePassword.newPassword") }}:
              <span class="text-red-500">*</span>
            </label>
            <Field v-slot="{ field, errorMessage, meta }" name="password" :validate-on-input="true">
              <div class="relative">
                <input v-bind="field" id="password" :type="showPassword ? 'text' : 'password'" :placeholder="$t('client.changePassword.newPasswordPlaceholder')" :class="['w-full border rounded px-3 py-2 text-sm focus:outline-none pr-10 transition-colors', errorMessage && meta.touched ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-sky-500']" />
                <button type="button" class="absolute right-3 top-2 text-sm text-gray-500 hover:text-gray-700" @click="showPassword = !showPassword" tabindex="-1">
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="w-4 h-4"></i>
                </button>
              </div>
              <small v-if="errorMessage && meta.touched" class="error-message">
                {{ errorMessage }}
              </small>
            </Field>
          </div>
          <div class="relative">
            <label for="passwordConfirmation" class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t("client.changePassword.confirmPassword") }}:
              <span class="text-red-500">*</span>
            </label>
            <Field v-slot="{ field, errorMessage, meta }" name="passwordConfirmation" :validate-on-input="true">
              <div class="relative">
                <input v-bind="field" id="passwordConfirmation" :type="showConfirmPassword ? 'text' : 'password'" :placeholder="$t('client.changePassword.confirmPasswordPlaceholder')" :class="['w-full border rounded px-3 py-2 text-sm focus:outline-none pr-10 transition-colors', errorMessage && meta.touched ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-sky-500']" />
                <button type="button" class="absolute right-3 top-2 text-sm text-gray-500 hover:text-gray-700" @click="showConfirmPassword = !showConfirmPassword" tabindex="-1">
                  <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="w-4 h-4"></i>
                </button>
              </div>
              <small v-if="errorMessage && meta.touched" class="error-message">
                {{ errorMessage }}
              </small>
            </Field>
          </div>
          <div class="flex justify-end gap-3 mt-4 sticky bottom-6 z-50">
            <Button
              type="submit"
              :label="$t('client.changePassword.submit')"
              :loading="isSubmitting"
              unstyled
              class="max-w-[160px] text-sm py-1.5 px-4 rounded shadow-sm text-white bg-teal-500 hover:bg-teal-600 focus:outline-none mt-5 disabled:opacity-50"
              :pt="{
                loadingicon: 'w-3 h-3 mr-2 mt-1',
              }"
            />
          </div>
          <p class="mt-6 text-center text-sm text-gray-600">
            {{ $t("client.changePassword.forgotPassword") }}
            <NuxtLink :to="TOFORGOT" class="text-cyan-600 hover:underline">{{ $t("client.changePassword.forgotPasswordLink") }}</NuxtLink>
          </p>
        </div>
        <div class="lg:col-span-4 bg-gray-100 rounded-lg p-4 space-y-2 text-sm">
          <p class="text-gray-700 font-normal">{{ $t("client.changePassword.requirements") }}</p>
          <ul class="space-y-1">
            <li class="flex items-center gap-2" :class="hasLen ? 'text-emerald-600' : 'text-[#f06548]'">
              <span class="w-4 text-center">{{ hasLen ? "✔" : "✖" }}</span>
              {{ $t("client.changePassword.minLength", { count: 8 }) }}
            </li>
            <li class="flex items-center gap-2" :class="hasLower ? 'text-emerald-600' : 'text-[#f06548]'">
              <span class="w-4 text-center">{{ hasLower ? "✔" : "✖" }}</span>
              {{ $t("client.changePassword.lowercase") }}
            </li>
            <li class="flex items-center gap-2" :class="hasUpper ? 'text-emerald-600' : 'text-[#f06548]'">
              <span class="w-4 text-center">{{ hasUpper ? "✔" : "✖" }}</span>
              {{ $t("client.changePassword.uppercase") }}
            </li>
            <li class="flex items-center gap-2" :class="hasDigit ? 'text-emerald-600' : 'text-[#f06548]'">
              <span class="w-4 text-center">{{ hasDigit ? "✔" : "✖" }}</span>
              {{ $t("client.changePassword.number") }}
            </li>
          </ul>
        </div>
      </div>
    </form>
  </div>
</template>
<script setup lang="ts">
import { Field } from "vee-validate";
import { useChangePassword } from "~/composables/auth/useChangePassword";
import { TOFORGOT } from "~/utils/constants";

const { t } = useI18n();

definePageMeta({
  layout: "client",
});

const showPasswordOld = ref(false);
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const { onSubmit, isSubmitting, values } = useChangePassword();

const passwordValue = computed(() => values.password || "");

const hasLen = computed(() => passwordValue.value.length >= 8);
const hasLower = computed(() => /[a-z]/.test(passwordValue.value));
const hasUpper = computed(() => /[A-Z]/.test(passwordValue.value));
const hasDigit = computed(() => /\d/.test(passwordValue.value));
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
