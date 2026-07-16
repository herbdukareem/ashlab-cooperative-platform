<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;use App\Models\Concerns\HasAuditTrail;use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class AccountingPeriod extends Model{use BelongsToTenant,HasAuditTrail,HasUlids;protected $fillable=['cooperative_id','name','start_date','end_date','status','closed_by','closed_at'];protected function casts():array{return ['start_date'=>'date','end_date'=>'date','closed_at'=>'datetime'];}}
