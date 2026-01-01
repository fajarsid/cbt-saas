<template>
    <Head>
        <title>Pengaturan Notifikasi - {{ tenant.name }}</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row mb-4">
            <div class="col-md-12">
                <h4><i class="fa fa-bell me-2"></i>Pengaturan Notifikasi WhatsApp</h4>
                <p class="text-muted">Konfigurasi Fonnte WhatsApp API dan template pesan notifikasi</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card border-0 shadow mb-3">
                    <div class="card-body">
                        <div class="nav flex-column nav-pills">
                            <button class="nav-link text-start" :class="{ active: activeTab === 'fonnte' }" @click="activeTab = 'fonnte'">
                                <i class="fab fa-whatsapp me-2"></i> Konfigurasi Fonnte
                            </button>
                            <button class="nav-link text-start" :class="{ active: activeTab === 'preferences' }" @click="activeTab = 'preferences'">
                                <i class="fa fa-sliders-h me-2"></i> Preferensi
                            </button>
                            <button class="nav-link text-start" :class="{ active: activeTab === 'templates' }" @click="activeTab = 'templates'">
                                <i class="fa fa-file-alt me-2"></i> Template Pesan
                            </button>
                            <button class="nav-link text-start" :class="{ active: activeTab === 'test' }" @click="activeTab = 'test'">
                                <i class="fa fa-paper-plane me-2"></i> Test Kirim
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Connection Status Card -->
                <div class="card border-0 shadow">
                    <div class="card-header">
                        <h6 class="mb-0">Status Koneksi</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2" :class="connectionStatusClass">●</span>
                            <span>{{ connectionStatusText }}</span>
                        </div>
                        <small class="text-muted" v-if="setting.fonnte_last_check">
                            Terakhir cek: {{ setting.fonnte_last_check }}
                        </small>
                        <button @click="testConnection" class="btn btn-sm btn-outline-primary mt-2 w-100" :disabled="!setting.fonnte_api_key_exists">
                            <i class="fa fa-sync me-1"></i> Cek Ulang
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <!-- Fonnte Configuration -->
                <div v-if="activeTab === 'fonnte'" class="card border-0 shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fab fa-whatsapp me-2 text-success"></i>Konfigurasi Fonnte WhatsApp</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>
                            Dapatkan API Key dari <a href="https://fonnte.com" target="_blank">fonnte.com</a>.
                            Pastikan device WhatsApp Anda sudah terhubung di dashboard Fonnte.
                        </div>
                        <form @submit.prevent="saveFonnte">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">API Key Fonnte <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input :type="showApiKey ? 'text' : 'password'" class="form-control"
                                               v-model="fonnteForm.fonnte_api_key"
                                               placeholder="Masukkan API Key dari Fonnte">
                                        <button type="button" class="btn btn-outline-secondary" @click="showApiKey = !showApiKey">
                                            <i :class="showApiKey ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">API Key untuk autentikasi dengan Fonnte</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Pengirim <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" v-model="fonnteForm.fonnte_sender"
                                           placeholder="08xxxxxxxxxx">
                                    <small class="text-muted">Nomor WhatsApp yang terdaftar di Fonnte</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" v-model="fonnteForm.whatsapp_enabled" id="wa_enabled">
                                    <label class="form-check-label" for="wa_enabled">
                                        <strong>Aktifkan Notifikasi WhatsApp</strong>
                                    </label>
                                </div>
                                <small class="text-muted">Jika diaktifkan, notifikasi akan dikirim via WhatsApp ke peserta</small>
                            </div>
                            <button type="submit" class="btn btn-primary border-0 shadow" :disabled="fonnteForm.processing">
                                <i class="fa fa-save me-1"></i> Simpan Konfigurasi
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Preferences -->
                <div v-if="activeTab === 'preferences'" class="card border-0 shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-sliders-h me-2"></i>Preferensi Notifikasi</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="savePreferences">
                            <div class="mb-4">
                                <h6>Pengingat Ujian</h6>
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" v-model="prefForm.exam_reminder_enabled" id="reminder_enabled">
                                            <label class="form-check-label" for="reminder_enabled">Kirim pengingat sebelum ujian dimulai</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="number" class="form-control" v-model="prefForm.exam_reminder_hours" min="1" max="168" :disabled="!prefForm.exam_reminder_enabled">
                                            <span class="input-group-text">jam sebelum ujian</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6>Notifikasi Hasil Ujian</h6>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" v-model="prefForm.result_notification_enabled" id="result_enabled">
                                    <label class="form-check-label" for="result_enabled">Kirim notifikasi ketika hasil ujian tersedia</label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6>Notifikasi Selamat Datang</h6>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" v-model="prefForm.welcome_notification_enabled" id="welcome_enabled">
                                    <label class="form-check-label" for="welcome_enabled">Kirim pesan selamat datang untuk peserta baru</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary border-0 shadow" :disabled="prefForm.processing">
                                <i class="fa fa-save me-1"></i> Simpan Preferensi
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Templates -->
                <div v-if="activeTab === 'templates'" class="card border-0 shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa fa-file-alt me-2"></i>Template Pesan</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <i class="fa fa-info-circle me-2"></i>
                            Gunakan variabel dalam kurung kurawal untuk data dinamis. Contoh: <code>{nama_siswa}</code>
                        </div>

                        <!-- Template Tabs -->
                        <ul class="nav nav-tabs mb-3">
                            <li class="nav-item" v-for="(label, key) in templateLabels" :key="key">
                                <button class="nav-link" :class="{ active: activeTemplate === key }" @click="activeTemplate = key">
                                    {{ label }}
                                </button>
                            </li>
                        </ul>

                        <!-- Template Editor -->
                        <form @submit.prevent="saveTemplates">
                            <div v-for="(label, key) in templateLabels" :key="key" v-show="activeTemplate === key">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Judul Notifikasi</label>
                                            <input type="text" class="form-control" v-model="templateForm.templates[key].title">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Isi Pesan</label>
                                            <textarea class="form-control" v-model="templateForm.templates[key].message" rows="8"></textarea>
                                            <small class="text-muted">Gunakan *teks* untuk bold, _teks_ untuk italic</small>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="button" @click="previewTemplate(key)" class="btn btn-outline-info">
                                                <i class="fa fa-eye me-1"></i> Preview
                                            </button>
                                            <button type="button" @click="resetTemplate(key)" class="btn btn-outline-secondary">
                                                <i class="fa fa-undo me-1"></i> Reset ke Default
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light">
                                            <div class="card-header">
                                                <strong>Variabel yang Tersedia</strong>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tbody>
                                                        <tr v-for="(desc, varName) in templateVariables[key]" :key="varName">
                                                            <td>
                                                                <code class="cursor-pointer" @click="insertVariable(key, varName)" title="Klik untuk menyalin">
                                                                    {{ varName }}
                                                                </code>
                                                            </td>
                                                            <td><small class="text-muted">{{ desc }}</small></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <button type="submit" class="btn btn-primary border-0 shadow" :disabled="templateForm.processing">
                                <i class="fa fa-save me-1"></i> Simpan Semua Template
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Test Send -->
                <div v-if="activeTab === 'test'" class="card border-0 shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-paper-plane me-2"></i>Test Kirim Pesan</h5>
                    </div>
                    <div class="card-body">
                        <div v-if="!setting.fonnte_api_key_exists" class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            Silakan konfigurasi API Key Fonnte terlebih dahulu.
                        </div>
                        <form @submit.prevent="sendTestMessage" v-else>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Tujuan</label>
                                    <input type="text" class="form-control" v-model="testForm.phone" placeholder="08xxxxxxxxxx">
                                    <small class="text-muted">Nomor WhatsApp penerima</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pesan</label>
                                <textarea class="form-control" v-model="testForm.message" rows="5"
                                          placeholder="Ketik pesan test Anda di sini..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success border-0 shadow" :disabled="testForm.processing">
                                <i class="fa fa-paper-plane me-1"></i> Kirim Test
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <div class="modal fade" id="previewModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Preview Pesan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="bg-success text-white p-2 rounded-top">
                            <strong>{{ previewData.title }}</strong>
                        </div>
                        <div class="border p-3 rounded-bottom" style="white-space: pre-wrap; background: #ECE5DD;">
                            {{ previewData.message }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Swal from 'sweetalert2';

export default {
    layout: LayoutAdmin,
    components: { Head },
    props: {
        tenant: Object,
        setting: Object,
        templates: Object,
        templateVariables: Object,
        defaultTemplates: Object,
    },
    setup(props) {
        const activeTab = ref('fonnte');
        const activeTemplate = ref('exam_reminder');
        const showApiKey = ref(false);
        const previewData = ref({ title: '', message: '' });

        const templateLabels = {
            'exam_reminder': 'Pengingat Ujian',
            'exam_result': 'Hasil Ujian',
            'welcome': 'Selamat Datang',
            'announcement': 'Pengumuman',
            'exam_start': 'Ujian Dimulai',
        };

        // Forms
        const fonnteForm = useForm({
            fonnte_api_key: props.setting.fonnte_api_key_exists ? '********' : '',
            fonnte_sender: props.setting.fonnte_sender || '',
            whatsapp_enabled: props.setting.whatsapp_enabled || false,
        });

        const prefForm = useForm({
            exam_reminder_enabled: props.setting.exam_reminder_enabled || false,
            exam_reminder_hours: props.setting.exam_reminder_hours || 24,
            result_notification_enabled: props.setting.result_notification_enabled || false,
            welcome_notification_enabled: props.setting.welcome_notification_enabled ?? true,
            email_enabled: props.setting.email_enabled || false,
        });

        const templateForm = useForm({
            templates: JSON.parse(JSON.stringify(props.templates)),
        });

        const testForm = useForm({
            phone: '',
            message: 'Ini adalah test pesan dari ' + props.tenant.name,
        });

        const connectionStatusClass = computed(() => {
            if (!props.setting.fonnte_api_key_exists) return 'text-secondary';
            if (props.setting.fonnte_device_status === 'connected') return 'text-success';
            return 'text-danger';
        });

        const connectionStatusText = computed(() => {
            if (!props.setting.fonnte_api_key_exists) return 'Belum dikonfigurasi';
            if (props.setting.fonnte_device_status === 'connected') return 'Terhubung';
            return 'Tidak terhubung';
        });

        const saveFonnte = () => {
            fonnteForm.post('/admin/notification-settings/fonnte', {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('Berhasil', 'Konfigurasi Fonnte berhasil disimpan', 'success');
                }
            });
        };

        const savePreferences = () => {
            prefForm.post('/admin/notification-settings/preferences', {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('Berhasil', 'Preferensi berhasil disimpan', 'success');
                }
            });
        };

        const saveTemplates = () => {
            templateForm.post('/admin/notification-settings/templates', {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('Berhasil', 'Template pesan berhasil disimpan', 'success');
                }
            });
        };

        const testConnection = () => {
            router.post('/admin/notification-settings/test-connection', {}, {
                preserveScroll: true,
            });
        };

        const sendTestMessage = () => {
            testForm.post('/admin/notification-settings/send-test', {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('Berhasil', 'Pesan test berhasil dikirim!', 'success');
                }
            });
        };

        const previewTemplate = async (type) => {
            try {
                const response = await fetch(`/admin/notification-settings/preview?type=${type}`);
                const data = await response.json();
                previewData.value = data;

                // Show modal using Bootstrap
                const modal = new bootstrap.Modal(document.getElementById('previewModal'));
                modal.show();
            } catch (error) {
                console.error('Preview error:', error);
            }
        };

        const resetTemplate = (type) => {
            Swal.fire({
                title: 'Reset Template?',
                text: 'Template akan dikembalikan ke default',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, reset'
            }).then((result) => {
                if (result.isConfirmed) {
                    router.post('/admin/notification-settings/reset-template', { type }, {
                        preserveScroll: true,
                        onSuccess: () => {
                            templateForm.templates[type] = props.defaultTemplates[type];
                        }
                    });
                }
            });
        };

        const insertVariable = (type, varName) => {
            // Copy to clipboard
            navigator.clipboard.writeText(varName);
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `${varName} disalin!`,
                showConfirmButton: false,
                timer: 1500
            });
        };

        return {
            activeTab,
            activeTemplate,
            showApiKey,
            templateLabels,
            fonnteForm,
            prefForm,
            templateForm,
            testForm,
            previewData,
            connectionStatusClass,
            connectionStatusText,
            saveFonnte,
            savePreferences,
            saveTemplates,
            testConnection,
            sendTestMessage,
            previewTemplate,
            resetTemplate,
            insertVariable,
        }
    }
}
</script>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}
.cursor-pointer:hover {
    background-color: #e9ecef;
}
</style>
