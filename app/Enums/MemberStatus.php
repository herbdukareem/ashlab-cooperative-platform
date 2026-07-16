<?php

namespace App\Enums;

enum MemberStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Rejected = 'rejected';
    case Suspended = 'suspended';
    case Inactive = 'inactive';
    case Retired = 'retired';
    case Resigned = 'resigned';
    case Deceased = 'deceased';
    case Terminated = 'terminated';
    case Blacklisted = 'blacklisted';
}

