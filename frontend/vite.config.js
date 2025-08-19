import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
// Tạm thời bỏ dòng này để kiểm tra
// import vueDevTools from 'vite-plugin-vue-devtools' 

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    // và tạm thời bỏ plugin này
    // vueDevTools(), 
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
})