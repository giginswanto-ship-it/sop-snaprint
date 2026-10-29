<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name');
            $table->string('operator_name');
            $table->string('operator_url');
            $table->string('document_title');
            $table->string('document_subtitle')->nullable();
            $table->string('document_version')->default('1.0');
            $table->string('effective_date')->default('1 November 2026');
            $table->string('status')->default('Master Franchise Operations Manual');
            $table->text('confidentiality_statement')->nullable();
            $table->timestamps();
        });

        Schema::create('sop_modules', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. 'MODUL 1'
            $table->string('title'); // e.g. 'OPERASIONAL & PELAYANAN PELANGGAN'
            $table->string('category')->nullable(); // e.g. 'FRONT OFFICE'
            $table->text('description')->nullable();
            $table->integer('order')->default(1);
            $table->timestamps();
        });

        Schema::create('sops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('sop_modules')->onDelete('cascade');
            $table->string('sop_code')->unique(); // e.g. 'SOP-1.1'
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->nullable();
            $table->text('purpose')->nullable();
            $table->text('scope')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('indicators')->nullable();
            $table->text('reference_forms')->nullable();
            $table->longText('content_html');
            $table->integer('order')->default(1);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('document_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->nullable()->constrained('sop_modules')->nullOnDelete();
            $table->string('form_code')->unique(); // e.g. 'FRM-SNA-FO-01'
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('reference_sop')->nullable(); // e.g. 'SOP-1.1'
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->longText('template_html');
            $table->json('fields_schema')->nullable();
            $table->integer('order')->default(1);
            $table->timestamps();
        });

        Schema::create('guidelines', function (Blueprint $table) {
            $table->id();
            $table->string('section_code')->unique(); // e.g. 'RINGKASAN_EKSEKUTIF', 'BAGIAN_1', 'BAGIAN_2'
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content_html');
            $table->integer('order')->default(1);
            $table->timestamps();
        });

        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_form_id')->nullable()->constrained('document_forms')->nullOnDelete();
            $table->string('form_code');
            $table->string('outlet_name');
            $table->string('submitted_by');
            $table->date('submission_date');
            $table->json('form_data');
            $table->string('status')->default('Submitted'); // Draft, Submitted, Verified, Approved
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('guidelines');
        Schema::dropIfExists('document_forms');
        Schema::dropIfExists('sops');
        Schema::dropIfExists('sop_modules');
        Schema::dropIfExists('company_profiles');
    }
};
