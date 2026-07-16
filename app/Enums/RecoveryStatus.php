<?php
namespace App\Enums;
enum RecoveryStatus:string { case Open='open'; case Contacted='contacted'; case PromiseToPay='promise_to_pay'; case Escalated='escalated'; case Legal='legal'; case Resolved='resolved'; case WrittenOff='written_off'; }
