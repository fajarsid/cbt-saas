<template>
    <Head>
        <title>Login Siswa - CBT SaaS</title>
    </Head>
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold">CBT SaaS</h2>
                <p class="text-muted">Platform Ujian Online</p>
            </div>
            <div class="bg-white shadow border-0 rounded border-light p-4 p-lg-5 w-100 fmxw-500">
                <h5 class="text-center mb-4">Login Siswa</h5>

                <div v-if="$page.props.session?.error" class="alert alert-danger">
                    {{ $page.props.session.error }}
                </div>

                <form @submit.prevent="submit">
                    <div class="form-group mb-4">
                        <label for="nisn">NISN</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-id-card"></i>
                            </span>
                            <input type="text" class="form-control" v-model="form.nisn" placeholder="Masukkan NISN">
                        </div>
                        <div v-if="errors.nisn" class="alert alert-danger mt-2">
                            {{ errors.nisn }}
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-lock"></i>
                            </span>
                            <input type="password" placeholder="Masukkan Password" class="form-control"
                                v-model="form.password">
                        </div>
                        <div v-if="errors.password" class="alert alert-danger mt-2">
                            {{ errors.password }}
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">LOGIN SISWA</button>
                    </div>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <p class="mb-2">Admin / Penyelenggara?</p>
                    <Link href="/login" class="btn btn-outline-secondary btn-sm me-2">
                        Login Admin
                    </Link>
                    <Link href="/register/tenant" class="btn btn-outline-primary btn-sm">
                        Daftar Organisasi
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import LayoutStudent from '../../../Layouts/Student.vue';

defineOptions({
    layout: LayoutStudent
});

defineProps({
    errors: Object,
});

const form = reactive({
    nisn: '',
    password: '',
});

const submit = () => {
    router.post('/students/login', {
        nisn: form.nisn,
        password: form.password,
    });
};
</script>
