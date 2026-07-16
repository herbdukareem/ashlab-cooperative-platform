<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;
class LoanApprovalDecision extends Model{use BelongsToTenant,HasUlids;public $timestamps=false;protected $fillable=['cooperative_id','loan_application_id','approval_workflow_step_id','actor_id','decision','comment','decided_at'];protected function casts():array{return ['decided_at'=>'datetime'];}public function application():BelongsTo{return $this->belongsTo(LoanApplication::class,'loan_application_id');}}
