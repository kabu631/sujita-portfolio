<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InquiryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Honeypot: real users never see or fill the "website" field.
        if (filled($request->input('website'))) {
            return back()->with('success', 'Thank you! Your message has been sent.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:160'],
            'project_type' => ['nullable', 'string', Rule::in(config('portfolio.project_types'))],
            'shoot_date' => ['nullable', 'date', 'after_or_equal:today'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        Inquiry::create($data + ['ip_address' => $request->ip()]);

        return back()->with('success', 'Thank you! Your booking request has been received — expect a reply within 48 hours.');
    }
}
