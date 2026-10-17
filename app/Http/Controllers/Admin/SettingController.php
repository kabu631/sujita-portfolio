<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function edit(): Response
    {
        $groups = collect(config('portfolio.settings'))->map(fn ($group, $key) => [
            'key' => $key,
            'label' => $group['label'],
            'description' => $group['description'] ?? null,
            'fields' => collect($group['fields'])->map(fn ($f, $k) => [
                'key' => $k,
                'label' => $f['label'],
                'type' => $f['type'] ?? 'text',
                'help' => $f['help'] ?? null,
            ])->values(),
        ])->values();

        return Inertia::render('Admin/Settings', [
            'groups' => $groups,
            'values' => Setting::publicPayload(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $schema = Setting::schema();

        $rules = [];
        foreach ($schema as $key => $field) {
            $rules[$key] = match ($field['type']) {
                'image' => ['nullable', 'image', 'max:8192'],
                'email' => ['nullable', 'email', 'max:190'],
                'url' => ['nullable', 'url', 'max:2000'],
                'textarea' => ['nullable', 'string', 'max:5000'],
                default => ['nullable', 'string', 'max:255'],
            };
            if ($field['type'] === 'image') {
                $rules["remove_{$key}"] = ['nullable', 'boolean'];
            }
        }

        $validated = $request->validate($rules);
        $current = Setting::allCached();
        $values = [];

        foreach ($schema as $key => $field) {
            if ($field['type'] === 'image') {
                if ($request->hasFile($key)) {
                    $values[$key] = Media::replace($current[$key] ?? null, $request->file($key), 'settings');
                } elseif ($request->boolean("remove_{$key}")) {
                    Media::delete($current[$key] ?? null);
                    $values[$key] = null;
                }

                continue;
            }

            if ($request->exists($key)) {
                $values[$key] = $validated[$key] ?? null;
            }
        }

        Setting::putMany($values);

        return back()->with('success', 'Site settings saved.');
    }
}
