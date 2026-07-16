<?php
namespace App\Enums;
enum LoanApplicationStatus:string { case Draft='draft'; case Submitted='submitted'; case UnderReview='under_review'; case Approved='approved'; case Rejected='rejected'; case Cancelled='cancelled'; case Disbursed='disbursed'; }
