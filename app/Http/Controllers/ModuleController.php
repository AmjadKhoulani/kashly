<?php

namespace App\Http\Controllers;

use App\Models\SystemModule;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = SystemModule::all();
        $userActiveModules = auth()->user()->modules()->pluck('module_id')->toArray();

        // If user has no modules active (legacy user), they have ledger & investments active by default
        if (empty($userActiveModules)) {
            $userActiveModules = ['ledger', 'investments'];
        }

        return view('settings.modules', compact('modules', 'userActiveModules'));
    }

    public function toggle(Request $request, $id)
    {
        $module = SystemModule::findOrFail($id);
        $user = auth()->user();

        // Prevent toggling non-existent modules or core
        if ($user->modules()->where('module_id', $id)->exists()) {
            $user->modules()->detach($id);
            $message = 'تم إلغاء تفعيل منظومة: ' . $module->name_ar;
        } else {
            $user->modules()->attach($id);
            $message = 'تم تفعيل منظومة: ' . $module->name_ar;
        }

        return redirect()->back()->with('success', $message);
    }
}
