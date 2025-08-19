<template>
  <a-layout style="min-height: 100vh">
    <TheSidebar />
    <a-layout class="admin-content-wrapper">
      <a-layout-content>
        <div class="admin-main-content">
          <router-view></router-view>
        </div>
      </a-layout-content>
    </a-layout>
  </a-layout>
</template>


<script setup>
import TheSidebar from "@/component/client/TheSidebar.vue";
</script>

<style scoped>
.admin-layout-container {
  display: flex;
  min-height: 100vh;
}

.admin-content-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  /* ngăn layout phồng */
}

.admin-main-content {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  /* scroll khi nội dung dài */
  max-height: 100vh;
  /* rất quan trọng để flex + scroll hoạt động */
  background: #fff;
  border-radius: 4px;
}
</style>
