<?php

namespace Test7Skin\LastUnits\Plugin;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\Product;

class ProductListPlugin
{
    protected $stockRegistry;
    protected $scopeConfig;
    protected $layoutFactory;

    public function __construct(
        StockRegistryInterface $stockRegistry,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->scopeConfig = $scopeConfig;
        $this->layoutFactory = $layoutFactory;
    }

    public function aroundGetProductDetailsHtml(ListProduct $subject, \Closure $proceed, \Magento\Catalog\Model\Product $product)
    {
        $result = $proceed($product);

        $blockId = 'lastunits.label';
        foreach($subject as $su){
            $result .= json_decode($su);
        }
        $layout = $this->layoutFactory->create();            
        $lastUnitsAlertQty = $this->scopeConfig->getValue('last_units_alert/qty', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $stockItem = $this->stockRegistry->getStockItemBySku($product->getSku());
        $product->setData('is_last_units', $stockItem->getQty() < $lastUnitsAlertQty);
 
        if (!$layout->getBlock($blockId)) {
            if ($stockItem && $stockItem->getQty() < $lastUnitsAlertQty) {
                $lastUnitsBlock = $layout->createBlock(
                    'Test7Skin\LastUnits\Block\LastUnits',
                    $blockId,
                    ['data' => ['template' => 'Test7Skin_LastUnits::product/lastunits-label.phtml']]
                )->setProduct($product);
                $result .= $lastUnitsBlock->toHtml();
            }
        }

        return $result;
    }
}