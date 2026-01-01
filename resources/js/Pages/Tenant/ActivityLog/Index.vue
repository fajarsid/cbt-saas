<template>
    <Head>
        <title>Log Aktivitas</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4><i class="fa fa-history me-2"></i>Log Aktivitas</h4>
                        <p class="text-muted mb-0">Riwayat aktivitas di sistem</p>
                    </div>
                    <a :href="exportUrl" class="btn btn-success">
                        <i class="fa fa-download me-1"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <form @submit.prevent="applyFilters" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tipe Log</label>
                                <select v-model="filterForm.log_type" class="form-select">
                                    <option value="">Semua Tipe</option>
                                    <option v-for="type in logTypes" :key="type" :value="type">
                                        {{ getTypeLabel(type) }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cari</label>
                                <input type="text" v-model="filterForm.search"
                                       class="form-control" placeholder="Cari deskripsi...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" v-model="filterForm.date_from" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" v-model="filterForm.date_to" class="form-control">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fa fa-search"></i> Filter
                                </button>
                                <button type="button" @click="resetFilters" class="btn btn-outline-secondary">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Log Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 140px;">Waktu</th>
                                        <th style="width: 100px;">Tipe</th>
                                        <th>Deskripsi</th>
                                        <th style="width: 150px;">User</th>
                                        <th style="width: 120px;">IP Address</th>
                                        <th style="width: 80px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="log in logs.data" :key="log.id">
                                        <td class="text-muted small">
                                            {{ formatDateTime(log.created_at) }}
                                        </td>
                                        <td>
                                            <span :class="getTypeBadgeClass(log.log_type)">
                                                {{ getTypeLabel(log.log_type) }}
                                            </span>
                                        </td>
                                        <td>{{ log.description }}</td>
                                        <td>
                                            <span v-if="log.causer">
                                                <i class="fa fa-user me-1"></i>
                                                {{ log.causer.name }}
                                            </span>
                                            <span v-else class="text-muted">System</span>
                                        </td>
                                        <td class="text-muted small">{{ log.ip_address || '-' }}</td>
                                        <td>
                                            <button @click="showDetail(log)"
                                                    class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="!logs.data || logs.data.length === 0">
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
                                            Tidak ada log aktivitas
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer" v-if="logs.last_page > 1">
                        <nav>
                            <ul class="pagination mb-0 justify-content-center">
                                <li class="page-item" :class="{ disabled: !logs.prev_page_url }">
                                    <a class="page-link" href="#" @click.prevent="goToPage(logs.current_page - 1)">
                                        <i class="fa fa-chevron-left"></i>
                                    </a>
                                </li>
                                <li v-for="page in visiblePages" :key="page" class="page-item"
                                    :class="{ active: page === logs.current_page }">
                                    <a class="page-link" href="#" @click.prevent="goToPage(page)">
                                        {{ page }}
                                    </a>
                                </li>
                                <li class="page-item" :class="{ disabled: !logs.next_page_url }">
                                    <a class="page-link" href="#" @click.prevent="goToPage(logs.current_page + 1)">
                                        <i class="fa fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
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
        logs: Object,
        logTypes: Array,
        filters: Object,
    },
    data() {
        return {
            filterForm: {
                log_type: this.filters?.log_type || '',
                search: this.filters?.search || '',
                date_from: this.filters?.date_from || '',
                date_to: this.filters?.date_to || '',
            },
        };
    },
    computed: {
        exportUrl() {
            const params = new URLSearchParams(this.filterForm);
            return `/admin/activity-logs/export?${params.toString()}`;
        },
        visiblePages() {
            const pages = [];
            const current = this.logs.current_page;
            const last = this.logs.last_page;

            let start = Math.max(1, current - 2);
            let end = Math.min(last, current + 2);

            for (let i = start; i <= end; i++) {
                pages.push(i);
            }

            return pages;
        },
    },
    methods: {
        formatDateTime(date) {
            if (!date) return '-';
            return new Date(date).toLocaleString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        },
        getTypeLabel(type) {
            const labels = {
                'create': 'Buat',
                'update': 'Ubah',
                'delete': 'Hapus',
                'login': 'Login',
                'logout': 'Logout',
                'export': 'Export',
                'import': 'Import',
                'billing': 'Billing',
                'notification': 'Notifikasi',
            };
            return labels[type] || type;
        },
        getTypeBadgeClass(type) {
            const classes = {
                'create': 'badge bg-success',
                'update': 'badge bg-info',
                'delete': 'badge bg-danger',
                'login': 'badge bg-primary',
                'logout': 'badge bg-secondary',
                'export': 'badge bg-warning text-dark',
                'import': 'badge bg-warning text-dark',
                'billing': 'badge bg-purple',
                'notification': 'badge bg-info',
            };
            return classes[type] || 'badge bg-secondary';
        },
        applyFilters() {
            router.get('/admin/activity-logs', this.filterForm, {
                preserveState: true,
                preserveScroll: true,
            });
        },
        resetFilters() {
            this.filterForm = {
                log_type: '',
                search: '',
                date_from: '',
                date_to: '',
            };
            router.get('/admin/activity-logs');
        },
        goToPage(page) {
            router.get('/admin/activity-logs', {
                ...this.filterForm,
                page: page,
            }, {
                preserveState: true,
                preserveScroll: true,
            });
        },
        showDetail(log) {
            let details = `
                <div class="text-start">
                    <p><strong>ID:</strong> ${log.id}</p>
                    <p><strong>Tipe:</strong> ${this.getTypeLabel(log.log_type)}</p>
                    <p><strong>Deskripsi:</strong> ${log.description}</p>
                    <p><strong>User:</strong> ${log.causer?.name || 'System'}</p>
                    <p><strong>IP:</strong> ${log.ip_address || '-'}</p>
                    <p><strong>Waktu:</strong> ${this.formatDateTime(log.created_at)}</p>
            `;

            if (log.properties && Object.keys(log.properties).length > 0) {
                details += `
                    <hr>
                    <p><strong>Data Tambahan:</strong></p>
                    <pre class="bg-light p-2 rounded small">${JSON.stringify(log.properties, null, 2)}</pre>
                `;
            }

            details += '</div>';

            Swal.fire({
                title: 'Detail Log Aktivitas',
                html: details,
                width: 600,
                confirmButtonText: 'Tutup',
            });
        },
    },
}
</script>

<style scoped>
.bg-purple {
    background-color: #6f42c1 !important;
    color: white;
}
</style>
