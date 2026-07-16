<?php
namespace App\Enums;
enum RepaymentFrequency: string { case Daily = 'daily'; case Weekly = 'weekly'; case Biweekly = 'biweekly'; case Monthly = 'monthly'; case Quarterly = 'quarterly'; case Bullet = 'bullet'; }
