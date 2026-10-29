<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyProfile;
use App\Models\SopModule;
use App\Models\Sop;
use App\Models\Guideline;
use App\Models\DocumentForm;

class SopController extends Controller
{
    /**
     * Display the full Master SOP Digital Book
     */
    public function index()
    {
        $company = CompanyProfile::first() ?? new CompanyProfile([
            'brand_name' => 'SNAPRINT DIGITAL PRINTING',
            'operator_name' => 'PT Duta Global Tech',
            'operator_url' => 'dutaglobaltech.com',
            'document_title' => 'BUKU PANDUAN OPERASIONAL FRANCHISE & STANDARISASI OUTLET',
            'document_version' => '1.0',
            'effective_date' => '1 November 2026',
        ]);

        $modules = SopModule::with('sops')->orderBy('order', 'asc')->get();
        $guidelines = Guideline::orderBy('order', 'asc')->get();
        $totalFormsCount = DocumentForm::count();

        return view('sop.index', compact('company', 'modules', 'guidelines', 'totalFormsCount'));
    }

    /**
     * Display a specific SOP
     */
    public function show($slug)
    {
        $sop = Sop::where('slug', $slug)->orWhere('sop_code', strtoupper($slug))->firstOrFail();
        $company = CompanyProfile::first();
        $modules = SopModule::with('sops')->orderBy('order', 'asc')->get();

        // Find related forms
        $relatedForms = DocumentForm::where('module_id', $sop->module_id)
            ->orWhere('reference_sop', 'LIKE', '%' . $sop->sop_code . '%')
            ->get();

        return view('sop.show', compact('sop', 'company', 'modules', 'relatedForms'));
    }

    /**
     * Live search across SOPs and Forms
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['sops' => [], 'forms' => []]);
        }

        $sops = Sop::where('title', 'LIKE', "%{$q}%")
            ->orWhere('sop_code', 'LIKE', "%{$q}%")
            ->orWhere('purpose', 'LIKE', "%{$q}%")
            ->select('id', 'sop_code', 'title', 'slug')
            ->limit(10)
            ->get();

        $forms = DocumentForm::where('title', 'LIKE', "%{$q}%")
            ->orWhere('form_code', 'LIKE', "%{$q}%")
            ->select('id', 'form_code', 'title', 'slug')
            ->limit(10)
            ->get();

        return response()->json([
            'sops' => $sops,
            'forms' => $forms,
        ]);
    }
}
