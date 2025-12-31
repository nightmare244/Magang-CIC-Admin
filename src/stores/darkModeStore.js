import { defineStore } from 'pinia';

export const useDarkModeStore = defineStore('darkMode', {
  state: () => ({
    isDark: localStorage.getItem('darkMode') === 'true',
  }),
  actions: {
    toggleDarkMode() {
      this.isDark = !this.isDark;
      localStorage.setItem('darkMode', this.isDark.toString());
      document.documentElement.classList.toggle('dark', this.isDark);  // Terapkan kelas 'dark' secara global
    },
    initializeDarkMode() {
      document.documentElement.classList.toggle('dark', this.isDark);
    }
  }
});
