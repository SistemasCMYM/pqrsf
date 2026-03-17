<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HoldingCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HoldingCompanyController extends Controller
{
    public function index(): View
    {
        return view('admin.holding.companies', [
            'companies' => HoldingCompany::query()->orderByDesc('is_default')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:140'],
            'slug' => ['required', 'string', 'max:140', 'unique:holding_companies,slug'],
            'tagline' => ['nullable', 'string', 'max:180'],
            'intro' => ['nullable', 'string', 'max:1000'],
            'support_booking_url' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'animation' => ['nullable', 'file', 'max:8192', 'mimes:gif,webp,png,mp4'],
            'is_default' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_default')) {
            HoldingCompany::query()->update(['is_default' => false]);
        }

        $company = new HoldingCompany();
        $company->fill([
            'name' => $data['name'],
            'slug' => Str::slug($data['slug']),
            'tagline' => $data['tagline'] ?? null,
            'intro' => $data['intro'] ?? null,
            'support_booking_url' => $data['support_booking_url'] ?? null,
            'is_default' => $request->boolean('is_default'),
            'active' => $request->boolean('active', true),
        ]);

        if ($request->hasFile('logo')) {
            $company->logo_path = $request->file('logo')->store('branding/logos', 'public');
        }

        if ($request->hasFile('animation')) {
            $company->animation_path = $request->file('animation')->store('branding/animations', 'public');
        }

        $company->save();

        return back()->with('success', 'Empresa creada y branding cargado.');
    }

    public function update(Request $request, HoldingCompany $company): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:140'],
            'tagline' => ['nullable', 'string', 'max:180'],
            'intro' => ['nullable', 'string', 'max:1000'],
            'support_booking_url' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'animation' => ['nullable', 'file', 'max:8192', 'mimes:gif,webp,png,mp4'],
            'is_default' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_default')) {
            HoldingCompany::query()->where('id', '!=', $company->id)->update(['is_default' => false]);
        }

        $company->fill([
            'name' => $data['name'],
            'tagline' => $data['tagline'] ?? null,
            'intro' => $data['intro'] ?? null,
            'support_booking_url' => $data['support_booking_url'] ?? null,
            'is_default' => $request->has('is_default'),
            'active' => $request->has('active'),
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $company->logo_path = $request->file('logo')->store('branding/logos', 'public');
        }

        if ($request->hasFile('animation')) {
            if ($company->animation_path) {
                Storage::disk('public')->delete($company->animation_path);
            }
            $company->animation_path = $request->file('animation')->store('branding/animations', 'public');
        }

        $company->save();

        return back()->with('success', 'Branding actualizado.');
    }
}
