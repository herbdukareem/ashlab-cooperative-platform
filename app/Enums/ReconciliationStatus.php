<?php
namespace App\Enums;
enum ReconciliationStatus:string { case Draft='draft'; case InProgress='in_progress'; case Completed='completed'; case Reopened='reopened'; }
