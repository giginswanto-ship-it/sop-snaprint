<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyProfile;
use App\Models\FormSubmission;
use App\Models\DocumentForm;

class SubmissionController extends Controller
{
    /**
     * Display logbook/archive of form submissions
     */
    public function index(Request $request)
    {
        $company = CompanyProfile::first();
        $query = FormSubmission::with('documentForm')->orderBy('created_at', 'desc');

        if ($request->filled('outlet')) {
            $query->where('outlet_name', 'LIKE', '%' . $request->outlet . '%');
        }

        if ($request->filled('form_code')) {
            $query->where('form_code', $request->form_code);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $submissions = $query->paginate(15);
        $forms = DocumentForm::select('form_code', 'title')->orderBy('form_code')->get();

        return view('submissions.index', compact('company', 'submissions', 'forms'));
    }

    /**
     * Display a specific submission record
     */
    public function show($id)
    {
        $company = CompanyProfile::first();
        $submission = FormSubmission::with('documentForm')->findOrFail($id);

        return view('submissions.show', compact('company', 'submission'));
    }

    /**
     * Update verification / approval status
     */
    public function updateStatus(Request $request, $id)
    {
        $submission = FormSubmission::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:Draft,Submitted,Verified,Approved',
            'notes' => 'nullable|string',
        ]);

        $submission->status = $validated['status'];
        if (!empty($validated['notes'])) {
            $submission->notes = ($submission->notes ? $submission->notes . "\n" : '') .
                '[' . now()->format('d/m/Y H:i') . '] Status diubah ke ' . $validated['status'] . ': ' . $validated['notes'];
        }
        $submission->save();

        return redirect()->back()->with('success', 'Status dokumen berhasil diperbarui menjadi ' . $submission->status);
    }
}
