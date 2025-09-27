<template>
  <div class="flex flex-wrap justify-between items-stretch bg-white shadow-sm overflow-hidden mb-6">
    <div v-for="(item, idx) in commitments" :key="item.title" class="flex-1 min-w-[180px] flex items-center text-left p-2" :class="{ 'border-l border-gray-200': idx !== 0 }">
      <div class="text-sky-500 text-4xl mr-2 flex items-center">
        <i :class="item.icon" />
      </div>
      <div>
        <h3 class="text-sm font-semibold mb-1">{{ $t(item.title) }}</h3>
        <p class="text-xs text-gray-500">{{ $t(item.desc) }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
const commitments = [
  { title: "client.commitment.quality.title", desc: "client.commitment.quality.desc", icon: "fas fa-certificate" },
  { title: "client.commitment.warranty.title", desc: "client.commitment.warranty.desc", icon: "fas fa-shield-alt" },
  { title: "client.commitment.inspect.title", desc: "client.commitment.inspect.desc", icon: "fas fa-box-open" },
  { title: "client.commitment.return.title", desc: "client.commitment.return.desc", icon: "fas fa-exchange-alt" },
  { title: "client.commitment.refund.title", desc: "client.commitment.refund.desc", icon: "fas fa-undo" },
];
</script>

<style scoped>
.flex-1 {
  flex: 1 1 0%;
}
@media (max-width: 900px) {
  .flex-1 {
    min-width: 50%;
    border-bottom: 1px solid #e5e7eb;
    border-left: none !important;
  }
  .flex-1:last-child {
    border-bottom: none;
  }
}
@media (max-width: 600px) {
  .flex-1 {
    min-width: 100%;
    border-bottom: 1px solid #e5e7eb;
    border-left: none !important;
  }
  .flex-1:last-child {
    border-bottom: none;
  }
}
</style>
