import { defineStore } from "pinia";

export const useUiStore = defineStore("uiStore", {
  state: () => ({
    sidebarOpen: true,
    darkMode: localStorage.getItem("darkMode") === "true"
  }),

  actions: {
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen;
    },

    setDarkMode(value) {
      this.darkMode = value;
      localStorage.setItem("darkMode", value);

      if (value) {
        document.documentElement.classList.add("dark");
      } else {
        document.documentElement.classList.remove("dark");
      }
    },

    initDarkMode() {
      this.setDarkMode(this.darkMode);
    }
  }
});
