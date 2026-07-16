<?php
namespace App\Models;
use App\Enums\PayoutStatus;use App\Enums\PayoutType;use App\Models\Concerns\BelongsToTenant;use App\Models\Concerns\HasAuditTrail;use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\HasMany;
class PayoutBatch extends Model{use BelongsToTenant,HasAuditTrail,HasUlids;protected $fillable=['cooperative_id','reference','idempotency_key','name','type','item_count','total_amount_minor','status','created_by'];protected function casts():array{return ['type'=>PayoutType::class,'status'=>PayoutStatus::class,'item_count'=>'integer','total_amount_minor'=>'integer'];}public function payouts():HasMany{return $this->hasMany(Payout::class);}}
