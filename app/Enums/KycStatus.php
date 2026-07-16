<?php

namespace App\Enums;

enum KycStatus: string
{
    case NotStarted = 'not_started';
    case Pending = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';
}

