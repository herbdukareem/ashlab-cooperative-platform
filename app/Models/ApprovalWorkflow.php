<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant; use App\Models\Concerns\HasAuditTrail; use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\HasMany; use Illuminate\Database\Eloquent\SoftDeletes;
class ApprovalWorkflow extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids, SoftDeletes;
    protected $fillable=['cooperative_id','name','code','entity_type','description','is_active']; protected function casts(): array { return ['is_active'=>'boolean']; }
    public function steps(): HasMany { return $this->hasMany(ApprovalWorkflowStep::class)->orderBy('sequence'); }
    public function loanProducts(): HasMany { return $this->hasMany(LoanProduct::class); }
}
