<template>
    <Head>
        <title>Tambah Organisasi - CBT SaaS</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row">
            <div class="col-md-12">
                <Link href="/admin/tenants" class="btn btn-md btn-primary border-0 shadow mb-3" type="button">
                    <i class="fa fa-long-arrow-alt-left me-2"></i> Kembali
                </Link>
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Tambah Organisasi Baru</h5>
                        <form @submit.prevent="submit">
                            <!-- Organisasi Info -->
                            <h6 class="text-muted mb-3">Informasi Organisasi</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nama Organisasi</label>
                                        <input type="text" class="form-control" v-model="form.name" placeholder="Nama Organisasi">
                                        <div v-if="errors.name" class="alert alert-danger mt-2">{{ errors.name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email Organisasi</label>
                                        <input type="email" class="form-control" v-model="form.email" placeholder="email@organisasi.com">
                                        <div v-if="errors.email" class="alert alert-danger mt-2">{{ errors.email }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Telepon</label>
                                        <input type="text" class="form-control" v-model="form.phone" placeholder="08xxxxxxxxxx">
                                        <div v-if="errors.phone" class="alert alert-danger mt-2">{{ errors.phone }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <select class="form-select" v-model="form.status">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="suspended">Suspended</option>
                                        </select>
                                        <div v-if="errors.status" class="alert alert-danger mt-2">{{ errors.status }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Plan</label>
                                        <select class="form-select" v-model="form.plan">
                                            <option value="free">Free</option>
                                            <option value="basic">Basic</option>
                                            <option value="premium">Premium</option>
                                            <option value="enterprise">Enterprise</option>
                                        </select>
                                        <div v-if="errors.plan" class="alert alert-danger mt-2">{{ errors.plan }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Maks. Siswa</label>
                                        <input type="number" class="form-control" v-model="form.max_students" min="1">
                                        <div v-if="errors.max_students" class="alert alert-danger mt-2">{{ errors.max_students }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Maks. Ujian</label>
                                        <input type="number" class="form-control" v-model="form.max_exams" min="1">
                                        <div v-if="errors.max_exams" class="alert alert-danger mt-2">{{ errors.max_exams }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Alamat</label>
                                <textarea class="form-control" rows="2" v-model="form.address" placeholder="Alamat lengkap organisasi"></textarea>
                                <div v-if="errors.address" class="alert alert-danger mt-2">{{ errors.address }}</div>
                            </div>

                            <hr class="my-4">

                            <!-- Admin Account -->
                            <h6 class="text-muted mb-3">Akun Admin Organisasi</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nama Admin</label>
                                        <input type="text" class="form-control" v-model="form.admin_name" placeholder="Nama lengkap admin">
                                        <div v-if="errors.admin_name" class="alert alert-danger mt-2">{{ errors.admin_name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email Admin</label>
                                        <input type="email" class="form-control" v-model="form.admin_email" placeholder="admin@organisasi.com">
                                        <div v-if="errors.admin_email" class="alert alert-danger mt-2">{{ errors.admin_email }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Password Admin</label>
                                        <input type="password" class="form-control" v-model="form.admin_password" placeholder="Min. 6 karakter">
                                        <div v-if="errors.admin_password" class="alert alert-danger mt-2">{{ errors.admin_password }}</div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-md btn-primary border-0 shadow">
                                <i class="fa fa-save me-2"></i> Simpan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import { Head, Link } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
    layout: LayoutAdmin,
    components: {
        Head,
        Link
    },
    props: {
        errors: Object,
    },
    setup() {
        const form = reactive({
            name: '',
            email: '',
            phone: '',
            address: '',
            status: 'active',
            plan: 'free',
            max_students: 50,
            max_exams: 10,
            admin_name: '',
            admin_email: '',
            admin_password: '',
        });

        const submit = () => {
            router.post('/admin/tenants', form);
        }

        return {
            form,
            submit,
        }
    }
}
</script>
