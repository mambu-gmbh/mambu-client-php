<?php

require_once 'Base.php';
require_once 'CustomInformation.php';

/**
* Representation of a loan in Mambu
*/
class Mambu_Loan extends Mambu_Base {

  // user supplied values
  public $clientID;
  public $productID;
  public $loanAmount;
  public $interestRate;
  public $installments;
  // public $gracelength;
  public $repaymentInterval;
  public $notes;
  public /* @var Mambu_CustomInformation[] */ $customInformation;

  // system generated values
  public $encodedKey;
  public $id;
  public $accountHolderKey;
  public $accountHolderType;
  public $creationDate;
  public $lastModifiedDate;
  public $accountState;
  public $productTypeKey;
  public $loanName;
  public $scheduleDueDatesMethod;
  public $repaymentPeriodCount;
  public $repaymentPeriodUnit;
  public $repaymentInstallments;

  /**
   * (non-PHPdoc)
   *
   * Implements the abstract function from Mambu_Base
   *
   * @see Mambu_Base::_getResourceName()
   */
  protected function _getResourceName() {
    return 'clients/'.$this->clientID.'/loans';
  }

  /**
   * Parses all additional information that is provided if fullDetails is set
   * to true into their matching Mambu objects
   */
  private function _parseFullDetails(){
    $this->_parseLoan();
    $this->_parseCustomInformations();
    // $this->_parseIdDocuments();
  }


  /**
   * Copies the values of the array from the 'customInformation' field to the
   * fields of this object and deletes the 'customInformation' field
   */
  private function _parseCustomInformations(){
    // copy to internal array
    $customInformationsAsArray = $this->customInformation;
    unset($this->customInformation);
    $this->customInformation = array();
    foreach($customInformationsAsArray as $customInformationAsArray){
      $customInformation = new Mambu_CustomInformation();
      Mambu_Base::parseSubResult($customInformation,
        $customInformationAsArray);
      $this->customInformation[] = $customInformation;
    }
  }

  /**
   * Copies the values of the array from the 'loan' field to the fields of
   * this object and deletes the 'loan' field
   */
  private function _parseLoan(){
    Mambu_Base::parseSubResult($this, $this->loan);
    unset($this->loan);
  }

}