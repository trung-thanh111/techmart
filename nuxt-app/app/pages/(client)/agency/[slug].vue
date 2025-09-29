<template>
  <div class="main-content max-w-7xl mx-auto">
    <Breadcrumb :crumbs="breadcrumbs" />
    <section v-if="isLoading" class="bg-white rounded-lg shadow-sm overflow-hidden md:mb-6 mb-4 animate-pulse">
      <div class="relative h-64 md:h-80 bg-gray-200" />
    </section>
    <section v-else-if="!agency" class="bg-white rounded-lg shadow-sm overflow-hidden md:mb-6 mb-4">
      <div class="p-6 text-center text-gray-600">{{ $t('client.agency.notFound') }}</div>
    </section>
    <section v-else class="bg-white rounded-lg shadow-sm overflow-hidden md:mb-6 mb-4">
      <div class="relative h-64 md:h-80">
        <NuxtImg :src="coverUrl" :alt="agency?.name || 'cover'" class="w-full h-full object-cover" format="webp" sizes="sm:100vw md:100vw lg:100vw" densities="x1 x2" loading="lazy" :placeholder="10" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
          <h1 class="text-2xl md:text-3xl font-bold mb-2">{{ agency?.name || $t('client.agency.agent') }}</h1>
          <p v-if="agency?.address" class="text-white/90 text-sm md:text-base">{{ agency.address }}</p>
        </div>
      </div>
    </section>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:mb-8 mb-4">
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="md:text-lg text-base font-semibold text-gray-700 mb-4 flex items-center">{{ $t('client.agency.contactInfo') }}</h2>
          <div class="space-y-3 text-sm">
            <div v-if="agency?.phone" class="flex items-center">
              <i class="fas fa-phone text-sky-500 w-5 mr-3"></i>
              <a :href="`tel:${agency.phone}`" class="text-sky-600 hover:underline font-medium">{{ formatPhone(agency.phone) }}</a>
            </div>
            <div v-if="agency?.email" class="flex items-center">
              <i class="fas fa-envelope text-sky-500 w-5 mr-3"></i>
              <a :href="`mailto:${agency.email}`" class="text-sky-600 hover:underline">{{ agency.email }}</a>
            </div>
            <div v-if="safeWebsite" class="flex items-center">
              <i class="fas fa-globe text-sky-500 w-5 mr-3"></i>
              <a :href="safeWebsite" target="_blank" rel="noopener" class="text-sky-600 hover:underline">{{ safeWebsite }}</a>
            </div>
            <div v-if="agency?.address" class="flex items-center">
              <i class="fas fa-map-marker-alt text-sky-500 w-5 mr-3"></i>
              <span class="text-gray-700">{{ agency.address }}</span>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm md:p-6 p-4 text-sm">
          <h2 class="md:text-lg text-base font-semibold text-gray-700 mb-4 flex items-center">{{ $t('client.agency.workingHours') }}</h2>
          <div v-if="workingHours.length" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="item in workingHours" :key="item.id" class="flex justify-between items-center py-2 shadow rounded-md p-2">
              <span class="font-medium text-gray-700">{{ item.dayOfWeek }}</span>
              <span class="text-gray-600">{{ item.openTime }} - {{ item.closeTime }}</span>
            </div>
          </div>
          <div v-else class="text-gray-500">{{ $t('client.agency.noWorkingHours') }}</div>
        </div>
      </div>
      <div class="space-y-6">
        <div class="bg-white rounded-lg text-sm shadow-sm md:p-6 p-4">
          <h2 class="md:text-lg text-base font-semibold text-gray-700 mb-4 flex items-center">{{ $t('client.agency.location') }}</h2>
          <div class="rounded-lg h-64 mb-4 overflow-hidden">
            <iframe v-if="mapEmbedSrc" :src="mapEmbedSrc" class="w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" />
            <div v-else class="bg-gray-200 h-full flex items-center justify-center">
              <div class="text-center text-gray-500">
                <i class="fas fa-map-marked-alt text-4xl mb-2"></i>
                <p>{{ $t('client.agency.interactiveMap') }}</p>
              </div>
            </div>
          </div>
          <NuxtLink v-if="internalMapLink" :to="internalMapLink" class="w-full bg-sky-500 text-white py-2 px-4 rounded-lg hover:bg-sky-600 transition flex items-center justify-center">
            <i class="fas fa-directions mr-2"></i>
            {{ $t('client.agency.directions') }}
          </NuxtLink>
          <a v-else-if="externalMapLink" :href="externalMapLink" target="_blank" rel="noopener" class="w-full bg-sky-500 text-white py-2 px-4 rounded-lg hover:bg-sky-600 transition flex items-center justify-center">
            <i class="fas fa-directions mr-2"></i>
            {{ $t('client.agency.directions') }}
          </a>
          <a v-else-if="externalMapLinkFromAddress" :href="externalMapLinkFromAddress" target="_blank" rel="noopener" class="w-full bg-sky-500 text-white py-2 px-4 rounded-lg hover:bg-sky-600 transition flex items-center justify-center">
            <i class="fas fa-directions mr-2"></i>
            {{ $t('client.agency.directions') }}
          </a>
          <button v-else disabled class="w-full bg-gray-300 text-gray-600 py-2 px-4 rounded-lg cursor-not-allowed flex items-center justify-center">
            <i class="fas fa-ban mr-2"></i>
            {{ $t('client.agency.noMapLink') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from "vue";
import { useRoute } from "vue-router";
import Breadcrumb from "~/components/shared/navigation/Breadcrumb.vue";
import { useAgency } from "~/composables/client/agency/useAgency";

const { fetchBySlug, agency, isLoading, getCoverUrl, getSafeWebsite, getWorkingHours, getMapEmbedSrcFromAddress, getExternalMapLinkFromAddress, formatPhone } = useAgency();

const slug = computed(() => String(useRoute().params.slug || ""));

const coverUrl = computed(() => getCoverUrl());

const safeWebsite = computed(() => getSafeWebsite());

const workingHours = computed(() => getWorkingHours());

const mapField = computed(() => (agency.value as any)?.iframeMap || "");
const mapEmbedSrc = computed(() => getMapEmbedSrcFromAddress());
const internalMapLink = computed(() => {
  const v = mapField.value as string;
  if (!v) return "";
  // coi là internal nếu bắt đầu bằng "/"
  return v.startsWith("/") ? v : "";
});
const externalMapLink = computed(() => {
  const v = mapField.value as string;
  if (!v) return "";
  return /^https?:\/\//i.test(v) ? v : "";
});
const externalMapLinkFromAddress = computed(() => getExternalMapLinkFromAddress());

onMounted(async () => {
  if (!slug.value) return;
  await fetchBySlug(slug.value);
});

const breadcrumbs = computed(() => [
  { label: $t('client.agency.breadcrumbDetail'), href: "/agency" },
  { label: agency.value?.name || slug.value, href: `/agency/${slug.value}` },
]);

definePageMeta({ layout: "client" });
</script>
