<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Models\CompanyProfile;
use App\Models\SopModule;
use App\Models\Sop;
use App\Models\DocumentForm;
use App\Models\Guideline;
use App\Models\FormSubmission;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin User
        User::firstOrCreate(
            ['email' => 'admin@dutaglobaltech.com'],
            [
                'name' => 'Snaprint HQ Administrator',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'storemanager@snaprint.id'],
            [
                'name' => 'Store Manager Gerai',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Load JSON seed data
        $dataPath = database_path('seeders/data/snaprint_seed.json');
        if (!File::exists($dataPath)) {
            $this->command->error("Seed data file not found at: {$dataPath}");
            return;
        }

        $json = json_decode(File::get($dataPath), true);

        // 3. Company Profile
        if (!empty($json['company_profile'])) {
            CompanyProfile::updateOrCreate(
                ['brand_name' => $json['company_profile']['brand_name']],
                $json['company_profile']
            );
        }

        // 4. Modules
        $moduleMap = [];
        if (!empty($json['modules'])) {
            foreach ($json['modules'] as $mod) {
                $module = SopModule::updateOrCreate(
                    ['code' => $mod['code']],
                    $mod
                );
                $moduleMap[$mod['code']] = $module->id;
            }
        }

        // 5. Guidelines
        if (!empty($json['guidelines'])) {
            foreach ($json['guidelines'] as $g) {
                Guideline::updateOrCreate(
                    ['section_code' => $g['section_code']],
                    $g
                );
            }
        }

        // 6. SOPs
        if (!empty($json['sops'])) {
            foreach ($json['sops'] as $s) {
                $moduleId = $moduleMap[$s['module_code']] ?? null;
                if ($moduleId) {
                    Sop::updateOrCreate(
                        ['sop_code' => $s['sop_code']],
                        [
                            'module_id' => $moduleId,
                            'title' => $s['title'],
                            'slug' => $s['slug'],
                            'order' => $s['order'],
                            'purpose' => $s['purpose'],
                            'scope' => $s['scope'],
                            'responsibilities' => $s['responsibilities'],
                            'indicators' => $s['indicators'],
                            'reference_forms' => $s['reference_forms'],
                            'content_html' => $s['content_html'],
                            'status' => 'active',
                        ]
                    );
                }
            }
        }

        // 7. Forms
        $formObjMap = [];
        if (!empty($json['forms'])) {
            foreach ($json['forms'] as $f) {
                $moduleId = $moduleMap[$f['module_code']] ?? null;
                $form = DocumentForm::updateOrCreate(
                    ['form_code' => $f['form_code']],
                    [
                        'module_id' => $moduleId,
                        'title' => $f['title'],
                        'slug' => $f['slug'],
                        'reference_sop' => $f['reference_sop'],
                        'category' => $f['category'],
                        'order' => $f['order'],
                        'fields_schema' => $f['fields_schema'],
                        'template_html' => $f['template_html'],
                    ]
                );
                $formObjMap[$f['form_code']] = $form;
            }
        }

        // 8. Sample Form Submissions
        $sampleSubmissions = [
            [
                'form_code' => 'FRM-SNA-FO-01',
                'outlet_name' => 'Snaprint Cabang BSD Tangerang',
                'submitted_by' => 'Rian Kurniawan (Store Manager)',
                'submission_date' => now()->format('Y-m-d'),
                'status' => 'Verified',
                'form_data' => [
                    'outlet_name' => 'Snaprint Cabang BSD Tangerang',
                    'operator_name' => 'Rian Kurniawan',
                    'check_date' => now()->format('Y-m-d'),
                    'shift' => 'Pagi (08.00 - 16.00)',
                    'opening_status' => 'OK Lengkap Sesuai Standar',
                    'notes' => 'Peralatan POS, mesin cetak Ricoh Pro C7200, dan AC operasional normal. Gerai siap buka pukul 08.00 tepat.',
                ],
                'notes' => 'Diverifikasi oleh Tim QC Area Pusat.'
            ],
            [
                'form_code' => 'FRM-SNA-MNT-01',
                'outlet_name' => 'Snaprint Cabang Kemang Jakarta',
                'submitted_by' => 'Budi Santoso (Lead Operator)',
                'submission_date' => now()->subDay()->format('Y-m-d'),
                'status' => 'Approved',
                'form_data' => [
                    'outlet_name' => 'Snaprint Cabang Kemang Jakarta',
                    'operator_name' => 'Budi Santoso',
                    'check_date' => now()->subDay()->format('Y-m-d'),
                    'shift' => 'Pagi (08.00 - 16.00)',
                    'machine_name' => 'Konica Minolta AccurioPress C4080',
                    'nozzle_status' => '100% Sempurna (No Clog)',
                    'counter_start' => 124500,
                    'counter_end' => 128920,
                    'notes' => 'Pembersihan roller fuser dan suction dust filter telah dilakukan sesuai SOP-3.1.',
                ],
                'notes' => 'Hasil uji nozzle check kliping terlampir.'
            ],
            [
                'form_code' => 'FRM-SNA-FIN-01',
                'outlet_name' => 'Snaprint Cabang Dipatiukur Bandung',
                'submitted_by' => 'Siti Nurhaliza (Kasir Shift 1)',
                'submission_date' => now()->format('Y-m-d'),
                'status' => 'Verified',
                'form_data' => [
                    'outlet_name' => 'Snaprint Cabang Dipatiukur Bandung',
                    'operator_name' => 'Siti Nurhaliza',
                    'check_date' => now()->format('Y-m-d'),
                    'shift' => 'Pagi (08.00 - 16.00)',
                    'total_cash' => 4850000,
                    'total_edc' => 8420000,
                    'system_sales' => 13270000,
                    'cash_diff' => 0,
                    'notes' => 'Z-Report POS telah dicetak. Kas fisik laci balance 100% dengan rekonsiliasi settlement EDC BCA & QRIS.',
                ],
                'notes' => 'Otorisasi setoran bank oleh Store Manager.'
            ]
        ];

        foreach ($sampleSubmissions as $sub) {
            $formObj = $formObjMap[$sub['form_code']] ?? null;
            FormSubmission::create([
                'document_form_id' => $formObj ? $formObj->id : null,
                'form_code' => $sub['form_code'],
                'outlet_name' => $sub['outlet_name'],
                'submitted_by' => $sub['submitted_by'],
                'submission_date' => $sub['submission_date'],
                'form_data' => $sub['form_data'],
                'status' => $sub['status'],
                'notes' => $sub['notes'],
            ]);
        }
    }
}
