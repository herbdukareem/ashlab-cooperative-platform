<?php
namespace App\Domain\Credit\Actions;
use App\Models\ApprovalWorkflow; use Illuminate\Support\Arr; use Illuminate\Support\Facades\DB;
class SaveApprovalWorkflow
{
    public function execute(array $data,?ApprovalWorkflow $workflow=null):ApprovalWorkflow{return DB::transaction(function()use($data,$workflow):ApprovalWorkflow{$workflow??=new ApprovalWorkflow();$workflow->fill(Arr::except($data,'steps'))->save();$workflow->steps()->delete();foreach($data['steps'] as $step)$workflow->steps()->create($step);return $workflow->refresh()->load('steps');});}
}
