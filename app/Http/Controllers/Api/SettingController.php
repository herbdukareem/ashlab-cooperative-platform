<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CooperativeSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(): mixed
    {
        return response()->json(['data' => CooperativeSetting::query()->orderBy('group')->orderBy('key')->get()->groupBy('group')]);
    }

    public function update(Request $request): mixed
    {
        $data = $request->validate([
            'settings' => ['required', 'array', 'max:100'],
            'settings.*.group' => ['required', 'string', 'max:80'],
            'settings.*.key' => ['required', 'string', 'max:120'],
            'settings.*.value' => ['nullable'],
        ]);

        foreach ($data['settings'] as $setting) {
            CooperativeSetting::query()->updateOrCreate(['group' => $setting['group'], 'key' => $setting['key']], ['value' => $setting['value']]);
        }

        return $this->index();
    }
}

