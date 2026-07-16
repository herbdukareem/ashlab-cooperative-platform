<?php
namespace App\Domain\Payouts\Actions;
use App\Enums\PayoutStatus;use App\Models\Payout;use Illuminate\Support\Facades\Auth;use Illuminate\Support\Facades\Crypt;use Illuminate\Support\Str;
class CreatePayout{public function execute(array $data):Payout{if($existing=Payout::query()->where('idempotency_key',$data['idempotency_key'])->first())return $existing;return Payout::query()->create([...$data,'reference'=>'OUT-'.strtoupper(Str::ulid()),'account_number_encrypted'=>isset($data['account_number'])?Crypt::encryptString(preg_replace('/\D/','',$data['account_number'])):null,'account_number_last_four'=>isset($data['account_number'])?substr(preg_replace('/\D/','',$data['account_number']),-4):null,'status'=>PayoutStatus::PendingReview,'created_by'=>Auth::id()]);}}
