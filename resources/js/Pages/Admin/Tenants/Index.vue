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
                                        <th class="border-0">Nama Organisasi</th>
                                        <th class="border-0">Email</th>
                                        <th class="border-0">Plan</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0">Kuota</th>
                                        <th class="border-0 rounded-end" style="width:15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(tenant, index) in tenants.data" :key="index">
                                        <td class="fw-bold text-center">{{ ++index + (tenants.current_page - 1) * tenants.per_page }}</td>
                                        <td>{{ tenant.name }}</td>
                                        <td>{{ tenant.email }}</td>
                                        <td>
                                            <span :class="planBadgeClass(tenant.plan)">{{ tenant.plan }}</span>
                                        </td>
                                        <td>
                                            <span :class="statusBadgeClass(tenant.status)">{{ tenant.status }}</span>
                                        </td>
                                        <td>
                                            <small>Siswa: {{ tenant.max_students }}<br>Ujian: {{ tenant.max_exams }}</small>
                                        </td>
                                        <td class="text-center">
                                            <Link :href="`/admin/tenants/${tenant.id}/edit`" class="btn btn-sm btn-info border-0 shadow me-2" type="button">
                                                <i class="fa fa-pencil-alt"></i>
                                            </Link>
                                            <button @click.prevent="destroy(tenant.id)" class="btn btn-sm btn-danger border-0">
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
            planBadgeClass,
            statusBadgeClass,
        }
    }
}
</script>
