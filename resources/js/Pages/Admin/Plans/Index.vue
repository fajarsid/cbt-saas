<template>
    <Head>
        <title>Paket Langganan - CBT SaaS</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row">
            <div class="col-md-12 mb-3">
                <Link href="/admin/plans/create" class="btn btn-md btn-primary border-0 shadow" type="button">
                    <i class="fa fa-plus-circle"></i> Tambah Paket
                </Link>
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
                                        <th class="border-0">Nama Paket</th>
                                        <th class="border-0">Harga</th>
                                        <th class="border-0">Kuota</th>
                                        <th class="border-0">Subscribers</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 rounded-end" style="width:15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(plan, index) in plans" :key="plan.id">
                                        <td class="fw-bold text-center">{{ index + 1 }}</td>
                                        <td>
                                            <strong>{{ plan.name }}</strong>
                                            <span v-if="plan.is_featured" class="badge bg-warning ms-1">Featured</span>
                                            <br>
                                            <small class="text-muted">{{ plan.description }}</small>
                                        </td>
                                        <td>
                                            <strong>Rp {{ formatNumber(plan.price) }}</strong>
                                            <small class="text-muted">/{{ plan.billing_cycle === 'monthly' ? 'bulan' : 'tahun' }}</small>
                                        </td>
                                        <td>
                                            <small>
                                                <i class="fa fa-users text-muted me-1"></i> {{ plan.max_students }} peserta<br>
                                                <i class="fa fa-file-alt text-muted me-1"></i> {{ plan.max_exams }} ujian<br>
                                                <i class="fa fa-user text-muted me-1"></i> {{ plan.max_users }} admin
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ plan.subscriptions_count || 0 }}</span>
                                        </td>
                                        <td>
                                            <span :class="plan.is_active ? 'badge bg-success' : 'badge bg-secondary'">
                                                {{ plan.is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <Link :href="`/admin/plans/${plan.id}/edit`" class="btn btn-sm btn-info border-0 shadow me-1">
                                                <i class="fa fa-pencil-alt"></i>
                                            </Link>
                                            <button @click.prevent="destroy(plan.id)" class="btn btn-sm btn-danger border-0" :disabled="plan.subscriptions_count > 0">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

export default {
    layout: LayoutAdmin,
    components: {
        Head,
        Link,
    },
    props: {
        plans: Array,
    },
    setup() {
        const formatNumber = (num) => {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        const destroy = (id) => {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Paket ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            })
            .then((result) => {
                if (result.isConfirmed) {
                    router.delete(`/admin/plans/${id}`);
                }
            });
        }

        return {
            formatNumber,
            destroy,
        }
    }
}
</script>
