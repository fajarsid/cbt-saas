<template>
    <Head>
        <title>Export Data</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row">
            <div class="col-md-12 mb-4">
                <h4><i class="fa fa-download me-2"></i>Export Data</h4>
                <p class="text-muted">Download data dalam format CSV atau backup lengkap</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-body text-center">
                        <i class="fa fa-users fa-3x text-primary mb-3"></i>
                        <h5>Data Peserta</h5>
                        <p class="text-muted">{{ stats.students }} peserta terdaftar</p>
                        <a href="/export/students" class="btn btn-primary border-0 shadow">
                            <i class="fa fa-download me-1"></i> Download CSV
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-body text-center">
                        <i class="fa fa-file-alt fa-3x text-success mb-3"></i>
                        <h5>Hasil Ujian</h5>
                        <p class="text-muted">{{ stats.grades }} hasil ujian</p>
                        <button @click="showExamSelector" class="btn btn-success border-0 shadow">
                            <i class="fa fa-download me-1"></i> Pilih Ujian
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-body text-center">
                        <i class="fa fa-archive fa-3x text-warning mb-3"></i>
                        <h5>Backup Lengkap</h5>
                        <p class="text-muted">Semua data dalam ZIP</p>
                        <a href="/export/backup" class="btn btn-warning border-0 shadow">
                            <i class="fa fa-download me-1"></i> Download Backup
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fa fa-info-circle me-2"></i>Informasi Export</h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Data Peserta: Berisi NISN, nama, email, telepon, kelas, dan status</li>
                            <li>Hasil Ujian: Berisi nilai per peserta untuk ujian yang dipilih</li>
                            <li>Backup Lengkap: Berisi semua data peserta, kelas, dan ujian dalam format ZIP</li>
                            <li>Format CSV kompatibel dengan Microsoft Excel</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import { Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

export default {
    layout: LayoutAdmin,
    components: { Head },
    props: {
        stats: Object,
    },
    setup() {
        const showExamSelector = () => {
            Swal.fire({
                title: 'Pilih Ujian',
                input: 'select',
                inputOptions: {}, // Will be populated from API
                inputPlaceholder: 'Pilih ujian untuk export',
                showCancelButton: true,
                confirmButtonText: 'Download',
                showLoaderOnConfirm: true,
                preConfirm: (examId) => {
                    if (examId) {
                        window.location.href = `/export/exam-results/${examId}`;
                    }
                }
            });
        };

        return { showExamSelector }
    }
}
</script>
