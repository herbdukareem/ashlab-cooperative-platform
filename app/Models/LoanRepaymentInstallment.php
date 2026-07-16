<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class LoanRepaymentInstallment extends Model{use BelongsToTenant,HasUlids;protected $fillable=['cooperative_id','loan_id','installment_number','due_date','principal_due_minor','interest_due_minor','charges_due_minor','amount_paid_minor','status'];protected function casts():array{return ['due_date'=>'date','principal_due_minor'=>'integer','interest_due_minor'=>'integer','charges_due_minor'=>'integer','amount_paid_minor'=>'integer'];}}
