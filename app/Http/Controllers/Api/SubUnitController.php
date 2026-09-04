<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubUnitResource;
use App\Models\SubUnit;
use Illuminate\Http\Request;

class SubUnitController extends Controller
{
    public function index(Request $request)
    {
        $query = SubUnit::withCount('songs')->orderBy('name');

        $perPage = (int) ($request->integer('per_page') ?: 25);

        return SubUnitResource::collection($query->paginate($perPage)->appends($request->query()));
    }

    public function show(SubUnit $subUnit)
    {
        $subUnit->load(['songs' => fn ($q) => $q->orderBy('debut_date')]);

        return (new SubUnitResource($subUnit))->response();
    }
}
