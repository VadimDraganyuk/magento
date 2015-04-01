<?php

class ShowInHeaderAndFooter_Customtabs_Model_Observer
{
	/**
	 * Flag to stop observer executing more than once
	 *
	 * @var static bool
	 */
	static protected $_singletonFlag = false;


	public function onBlockHtmlBefore(Varien_Event_Observer $observer) {
		$block = $observer->getBlock();
		if (!isset($block)) return;

		switch ($block->getType()) {
			case 'adminhtml/catalog_product_grid':
				/* @var $block Mage_Adminhtml_Block_Catalog_Product_Grid */
				$block->addColumn('show_header_footer', array(
					'header' => 'Show in header or footer',
					'index'  => 'show_header_footer',
				));
				break;
		}
	}

	public function onEavLoadBefore(Varien_Event_Observer $observer) {
		$collection = $observer->getEvent()->getCollection();
		if (!isset($collection)) return;

		if (is_a($collection, 'Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection')) {
			/* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
			// Manipulate $collection here to add a COLUMN_ID column
			$collection->addExpressionAttributeToSelect('COLUMN_ID', '...Some SQL goes here...');
		}
	}

	public function saveProductTabData(Varien_Event_Observer $observer)
	{
		if (!self::$_singletonFlag) {
			self::$_singletonFlag = true;

			$product = $observer->getEvent()->getProduct();

			try {
				/**
				 * Perform any actions you want here
				 *
				 */
				$customFieldValue =  $this->_getRequest()->getPost('product[show_header_footer]');


				$product->setData('show_header_footer',$customFieldValue);
				$product->save();
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
	}

	/**
	 * Retrieve the product model
	 *
	 * @return Mage_Catalog_Model_Product $product
	 */
	public function getProduct()
	{
		return Mage::registry('product');
	}

	/**
	 * Shortcut to getRequest
	 *
	 */
	protected function _getRequest()
	{
		return Mage::app()->getRequest();
	}
}