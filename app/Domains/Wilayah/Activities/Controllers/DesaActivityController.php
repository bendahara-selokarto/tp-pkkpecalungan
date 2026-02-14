<?php

namespace App\Domains\Wilayah\Activities\Controllers;

use App\Http\Controllers\Controller; // ✅ HARUS INI
use Illuminate\Http\Request;
use App\Domains\Wilayah\Activities\Models\Activity;

class DesaActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:desa'); // ✅ middleware valid
    }

    public function index()
    {
        $user = auth()->user();

        $activities = Activity::where('area_id', $user->area_id)
            ->where('level', 'desa')
            ->get();

        return view('desa.activities.index', compact('activities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_date' => 'required|date',
        ]);

        Activity::create([
            'title' => $request->title,
            'description' => $request->description,
            'level' => 'desa',
            'area_id' => auth()->user()->area_id,
            'created_by' => auth()->id(),
            'activity_date' => $request->activity_date,
            'status' => 'draft',
        ]);

        return redirect()->back();
    }
}
