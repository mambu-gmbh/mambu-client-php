<?php

require_once 'Base.php';
require_once 'Detail.php';


/**
 * Representation of transactions in Mambu
 */
class Mambu_Transaction extends Mambu_Base {

  // user provided values
  public $loanID;
  public $type;
  public $loanAmount;
  public $amount;
  public $date;
  public $method;
  public $receiptNumber;
  public $repayment;
  public $firstRepaymentDate;
  public $notes;

  // system generated values
  public $encodedKey;
  public $transactionId;
  public $parentAccountKey;
  public $comment;
  public $transactionDate;
  public $entryDate;
  public $newState;
  public $principalPaid;
  public $interestPaid;
  public $feesPaid;
  public $penaltyPaid;
  public $balance;
  public /* @var Mambu_Detail[] */ $details;

  /**
   * Below are the list of the seven(7) methods we
   * need to map to the Mambu API.
   *
   * The resource we're going to communicate with is: /api/loans/<LOAN_ID>/transactions
   * @see http://developer.mambu.com/api-reference/api-loan-transaction#POST
   *
   * 1. Post a repayment to loan account.
   * 2. Post a backdated repayment to account and record it as bank receipt.
   * 3. Post a repayment of (X) amount and record it as a cash repayment.
   * 4. Approve loan.
   * 5. Disburse a loan with a few notes backdated to (X) date(yyyy-mm-dd) and
   *    repayments starting in (X) days.
   * 6. Apply a fee of (X) to a dynamic account.
   * 7. Apply a fee of (X) to due on repayment #5 for a fixed account.
   *
   *
   * GENERAL FLOW OF THE TRANSACTIONS DEPICTED IN THE FREEHAND SKETCH BELOW.
   * ===========================================================================
   *
   *   """""""""""""""""""
   *   || CREATE CLIENT ||
   *   """""""""""""""""""
   *           |
   *           |
   *   """""""""""""""""""
   *   ||  CREATE LOAN  ||
   *   """""""""""""""""""
   *           |
   *           |
   *   """"""""""""""""""             """""""""""""""""""
   *   || APPROVE LOAN || ----------->|| DISBURSE LOAN ||
   *   """"""""""""""""""             """""""""""""""""""
   *                                          |
   *                              ____________|_________________
   *                             /            |                 \
   *                            /             |                  \
   *                 """""""""""""""   """"""""""""""""""""   """"""""""""""""""""
   *                 || APPLY FEE ||   || LOAN REPAYMENT ||   || APPLY INTEREST ||
   *                 """""""""""""""   """"""""""""""""""""   """"""""""""""""""""
   */


  /**
   * (non-PHPdoc)
   *
   * Implements the abstract function from Mambu_Base
   *
   * @see Mambu_Base::_getResourceName()
   */
  protected function _getResourceName() {
    return 'loans/'.$this->loanID.'/transactions';
  }

  /**
   * Parses all additional information that is provided if fullDetails is set
   * to true into their matching Mambu objects
   */
  private function _parseFullDetails(){
    $this->_parseTransaction();
    $this->_parseDetails();
  }

  /**
   * Copies the values of the array from the 'details' field to the
   * fields of this object and deletes the 'details' field
   */
  private function _parseDetails() {
    // copy to internal array
    $detailsAsArray = $this->details;
    unset($this->details);
    $this->details = array();
    foreach ($detailsAsArray as $detailAsArray) {
      $detail = new Mambu_Detail();
      Mambu_Base::parseSubResult($detail, $detailAsArray);
      $this->details[] = $detail;
    }
  }

  /**
   * Copies the values of the array from the 'transaction' field to the fields of
   * this object and deletes the 'transaction' field
   */
  private function _parseTransaction(){
    Mambu_Base::parseSubResult($this, $this->transaction);
    unset($this->transaction);
  }

}