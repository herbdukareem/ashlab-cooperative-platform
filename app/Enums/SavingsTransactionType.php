<?php

namespace App\Enums;

enum SavingsTransactionType: string
{
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
    case TransferIn = 'transfer_in';
    case TransferOut = 'transfer_out';
    case AdjustmentCredit = 'adjustment_credit';
    case AdjustmentDebit = 'adjustment_debit';
    case Reversal = 'reversal';
}
