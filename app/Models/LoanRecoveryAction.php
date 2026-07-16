<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;use App\Models\Concerns\HasAuditTrail;use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class LoanRecoveryAction extends Model{use BelongsToTenant,HasAuditTrail,HasUlids;protected $fillable=['cooperative_id','loan_recovery_case_id','type','notes','next_action_at','expense_minor','performed_by','performed_at'];protected function casts():array{return ['next_action_at'=>'datetime','expense_minor'=>'integer','performed_at'=>'datetime'];}}
