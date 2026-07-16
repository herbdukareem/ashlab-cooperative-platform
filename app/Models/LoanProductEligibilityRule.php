<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant; use App\Models\Concerns\HasAuditTrail; use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class LoanProductEligibilityRule extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;
    protected $fillable=['cooperative_id','loan_product_id','name','field','operator','comparison_value','failure_message','is_hard_rule','sequence','is_active'];
    protected function casts(): array { return ['comparison_value'=>'array','is_hard_rule'=>'boolean','sequence'=>'integer','is_active'=>'boolean']; }
    public function product(): BelongsTo { return $this->belongsTo(LoanProduct::class,'loan_product_id'); }
}
