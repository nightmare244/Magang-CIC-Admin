/*
 * Pinia Store untuk mengelola "Keranjang Peminjaman" di sisi frontend.
 * Ini TIDAK terhubung ke backend sampai pengguna checkout.
 */
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useCartStore = defineStore('peminjamanCart', () => {
  // === STATE ===
  // Mengambil data keranjang dari localStorage agar tidak hilang saat refresh
  const items = ref(JSON.parse(localStorage.getItem('cartItems')) || []);

  // === GETTERS ===
  // Menghitung jumlah total barang di keranjang
  const totalItems = computed(() => items.value.length);
  
  // Mengecek apakah barang TERTENTU sudah ada di keranjang
  const isItemInCart = computed(() => {
    return (itemId) => items.value.some(item => item.id === itemId);
  });

  // === ACTIONS ===

  // Fungsi untuk menyimpan ke localStorage
  function saveToLocalStorage() {
    localStorage.setItem('cartItems', JSON.stringify(items.value));
  }

  /**
   * Menambahkan barang ke keranjang.
   * 'barang' adalah objek dari API (minimal harus ada { id, nama, kode_barang }).
   */
  function addItem(barang) {
    // Cek agar barang tidak duplikat
    if (!isItemInCart.value(barang.id)) {
      items.value.push(barang);
      saveToLocalStorage();
      alert(`"${barang.nama}" telah ditambahkan ke keranjang.`);
    } else {
      alert('Barang ini sudah ada di keranjang.');
    }
  }

  /**
   * Menghapus barang dari keranjang berdasarkan ID-nya.
   */
  function removeItem(itemId) {
    items.value = items.value.filter(item => item.id !== itemId);
    saveToLocalStorage();
  }

  /**
   * Mengosongkan seluruh keranjang (setelah checkout/submit).
   */
  function clearCart() {
    items.value = [];
    localStorage.removeItem('cartItems');
  }

  return {
    items,
    totalItems,
    isItemInCart,
    addItem,
    removeItem,
    clearCart,
  };
});