<?php
namespace App\Http\Requests;
use App\Support\Tenancy\TenantContext; use Illuminate\Foundation\Http\FormRequest; use Illuminate\Validation\Rule;
class StoreApprovalWorkflowRequest extends FormRequest
{
    public function authorize(): bool{return true;} public function rules(): array{return ['name'=>['required','string','max:160'],'code'=>['required','alpha_dash:ascii','max:40',Rule::unique('approval_workflows')->where('cooperative_id',app(TenantContext::class)->id())->ignore($this->route('approval_workflow'))],'entity_type'=>['required','in:loan_application,payout,withdrawal'],'description'=>['nullable','string','max:2000'],'is_active'=>['required','boolean'],'steps'=>['required','array','min:1'],'steps.*.sequence'=>['required','integer','min:1','distinct'],'steps.*.name'=>['required','string','max:160'],'steps.*.required_permission'=>['required','string','max:100'],'steps.*.minimum_approvals'=>['required','integer','between:1,20'],'steps.*.minimum_amount_minor'=>['nullable','integer','min:0'],'steps.*.maximum_amount_minor'=>['nullable','integer','min:0'],'steps.*.requires_distinct_actor'=>['required','boolean'],'steps.*.configuration'=>['nullable','array']];}
}
