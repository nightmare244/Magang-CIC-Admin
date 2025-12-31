<script setup>
import { ref } from "vue";
import api from "@/services/api";
import { useRouter } from "vue-router";

const router = useRouter();
const file = ref(null);
const preview = ref(null);
const loading = ref(false);

const onFileChange = (e) => {
    file.value = e.target.files[0];
    preview.value = URL.createObjectURL(file.value);
};

const upload = async () => {
    if (!file.value) return;

    loading.value = true;

    const formData = new FormData();
    formData.append("foto_profil", file.value);

    await api.post("/karyawan/profil/upload-photo", formData, {
        headers: { "Content-Type": "multipart/form-data" },
    });

    router.push("/karyawan/profil");
};
</script>

<template>
<div class="p-6 max-w-xl mx-auto">
    <h1 class="text-xl font-bold mb-6">Ganti Foto Profil</h1>

    <input type="file" accept="image/*" @change="onFileChange" />

    <div v-if="preview" class="mt-4">
        <img :src="preview" class="w-40 h-40 rounded-full object-cover" />
    </div>

    <button
        class="px-4 py-2 mt-4 bg-blue-600 text-white rounded-lg"
        @click="upload"
        :disabled="loading"
    >
        Upload
    </button>
</div>
</template>
