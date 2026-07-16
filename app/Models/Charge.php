<?php
namespace App\Models;
use App\Enums\ChargeCalculationType; use App\Models\Concerns\BelongsToTenant; use App\Models\Concerns\HasAuditTrail; use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsToMany; use Illuminate\Database\Eloquent\SoftDeletes;
class Charge extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, HasUlids, SoftDeletes;
    protected $fillable=['cooperative_id','name','code','description','calculation_type','fixed_amount_minor','rate_basis_points','calculation_basis','minimum_amount_minor','maximum_amount_minor','application_timing','treatment','is_refundable','exempt_member_category_ids','configuration','is_active'];
    protected function casts(): array { return ['calculation_type'=>ChargeCalculationType::class,'fixed_amount_minor'=>'integer','rate_basis_points'=>'integer','minimum_amount_minor'=>'integer','maximum_amount_minor'=>'integer','is_refundable'=>'boolean','exempt_member_category_ids'=>'array','configuration'=>'array','is_active'=>'boolean']; }
    public function loanProducts(): BelongsToMany { return $this->belongsToMany(LoanProduct::class,'loan_product_charges')->withPivot(['sequence','is_mandatory','overrides'])->withTimestamps(); }
}
