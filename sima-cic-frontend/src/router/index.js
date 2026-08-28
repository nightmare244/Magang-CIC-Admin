import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/authStore.js";
import NProgress from 'nprogress';
import 'nprogress/nprogress.css';

// ================= LAYOUTS =================
import AdminLayout from "@/layouts/AdminLayout.vue";
import KaryawanLayout from "@/layouts/KaryawanLayout.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";

// ================= AUTH =================
import Login from "@/pages/Auth/Login.vue";

// ================= ADMIN PAGES =================
import AdminDashboard from "@/pages/admin/Dashboard/Dashboard.vue";
import DepartemenList from "@/pages/admin/departemen/index.vue";
import DepartemenDetail from "@/pages/admin/departemen/Detail.vue";
import DepartemenEdit from "@/pages/admin/departemen/Edit.vue";
import DepartemenCreate from "@/pages/admin/departemen/Create.vue";

import InventarisList from "@/pages/admin/inventaris/index.vue";
import InventarisDetail from "@/pages/admin/inventaris/Detail.vue";
import InventarisEdit from "@/pages/admin/inventaris/Edit.vue";
import InventarisCreate from "@/pages/admin/inventaris/Create.vue";

import IzinListAdmin from "@/pages/admin/izin/Index.vue";
import IzinDetailAdmin from "@/pages/admin/izin/Detail.vue";

import KaryawanListAdmin from "@/pages/admin/karyawan/KaryawanList.vue";
import KaryawanDetailAdmin from "@/pages/admin/karyawan/KaryawanDetail.vue";
import KaryawanEditAdmin from "@/pages/admin/karyawan/KaryawanEdit.vue";
import KaryawanCreateAdmin from "@/pages/admin/karyawan/KaryawanCreate.vue";

import PeminjamanList from "@/pages/admin/Peminjaman/index.vue";
import PersetujuanDetail from "@/pages/admin/Peminjaman/PersetujuanDetail.vue";

import PengumumanList from "@/pages/admin/pengumuman/index.vue";
import PengumumanDetail from "@/pages/admin/pengumuman/Detail.vue";
import PengumumanEdit from "@/pages/admin/pengumuman/Edit.vue";
import PengumumanCreate from "@/pages/admin/pengumuman/Create.vue";

import AbsensiSettings from "@/pages/admin/absensi/Settings.vue"; 
import AbsensiReport from "@/pages/admin/absensi/Report.vue"; 
import AbsensiDetailAdmin from "@/pages/admin/absensi/Detail.vue";

import TransaksiIndex from "@/pages/admin/keuangan/Transaksi/index.vue";
import TransaksiCreate from "@/pages/admin/keuangan/Transaksi/Create.vue";
import TransaksiEdit from "@/pages/admin/keuangan/Transaksi/Edit.vue";
import TransaksiDetail from "@/pages/admin/keuangan/Transaksi/Detail.vue";
import AkunIndex from "@/pages/admin/keuangan/Akun/index.vue";
import JurnalKasIndex from "@/pages/admin/keuangan/Jurnal/index.vue";
import LaporanKeuanganIndex from "@/pages/admin/keuangan/Laporan/index.vue";

import PemasukanIndex from "@/pages/admin/keuangan/Pemasukan/index.vue";
import PemasukanCreate from "@/pages/admin/keuangan/Pemasukan/Create.vue";
import PemasukanEdit from "@/pages/admin/keuangan/Pemasukan/Edit.vue";
import PemasukanDetail from "@/pages/admin/keuangan/Pemasukan/Detail.vue";

import PengeluaranIndex from "@/pages/admin/keuangan/Pengeluaran/index.vue";
import PengeluaranCreate from "@/pages/admin/keuangan/Pengeluaran/Create.vue";
import PengeluaranEdit from "@/pages/admin/keuangan/Pengeluaran/Edit.vue";
import PengeluaranDetail from "@/pages/admin/keuangan/Pengeluaran/Detail.vue";
import GrafikKeuangan from "@/pages/admin/keuangan/grafik/index.vue";
import RekapBulanan from "@/pages/admin/rekap-bulanan/index.vue";
import LogAktivitas from "@/pages/admin/log-aktivitas/index.vue";

// ================= KARYAWAN PAGES =================
import KaryawanDashboard from "@/pages/karyawan/dashboard/Dashboard.vue";
import AbsensiIndex from "@/pages/karyawan/absensi/index.vue";
import AbsensiDetail from "@/pages/karyawan/absensi/Detail.vue";
import AbsensiHistory from "@/pages/karyawan/absensi/History.vue";

import InventarisIndexKaryawan from "@/pages/karyawan/Inventaris/index.vue";
import InventarisDetailKaryawan from "@/pages/karyawan/Inventaris/Detail.vue";

import IzinIndexKaryawan from "@/pages/karyawan/Izin/index.vue";
import IzinAjukan from "@/pages/karyawan/Izin/AjukanIzin.vue";
import IzinDetailKaryawan from "@/pages/karyawan/Izin/Detail.vue";

import PeminjamanIndexKaryawan from "@/pages/karyawan/Peminjaman/index.vue";
import PeminjamanKeranjang from "@/pages/karyawan/Peminjaman/views/Keranjang.vue";
import PeminjamanDetailKaryawan from "@/pages/karyawan/Peminjaman/views/DetailPeminjaman.vue"; 
import PeminjamanRiwayatKaryawan from "@/pages/karyawan/Peminjaman/views/RiwayatPeminjaman.vue";

import PengumumanIndexKaryawan from "@/pages/karyawan/Pengumuman/index.vue";
import PengumumanDetailKaryawan from "@/pages/karyawan/Pengumuman/detail.vue";

import KaryawanProfilIndex from "@/pages/karyawan/profil/index.vue";
import KaryawanProfilEdit from "@/pages/karyawan/profil/Edit.vue";
import KaryawanProfilChangePassword from "@/pages/karyawan/profil/ChangePassword.vue";
import KaryawanProfilUploadPhoto from "@/pages/karyawan/profil/UploadPhoto.vue";

const routes = [
    { path: "/", redirect: "/admin/dashboard" },
    {
        path: "/login",
        component: AuthLayout,
        meta: { guest: true, title: "Login" },
        children: [{ path: "", name: "login", component: Login }],
    },
    {
        path: "/landing",
        name: "landing",
        component: () => import("@/pages/LandingPage.vue"),
        meta: { guest: true, title: "Selamat Datang" }
    },
    
    // ================= ADMIN ROUTES =================
    {
        path: "/admin",
        component: AdminLayout,
        meta: { requiresAuth: true, role: "admin" },
        children: [
            { path: "dashboard", name: "admin.dashboard", component: AdminDashboard, meta: { title: "Dashboard Admin" } },
            
            // Departemen
            { path: 'departemen', name: "admin.departemen.index", component: DepartemenList, meta: { title: "Daftar Departemen" } },
            { path: 'departemen/create', name: "admin.departemen.create", component: DepartemenCreate, meta: { title: "Tambah Departemen" } },
            { path: 'departemen/:id', name: "admin.departemen.detail", component: DepartemenDetail, meta: { title: "Detail Departemen" } },
            { path: 'departemen/:id/edit', name: "admin.departemen.edit", component: DepartemenEdit, meta: { title: "Edit Departemen" } },

            // Inventaris
            { path: "inventaris", name: "admin.inventaris.index", component: InventarisList, meta: { title: "Inventaris Asset" } },
            { path: "inventaris/create", name: "admin.inventaris.create", component: InventarisCreate, meta: { title: "Tambah Asset" } },
            { path: "inventaris/:id", name: "admin.inventaris.detail", component: InventarisDetail, meta: { title: "Detail Asset" } },
            { path: "inventaris/:id/edit", name: "admin.inventaris.edit", component: InventarisEdit, meta: { title: "Edit Asset" } },

            // Izin (Admin)
            { path: "izin", name: "admin.izin.index", component: IzinListAdmin, meta: { title: "Persetujuan Izin" } },
            { path: "izin/:id", name: "admin.izin.detail", component: IzinDetailAdmin, meta: { title: "Detail Otorisasi Izin" } },

            // Manajemen Karyawan
            { path: "karyawan", name: "admin.karyawan.index", component: KaryawanListAdmin, meta: { title: "Manajemen Karyawan" } },
            { path: "karyawan/create", name: "admin.karyawan.create", component: KaryawanCreateAdmin, meta: { title: "Tambah Karyawan" } },
            { path: "karyawan/:id", name: "admin.karyawan.detail", component: KaryawanDetailAdmin, meta: { title: "Profil Karyawan" } },
            { path: "karyawan/:id/edit", name: "admin.karyawan.edit", component: KaryawanEditAdmin, meta: { title: "Edit Karyawan" } },

            // Peminjaman (Admin)
            { path: "peminjaman", name: "admin.peminjaman.index", component: PeminjamanList, meta: { title: "Data Peminjaman" } }, 
            { path: "persetujuan-peminjaman/:id", name: "admin.peminjaman.detail", component: PersetujuanDetail, meta: { title: "Persetujuan Peminjaman" } },

            // Pengumuman (Admin)
            { path: "pengumuman", name: "admin.pengumuman.index", component: PengumumanList, meta: { title: "Kelola Pengumuman" } },
            { path: "pengumuman/create", name: "admin.pengumuman.create", component: PengumumanCreate, meta: { title: "Buat Pengumuman" } },
            { path: "pengumuman/:id", name: "admin.pengumuman.detail", component: PengumumanDetail, meta: { title: "Detail Pengumuman" } },
            { path: "pengumuman/:id/edit", name: "admin.pengumuman.edit", component: PengumumanEdit, meta: { title: "Edit Pengumuman" } },
            
            // Keuangan & Akuntansi (Terpadu)
            { path: "keuangan/transaksi", name: "admin.keuangan.transaksi.index", component: TransaksiIndex, meta: { title: "Pemasukan & Pengeluaran" } },
            { path: "keuangan/transaksi/create", name: "admin.keuangan.transaksi.create", component: TransaksiCreate, meta: { title: "Tambah Transaksi Keuangan" } },
            { path: "keuangan/transaksi/:id", name: "admin.keuangan.transaksi.detail", component: TransaksiDetail, meta: { title: "Detail Transaksi Keuangan" } },
            { path: "keuangan/transaksi/:id/edit", name: "admin.keuangan.transaksi.edit", component: TransaksiEdit, meta: { title: "Edit Transaksi Keuangan" } },
            
            // Master Daftar Akun (CoA)
            { path: "keuangan/akun", name: "admin.keuangan.akun.index", component: AkunIndex, meta: { title: "Daftar Akun (CoA)" } },
            
            // Jurnal Kas
            { path: "keuangan/jurnal-kas", name: "admin.keuangan.jurnal.index", component: JurnalKasIndex, meta: { title: "Jurnal Kas" } },
            
            // Laporan Keuangan
            { path: "keuangan/laporan", name: "admin.keuangan.laporan.index", component: LaporanKeuanganIndex, meta: { title: "Laporan Keuangan" } },
            { path: "keuangan/laporan/arus-kas", name: "admin.keuangan.laporan.arus_kas", component: LaporanKeuanganIndex, meta: { title: "Laporan Arus Kas" } },
            { path: "keuangan/laporan/laba-rugi", name: "admin.keuangan.laporan.laba_rugi", component: LaporanKeuanganIndex, meta: { title: "Laporan Laba Rugi" } },
            { path: "keuangan/laporan/neraca", name: "admin.keuangan.laporan.neraca", component: LaporanKeuanganIndex, meta: { title: "Laporan Neraca" } },

            // Keuangan - Pemasukan (Legacy Routes - redirect / fallback)
            { path: "pemasukan", name: "admin.pemasukan.index", redirect: "/admin/keuangan/transaksi" },
            { path: "pemasukan/create", name: "admin.pemasukan.create", redirect: "/admin/keuangan/transaksi/create" },
            { path: "pemasukan/:id", name: "admin.pemasukan.detail", component: PemasukanDetail, meta: { title: "Detail Pemasukan" } },
            { path: "pemasukan/:id/edit", name: "admin.pemasukan.edit", component: PemasukanEdit, meta: { title: "Edit Pemasukan" } },
            
            // Keuangan - Pengeluaran (Legacy Routes - redirect / fallback)
            { path: "pengeluaran", name: "admin.pengeluaran.index", redirect: "/admin/keuangan/transaksi" },
            { path: "pengeluaran/create", name: "admin.pengeluaran.create", redirect: "/admin/keuangan/transaksi/create" },
            { path: "pengeluaran/:id", name: "admin.pengeluaran.detail", component: PengeluaranDetail, meta: { title: "Detail Pengeluaran" } },
            { path: "pengeluaran/:id/edit", name: "admin.pengeluaran.edit", component: PengeluaranEdit, meta: { title: "Edit Pengeluaran" } },
            
            // Keuangan - Grafik
            { path: "keuangan/grafik", name: "admin.keuangan.grafik", component: GrafikKeuangan, meta: { title: "Dashboard Grafik Keuangan" } },
            
            // Rekap Bulanan
            { path: "rekap-bulanan", name: "admin.rekap.bulanan", component: RekapBulanan, meta: { title: "Rekapitulasi Bulanan" } },
            
            // Log Aktivitas
            { path: "log-aktivitas", name: "admin.log.aktivitas", component: LogAktivitas, meta: { title: "Log Aktivitas Sistem" } },
            
            // Absensi (Admin)
            { path: "absensi/settings", name: "admin.absensi.settings", component: AbsensiSettings, meta: { title: "Pengaturan Absensi" } },
            { path: "absensi/laporan", name: "admin.absensi.laporan", component: AbsensiReport, meta: { title: "Laporan Kehadiran" } },
            { path: "absensi/detail/:id", name: "admin.absensi.detail", component: AbsensiDetailAdmin, meta: { title: "Detail Kehadiran" } }
        ],
    },

    // ================= KARYAWAN ROUTES =================
    {
        path: "/karyawan",
        component: KaryawanLayout,
        meta: { requiresAuth: true, role: "karyawan" },
        children: [
            { path: "dashboard", name: "karyawan.dashboard", component: KaryawanDashboard, meta: { title: "Dashboard" } },

            // Profil
            { path: "profil", name: "karyawan.profil", component: KaryawanProfilIndex, meta: { title: "Profil Saya" } },
            { path: "profil/edit", name: "karyawan.profil.edit", component: KaryawanProfilEdit, meta: { title: "Edit Profil" } },
            { path: "profil/change-password", name: "karyawan.profil.password", component: KaryawanProfilChangePassword, meta: { title: "Keamanan Akun" } },
            { path: "profil/upload-photo", name: "karyawan.profil.upload_photo", component: KaryawanProfilUploadPhoto, meta: { title: "Upload Foto" } },

            // Absensi
            { path: "absensi", name: "karyawan.absensi.index", component: AbsensiIndex, meta: { title: "Presensi" } },
            { path: "absensi/history", name: "karyawan.absensi.history", component: AbsensiHistory, meta: { title: "Riwayat Kehadiran" } },
            { path: "absensi/:id", name: "karyawan.absensi.detail", component: AbsensiDetail, meta: { title: "Detail Absensi" } },

            // Inventaris
            { path: "inventaris", name: "karyawan.inventaris.index", component: InventarisIndexKaryawan, meta: { title: "Daftar Asset" } },
            { path: "inventaris/:id", name: "karyawan.inventaris.detail", component: InventarisDetailKaryawan, meta: { title: "Detail Asset" } },

            // Izin
            { path: "izin", name: "karyawan.izin.index", component: IzinIndexKaryawan, meta: { title: "Izin & Sakit" } },
            { path: "izin/ajukan", name: "karyawan.izin.ajukan", component: IzinAjukan, meta: { title: "Ajukan Izin" } },
            { path: "izin/:id", name: "karyawan.izin.detail", component: IzinDetailKaryawan, meta: { title: "Detail Izin" } },

            // Peminjaman
            { path: "peminjaman", name: "karyawan.peminjaman.index", component: PeminjamanIndexKaryawan, meta: { title: "Peminjaman Alat" } }, 
            { path: "peminjaman/keranjang", name: "karyawan.peminjaman.keranjang", component: PeminjamanKeranjang, meta: { title: "Keranjang Pinjam" } },
            { path: "peminjaman/checkout", name: "karyawan.peminjaman.checkout", component: PeminjamanDetailKaryawan, meta: { title: "Konfirmasi Pinjam" } }, 
            { path: "peminjaman/riwayat", name: "karyawan.peminjaman.riwayat_list", component: PeminjamanRiwayatKaryawan, meta: { title: "Riwayat Pinjam" } },
        { 
    path: "peminjaman/:id", 
    name: "karyawan.peminjaman.detail", 
    // TAMBAHKAN /views/ DI PATH NYA
    component: () => import('@/pages/karyawan/Peminjaman/views/PeminjamanDetail.vue'), 
    meta: { title: "Detail Peminjaman" } 
},

            // Pengumuman
            { path: "pengumuman", name: "karyawan.pengumuman.index", component: PengumumanIndexKaryawan, meta: { title: "Pusat Pengumuman" } }, 
            { path: "pengumuman/:id", name: "karyawan.pengumuman.detail", component: PengumumanDetailKaryawan, meta: { title: "Detail Informasi" } },
        ],
    },

    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: () => import('@/pages/NotFound.vue'),
        meta: { title: "404 Not Found" }
    }
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL), // Sinkron dengan base di vite.config.js
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) return savedPosition;
        return { top: 0 };
    }
});

// ================= NAVIGATION GUARD =================
router.beforeEach(async (to, from, next) => {
    NProgress.start();
    const auth = useAuthStore();

    if (auth.token && !auth.user) {
        try {
            await auth.fetchUser();
        } catch (e) {
            auth.logout();
            return next({ name: "login" });
        }
    }

    const isLoggedIn = !!auth.token;
    const role = auth.user?.role || null;

    const baseTitle = "SIMA CIC";
    document.title = to.meta.title ? `${baseTitle} | ${to.meta.title}` : baseTitle;

    if ((to.meta.guest || to.path === '/') && isLoggedIn) {
        return next(role === "admin" ? "/admin/dashboard" : "/karyawan/dashboard");
    }

    if (to.meta.requiresAuth && !isLoggedIn) {
        NProgress.done();
        return next({ path: "/login", query: { redirect: to.fullPath } });
    }

    if (to.meta.role && role && to.meta.role !== role) {
        NProgress.done();
        return next(role === "admin" ? "/admin/dashboard" : "/karyawan/dashboard");
    }

    next();
});

router.afterEach(() => {
    NProgress.done();
});

export default router;

