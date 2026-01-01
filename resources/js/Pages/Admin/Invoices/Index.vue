<template>
    <Head>
        <title>Invoice - CBT SaaS</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-select" v-model="selectedStatus" @change="filterByStatus">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Lunas</option>
                    <option value="overdue">Jatuh Tempo</option>
                    <option value="cancelled">Dibatalkan</option>
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
                                        <th class="border-0 rounded-start">No. Invoice</th>
                                        <th class="border-0">Organisasi</th>
                                        <th class="border-0">Paket</th>
                                        <th class="border-0">Total</th>
                                        <th class="border-0">Jatuh Tempo</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 rounded-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="invoice in invoices.data" :key="invoice.id">
                                        <td><strong>{{ invoice.invoice_number }}</strong></td>
                                        <td>
                                            {{ invoice.tenant?.name }}
                                            <br><small class="text-muted">{{ invoice.tenant?.email }}</small>
                                        </td>
                                        <td>{{ invoice.subscription?.plan?.name }}</td>
                                        <td>Rp {{ formatNumber(invoice.total) }}</td>
                                        <td>{{ formatDate(invoice.due_date) }}</td>
                                        <td>
                                            <span :class="statusBadge(invoice.status)">{{ statusText(invoice.status) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <Link :href="`/admin/invoices/${invoice.id}`" class="btn btn-sm btn-info border-0 me-1">
                                                <i class="fa fa-eye"></i>
                                            </Link>
                                            <button v-if="invoice.status === 'pending'" @click="markPaid(invoice.id)" class="btn btn-sm btn-success border-0">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="invoices.links" align="end" />
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
        invoices: Object,
        filters: Object,
    },
    setup(props) {
        const selectedStatus = ref(props.filters?.status || '');

        const formatNumber = (num) => new Intl.NumberFormat('id-ID').format(num);
        const formatDate = (date) => new Date(date).toLocaleDateString('id-ID');

        const statusBadge = (status) => ({
            'pending': 'badge bg-warning',
            'paid': 'badge bg-success',
            'overdue': 'badge bg-danger',
            'cancelled': 'badge bg-secondary',
        }[status] || 'badge bg-secondary');

        const statusText = (status) => ({
            'pending': 'Menunggu',
            'paid': 'Lunas',
            'overdue': 'Jatuh Tempo',
            'cancelled': 'Dibatalkan',
        }[status] || status);

        const filterByStatus = () => {
            router.get('/admin/invoices', { status: selectedStatus.value });
        }

        const markPaid = (id) => {
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                html: `
                    <div class="mb-3 text-start">
                        <label class="form-label">Metode Pembayaran</label>
                        <select id="payment_method" class="form-select">
                            <option value="transfer">Transfer Bank</option>
                            <option value="cash">Tunai</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Referensi</label>
                        <input type="text" id="payment_reference" class="form-control" placeholder="No. Rekening / Bukti Transfer">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Konfirmasi',
                preConfirm: () => ({
                    payment_method: document.getElementById('payment_method').value,
                    payment_reference: document.getElementById('payment_reference').value,
                })
            }).then((result) => {
                if (result.isConfirmed) {
                    router.post(`/admin/invoices/${id}/mark-paid`, result.value);
                }
            });
        }

        return { selectedStatus, formatNumber, formatDate, statusBadge, statusText, filterByStatus, markPaid }
    }
}
</script>
