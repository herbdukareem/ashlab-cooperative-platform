<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;use App\Models\Concerns\HasAuditTrail;use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\HasMany;
class CooperativeBankAccount extends Model{use BelongsToTenant,HasAuditTrail,HasUlids;protected $fillable=['cooperative_id','ledger_account_id','name','bank_code','bank_name','account_number_encrypted','account_number_hash','account_number_last_four','currency','is_active'];protected $hidden=['account_number_encrypted','account_number_hash'];protected function casts():array{return ['is_active'=>'boolean'];}public function reconciliations():HasMany{return $this->hasMany(BankReconciliation::class);}}
