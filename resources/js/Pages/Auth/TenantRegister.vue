<template>
    <Head>
        <title>Daftar Organisasi - CBT SaaS</title>
    </Head>
    
    <div class="min-vh-100 d-flex align-items-center py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <h2 class="fw-bold text-primary">CBT SaaS</h2>
                                <p class="text-muted">Daftarkan Organisasi Anda</p>
                            </div>

                            <div v-if="$page.props.session?.error" class="alert alert-danger">
                                {{ $page.props.session.error }}
                            </div>

                            <form @submit.prevent="submit">
                                <h5 class="mb-3 text-secondary">Informasi Organisasi</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nama Organisasi</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        :class="{'is-invalid': errors.organization_name}"
                                        v-model="form.organization_name"
                                        placeholder="PT. Contoh Indonesia"
                                    >
                                    <div v-if="errors.organization_name" class="invalid-feedback">
                                        {{ errors.organization_name }}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Organisasi</label>
                                        <input 
                                            type="email" 
                                            class="form-control"
                                            :class="{'is-invalid': errors.email}"
                                            v-model="form.email"
                                            placeholder="info@perusahaan.com"
                                        >
                                        <div v-if="errors.email" class="invalid-feedback">
                                            {{ errors.email }}
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Telepon (Opsional)</label>
                                        <input 
                                            type="tel" 
                                            class="form-control"
                                            :class="{'is-invalid': errors.phone}"
                                            v-model="form.phone"
                                            placeholder="021-1234567"
                                        >
                                        <div v-if="errors.phone" class="invalid-feedback">
                                            {{ errors.phone }}
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h5 class="mb-3 text-secondary">Akun Administrator</h5>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Admin</label>
                                        <input 
                                            type="text" 
                                            class="form-control"
                                            :class="{'is-invalid': errors.admin_name}"
                                            v-model="form.admin_name"
                                            placeholder="Nama Lengkap"
                                        >
                                        <div v-if="errors.admin_name" class="invalid-feedback">
                                            {{ errors.admin_name }}
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Admin</label>
                                        <input 
                                            type="email" 
                                            class="form-control"
                                            :class="{'is-invalid': errors.admin_email}"
                                            v-model="form.admin_email"
                                            placeholder="admin@perusahaan.com"
                                        >
                                        <div v-if="errors.admin_email" class="invalid-feedback">
                                            {{ errors.admin_email }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Password</label>
                                        <input 
                                            type="password" 
                                            class="form-control"
                                            :class="{'is-invalid': errors.password}"
                                            v-model="form.password"
                                            placeholder="Min. 8 karakter"
                                        >
                                        <div v-if="errors.password" class="invalid-feedback">
                                            {{ errors.password }}
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Konfirmasi Password</label>
                                        <input 
                                            type="password" 
                                            class="form-control"
                                            :class="{'is-invalid': errors.password_confirmation}"
                                            v-model="form.password_confirmation"
                                            placeholder="Ulangi password"
                                        >
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-4">
                                    <button 
                                        type="submit" 
                                        class="btn btn-primary btn-lg"
                                        :disabled="form.processing"
                                    >
                                        <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                        Daftar Sekarang
                                    </button>
                                </div>

                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        Sudah punya akun? 
                                        <Link href="/login" class="text-primary">Login disini</Link>
                                    </small>
                                </div>

                                <div class="text-center mt-4">
                                    <small class="text-muted">
                                        Free trial 14 hari &bull; Hingga 50 peserta &bull; 10 ujian
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    errors: Object,
});

const form = useForm({
    organization_name: '',
    email: '',
    phone: '',
    admin_name: '',
    admin_email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register/tenant');
};
</script>
