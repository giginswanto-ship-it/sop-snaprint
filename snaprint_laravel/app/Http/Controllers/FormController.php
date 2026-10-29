<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyProfile;
use App\Models\SopModule;
use App\Models\DocumentForm;
use App\Models\FormSubmission;

class FormController extends Controller
{
    /**
     * Display all operational control forms
     */
    public function index()
    {
        $company = CompanyProfile::first();
        $modules = SopModule::with('forms')->orderBy('order', 'asc')->get();
        $forms = DocumentForm::with('module')->orderBy('order', 'asc')->get();

        return view('forms.index', compact('company', 'modules', 'forms'));
    }

    /**
     * Show single form template and interactive fill option
     */
    public function show($slug)
    {
        $company = CompanyProfile::first();
        $form = DocumentForm::where('slug', $slug)
            ->orWhere('form_code', strtoupper($slug))
            ->firstOrFail();

        $modules = SopModule::with('forms')->orderBy('order', 'asc')->get();
        $recentSubmissions = $form->submissions()->limit(5)->get();

        return view('forms.show', compact('company', 'form', 'modules', 'recentSubmissions'));
    }

    /**
     * Submit an interactive form log/entry
     */
    public function submit(Request $request, $slug)
    {
        $form = DocumentForm::where('slug', $slug)
            ->orWhere('form_code', strtoupper($slug))
            ->firstOrFail();

        $validated = $request->validate([
            'outlet_name' => 'required|string|max:255',
            'submitted_by' => 'required|string|max:255',
            'submission_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Capture all dynamic form fields into form_data
        $formData = $request->except(['_token', 'outlet_name', 'submitted_by', 'submission_date', 'notes']);

        $submission = FormSubmission::create([
            'document_form_id' => $form->id,
            'form_code' => $form->form_code,
            'outlet_name' => $validated['outlet_name'],
            'submitted_by' => $validated['submitted_by'],
            'submission_date' => $validated['submission_date'],
            'form_data' => $formData,
            'status' => 'Submitted',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('submissions.show', $submission->id)
            ->with('success', 'Formulir kontrol ' . $form->form_code . ' berhasil dicatat ke dalam database SQL!');
    }
}
