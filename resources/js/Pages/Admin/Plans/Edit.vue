<template>
    <Head>
        <title>Edit Paket - CBT SaaS</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row">
            <div class="col-md-12">
                <Link href="/admin/plans" class="btn btn-md btn-secondary border-0 shadow mb-3">
                    <i class="fa fa-arrow-left"></i> Kembali
                </Link>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-box me-2"></i>Edit Paket: {{ plan.name }}</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="update">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Paket <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" v-model="form.name">
                                    <div v-if="errors.name" class="text-danger small">{{ errors.name }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Slug <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" v-model="form.slug">
                                    <div v-if="errors.slug" class="text-danger small">{{ errors.slug }}</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" v-model="form.description" rows="2"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Harga <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" v-model="form.price" min="0">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Periode Tagihan <span class="text-danger">*</span></label>
                                    <select class="form-select" v-model="form.billing_cycle">
                                        <option value="monthly">Bulanan</option>
                                        <option value="yearly">Tahunan</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <h6 class="mb-3">Kuota</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Maks. Peserta</label>
                                    <input type="number" class="form-control" v-model="form.max_students" min="1">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Maks. Ujian</label>
                                    <input type="number" class="form-control" v-model="form.max_exams" min="1">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Maks. Admin</label>
                                    <input type="number" class="form-control" v-model="form.max_users" min="1">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Masa Trial (hari)</label>
                                    <input type="number" class="form-control" v-model="form.trial_days" min="0">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" class="form-check-input" v-model="form.is_active" id="is_active">
                                        <label class="form-check-label" for="is_active">Aktif</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" class="form-check-input" v-model="form.is_featured" id="is_featured">
                                        <label class="form-check-label" for="is_featured">Featured</label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary border-0 shadow" :disabled="form.processing">
                                <i class="fa fa-save me-1"></i> Update
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
    components: {
        Head,
        Link,
    },
    props: {
        plan: Object,
        errors: Object,
    },
    setup(props) {
        const form = useForm({
            name: props.plan.name,
            slug: props.plan.slug,
            description: props.plan.description || '',
            price: props.plan.price,
            billing_cycle: props.plan.billing_cycle,
            max_students: props.plan.max_students,
            max_exams: props.plan.max_exams,
            max_users: props.plan.max_users,
            trial_days: props.plan.trial_days,
            is_active: props.plan.is_active,
            is_featured: props.plan.is_featured,
            features: props.plan.features || [],
        });

        const update = () => {
            form.put(`/admin/plans/${props.plan.id}`);
        }

        return {
            form,
            update,
        }
    }
}
</script>
