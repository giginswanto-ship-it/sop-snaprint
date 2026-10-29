<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyProfile;
use App\Models\SopModule;
use App\Models\Sop;
use App\Models\DocumentForm;
use App\Models\FormSubmission;
use App\Models\Guideline;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display database and system overview
     */
    public function index()
    {
        $company = CompanyProfile::first();
        $totalModules = SopModule::count();
        $totalSops = Sop::count();
        $totalForms = DocumentForm::count();
        $totalGuidelines = Guideline::count();
        $totalSubmissions = FormSubmission::count();

        $modules = SopModule::withCount(['sops', 'forms'])->orderBy('order', 'asc')->get();
        $recentSubmissions = FormSubmission::with('documentForm')->orderBy('created_at', 'desc')->limit(5)->get();

        $dbConnection = config('database.default');

        return view('dashboard.index', compact(
            'company',
            'totalModules',
            'totalSops',
            'totalForms',
            'totalGuidelines',
            'totalSubmissions',
            'modules',
            'recentSubmissions',
            'dbConnection'
        ));
    }
}
