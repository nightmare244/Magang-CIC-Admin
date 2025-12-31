/** @type {import('tailwindcss').Config} */
export default {
  darkMode: "class", // ⬅ WAJIB untuk dark mode berbasis class
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      fontFamily: {
        poppins: ["Poppins", "sans-serif"],
      },
      colors: {
        army: {
          DEFAULT: "#4b5d3a",
          light: "#60744b",
          dark: "#3c4a2e",
        },
      },
    },
  },
  plugins: [],
};
