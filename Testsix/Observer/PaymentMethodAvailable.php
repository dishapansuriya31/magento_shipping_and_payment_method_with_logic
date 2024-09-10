<?php
namespace Kitchen\Testsix\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session as CustomerSession;

class PaymentMethodAvailable implements ObserverInterface
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @param CustomerSession $customerSession
     */
    public function __construct(
        CustomerSession $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * Handler for payment_method_is_active event.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var MethodInterface $methodInstance */
        $methodInstance = $observer->getMethodInstance();
        
        /** @var Quote $quote */
        $quote = $observer->getQuote();
        if ($this->customerSession->isLoggedIn()) {
           
            $customerGroupId = $this->customerSession->getCustomer()->getGroupId();
            
        
            
            if ($methodInstance->getCode() == 'custompaymentmethod') {
               
                $CustomerGroup = $methodInstance->getConfigData('customer_groups');
                
                
                if ($customerGroupId == $CustomerGroup
               
                ) {
                   
                    $result = $observer->getResult();
                    $result->setData('is_available', true);
                } else {
                    
                    $result = $observer->getResult();
                    $result->setData('is_available', false);
                }
            }
        }
    }
}
