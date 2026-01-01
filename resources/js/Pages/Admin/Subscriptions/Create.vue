<template>
    <Head>
        <title>Tambah Langganan - CBT SaaS</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row">
            <div class="col-md-12">
                <Link href="/admin/subscriptions" class="btn btn-md btn-secondary border-0 shadow mb-3">
                    <i class="fa fa-arrow-left"></i> Kembali
                </Link>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-credit-card me-2"></i>Tambah Langganan</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="store">
                            <div class="mb-3">
                                <label class="form-label">Organisasi <span class="text-danger">*</span></label>
                                <select class="form-select" v-model="form.tenant_id">
                                    <option value="">Pilih Organisasi</option>
                                    <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                                        {{ tenant.name }}
                                    </option>
                                </select>
                                <div v-if="errors.tenant_id" class="text-danger small">{{ errors.tenant_id }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Paket <span class="text-danger">*</span></label>
                                <select class="form-select" v-model="form.plan_id" @change="selectPlan">
                                    <option value="">Pilih Paket</option>
                                    <option v-for="plan in plans" :key="plan.id" :value="plan.id">
                                        {{ plan.name }} - Rp {{ formatNumber(plan.price) }}/{{ plan.billing_cycle === 'monthly' ? 'bulan' : 'tahun' }}
                                    </option>
                                </select>
                                <div v-if="errors.plan_id" class="text-danger small">{{ errors.plan_id }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mulai</label>
                                    <input type="date" class="form-control" v-model="form.starts_at">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Berakhir</label>
                                    <input type="date" class="form-control" v-model="form.ends_at">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan</label>
                                <textarea class="form-control" v-model="form.notes" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary border-0 shadow" :disabled="form.processing">
                                <i class="fa fa-save me-1"></i> Simpan
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
import { Head, Link, useForm } from '@inertiajs/vue3';

export default {
    layout: LayoutAdmin,
    components: { Head, Link },
    props: {
        tenants: Array,
        plans: Array,
        errors: Object,
    },
    setup(props) {
        const today = new Date().toISOString().split('T')[0];
        const nextMonth = new Date();
        nextMonth.setMonth(nextMonth.getMonth() + 1);

        const form = useForm({
            tenant_id: '',
            plan_id: '',
            starts_at: today,
            ends_at: nextMonth.toISOString().split('T')[0],
            notes: '',
        });

        const formatNumber = (num) => new Intl.NumberFormat('id-ID').format(num);

        const selectPlan = () => {
            const plan = props.plans.find(p => p.id === form.plan_id);
            if (plan) {
                const end = new Date();
                if (plan.billing_cycle === 'monthly') {
                    end.setMonth(end.getMonth() + 1);
                } else {
                    end.setFullYear(end.getFullYear() + 1);
                }
                form.ends_at = end.toISOString().split('T')[0];
            }
        }

        const store = () => {
            form.post('/admin/subscriptions');
        }

        return { form, formatNumber, selectPlan, store }
    }
}
</script>
