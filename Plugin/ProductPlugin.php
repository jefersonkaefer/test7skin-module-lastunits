<?php

namespace Test7Skin\LastUnits\Plugin;

use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ProductPlugin
{
    protected $stockRegistry;
    protected $scopeConfig;

    public function __construct(
        StockRegistryInterface $stockRegistry,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->scopeConfig = $scopeConfig;
    }

    public function afterLoad($subject, $result)
    {
        // Verifica se o objeto passado é uma instância de Magento\Catalog\Model\Product
        if ($result instanceof Product) {
            $stockItem = $result->getExtensionAttributes()->getStockItem();
            if ($stockItem) {
                $lastUnitsAlertQty = $this->scopeConfig->getValue('last_units_alert/qty', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $result->setData('is_last_units', $stockItem->getQty() < $lastUnitsAlertQty);
            }
        }
        return $result;
    }

    public function afterGetItems($subject, $result)
    {
        // Método afterGetItems para uma coleção de produtos
        $lastUnitsAlertQty = $this->scopeConfig->getValue('last_units_alert/qty', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
        foreach ($result as $product) {
            $stockItem = $this->stockRegistry->getStockItemBySku($product->getSku());
            if ($stockItem) {
                $product->setData('is_last_units', $stockItem->getQty() < $lastUnitsAlertQty);
            }
        }
        
        return $result;
    }
}
