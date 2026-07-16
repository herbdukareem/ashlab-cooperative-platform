<?php

namespace App\Enums;

enum CooperativeStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Inactive = 'inactive';
}

