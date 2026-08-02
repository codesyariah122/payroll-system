<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'company' => $request->user()->company,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($user->isAdmin()) {
            $company = $user->company ?: Company::create([
                'name' => $validated['company_name'] ?: $user->name,
            ]);

            if (! $user->company_id) {
                $user->company_id = $company->id;
                $user->save();
            }

            $companyData = [
                'name' => $validated['company_name'] ?: $company->name,
                'description' => $validated['company_description'] ?? null,
                'email' => $validated['company_email'] ?? null,
                'phone' => $validated['company_phone'] ?? null,
                'website' => $validated['company_website'] ?? null,
                'address' => $validated['company_address'] ?? null,
                'city' => $validated['company_city'] ?? null,
                'province' => $validated['company_province'] ?? null,
                'postal_code' => $validated['company_postal_code'] ?? null,
                'country' => ($validated['company_country'] ?? null) ?: 'Indonesia',
                'tax_id' => $validated['company_tax_id'] ?? null,
            ];

            if ($request->hasFile('company_logo')) {
                $companyData['logo_path'] = $this->storeCompanyLogo($request, $company);
            }

            $company->update($companyData);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    private function storeCompanyLogo(ProfileUpdateRequest $request, Company $company): string
    {
        $directory = public_path('company-logos');

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if ($company->logo_path && File::exists(public_path($company->logo_path))) {
            File::delete(public_path($company->logo_path));
        }

        $file = $request->file('company_logo');
        $filename = Str::slug($company->name ?: 'company') . '-' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'company-logos/' . $filename;
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
