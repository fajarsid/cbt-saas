<template>
    <Head>
        <title>Activity Log - CBT SaaS</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-select" v-model="filters.tenant_id" @change="applyFilters">
                    <option value="">Semua Organisasi</option>
                    <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                        {{ tenant.name }}
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" v-model="filters.log_type" @change="applyFilters">
                    <option value="">Semua Tipe</option>
                    <option v-for="type in logTypes" :key="type" :value="type">{{ type }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" v-model="filters.date_from" @change="applyFilters">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" v-model="filters.date_to" @change="applyFilters">
            </div>
            <div class="col-md-2">
                <a :href="exportUrl" class="btn btn-success border-0 w-100">
                    <i class="fa fa-download me-1"></i> Export
                </a>
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
                                        <th class="border-0 rounded-start" style="width:15%">Waktu</th>
                                        <th class="border-0">Organisasi</th>
                                        <th class="border-0" style="width:10%">Tipe</th>
                                        <th class="border-0">Deskripsi</th>
                                        <th class="border-0">User</th>
                                        <th class="border-0 rounded-end">IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="log in logs.data" :key="log.id">
                                        <td>
                                            <small>{{ formatDateTime(log.created_at) }}</small>
                                        </td>
                                        <td>{{ log.tenant?.name || 'System' }}</td>
                                        <td>
                                            <span :class="typeBadge(log.log_type)">{{ log.log_type }}</span>
                                        </td>
                                        <td>{{ log.description }}</td>
                                        <td>{{ log.causer?.name || 'System' }}</td>
                                        <td><small>{{ log.ip_address }}</small></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="logs.links" align="end" />
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
import { reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
    layout: LayoutAdmin,
    components: { Head, Link, Pagination },
    props: {
        logs: Object,
        tenants: Array,
        logTypes: Array,
        filters: Object,
    },
    setup(props) {
        const filters = reactive({
            tenant_id: props.filters?.tenant_id || '',
            log_type: props.filters?.log_type || '',
            date_from: props.filters?.date_from || '',
            date_to: props.filters?.date_to || '',
        });

        const formatDateTime = (date) => new Date(date).toLocaleString('id-ID');

        const typeBadge = (type) => ({
            'create': 'badge bg-success',
            'update': 'badge bg-info',
            'delete': 'badge bg-danger',
            'login': 'badge bg-primary',
            'export': 'badge bg-warning',
        }[type] || 'badge bg-secondary');

        const applyFilters = () => {
            router.get('/admin/activity-logs', filters);
        }

        const exportUrl = computed(() => {
            const params = new URLSearchParams(filters);
            return `/admin/activity-logs/export?${params.toString()}`;
        });

        return { filters, formatDateTime, typeBadge, applyFilters, exportUrl }
    }
}
</script>
