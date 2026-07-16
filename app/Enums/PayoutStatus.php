<?php
namespace App\Enums;
enum PayoutStatus:string { case Draft='draft'; case PendingReview='pending_review'; case Approved='approved'; case Processing='processing'; case Paid='paid'; case Failed='failed'; case Reversed='reversed'; case Cancelled='cancelled'; }
