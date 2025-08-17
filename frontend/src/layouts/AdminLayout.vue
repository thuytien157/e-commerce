<template>
    <a-layout style="min-height: 100vh">
        <TheSidebar />
        <a-layout>

      <a-layout-content style="margin: 24px 16px 0;">
        <div :style="{ padding: '24px', background: '#fff', minHeight: '360px' }">
          <router-view></router-view>
        </div>
      </a-layout-content>

    </a-layout>
    </a-layout>
    
</template>

<script setup>
import TheSidebar from '@/component/client/TheSidebar.vue';
</script>

<style scoped>
.admin-layout-container {
    display: flex;
    min-height: 100vh;
    /* background-color: #f0f2f5; */
}

.admin-content-wrapper {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.admin-main-content {
    flex-grow: 1;
    padding: 20px;
}
</style>