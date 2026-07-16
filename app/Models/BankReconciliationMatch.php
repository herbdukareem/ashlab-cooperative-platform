<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class BankReconciliationMatch extends Model{use BelongsToTenant,HasUlids;public $timestamps=false;protected $fillable=['cooperative_id','bank_statement_line_id','journal_entry_line_id','matched_amount_minor','match_type','matched_by','matched_at'];protected function casts():array{return ['matched_amount_minor'=>'integer','matched_at'=>'datetime'];}}
