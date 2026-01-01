<template>
    <Head>
        <title>Tambah User - Aplikasi Ujian Online</title>
    </Head>

    <div class="container-fluid mb-5 mt-5">
        <div class="row">
            <div class="col-md-12">
                <Link
                    href="/admin/users"
                    class="btn btn-md btn-primary border-0 shadow mb-3"
                    type="button">
                    <i class="fa fa-long-arrow-alt-left me-2"></i>
                    Kembali
                </Link>

                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h5><i class="fa fa-user-plus"></i> Tambah User</h5>
                        <hr>

                        <form @submit.prevent="submit">

                            <div class="mb-3">
                                <label>Nama</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Masukkan Nama"
                                    v-model="form.name">

                                <div v-if="errors.name" class="alert alert-danger mt-2">
                                    {{ errors.name }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Email</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    placeholder="Masukkan Email"
                                    v-model="form.email">

                                <div v-if="errors.email" class="alert alert-danger mt-2">
                                    {{ errors.email }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>Password</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    placeholder="Masukkan Password"
                                    v-model="form.password">

                                <div v-if="errors.password" class="alert alert-danger mt-2">
                                    {{ errors.password }}
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-md btn-primary border-0 shadow me-2">
                                Simpan
                            </button>
                            <button
                                type="reset"
                                class="btn btn-md btn-warning border-0 shadow">
                                Reset
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
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import Swal from 'sweetalert2';

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
            password: '',
        });

        const submit = () => {
            router.post('/admin/users', {
                name: form.name,
                email: form.email,
                password: form.password,
            }, {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'User berhasil disimpan.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000
                    });
                },
            });
        };

        return {
            form,
            submit,
        };
    }
};
</script>
