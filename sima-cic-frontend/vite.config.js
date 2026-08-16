import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import path from "path";  // Pastikan path diimpor

export default defineConfig({
  base: "/", // Base URL untuk root subdomain sima.ciwangunparkland.id
  plugins: [vue()],
  resolve: {
    alias: {
      vue: "vue/dist/vue.esm-bundler.js",
      "@": path.resolve(__dirname, "src"),  // Pastikan menggunakan path.resolve untuk alias
    },
  },
});
