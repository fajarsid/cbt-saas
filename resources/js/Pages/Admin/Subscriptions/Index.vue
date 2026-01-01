<template>
    <Head>
        <title>Langganan - CBT SaaS</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row mb-3">
            <div class="col-md-3">
                <Link href="/admin/subscriptions/create" class="btn btn-md btn-primary border-0 shadow">
                    <i class="fa fa-plus-circle"></i> Tambah Langganan
                </Link>
            </div>
            <div class="col-md-3">
                <select class="form-select" v-model="selectedStatus" @change="filterByStatus">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="cancelled">Dibatalkan</option>
                    <option value="expired">Kadaluarsa</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-centered mb-0 rounded">
                                <thead class="thead-dark">
                                    <tr class="border-0">
                                        <th class="border-0 rounded-start">No.</th>
                                        <th class="border-0">Organisasi</th>
                                        <th class="border-0">Paket</th>
                                        <th class="border-0">Harga</th>
                                        <th class="border-0">Periode</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 rounded-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(sub, index) in subscriptions.data" :key="sub.id">
                                        <td class="fw-bold text-center">{{ ++index + (subscriptions.current_page - 1) * subscriptions.per_page }}</td>
                                        <td>
                                            <strong>{{ sub.tenant?.name }}</strong>
                                            <br><small class="text-muted">{{ sub.tenant?.email }}</small>
                                        </td>
                                        <td>{{ sub.plan?.name }}</td>
                                        <td>Rp {{ formatNumber(sub.price) }}</td>
                                        <td>
                                            <small>
                                                {{ formatDate(sub.starts_at) }} - {{ formatDate(sub.ends_at) }}
                                            </small>
                                        </td>
                                        <td>
                                            <span :class="statusBadge(sub.status)">{{ sub.status }}</span>
                                        </td>
                                        <td class="text-center">
                                            <Link :href="`/admin/subscriptions/${sub.id}`" class="btn btn-sm btn-info border-0 me-1">
                                                <i class="fa fa-eye"></i>
                                            </Link>
                                            <button v-if="sub.status === 'active'" @click="cancel(sub.id)" class="btn btn-sm btn-danger border-0">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="subscriptions.links" align="end" />
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
    components: { Head, Link, Pagination },
    props: {
        subscriptions: Object,
        filters: Object,
    },
    setup(props) {
        const selectedStatus = ref(props.filters?.status || '');

        const formatNumber = (num) => new Intl.NumberFormat('id-ID').format(num);
        const formatDate = (date) => new Date(date).toLocaleDateString('id-ID');

        const statusBadge = (status) => ({
            'active': 'badge bg-success',
            'cancelled': 'badge bg-danger',
            'expired': 'badge bg-secondary',
        }[status] || 'badge bg-secondary');

        const filterByStatus = () => {
            router.get('/admin/subscriptions', { status: selectedStatus.value });
        }

        const cancel = (id) => {
            Swal.fire({
                title: 'Batalkan Langganan?',
                text: "Langganan akan dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, batalkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    router.post(`/admin/subscriptions/${id}/cancel`);
                }
            });
        }

        return { selectedStatus, formatNumber, formatDate, statusBadge, filterByStatus, cancel }
    }
}
</script>
