<?php

namespace App\Enums;

enum ContributionObligationStatus: string
{
    case Upcoming = 'upcoming';
    case Due = 'due';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Waived = 'waived';
    case Cancelled = 'cancelled';
}
