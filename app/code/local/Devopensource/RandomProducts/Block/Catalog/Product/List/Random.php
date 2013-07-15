<?php

/**
 * New products with random
 * @package Devopensource_RandomProducts_Block_Catalog_Product_List_Random
 * @subpackage Mage_Catalog_Block_Product_List
 */

class Devopensource_RandomProducts_Block_Catalog_Product_List_Random extends Mage_Catalog_Block_Product_List{

    protected $_productCollection;
    protected $_productsCount = null;

    const DEFAULT_PRODUCTS_COUNT = 5;

    public function __construct()
    {
        $this->_blockGroup = 'randomproducts';
        parent::__construct();
    }

    /**
     * Set how much product should be displayed at once.
     *
     * @param $count
     * @return Mage_Catalog_Block_Product_New
     */
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

    protected function _beforeToHtml()
    {
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $collection = Mage::getResourceModel('catalog/product_collection');

        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());
        $collection->getSelect()->order('rand()');
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate))
            ->addAttributeToFilter('news_to_date', array('or'=> array(
            0 => array('date' => true, 'from' => $todayDate),
            1 => array('is' => new Zend_Db_Expr('null')))
        ), 'left')
            ->addAttributeToSort('news_from_date', 'desc')
            ->setPageSize($this->getProductsCount())
        ;

        $this->setProductCollection($collection);

        return $collection;
    }

}