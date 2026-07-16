<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant; use App\Models\Concerns\HasAuditTrail; use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ApprovalWorkflowStep extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;
    protected $fillable=['cooperative_id','approval_workflow_id','sequence','name','required_permission','minimum_approvals','minimum_amount_minor','maximum_amount_minor','requires_distinct_actor','configuration'];
    protected function casts(): array { return ['sequence'=>'integer','minimum_approvals'=>'integer','minimum_amount_minor'=>'integer','maximum_amount_minor'=>'integer','requires_distinct_actor'=>'boolean','configuration'=>'array']; }
    public function workflow(): BelongsTo { return $this->belongsTo(ApprovalWorkflow::class,'approval_workflow_id'); }
}
