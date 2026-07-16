<?php
namespace App\Enums;
enum RepaymentStatus:string { case Successful='successful'; case Reversal='reversal'; case Reversed='reversed'; case Failed='failed'; }
