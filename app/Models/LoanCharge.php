<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class LoanCharge extends Model{use BelongsToTenant,HasUlids;protected $fillable=['cooperative_id','loan_id','charge_id','name','treatment','amount_minor','snapshot'];protected function casts():array{return ['amount_minor'=>'integer','snapshot'=>'array'];}}
