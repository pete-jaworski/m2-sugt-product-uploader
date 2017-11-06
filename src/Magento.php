<?php
namespace Appe;

 
class Magento implements \Appe\EcommerceInterface
{
    const PRODUCT_PREFIX = 'PROD-MA-';
    
    private $curl;
    private $logger;
    private $bearer; 
    
    public function __construct(
            \Curl\Curl $curl,
            \Dotenv\Dotenv $dotenv,
            \Appe\LoggerInterface $logger
    )
    {
        $dotenv->load();
        $this->curl = $curl;
        $this->logger = $logger;        
        $this->curl->setHeader('Content-Type', 'application/json');
        
        $this->curl->post(getenv('TOKEN_API_URL'), json_encode(array(
                                'username' => getenv('API_USERNAME'),
                                'password' => getenv('API_PASSWORD'),
                            )));
        $this->bearer = str_replace('"','', $this->curl->response);
        
        if($this->bearer){
            $this->logger->log('Auth token retrieved: '.$this->bearer); 
        } else {
            $this->logger->log('Auth token not retrieved.'); 
        }
                   

    }
    
    
    public function getProducts()
    {
        try {
            $temp = array();
            $this->logger->log("Retrieving products from Magento...");
            $this->curl->setHeader('Content-Type', 'application/json');
            $this->curl->setHeader('Accept', 'application/json');
            $this->curl->setHeader('Authorization', 'Bearer '.$this->bearer);
            $results = $this->curl->get(getenv('PRODUCT_API_URL'))->response;
            $this->curl->close();   
            $this->logger->log('Data from Magento retrieved'); 
            
            if(!empty(json_decode($results, true)['message'])){
                $this->logger->log('Could not to login to Magento: '.json_decode($results, true)['message']); 
                return false;
            }
            
            if($results){
                foreach(json_decode($results, true)['items'] as $product){
                    $temp[] = array(
                        'symbol'    => $product['sku'],
                        'aktywny'   => true,
                        'rodzaj'    => 1,
                        'nazwa'     => $product['name'],
                        'ilosc'     => 1,
                        'opis'      => $product['custom_attributes'][0]['value'],
                        'ceny'      => array(
                            'detaliczna'    =>   array('netto' => '', 'brutto' => $product['price'], 'waluta' => 'PLN'),
                            'hurtowa'       =>   array('netto' => '', 'brutto' => $product['price'], 'waluta' => 'PLN'),
                            'specjalna'     =>   array('netto' => '', 'brutto' => $product['price'], 'waluta' => 'PLN'),                
                        )
                    );
                }
            }
            
            return $temp;       
            
        } catch (Exception $ex) {
            $this->logger->log('Data from Magento failed: '.$ex->getMessage()); 
            return false;
        }
        
        
         
    }
}
