<?php
namespace Kitchen\Testsix\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Response\RedirectInterface;

class Shipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    protected $_code = 'customshipping';
    protected $customerSession;
    protected $customerRepository;
    protected $_rateResultFactory;
    protected $_rateMethodFactory;
    protected $redirect;

    public function __construct(
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        RedirectInterface $redirect,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->redirect = $redirect;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = $this->_rateResultFactory->create();
        $shippingPrice = $this->getConfigData('price');
        $customerId = $this->customerSession->getCustomerId();
        $customerShippingAmount = 0; // Default value

        if ($customerId) {
            try {
                $customer = $this->customerRepository->getById($customerId);
                
                $customerShippingAmountAttribute = $customer->getCustomAttribute('custom_shipping_amount');
                if ($customerShippingAmountAttribute) {
                    $customerShippingAmount = (float) $customerShippingAmountAttribute->getValue();
                }
                if ($customerShippingAmount == 0) {
                   
                    $this->redirect->redirect($observer->getControllerAction()->getResponse());
                }
            } catch (\Exception $e) {
                // Handle exception
            }
        }

        // Calculate total shipping amount
        $totalShippingAmount = $shippingPrice + $customerShippingAmount;

        $method = $this->_rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));
        $method->setPrice($totalShippingAmount);
        $method->setCost($totalShippingAmount);

        $result->append($method);

        return $result;
    }
}
