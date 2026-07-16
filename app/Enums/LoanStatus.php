<?php
namespace App\Enums;
enum LoanStatus:string { case PendingDisbursement='pending_disbursement'; case Active='active'; case InArrears='in_arrears'; case Settled='settled'; case WrittenOff='written_off'; case Restructured='restructured'; }
