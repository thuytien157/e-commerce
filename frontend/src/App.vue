<template>
  <TheHeader/>
  <router-view></router-view>
  <TheFooter/>
</template>
<script setup>
import TheHeader from './component/TheHeader.vue';
import TheFooter from './component/TheFooter.vue';

</script>
<style scoped></style>
