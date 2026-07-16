<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;use App\Models\Concerns\HasAuditTrail;use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class LoanPenaltyApplication extends Model{use BelongsToTenant,HasAuditTrail,HasUlids;protected $fillable=['cooperative_id','loan_id','loan_repayment_installment_id','charge_id','assessment_date','basis_amount_minor','amount_minor','status','rule_snapshot','waived_by','waived_at','waiver_reason'];protected function casts():array{return ['assessment_date'=>'date','basis_amount_minor'=>'integer','amount_minor'=>'integer','rule_snapshot'=>'array','waived_at'=>'datetime'];}}
