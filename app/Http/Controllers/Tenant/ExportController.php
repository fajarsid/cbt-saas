<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use ZipArchive;

class ExportController extends Controller
{
    /**
     * Show export page
     */
    public function index(): Response
    {
        $tenant = auth()->user()->tenant;

        return Inertia::render('Tenant/Export/Index', [
            'stats' => [
                'students' => $tenant->students()->count(),
                'exams' => $tenant->exams()->count(),
                'grades' => Grade::whereHas('student', fn($q) => $q->where('tenant_id', $tenant->id))->count(),
            ],
        ]);
    }

    /**
     * Export students data
     */
    public function exportStudents(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $students = $tenant->students()->with('classroom')->get();

        $filename = "students-{$tenant->slug}-" . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['NISN', 'Nama', 'Email', 'Telepon', 'Jenis Kelamin', 'Kelas', 'Status', 'Dibuat']);

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->nisn,
                    $student->name,
                    $student->email ?? '',
                    $student->phone ?? '',
                    $student->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                    $student->classroom?->name ?? '',
                    $student->status ?? 'active',
                    $student->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        ActivityLog::log('export', 'Export data peserta', null, auth()->user());

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export exam results
     */
    public function exportExamResults(Request $request, Exam $exam)
    {
        $tenant = auth()->user()->tenant;

        // Verify exam belongs to tenant
        if ($exam->tenant_id !== $tenant->id) {
            abort(403);
        }

        $grades = Grade::with(['student.classroom', 'examSession'])
            ->whereHas('examSession', fn($q) => $q->where('exam_id', $exam->id))
            ->get();

        $filename = "hasil-{$exam->title}-" . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($grades, $exam) {
            $file = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['NISN', 'Nama Peserta', 'Kelas', 'Sesi', 'Nilai', 'Durasi (menit)', 'Waktu Selesai']);

            foreach ($grades as $grade) {
                fputcsv($file, [
                    $grade->student->nisn,
                    $grade->student->name,
                    $grade->student->classroom?->name ?? '',
                    $grade->examSession->title ?? '',
                    $grade->grade,
                    $grade->duration ?? '',
                    $grade->end_time?->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($file);
        };

        ActivityLog::log('export', "Export hasil ujian: {$exam->title}", $exam, auth()->user());

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export all data (backup)
     */
    public function exportBackup(Request $request)
    {
        $tenant = auth()->user()->tenant;

        // Create temporary directory
        $tempDir = storage_path('app/temp/backup-' . $tenant->slug . '-' . now()->format('YmdHis'));
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Export students
        $this->exportToCsv($tempDir . '/students.csv',
            $tenant->students()->with('classroom')->get(),
            ['nisn', 'name', 'email', 'phone', 'gender', 'classroom.name', 'status', 'created_at']
        );

        // Export classrooms
        $this->exportToCsv($tempDir . '/classrooms.csv',
            $tenant->classrooms,
            ['id', 'name', 'created_at']
        );

        // Export exams
        $this->exportToCsv($tempDir . '/exams.csv',
            $tenant->exams()->with('lesson')->get(),
            ['id', 'title', 'lesson.name', 'duration', 'random_question', 'random_answer', 'created_at']
        );

        // Create zip file
        $zipFilename = "backup-{$tenant->slug}-" . now()->format('Y-m-d') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFilename);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Cleanup temp directory
        array_map('unlink', glob($tempDir . '/*'));
        rmdir($tempDir);

        ActivityLog::log('export', 'Export backup data lengkap', null, auth()->user());

        return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);
    }

    /**
     * Helper to export data to CSV
     */
    protected function exportToCsv(string $filepath, $data, array $columns): void
    {
        $file = fopen($filepath, 'w');

        // BOM for Excel UTF-8
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header
        fputcsv($file, $columns);

        foreach ($data as $row) {
            $rowData = [];
            foreach ($columns as $column) {
                $value = $this->getNestedValue($row, $column);
                $rowData[] = $value instanceof \DateTime ? $value->format('Y-m-d H:i:s') : ($value ?? '');
            }
            fputcsv($file, $rowData);
        }

        fclose($file);
    }

    /**
     * Get nested value from object/array using dot notation
     */
    protected function getNestedValue($object, string $key)
    {
        $keys = explode('.', $key);
        $value = $object;

        foreach ($keys as $k) {
            if (is_array($value)) {
                $value = $value[$k] ?? null;
            } elseif (is_object($value)) {
                $value = $value->{$k} ?? null;
            } else {
                return null;
            }
        }

        return $value;
    }
}
