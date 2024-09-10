<?php
namespace Kitchen\Testsix\Model;
class Custompaymentmethod extends \Magento\Payment\Model\Method\AbstractMethod
{
const PAYMENT_METHOD_CUSTOM_INVOICE_CODE = 'custompaymentmethod';
/**
* Payment method code
*
* @var string
*/
protected $_code = self::PAYMENT_METHOD_CUSTOM_INVOICE_CODE;
}