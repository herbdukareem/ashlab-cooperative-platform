<?php

namespace App\Enums;

enum SavingsAccountStatus: string
{
    case Active = 'active';
    case Frozen = 'frozen';
    case Matured = 'matured';
    case Closed = 'closed';
}
