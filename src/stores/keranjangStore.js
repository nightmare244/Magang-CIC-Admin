import { defineStore } from 'pinia';

export const useKeranjangStore = defineStore('keranjang', {
    state: () => {
        const savedItems = JSON.parse(
            localStorage.getItem('peminjaman_keranjang')
        ) || [];

        return {
            items: savedItems.map(item => ({
                ...item,
                quantity_pinjam: item.quantity_pinjam ?? 1,
            })),
        };
    },

    actions: {
        _saveToLocalStorage() {
            localStorage.setItem(
                'peminjaman_keranjang',
                JSON.stringify(this.items)
            );
        },

        addItem(itemData) {
            const existingItem = this.items.find(
                item => item.id === itemData.id
            );

            // ⛔ JANGAN DEFAULT KE 1 UNTUK ITEM EXISTING
            if (existingItem) {
                if (typeof itemData.quantity_pinjam === 'number') {
                    existingItem.quantity_pinjam = itemData.quantity_pinjam;
                }
            } else {
                // ✅ ITEM BARU → BOLEH DEFAULT 1
                this.items.push({
                    id: itemData.id,
                    nama_barang: itemData.nama_barang,
                    kode_barang: itemData.kode_barang,
                    foto_barang: itemData.foto_barang,
                    quantity_pinjam: itemData.quantity_pinjam ?? 1,
                    stok_maksimal: itemData.quantity,
                });
            }

            this._saveToLocalStorage();
        },

        removeItem(id) {
            this.items = this.items.filter(item => item.id !== id);
            this._saveToLocalStorage();
        },

        clearCart() {
            this.items = [];
            localStorage.removeItem('peminjaman_keranjang');
        },
    },
});
