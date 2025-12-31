import { ref, onUnmounted } from 'vue';

export function useGeolocation() {
  const coords = ref(null);
  const error = ref(null);
  const isSupported = 'geolocation' in navigator;

  const getPosition = () => {
    return new Promise((resolve, reject) => {
      if (!isSupported) {
        error.value = 'Geolocation tidak didukung oleh browser Anda.';
        return reject(error.value);
      }

      navigator.geolocation.getCurrentPosition(
        (position) => {
          coords.value = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            accuracy: position.coords.accuracy // Tambahkan akurasi untuk memantau kualitas sinyal
          };
          error.value = null;
          resolve(coords.value);
        },
        (err) => {
          // Map error code ke pesan yang ramah
          switch(err.code) {
            case 1: error.value = "Izin lokasi ditolak. Harap aktifkan GPS."; break;
            case 2: error.value = "Lokasi tidak tersedia (sinyal GPS buruk)."; break;
            case 3: error.value = "Waktu habis. Coba lagi di tempat terbuka."; break;
            default: error.value = "Gagal mengambil lokasi.";
          }
          reject(error.value);
        },
        {
          enableHighAccuracy: true, // WAJIB untuk absensi
          timeout: 15000,           // Naikkan ke 15 detik agar satelit sempat mengunci posisi
          maximumAge: 0             // WAJIB: Jangan gunakan lokasi lama (cache)
        }
      );
    });
  };

  return { coords, error, getPosition, isSupported };
}