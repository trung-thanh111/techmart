<template>
  <div class="main-content rounded-lg">
    <div class="relative w-full h-full">
      <NuxtImg src="/images/bg-auth.png" alt="image-background" class="w-full h-full object-cover" loading="lazy" />
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="flex flex-col lg:flex-row max-h-[800px] max-w-lg mx-auto shadow-lg" style="width: 30%">
          <div class="flex flex-col justify-center p-6 lg:p-8 w-full bg-white">
            <div class="w-full">
              <h2 class="text-xl text-center font-semibold text-cyan-600 mb-1">{{ $t("common.resetPassword") }}</h2>
              <p class="text-sm text-center text-gray-500 mb-8">{{ $t("common.enterNewPassword") }}</p>
              <form class="space-y-4 md:space-y-6" @submit.prevent.stop="onSubmit">
                <div class="relative">
                  <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t("auth.form.password") }}:
                    <span class="text-red-500">*</span>
                  </label>
                  <Field v-slot="{ field, errorMessage, meta }" name="password" :validate-on-input="true">
                    <div class="relative">
                      <input v-bind="field" id="password" :type="showPassword ? 'text' : 'password'" :placeholder="$t('auth.form.passwordPlaceholder')" :class="['w-full border rounded px-3 py-2 text-sm focus:outline-none pr-10 transition-colors', errorMessage && meta.touched ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-sky-500']" />
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
                    {{ $t("auth.form.confirmPassword") }}:
                    <span class="text-red-500">*</span>
                  </label>
                  <Field v-slot="{ field, errorMessage, meta }" name="passwordConfirmation" :validate-on-input="true">
                    <div class="relative">
                      <input v-bind="field" id="passwordConfirmation" :type="showConfirmPassword ? 'text' : 'password'" :placeholder="$t('auth.form.confirmPasswordPlaceholder')" :class="['w-full border rounded px-3 py-2 text-sm focus:outline-none pr-10 transition-colors', errorMessage && meta.touched ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-sky-500']" />
                      <button type="button" class="absolute right-3 top-2 text-sm text-gray-500 hover:text-gray-700" @click="showConfirmPassword = !showConfirmPassword" tabindex="-1">
                        <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="w-4 h-4"></i>
                      </button>
                    </div>
                    <small v-if="errorMessage && meta.touched" class="error-message">
                      {{ errorMessage }}
                    </small>
                  </Field>
                </div>

                <div class="flex items-center">
                  <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded" />
                  <label for="remember" class="ml-2 block text-sm text-gray-900">{{ $t("auth.form.remember") }}</label>
                </div>

                <div class="bg-gray-100 rounded-lg p-4 space-y-2 text-sm">
                  <p class="text-gray-700 font-normal">{{ $t("changePassword.requirements") }}</p>
                  <ul class="space-y-1">
                    <li class="flex items-center gap-2" :class="hasLen ? 'text-emerald-600' : 'text-[#f06548]'">
                      <span class="w-4 text-center">{{ hasLen ? "✔" : "✖" }}</span>
                      {{ $t("changePassword.minLength", { count: 8 }) }}
                    </li>
                    <li class="flex items-center gap-2" :class="hasLower ? 'text-emerald-600' : 'text-[#f06548]'">
                      <span class="w-4 text-center">{{ hasLower ? "✔" : "✖" }}</span>
                      {{ $t("changePassword.lowercase") }}
                    </li>
                    <li class="flex items-center gap-2" :class="hasUpper ? 'text-emerald-600' : 'text-[#f06548]'">
                      <span class="w-4 text-center">{{ hasUpper ? "✔" : "✖" }}</span>
                      {{ $t("changePassword.uppercase") }}
                    </li>
                    <li class="flex items-center gap-2" :class="hasDigit ? 'text-emerald-600' : 'text-[#f06548]'">
                      <span class="w-4 text-center">{{ hasDigit ? "✔" : "✖" }}</span>
                      {{ $t("changePassword.number") }}
                    </li>
                  </ul>
                </div>

                <Button
                  type="submit"
                  :label="$t('common.confirm')"
                  :loading="isLoading"
                  unstyled
                  class="w-full text-sm font-semibold flex justify-center py-2 px-4 rounded shadow-sm text-white bg-teal-500 hover:bg-teal-600 focus:outline-none mt-5 disabled:opacity-50"
                  :pt="{
                    loadingicon: 'w-3 h-3 mr-2 mt-1',
                  }"
                />
              </form>

              <p class="mt-6 text-center text-sm text-gray-600">
                {{ $t("common.alreadyHavePassword") }}
                <NuxtLink :to="TOLOGIN" class="text-cyan-600 hover:underline">{{ $t("common.clickHere") }}</NuxtLink>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, computed } from "vue";
import { Field } from "vee-validate";
import { useForgotReset } from "~/composables/auth/useForgotReset";
import { TOLOGIN } from "~/utils/constants";
import { useAuthSlider } from "~/composables/useAuthSlider";

definePageMeta({
  layout: "auth",
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const { onSubmit, isLoading, values } = useForgotReset();
const { authOptions, forgotItems } = useAuthSlider();

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
