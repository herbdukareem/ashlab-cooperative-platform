<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; use App\Http\Requests\StoreChargeRequest; use App\Models\Charge; use Illuminate\Http\JsonResponse;
class ChargeController extends Controller
{
    public function index():JsonResponse{return response()->json(Charge::query()->withCount('loanProducts')->orderBy('name')->paginate());} public function store(StoreChargeRequest $request):JsonResponse{return response()->json(Charge::query()->create($request->validated()),201);} public function show(Charge $charge):JsonResponse{return response()->json($charge->load('loanProducts'));} public function update(StoreChargeRequest $request,Charge $charge):JsonResponse{$charge->update($request->validated());return response()->json($charge->refresh());} public function destroy(Charge $charge):mixed{abort_if($charge->loanProducts()->exists(),422,'A charge attached to a loan product cannot be deleted.');$charge->delete();return response()->noContent();}
}
