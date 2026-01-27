<template>
  <div class="main-content rounded-lg">
    <div class="relative w-full h-full">
      <NuxtImg src="/client/images/bg-auth.png" alt="image-background" class="w-full h-full object-cover" loading="lazy" />
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="flex flex-col lg:flex-row max-h-[800px] max-w-lg mx-auto shadow-lg">
          <div class="flex flex-col justify-center p-6 lg:p-8 w-full bg-white">
            <div class="w-full">
              <h2 class="text-xl text-center font-semibold text-cyan-600 mb-1">{{ $t("client.common.forgotPassword") }}</h2>
              <p class="text-sm text-center text-gray-500 mb-8">{{ $t("client.common.verifyInfo") }}</p>
              <span class="inline-flex items-center rounded-md bg-[#fef4e4] px-3 py-2 text-xs text-[#d29c40] mb-5">{{ $t("client.common.enterCorrectEmail") }}</span>
              <form @submit.prevent="handleVerify" novalidate class="space-y-4">
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

                <Button
                  type="submit"
                  :label="$t('client.common.verify')"
                  :loading="isLoading"
                  unstyled
                  class="w-full text-sm font-semibold flex justify-center py-2 px-4 rounded shadow-sm text-white bg-teal-500 hover:bg-teal-600 focus:outline-none mt-5 disabled:opacity-50"
                  :pt="{
                    loadingicon: 'w-3 h-3 mr-2 mt-1',
                  }"
                />
              </form>

              <p class="mt-6 text-center text-sm text-gray-600">
                {{ $t("client.common.alreadyHavePassword") }}
                <NuxtLink :to="TOLOGIN" class="text-cyan-600 hover:underline">{{ $t("client.common.clickHere") }}</NuxtLink>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { definePageMeta } from "#imports";
import { Field } from "vee-validate";
import { useForgot } from "~/composables/auth/useForgot";
import { TOLOGIN } from "~/utils/constants";

definePageMeta({
  layout: "client",
});

const { handleVerify, isLoading } = useForgot();
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
