<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSavingsProductRequest;
use App\Models\SavingsProduct;
use Illuminate\Http\JsonResponse;
class SavingsProductController extends Controller
{
    public function index(): JsonResponse { return response()->json(SavingsProduct::query()->withCount('accounts')->orderBy('name')->paginate()); }
    public function store(StoreSavingsProductRequest $request): JsonResponse { return response()->json(SavingsProduct::query()->create($request->validated()), 201); }
    public function show(SavingsProduct $savingsProduct): JsonResponse { return response()->json($savingsProduct->loadCount('accounts')); }
    public function update(StoreSavingsProductRequest $request, SavingsProduct $savingsProduct): JsonResponse { $savingsProduct->update($request->validated()); return response()->json($savingsProduct->refresh()); }
    public function destroy(SavingsProduct $savingsProduct): mixed { abort_if($savingsProduct->accounts()->exists(), 422, 'A product with accounts cannot be deleted.'); $savingsProduct->delete(); return response()->noContent(); }
}
