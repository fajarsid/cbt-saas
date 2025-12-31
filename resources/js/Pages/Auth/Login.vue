<template>
    <Head>
        <title>Login Admin - CBT SaaS</title>
    </Head>
    <div class="bg-white shadow border-0 rounded border-light p-4 p-lg-5 w-100 fmxw-500">
        <div class="text-center mb-4">
            <h3 class="fw-bold">CBT SaaS</h3>
            <p class="text-muted">Login Administrator</p>
        </div>

        <div v-if="$page.props.session?.error" class="alert alert-danger">
            {{ $page.props.session.error }}
        </div>
        <div v-if="$page.props.session?.success" class="alert alert-success">
            {{ $page.props.session.success }}
        </div>

        <form @submit.prevent="submit">
            <div class="form-group mb-4">
                <label for="email">Email</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa fa-envelope"></i>
                    </span>
                    <input type="email" class="form-control" v-model="form.email" placeholder="admin@company.com">
                </div>
                <div v-if="errors.email" class="alert alert-danger mt-2">
                    {{ errors.email }}
                </div>
            </div>

            <div class="form-group mb-4">
                <label for="password">Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa fa-lock"></i>
                    </span>
                    <input type="password" placeholder="Password" class="form-control" v-model="form.password">
                </div>
                <div v-if="errors.password" class="alert alert-danger mt-2">
                    {{ errors.password }}
                </div>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary btn-lg">LOGIN</button>
            </div>
        </form>

        <hr class="my-4">

        <div class="text-center">
            <p class="mb-2">Belum punya akun?</p>
            <Link href="/register/tenant" class="btn btn-outline-primary">
                Daftar Organisasi Baru
            </Link>
        </div>

        <div class="text-center mt-3">
            <Link href="/" class="text-muted small">
                Login sebagai Siswa
            </Link>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import LayoutAuth from '../../Layouts/Auth.vue';

defineOptions({
    layout: LayoutAuth
});

defineProps({
    errors: Object,
});

const form = reactive({
    email: '',
    password: '',
});

const submit = () => {
    router.post('/login', {
        email: form.email,
        password: form.password,
    });
};
</script>
