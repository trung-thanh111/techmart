<template>
  <div class="main-content rounded-lg">
    <div class="relative w-full h-full">
      <nuxt-img src="/client/images/bg-auth.png" alt="image-background" class="w-full h-full object-cover" loading="lazy" />
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="flex flex-col justify-center p-6 lg:p-8 w-full max-w-xs sm:max-w-sm md:max-w-md bg-white shadow-lg rounded-lg mx-auto">
          <div class="flex justify-center mb-6">
            <div class="w-20 h-20 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center p-4">
              <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                <path d="M64 112c-8.8 0-16 7.2-16 16l0 22.1L220.5 291.7c20.7 17 50.4 17 71.1 0L464 150.1l0-22.1c0-8.8-7.2-16-16-16L64 112zM48 212.2L48 384c0 8.8 7.2 16 16 16l384 0c8.8 0 16-7.2 16-16l0-171.8L322 328.8c-38.4 31.5-93.7 31.5-132 0L48 212.2zM0 128C0 92.7 28.7 64 64 64l384 0c35.3 0 64 28.7 64 64l0 256c0 35.3-28.7 64-64 64L64 448c-35.3 0-64-28.7-64-64L0 128z" />
              </svg>
            </div>
          </div>
          <h2 class="text-xl text-center font-semibold text-cyan-600 mb-2">Xác thực email của bạn!</h2>
          <p class="text-sm text-center text-gray-500 my-1">Hãy điền thông tin được gửi đến mail.</p>
          <p class="font-semibold text-sm text-gray-800 text-center mb-6">{{ values.email }}</p>
          <form class="space-y-4" @submit.prevent.stop="onSubmit">
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                OTP:
                <span class="text-red-500">*</span>
              </label>
              <Field v-slot="{ field, errorMessage, meta }" name="code" :validate-on-input="true">
                <div class="flex justify-center">
                  <InputOtp
                    :modelValue="field.value"
                    @update:modelValue="(val:any) => field.onChange(val)"
                    @blur="field.onBlur"
                    :length="6"
                    integerOnly
                    :pt="{
                      root: { class: 'justify-center' },
                      input: { class: 'p-inputtext-lg w-10 h-12 md:w-12 md:h-14 text-lg md:text-2xl' },
                    }"
                  />
                </div>
                <small v-if="errorMessage && meta.touched" class="error-message">{{ errorMessage }}</small>
              </Field>
            </div>

            <Button
              type="submit"
              label="Xác nhận"
              :loading="isLoading"
              unstyled
              class="w-full text-sm font-semibold flex justify-center py-2 px-4 rounded shadow-sm text-white bg-teal-500 hover:bg-teal-600 focus:outline-none mt-5 disabled:opacity-50"
              :pt="{
                loadingicon: 'w-3 h-3 mr-2 mt-1',
              }"
            />
            <p class="mt-4 text-center text-sm text-gray-600">
              Chờ đã, tôi chưa nhận được code?
              <button type="button" @click="resend" :disabled="canResendIn > 0" class="text-cyan-600 hover:underline disabled:opacity-50">
                Gửi lại
                <span v-if="canResendIn > 0">({{ canResendIn }})</span>
              </button>
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { Field } from "vee-validate";
import InputOtp from "primevue/inputotp";
import { useForgotVerify } from "~/composables/auth/useForgotVerify";

definePageMeta({
  layout: "client",
});

const { onSubmit, isLoading, resend, canResendIn, values } = useForgotVerify();
</script>

<style>
.error-message {
  color: red;
  font-size: 12px;
  font-weight: 400;
}
</style>
