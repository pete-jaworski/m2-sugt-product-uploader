<?php
namespace Appe;

 
class SubiektGT implements \Appe\ERPInterface
{
 
    private $subiektInstance;
    private $logger;
    
    public function __construct(\COM $com, \Appe\LoggerInterface $logger, \Dotenv\Dotenv $dotenv)
    {
        $this->logger = $logger;
        $gt = $com;   
        $dotenv->load();
        $gt->Autentykacja       = 0; 
        $gt->Serwer             = getenv('ECOMM_DB_SERVER');
        $gt->Uzytkownik         = getenv('ECOMM_USER') ;
        $gt->UzytkownikHaslo    = getenv('ECOMM_PASSWORD');
        $gt->Baza               = getenv('ECOMM_DB_DB');
        $gt->Operator           = getenv('ECOMM_OPERATOR_USERNAME');
        $gt->OperatorHaslo      = getenv('ECOMM_OPERATOR_PASSWORD');
        $this->subiektInstance  = $gt->Uruchom(0, 4);                
        $this->subiektInstance->MagazynId = getenv('ECOMM_WAREHOUSE_ID');
    }
    
    
    
    
    public function uploadProducts(array $products)
    {
        $pz = $this->subiektInstance->SuDokumentyManager->DodajPW();
        $pz->KontrahentId = 411;

        foreach($products as $product){
            if(!$this->subiektInstance->TowaryManager->IstniejeWg($product['symbol'], 2)){
                if(!$this->addTowar($product)){
                    continue;
                }                  
            }  
            $pozycja = $pz->Pozycje->Dodaj($product['symbol']);
            $pozycja->IloscJm = 100;
        }
        
        try {
            $pz->Zapisz();
            $this->logger->log("Added PZ ok");
        } catch (\Exception $ex) {
            $this->logger->log("Added PZ failed: ".$ex->getMessage());
        }
        
    }
    
    
    
    
    
    
    private function addTowar(array $product)
        {
            if($product){
                try {
                    $towar = $this->subiektInstance->Towary->dodaj(1); 
                    $towar->Aktywny                     = $product['aktywny'];
                    $towar->Symbol                      = $product['symbol'];
                    //$towar->Opis                        = $product['opis'];
                    $towar->Nazwa                       = $this->fixEncoding(substr($product['nazwa'], 0, 49));
                    $towar->Ceny->Element(1)->Brutto    = round($product['ceny']['detaliczna']['brutto'], 2);
                    $towar->Ceny->Element(2)->Brutto    = round($product['ceny']['hurtowa']['brutto'], 2);
                    $towar->Zapisz();  
                    $this->logger->log("Added TOWAR: ".$product['symbol']." ok");
                    return true;
                } catch (\Exception $ex) {
                    $this->logger->log("Added TOWAR: ".$product['symbol']." failed: ".$ex->getMessage());
                    return false;            
                }
            }
      }

        

      
      
    private function fixEncoding($string)
    {
        return iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE", $string); 
    }      
 
    
}
