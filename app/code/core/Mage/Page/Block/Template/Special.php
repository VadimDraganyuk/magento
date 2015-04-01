<?php

class Mage_Page_Block_Template_Special extends Mage_Catalog_Block_Product_Abstract
{
	const DEFAULT_PRODUCTS_COUNT = 6;

	protected
		$_productCollection,
		$_productsCount;


	public function setProductsCount($count)
	{
		$this->_productsCount = $count;
		return $this;
	}

	/**
	 * Get how much products should be displayed at once.
	 *
	 * @return int
	 */
	public function getProductsCount()
	{
		if (null === $this->_productsCount) {
			$this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
		}
		return $this->_productsCount;
	}

	public function getProductCollection()
	{
		$categoryId = Mage::app()->getStore()->getRootCategoryId();
		$this->_productCollection = Mage::getResourceModel('catalog/product_collection')
			->setStoreId(Mage::app()->getStore()->getId())
			->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
			->addMinimalPrice()
			->addFinalPrice()
			->addTaxPercents()
			->addUrlRewrite($categoryId);
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($this->_productCollection);
		return $this->_productCollection;
	}

	public function getSpecialProducts()
	{
		$date = Mage::getModel('core/date');
		return $this->getProductCollection()
			->addAttributeToSort('special_from_date','desc')
			->addAttributeToFilter('special_from_date', array(
				'date' => true, 'to' => $date->date()
			))
			->addAttributeToFilter('special_to_date', array( 'or' => array(
				0 => array('date' => true, 'from' => $date->timestamp() + 86400), // tomorrow date
				1 => array('is'   => new Zend_Db_Expr('null')))
			), 'left')
			->setCurPage(1)
			->setPageSize($this->getProductsCount());
	}
}