<?php

namespace App\Models;

use App\Enums\KycStatus;
use App\Enums\MemberStatus;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, HasUlids, SoftDeletes;

    protected $fillable = ['cooperative_id', 'branch_id', 'member_category_id', 'membership_number', 'first_name', 'middle_name', 'last_name', 'gender', 'date_of_birth', 'marital_status', 'phone', 'email', 'residential_address', 'state_of_origin', 'local_government_area', 'occupation', 'employer', 'staff_number', 'department', 'date_joined', 'status', 'kyc_status', 'passport_path', 'signature_path', 'approved_by', 'approved_at', 'status_reason'];

    protected function casts(): array { return ['date_of_birth' => 'date', 'date_joined' => 'date', 'approved_at' => 'datetime', 'status' => MemberStatus::class, 'kyc_status' => KycStatus::class]; }

    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function category(): BelongsTo { return $this->belongsTo(MemberCategory::class, 'member_category_id'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function identifications(): HasMany { return $this->hasMany(MemberIdentification::class); }
    public function documents(): HasMany { return $this->hasMany(MemberDocument::class); }
    public function bankAccounts(): HasMany { return $this->hasMany(MemberBankAccount::class); }
    public function beneficiaries(): HasMany { return $this->hasMany(MemberBeneficiary::class); }
    public function guarantors(): HasMany { return $this->hasMany(MemberGuarantor::class); }
    public function statusHistory(): HasMany { return $this->hasMany(MemberStatusHistory::class); }
}
