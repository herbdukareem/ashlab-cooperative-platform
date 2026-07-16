<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class PayoutEvent extends Model{use BelongsToTenant,HasUlids;public $timestamps=false;protected $fillable=['cooperative_id','payout_id','event_type','provider_event_id','payload','occurred_at'];protected function casts():array{return ['payload'=>'array','occurred_at'=>'datetime'];}}
