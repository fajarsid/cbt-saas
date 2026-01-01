<template>
    <Head>
        <title>Billing & Langganan</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row">
            <div class="col-md-12 mb-4">
                <h4><i class="fa fa-credit-card me-2"></i>Billing & Langganan</h4>
                <p class="text-muted">Kelola langganan dan pembayaran Anda</p>
            </div>
        </div>

        <!-- Current Subscription -->
        <div class="row">
            <div class="col-md-8 mb-4">
                <div class="card border-0 shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fa fa-crown me-2"></i>Paket Saat Ini</h5>
                    </div>
                    <div class="card-body">
                        <div v-if="currentPlan" class="row">
                            <div class="col-md-6">
                                <h3 class="text-primary">{{ currentPlan.name }}</h3>
                                <p class="text-muted">{{ currentPlan.description }}</p>
                                <h4>{{ currentPlan.formatted_price }}<small class="text-muted">/{{ currentPlan.billing_cycle === 'monthly' ? 'bulan' : 'tahun' }}</small></h4>
                            </div>
                            <div class="col-md-6">
                                <div v-if="subscription">
                                    <p><strong>Status:</strong>
                                        <span :class="statusBadgeClass">{{ statusText }}</span>
                                    </p>
                                    <p><strong>Mulai:</strong> {{ formatDate(subscription.starts_at) }}</p>
                                    <p><strong>Berakhir:</strong> {{ formatDate(subscription.ends_at) }}</p>
                                    <p v-if="subscription.trial_ends_at">
                                        <strong>Trial Berakhir:</strong> {{ formatDate(subscription.trial_ends_at) }}
                                    </p>
                                </div>
                                <div v-else class="alert alert-info">
                                    <i class="fa fa-info-circle me-1"></i>
                                    Tidak ada langganan aktif
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-4">
                            <i class="fa fa-package fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada paket langganan</p>
                            <a href="/admin/billing/plans" class="btn btn-primary">
                                <i class="fa fa-shopping-cart me-1"></i> Pilih Paket
                            </a>
                        </div>
                    </div>
                    <div class="card-footer" v-if="currentPlan">
                        <a href="/admin/billing/plans" class="btn btn-outline-primary">
                            <i class="fa fa-arrow-up me-1"></i> Upgrade Paket
                        </a>
                        <button v-if="subscription && subscription.status === 'active'"
                                @click="cancelSubscription"
                                class="btn btn-outline-danger ms-2">
                            <i class="fa fa-times me-1"></i> Batalkan Langganan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Usage Stats -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-chart-pie me-2"></i>Penggunaan Kuota</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Peserta</span>
                                <span>{{ usage.students.current }} / {{ usage.students.max }}</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar"
                                     :class="getProgressClass(usage.students.percentage)"
                                     :style="{ width: usage.students.percentage + '%' }">
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Ujian</span>
                                <span>{{ usage.exams.current }} / {{ usage.exams.max }}</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar"
                                     :class="getProgressClass(usage.exams.percentage)"
                                     :style="{ width: usage.exams.percentage + '%' }">
                                </div>
                            </div>
                        </div>
                        <div v-if="usage.students.percentage >= 80 || usage.exams.percentage >= 80"
                             class="alert alert-warning small">
                            <i class="fa fa-exclamation-triangle me-1"></i>
                            Kuota hampir habis. Pertimbangkan untuk upgrade paket.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Invoices -->
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa fa-file-invoice me-2"></i>Invoice Terbaru</h5>
                        <a href="/admin/billing/invoices" class="btn btn-sm btn-outline-primary">
                            Lihat Semua <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>No. Invoice</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="invoice in invoices" :key="invoice.id">
                                        <td>{{ invoice.invoice_number }}</td>
                                        <td>{{ formatDate(invoice.created_at) }}</td>
                                        <td>{{ invoice.formatted_total }}</td>
                                        <td>
                                            <span :class="getInvoiceStatusClass(invoice.status)">
                                                {{ getInvoiceStatusText(invoice.status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a :href="`/admin/billing/invoice/${invoice.id}`"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr v-if="!invoices || invoices.length === 0">
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Belum ada invoice
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Plans -->
        <div class="row mt-4">
            <div class="col-md-12 mb-3">
                <h5><i class="fa fa-list me-2"></i>Paket Tersedia</h5>
            </div>
            <div class="col-md-4 mb-4" v-for="plan in plans" :key="plan.id">
                <div class="card border-0 shadow h-100"
                     :class="{ 'border-primary': plan.is_featured }">
                    <div v-if="plan.is_featured" class="card-header bg-primary text-white text-center">
                        <i class="fa fa-star me-1"></i> Rekomendasi
                    </div>
                    <div class="card-body text-center">
                        <h4>{{ plan.name }}</h4>
                        <h2 class="text-primary">{{ plan.formatted_price }}</h2>
                        <p class="text-muted">per {{ plan.billing_cycle === 'monthly' ? 'bulan' : 'tahun' }}</p>
                        <hr>
                        <ul class="list-unstyled text-start">
                            <li v-for="feature in plan.features" :key="feature" class="mb-2">
                                <i class="fa fa-check text-success me-2"></i> {{ feature }}
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer text-center">
                        <button v-if="currentPlan && currentPlan.id === plan.id"
                                class="btn btn-secondary" disabled>
                            <i class="fa fa-check me-1"></i> Paket Anda
                        </button>
                        <button v-else @click="selectPlan(plan)"
                                class="btn btn-primary w-100">
                            <i class="fa fa-shopping-cart me-1"></i> Pilih Paket
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import { Head, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

export default {
    layout: LayoutAdmin,
    components: { Head },
    props: {
        subscription: Object,
        currentPlan: Object,
        plans: Array,
        invoices: Array,
        usage: Object,
    },
    computed: {
        statusBadgeClass() {
            if (!this.subscription) return 'badge bg-secondary';
            const classes = {
                'active': 'badge bg-success',
                'cancelled': 'badge bg-danger',
                'expired': 'badge bg-warning',
                'past_due': 'badge bg-danger',
                'pending': 'badge bg-info',
            };
            return classes[this.subscription.status] || 'badge bg-secondary';
        },
        statusText() {
            if (!this.subscription) return '-';
            const texts = {
                'active': 'Aktif',
                'cancelled': 'Dibatalkan',
                'expired': 'Kedaluwarsa',
                'past_due': 'Jatuh Tempo',
                'pending': 'Menunggu',
            };
            return texts[this.subscription.status] || this.subscription.status;
        },
    },
    methods: {
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
        },
        getProgressClass(percentage) {
            if (percentage >= 90) return 'bg-danger';
            if (percentage >= 75) return 'bg-warning';
            return 'bg-success';
        },
        getInvoiceStatusClass(status) {
            const classes = {
                'paid': 'badge bg-success',
                'pending': 'badge bg-warning',
                'cancelled': 'badge bg-secondary',
                'refunded': 'badge bg-info',
            };
            return classes[status] || 'badge bg-secondary';
        },
        getInvoiceStatusText(status) {
            const texts = {
                'paid': 'Lunas',
                'pending': 'Menunggu',
                'cancelled': 'Dibatalkan',
                'refunded': 'Refund',
            };
            return texts[status] || status;
        },
        selectPlan(plan) {
            Swal.fire({
                title: `Pilih Paket ${plan.name}?`,
                html: `
                    <p>Anda akan meng-upgrade ke paket <strong>${plan.name}</strong></p>
                    <p class="text-primary h4">${plan.formatted_price}/${plan.billing_cycle === 'monthly' ? 'bulan' : 'tahun'}</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Pilih Paket',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    router.post('/admin/billing/upgrade', {
                        plan_id: plan.id,
                    });
                }
            });
        },
        cancelSubscription() {
            Swal.fire({
                title: 'Batalkan Langganan?',
                html: `
                    <p>Apakah Anda yakin ingin membatalkan langganan?</p>
                    <p class="text-muted small">Anda masih dapat menggunakan layanan sampai periode berakhir.</p>
                    <textarea id="cancel-reason" class="form-control mt-3" placeholder="Alasan pembatalan (opsional)"></textarea>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Tidak',
                confirmButtonColor: '#dc3545',
                preConfirm: () => {
                    return document.getElementById('cancel-reason').value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    router.post('/admin/billing/cancel', {
                        reason: result.value,
                    });
                }
            });
        },
    },
}
</script>
