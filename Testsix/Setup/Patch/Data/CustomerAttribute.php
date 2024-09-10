<?php
declare(strict_types=1);

namespace Kitchen\Testsix\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CustomerAttribute for creating the custom_shipping_amount attribute for customers.
 */
class CustomerAttribute implements DataPatchInterface
{
   /**
    * @var ModuleDataSetupInterface
    */
   private $moduleDataSetup;

   /**
    * @var EavSetupFactory
    */
   private $eavSetupFactory;

   /**
    * @var Config
    */
   private $eavConfig;

   /**
    * @var LoggerInterface
    */
   private $logger;

   /**
    * CustomerAttribute Constructor
    *
    * @param EavSetupFactory $eavSetupFactory
    * @param Config $eavConfig
    * @param LoggerInterface $logger
    * @param ModuleDataSetupInterface $moduleDataSetup
    */
   public function __construct(
       EavSetupFactory $eavSetupFactory,
       Config $eavConfig,
       LoggerInterface $logger,
       ModuleDataSetupInterface $moduleDataSetup
   ) {
       $this->eavSetupFactory = $eavSetupFactory;
       $this->eavConfig = $eavConfig;
       $this->logger = $logger;
       $this->moduleDataSetup = $moduleDataSetup;
   }

   /**
    * {@inheritdoc}
    */
   public function apply()
   {
       $this->moduleDataSetup->getConnection()->startSetup();
       $this->addCustomShippingAmountAttribute();
       $this->moduleDataSetup->getConnection()->endSetup();
   }

   /**
    * Adds the custom_shipping_amount attribute to the customer entity.
    */
   public function addCustomShippingAmountAttribute()
   {
       $eavSetup = $this->eavSetupFactory->create();
       $eavSetup->addAttribute(
           Customer::ENTITY,
           'custom_shipping_amount',
           [
               'type' => 'decimal',
               'label' => 'Custom Shipping Amount',
               'input' => 'text',
               'required' => false,
               'visible' => true,
               'user_defined' => true,
               'system' => false,
               'position' => 100,
           ]
       );

       $attributeSetId = $eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
       $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Customer::ENTITY);

       $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'custom_shipping_amount');
       $attribute->setData('attribute_set_id', $attributeSetId);
       $attribute->setData('attribute_group_id', $attributeGroupId);

       $attribute->setData('used_in_forms', [
           'adminhtml_customer',
           'adminhtml_customer_address',
           'customer_account_edit',
           'customer_address_edit',
           'customer_register_address',
           'customer_account_create'
       ]);

       $attribute->save();
   }

   /**
    * {@inheritdoc}
    */
   public static function getDependencies()
   {
       return [];
   }

   /**
    * {@inheritdoc}
    */
   public function revert()
   {
   }

   /**
    * {@inheritdoc}
    */
   public function getAliases()
   {
       return [];
   }
}
