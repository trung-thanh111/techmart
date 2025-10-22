<template>
  <footer class="footer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">© {{ year }} {{ $t('admin.footer.copyright') }} TechMart.</div>
        <div class="col-sm-6">
          <div class="text-sm-end d-none d-sm-block">{{ $t('admin.footer.designedBy') }}</div>
        </div>
      </div>
    </div>
  </footer>
</template>
<script setup>
const year = new Date().getFullYear();
</script>
