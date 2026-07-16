<?php
namespace App\Http\Controllers\Api;
use App\Domain\Credit\Actions\SaveApprovalWorkflow; use App\Http\Controllers\Controller; use App\Http\Requests\StoreApprovalWorkflowRequest; use App\Models\ApprovalWorkflow; use Illuminate\Http\JsonResponse;
class ApprovalWorkflowController extends Controller
{
    public function index():JsonResponse{return response()->json(ApprovalWorkflow::query()->with('steps')->orderBy('name')->paginate());} public function store(StoreApprovalWorkflowRequest $request,SaveApprovalWorkflow $action):JsonResponse{return response()->json($action->execute($request->validated()),201);} public function show(ApprovalWorkflow $approvalWorkflow):JsonResponse{return response()->json($approvalWorkflow->load('steps'));} public function update(StoreApprovalWorkflowRequest $request,ApprovalWorkflow $approvalWorkflow,SaveApprovalWorkflow $action):JsonResponse{return response()->json($action->execute($request->validated(),$approvalWorkflow));} public function destroy(ApprovalWorkflow $approvalWorkflow):mixed{abort_if($approvalWorkflow->loanProducts()->exists(),422,'A workflow assigned to a loan product cannot be deleted.');$approvalWorkflow->delete();return response()->noContent();}
}
