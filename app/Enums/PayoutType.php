<?php
namespace App\Enums;
enum PayoutType:string { case LoanDisbursement='loan_disbursement'; case SavingsWithdrawal='savings_withdrawal'; case Dividend='dividend'; case PatronageRefund='patronage_refund'; case MemberRefund='member_refund'; case WelfareBenefit='welfare_benefit'; case InsuranceClaim='insurance_claim'; case SupplierPayment='supplier_payment'; case ExpenseReimbursement='expense_reimbursement'; case Payroll='payroll'; case Recurring='recurring'; case Scheduled='scheduled'; case Bulk='bulk'; case General='general'; }
