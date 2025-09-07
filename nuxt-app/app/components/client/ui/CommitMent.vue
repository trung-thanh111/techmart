<template>
  <div class="flex flex-wrap justify-between items-stretch bg-white shadow-sm overflow-hidden mb-6">
    <div v-for="(item, idx) in commitments" :key="item.title" class="flex-1 min-w-[180px] flex items-center text-left p-2" :class="{ 'border-l border-gray-200': idx !== 0 }">
      <div class="text-sky-500 text-4xl mr-2 flex items-center">
        <i :class="item.icon" />
      </div>
      <div>
        <h3 class="text-sm font-semibold mb-1">{{ item.title }}</h3>
        <p class="text-xs text-gray-500">{{ item.desc }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
const commitments = [
  {
    title: "Cam kết chất lượng",
    desc: "Sản phẩm được kiểm định chất lượng trước khi giao",
    icon: "fas fa-certificate",
  },
  {
    title: "Bảo hành chính hãng",
    desc: "Tất cả sản phẩm đều có bảo hành chính hãng",
    icon: "fas fa-shield-alt",
  },
  {
    title: "Kiểm tra hàng",
    desc: "Cho phép mở kiểm tra trước khi thanh toán",
    icon: "fas fa-box-open",
  },
  {
    title: "Đổi trả miễn phí",
    desc: "Đổi trả trong vòng 30 ngày không cần lý do",
    icon: "fas fa-exchange-alt",
  },
  {
    title: "Hoàn trả 100%",
    desc: "Hoàn tiền nếu sản phẩm lỗi kỹ thuật trong 7 ngày",
    icon: "fas fa-undo",
  },
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
