<?php
namespace Appe;

class Controller
{
    private $db;
    private $erp;
    private $logger;
    private $ecommerce;
    
    public function __construct
    (
        \Appe\Magento $ecommerce,            
        \Appe\ERPInterface $erp,
        \Appe\Logger $logger
    )
    {
        $this->ecommerce = $ecommerce;
        $this->logger = $logger;
        $this->logger->log('Product Synchronisation initialized');                
        $this->erp = $erp;
    }

 
    public function uploadProducts()
    {
         if(!$products = $this->ecommerce->getProducts()){
            $this->logger->log('Data from Magento failed. Exiting... '); 
            die();             
         } else {
            $this->erp->uploadProducts($products);
         }
    }
 
    
}
