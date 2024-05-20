<?php

namespace Test7Skin\LastUnits\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;

class LastUnits extends AbstractProduct
{
    protected $stockRegistry;
    protected $scopeConfig;
    protected $registry;

    public function __construct(
        Context $context,
        StockRegistryInterface $stockRegistry,
        ScopeConfigInterface $scopeConfig,
        Registry $registry,
        array $data = []
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->scopeConfig = $scopeConfig;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    public function isLastUnits()
    {
        $product = $this->getCurrentProduct() ?? $this->getData('product');
        return $product->getData('is_last_units') ?? false;
    }

    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

}
