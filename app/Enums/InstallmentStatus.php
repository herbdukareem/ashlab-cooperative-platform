<?php
namespace App\Enums;
enum InstallmentStatus:string { case Upcoming='upcoming'; case Due='due'; case PartiallyPaid='partially_paid'; case Paid='paid'; case Overdue='overdue'; case Rescheduled='rescheduled'; case Waived='waived'; }
