<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;
class LoanRepaymentAllocation extends Model{use BelongsToTenant,HasUlids;public $timestamps=false;protected $fillable=['cooperative_id','loan_repayment_id','loan_repayment_installment_id','component','amount_minor','created_at'];protected function casts():array{return ['amount_minor'=>'integer','created_at'=>'datetime'];}public function repayment():BelongsTo{return $this->belongsTo(LoanRepayment::class,'loan_repayment_id');}public function installment():BelongsTo{return $this->belongsTo(LoanRepaymentInstallment::class,'loan_repayment_installment_id');}}
