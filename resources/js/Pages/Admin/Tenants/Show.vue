<template>
    <Head>
        <title>{{ tenant.name }} - CBT SaaS</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <Link href="/admin/tenants" class="btn btn-md btn-primary border-0 shadow" type="button">
                        <i class="fa fa-long-arrow-alt-left me-2"></i> Kembali
                    </Link>
                    <div>
                        <Link :href="`/admin/tenants/${tenant.id}/edit`" class="btn btn-info border-0 shadow me-2">
                            <i class="fa fa-pencil-alt me-2"></i> Edit
                        </Link>
                        <button @click="impersonate" class="btn btn-warning border-0 shadow">
                            <i class="fa fa-sign-in-alt me-2"></i> Login as Tenant
                        </button>
                    </div>
                </div>

                <!-- Header Card -->
                <div class="card border-0 shadow mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="mb-1">{{ tenant.name }}</h4>
                                <p class="text-muted mb-2">{{ tenant.email }}</p>
                                <span :class="statusBadgeClass(tenant.status)" class="me-2">{{ tenant.status }}</span>
                                <span :class="planBadgeClass(tenant.plan)">{{ tenant.plan }}</span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">Terdaftar sejak</small>
                                <p class="mb-0 fw-bold">{{ formatDate(tenant.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card border-0 shadow">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ stats.students_count }}/{{ tenant.max_students }}</h3>
                                <small class="text-muted">Siswa</small>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar" :style="{ width: (stats.students_count / tenant.max_students * 100) + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 shadow">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ stats.exams_count }}/{{ tenant.max_exams }}</h3>
                                <small class="text-muted">Ujian</small>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar bg-info" :style="{ width: (stats.exams_count / tenant.max_exams * 100) + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 shadow">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ stats.exam_sessions_count }}</h3>
                                <small class="text-muted">Sesi Ujian</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 shadow">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ stats.classrooms_count }}</h3>
                                <small class="text-muted">Kelas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 shadow">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ stats.lessons_count }}</h3>
                                <small class="text-muted">Mata Pelajaran</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 shadow">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ stats.users_count }}</h3>
                                <small class="text-muted">Admin</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Upcoming Exams -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Ujian Terjadwal</h6>
                            </div>
                            <div class="card-body">
                                <div v-if="upcomingExams.length > 0">
                                    <div v-for="exam in upcomingExams" :key="exam.id" class="d-flex justify-content-between align-items-center border-bottom py-2">
                                        <div>
                                            <strong>{{ exam.title }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ exam.exam?.lesson?.title }} - {{ exam.exam?.classroom?.title }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">{{ formatDateTime(exam.start_time) }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center text-muted py-3">
                                    Tidak ada ujian terjadwal
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Users -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Admin Organisasi</h6>
                            </div>
                            <div class="card-body">
                                <div v-if="admins.length > 0">
                                    <div v-for="admin in admins" :key="admin.id" class="d-flex justify-content-between align-items-center border-bottom py-2">
                                        <div>
                                            <strong>{{ admin.name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ admin.email }}</small>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">
                                                Bergabung {{ formatDate(admin.created_at) }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center text-muted py-3">
                                    Tidak ada admin
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tenant Details -->
<div class="card border-0 shadow mt-4">
    <div class="card-header bg-white">
        <h6 class="mb-0">Detail Organisasi</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td class="text-muted" width="150">Telepon</td>
                            <td>{{ tenant.phone || '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat</td>
                            <td>{{ tenant.address || '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Slug</td>
                            <td><code>{{ tenant.slug }}</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td class="text-muted" width="150">Domain</td>
                            <td>{{ tenant.domain || '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Plan</td>
                            <td>
                                <span :class="planBadgeClass(tenant.plan)">
                                    {{ tenant.plan }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                <span :class="statusBadgeClass(tenant.status)">
                                    {{ tenant.status }}
                                </span>
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
    </div>
</template>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

export default {
    layout: LayoutAdmin,
    components: {
        Head,
        Link
    },
    props: {
        tenant: Object,
        stats: Object,
        upcomingExams: Array,
        admins: Array,
    },
    setup(props) {
        const planBadgeClass = (plan) => {
            const classes = {
                'free': 'badge bg-secondary',
                'basic': 'badge bg-info',
                'premium': 'badge bg-warning',
                'enterprise': 'badge bg-primary'
            };
            return classes[plan] || 'badge bg-secondary';
        };

        const statusBadgeClass = (status) => {
            const classes = {
                'active': 'badge bg-success',
                'inactive': 'badge bg-secondary',
                'suspended': 'badge bg-danger'
            };
            return classes[status] || 'badge bg-secondary';
        };

        const formatDate = (date) => {
            return new Date(date).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        };

        const formatDateTime = (date) => {
            return new Date(date).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        };

        const impersonate = () => {
            router.post(`/admin/tenants/${props.tenant.id}/impersonate`);
        };

        return {
            planBadgeClass,
            statusBadgeClass,
            formatDate,
            formatDateTime,
            impersonate,
        };
    }
}
</script>
