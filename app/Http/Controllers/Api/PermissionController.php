<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function __invoke(): mixed
    {
        return response()->json(['data' => Permission::query()->orderBy('group')->orderBy('name')->get()->groupBy('group')]);
    }
}

