<template>
    <Head>
        <title>Organisasi - CBT SaaS</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-3 col-12 mb-2">
                        <Link href="/admin/tenants/create" class="btn btn-md btn-primary border-0 shadow w-100" type="button">
                            <i class="fa fa-plus-circle"></i> Tambah
                        </Link>
                    </div>
                    <div class="col-md-9 col-12 mb-2">
                        <form @submit.prevent="handleSearch">
                            <div class="input-group">
                                <input type="text" class="form-control border-0 shadow" v-model="search" placeholder="cari nama atau email organisasi...">
                                <span class="input-group-text border-0 shadow">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-centered table-nowrap mb-0 rounded">
                                <thead class="thead-dark">
                                    <tr class="border-0">
                                        <th class="border-0 rounded-start" style="width:5%">No.</th>
                                        <th class="border-0">Organisasi</th>
                                        <th class="border-0">Plan</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0">Statistik</th>
                                        <th class="border-0 rounded-end" style="width:18%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(tenant, index) in tenants.data" :key="index">
                                        <td class="fw-bold text-center">{{ ++index + (tenants.current_page - 1) * tenants.per_page }}</td>
                                        <td>
                                            <strong>{{ tenant.name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ tenant.email }}</small>
                                        </td>
                                        <td>
                                            <span :class="planBadgeClass(tenant.plan)">{{ tenant.plan }}</span>
                                        </td>
                                        <td>
                                            <span :class="statusBadgeClass(tenant.status)">{{ tenant.status }}</span>
                                        </td>
                                        <td>
                                            <small>
                                                <i class="fa fa-users text-muted me-1"></i> {{ tenant.students_count || 0 }}/{{ tenant.max_students }}
                                                <span class="mx-2">|</span>
                                                <i class="fa fa-file-alt text-muted me-1"></i> {{ tenant.exams_count || 0 }}/{{ tenant.max_exams }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <Link :href="`/admin/tenants/${tenant.id}`" class="btn btn-sm btn-success border-0 shadow me-1" title="Lihat Detail">
                                                <i class="fa fa-eye"></i>
                                            </Link>
                                            <Link :href="`/admin/tenants/${tenant.id}/edit`" class="btn btn-sm btn-info border-0 shadow me-1" title="Edit">
                                                <i class="fa fa-pencil-alt"></i>
                                            </Link>
                                            <button @click.prevent="impersonate(tenant.id)" class="btn btn-sm btn-warning border-0 me-1" title="Login as Tenant">
                                                <i class="fa fa-sign-in-alt"></i>
                                            </button>
                                            <button @click.prevent="destroy(tenant.id)" class="btn btn-sm btn-danger border-0" title="Hapus">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="tenants.links" align="end" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import Pagination from '../../../Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

export default {
    layout: LayoutAdmin,
    components: {
        Head,
        Link,
        Pagination
    },
    props: {
        tenants: Object,
    },
    setup() {
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        const handleSearch = () => {
            router.get('/admin/tenants', { q: search.value });
        }

        const destroy = (id) => {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Semua data organisasi ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            })
            .then((result) => {
                if (result.isConfirmed) {
                    router.delete(`/admin/tenants/${id}`);
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Organisasi berhasil dihapus!',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            });
        }

        const impersonate = (id) => {
            Swal.fire({
                title: 'Login sebagai Tenant?',
                text: "Anda akan masuk ke dashboard tenant ini",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lanjutkan!'
            })
            .then((result) => {
                if (result.isConfirmed) {
                    router.post(`/admin/tenants/${id}/impersonate`);
                }
            });
        }

        const planBadgeClass = (plan) => {
            const classes = {
                'free': 'badge bg-secondary',
                'basic': 'badge bg-info',
                'premium': 'badge bg-warning',
                'enterprise': 'badge bg-primary'
            };
            return classes[plan] || 'badge bg-secondary';
        }

        const statusBadgeClass = (status) => {
            const classes = {
                'active': 'badge bg-success',
                'inactive': 'badge bg-secondary',
                'suspended': 'badge bg-danger'
            };
            return classes[status] || 'badge bg-secondary';
        }

        return {
            search,
            handleSearch,
            destroy,
            impersonate,
            planBadgeClass,
            statusBadgeClass,
        }
    }
}
</script>
