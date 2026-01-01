<template>
    <Head>
        <title>Pengaturan - {{ tenant.name }}</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row">
            <div class="col-md-3">
                <div class="card border-0 shadow mb-3">
                    <div class="card-body">
                        <div class="nav flex-column nav-pills">
                            <button class="nav-link text-start" :class="{ active: activeTab === 'general' }" @click="activeTab = 'general'">
                                <i class="fa fa-cog me-2"></i> Umum
                            </button>
                            <button class="nav-link text-start" :class="{ active: activeTab === 'branding' }" @click="activeTab = 'branding'">
                                <i class="fa fa-palette me-2"></i> Branding
                            </button>
                            <button class="nav-link text-start" :class="{ active: activeTab === 'notifications' }" @click="activeTab = 'notifications'">
                                <i class="fa fa-bell me-2"></i> Notifikasi
                            </button>
                            <button class="nav-link text-start" :class="{ active: activeTab === 'quota' }" @click="activeTab = 'quota'">
                                <i class="fa fa-chart-pie me-2"></i> Kuota
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <!-- General Settings -->
                <div v-if="activeTab === 'general'" class="card border-0 shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-cog me-2"></i>Pengaturan Umum</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="updateGeneral">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Organisasi</label>
                                    <input type="text" class="form-control" v-model="generalForm.name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" v-model="generalForm.email">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" class="form-control" v-model="generalForm.phone">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Website</label>
                                    <input type="url" class="form-control" v-model="generalForm.website">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" v-model="generalForm.address" rows="2"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Timezone</label>
                                    <select class="form-select" v-model="generalForm.timezone">
                                        <option value="Asia/Jakarta">WIB (Jakarta)</option>
                                        <option value="Asia/Makassar">WITA (Makassar)</option>
                                        <option value="Asia/Jayapura">WIT (Jayapura)</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Format Tanggal</label>
                                    <select class="form-select" v-model="generalForm.date_format">
                                        <option value="d/m/Y">DD/MM/YYYY</option>
                                        <option value="Y-m-d">YYYY-MM-DD</option>
                                        <option value="d M Y">DD Mon YYYY</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Format Waktu</label>
                                    <select class="form-select" v-model="generalForm.time_format">
                                        <option value="H:i">24 Jam</option>
                                        <option value="h:i A">12 Jam</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary border-0 shadow" :disabled="generalForm.processing">
                                <i class="fa fa-save me-1"></i> Simpan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Branding Settings -->
                <div v-if="activeTab === 'branding'" class="card border-0 shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-palette me-2"></i>Pengaturan Branding</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="updateBranding" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Warna Primer</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" v-model="brandingForm.primary_color">
                                        <input type="text" class="form-control" v-model="brandingForm.primary_color">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Warna Sekunder</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" v-model="brandingForm.secondary_color">
                                        <input type="text" class="form-control" v-model="brandingForm.secondary_color">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Logo</label>
                                    <input type="file" class="form-control" @change="handleLogo" accept="image/*">
                                    <img v-if="tenant.logo" :src="`/storage/${tenant.logo}`" class="mt-2" style="max-height:60px">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Favicon</label>
                                    <input type="file" class="form-control" @change="handleFavicon" accept="image/*">
                                    <img v-if="tenant.favicon" :src="`/storage/${tenant.favicon}`" class="mt-2" style="max-height:32px">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary border-0 shadow" :disabled="brandingForm.processing">
                                <i class="fa fa-save me-1"></i> Simpan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div v-if="activeTab === 'notifications'" class="card border-0 shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-bell me-2"></i>Pengaturan Notifikasi</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="updateNotification">
                            <h6 class="mb-3">WhatsApp (Fonnte)</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">API Key Fonnte</label>
                                    <input type="password" class="form-control" v-model="notifForm.fonnte_api_key" placeholder="Masukkan API Key">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Pengirim</label>
                                    <input type="text" class="form-control" v-model="notifForm.fonnte_sender" placeholder="08xxxxxxxx">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" v-model="notifForm.whatsapp_enabled" id="wa_enabled">
                                    <label class="form-check-label" for="wa_enabled">Aktifkan WhatsApp</label>
                                </div>
                            </div>
                            <button type="button" @click="testWhatsApp" class="btn btn-outline-success btn-sm mb-3">
                                <i class="fa fa-paper-plane me-1"></i> Test Koneksi
                            </button>
                            <hr>
                            <h6 class="mb-3">Pengingat Ujian</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" v-model="notifForm.exam_reminder_enabled" id="reminder_enabled">
                                        <label class="form-check-label" for="reminder_enabled">Kirim Pengingat Ujian</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jam Sebelum Ujian</label>
                                    <input type="number" class="form-control" v-model="notifForm.exam_reminder_hours" min="1" max="72">
                                </div>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" class="form-check-input" v-model="notifForm.result_notification_enabled" id="result_enabled">
                                <label class="form-check-label" for="result_enabled">Kirim Notifikasi Hasil Ujian</label>
                            </div>
                            <button type="submit" class="btn btn-primary border-0 shadow" :disabled="notifForm.processing">
                                <i class="fa fa-save me-1"></i> Simpan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Quota Info -->
                <div v-if="activeTab === 'quota'" class="card border-0 shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-chart-pie me-2"></i>Penggunaan Kuota</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <h6>Peserta</h6>
                                <div class="progress" style="height: 25px">
                                    <div class="progress-bar" :class="getProgressColor(studentsPercentage)"
                                         :style="`width: ${studentsPercentage}%`">
                                        {{ studentsCount }} / {{ tenant.max_students }}
                                    </div>
                                </div>
                                <small class="text-muted">{{ studentsPercentage }}% terpakai</small>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h6>Ujian</h6>
                                <div class="progress" style="height: 25px">
                                    <div class="progress-bar" :class="getProgressColor(examsPercentage)"
                                         :style="`width: ${examsPercentage}%`">
                                        {{ examsCount }} / {{ tenant.max_exams }}
                                    </div>
                                </div>
                                <small class="text-muted">{{ examsPercentage }}% terpakai</small>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <strong>Paket Saat Ini:</strong> {{ tenant.plan }}
                            <br>
                            <small>Butuh kuota lebih? Hubungi admin untuk upgrade paket.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

export default {
    layout: LayoutAdmin,
    components: { Head },
    props: {
        tenant: Object,
        notificationSetting: Object,
    },
    setup(props) {
        const activeTab = ref('general');

        const generalForm = useForm({
            name: props.tenant.name,
            email: props.tenant.email,
            phone: props.tenant.phone || '',
            website: props.tenant.website || '',
            address: props.tenant.address || '',
            timezone: props.tenant.timezone || 'Asia/Jakarta',
            date_format: props.tenant.date_format || 'd/m/Y',
            time_format: props.tenant.time_format || 'H:i',
        });

        const brandingForm = useForm({
            primary_color: props.tenant.primary_color || '#1F2937',
            secondary_color: props.tenant.secondary_color || '#3B82F6',
            logo: null,
            favicon: null,
        });

        const notifForm = useForm({
            fonnte_api_key: props.notificationSetting?.fonnte_api_key || '',
            fonnte_sender: props.notificationSetting?.fonnte_sender || '',
            whatsapp_enabled: props.notificationSetting?.whatsapp_enabled || false,
            email_enabled: props.notificationSetting?.email_enabled || false,
            exam_reminder_enabled: props.notificationSetting?.exam_reminder_enabled || false,
            exam_reminder_hours: props.notificationSetting?.exam_reminder_hours || 24,
            result_notification_enabled: props.notificationSetting?.result_notification_enabled || false,
        });

        const studentsCount = computed(() => props.tenant.students_count || 0);
        const examsCount = computed(() => props.tenant.exams_count || 0);
        const studentsPercentage = computed(() => Math.round((studentsCount.value / props.tenant.max_students) * 100));
        const examsPercentage = computed(() => Math.round((examsCount.value / props.tenant.max_exams) * 100));

        const getProgressColor = (percentage) => {
            if (percentage >= 90) return 'bg-danger';
            if (percentage >= 70) return 'bg-warning';
            return 'bg-success';
        };

        const handleLogo = (e) => { brandingForm.logo = e.target.files[0]; };
        const handleFavicon = (e) => { brandingForm.favicon = e.target.files[0]; };

        const updateGeneral = () => generalForm.post('/settings/general');
        const updateBranding = () => brandingForm.post('/settings/branding');
        const updateNotification = () => notifForm.post('/settings/notification');

        const testWhatsApp = () => {
            router.post('/settings/test-whatsapp', {}, {
                onSuccess: () => Swal.fire('Berhasil', 'Test koneksi berhasil!', 'success'),
                onError: () => Swal.fire('Gagal', 'Koneksi gagal. Periksa API key.', 'error'),
            });
        };

        return {
            activeTab, generalForm, brandingForm, notifForm,
            studentsCount, examsCount, studentsPercentage, examsPercentage,
            getProgressColor, handleLogo, handleFavicon,
            updateGeneral, updateBranding, updateNotification, testWhatsApp
        }
    }
}
</script>
