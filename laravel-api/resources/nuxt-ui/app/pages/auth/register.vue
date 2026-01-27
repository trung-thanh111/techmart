<template>
    <div class="main-content rounded-lg h-screen overflow-hidden">
        <div class="relative w-full h-full">
            <NuxtImg src="/client/images/bg-auth.png" alt="image-background" class="w-full h-full object-cover" loading="lazy" />
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="flex flex-col lg:flex-row max-w-4xl w-full mx-auto overflow-hidden lg:h-[650px]">
                    <div class="relative lg:w-1/2 hidden lg:block h-full">
                        <NuxtImg src="/client/images/showroom/image-showroom.jpg" alt="Nền đăng nhập" class="absolute inset-0 w-full h-full object-cover z-0" loading="lazy" />
                        <div class="absolute inset-0 bg-sky-200/80 opacity-90 z-10"></div>
                        <div class="relative z-40 flex flex-col p-6 lg:p-8 h-full">
                            <NuxtImg src="/shared/images/logo/logo-website.png" alt="Logo" class="w-40 h-8 mb-6" loading="lazy" />
                            <div class="flex-grow"></div>
                            <i class="fas fa-quote-left text-4xl text-teal-500 mb-2"></i>
                            <div class="overflow-blur-content absolute bottom-0 left-0 w-full h-full max-h-40 transparent-img bg-gradient-to-t from-gray-500"></div>
                            <Splide :options="authOptions" class="w-full">
                                <SplideSlide v-for="(item, index) in registerItems" :key="index">
                                    <div class="italic text-lg text-white text-center min-h-[60px] flex items-center justify-center">"{{ item.title }}"</div>
                                </SplideSlide>
                            </Splide>
                        </div>
                    </div>
                    <div class="flex flex-col justify-center p-6 lg:p-8 w-full lg:w-1/2 bg-white max-w-xs sm:max-w-sm md:max-w-md mx-auto">
                        <h2 class="text-xl font-semibold text-cyan-600 mb-1 text-center">{{ $t("auth.registerTitle") }}</h2>
                        <p class="text-sm text-gray-500 mb-6 text-center">{{ $t("auth.registerDescription") }}</p>

                        <form @submit.prevent="handleRegister" class="space-y-2.5">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $t("auth.form.fullName") }}:
                                    <span class="text-red-500">*</span>
                                </label>
                                <Field v-slot="{ field, errorMessage, meta }" name="name" :validate-on-input="true">
                                    <input v-bind="field" id="name" type="text" :placeholder="$t('auth.form.fullNamePlaceholder')" :class="['w-full border rounded px-3 py-2 text-sm focus:outline-none transition-colors', errorMessage && meta.touched ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-sky-500']" />
                                    <small v-if="errorMessage && meta.touched" class="error-message">
                                        {{ errorMessage }}
                                    </small>
                                </Field>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $t("auth.form.email") }}:
                                    <span class="text-red-500">*</span>
                                </label>
                                <Field v-slot="{ field, errorMessage, meta }" name="email" :validate-on-input="true">
                                    <input v-bind="field" id="email" type="email" :placeholder="$t('auth.form.emailPlaceholder')" :class="['w-full border rounded px-3 py-2 text-sm focus:outline-none transition-colors', errorMessage && meta.touched ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-sky-500']" />
                                    <small v-if="errorMessage && meta.touched" class="error-message">
                                        {{ errorMessage }}
                                    </small>
                                </Field>
                            </div>

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
                                <label for="agreeTerms" class="ml-2 block text-xs text-gray-700">
                                    {{ $t("auth.agreeTerms") }}
                                </label>
                            </div>
                            <div class="flex justify-center">
                                <span v-if="verifyNotice" class="inline-flex items-center rounded bg-teal-100 text-teal-700 text-[10px] px-2 py-0.5 shadow border border-teal-200 select-none">
                                    {{ verifyNotice }}
                                </span>
                            </div>
                            <Button
                                type="submit"
                                :label="$t('auth.form.registerButton')"
                                :loading="isLoading"
                                unstyled
                                class="w-full text-sm font-semibold flex justify-center py-2 px-4 rounded shadow-sm text-white bg-teal-500 hover:bg-teal-600 focus:outline-none mt-5 disabled:opacity-50"
                                :pt="{
                                    loadingicon: 'w-3 h-3 mr-2 mt-1',
                                }"
                            />

                            <div class="text-center mt-4">
                                <p class="text-xs text-gray-600 mb-2">{{ $t("auth.orRegisterWith") }}</p>
                                <div class="flex justify-center gap-2">
                                    <a href="#" tabindex="-1" class="bg-blue-600 text-white w-8 h-8 flex items-center justify-center rounded-full hover:bg-blue-700"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#" tabindex="-1" class="bg-red-600 text-white w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-700"><i class="fab fa-google"></i></a>
                                    <a href="#" tabindex="-1" class="bg-gray-800 text-white w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-900"><i class="fab fa-github"></i></a>
                                    <a href="#" tabindex="-1" class="bg-sky-500 text-white w-8 h-8 flex items-center justify-center rounded-full hover:bg-sky-600"><i class="fab fa-twitter"></i></a>
                                </div>
                            </div>
                        </form>
                        <p class="mt-4 text-center text-sm text-gray-600">
                            {{ $t("auth.hasAccount") }}
                            <NuxtLink :to="TOLOGIN" class="text-cyan-600 hover:underline">{{ $t("auth.login") }}</NuxtLink>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { Field } from "vee-validate";
import { TOLOGIN } from "~/utils/constants";
import { useAuthSlider } from "~/composables/useAuthSlider";
import { useRegister } from "~/composables/auth/useRegister";

// States
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const { authOptions, registerItems } = useAuthSlider();
const { handleRegister, isLoading, verifyNotice } = useRegister();

definePageMeta({
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
