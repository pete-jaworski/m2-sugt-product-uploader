<?php
namespace Appe;

class MSSQL implements \Appe\DatabaseInterface
{
    public $connection;
    private $logger;

    public function __construct(\Appe\LoggerInterface $logger, \Dotenv\Dotenv $dotenv)
    {
        $this->logger = $logger;
        $dotenv->load();
        
        try {
            ini_set('mssql.charset', 'UTF-8');
            //$this->connection = new \PDO('sqlsrv:Server=DESKTOP-HP2DPHD\INSERTGT;Database=SubiektGT_Magento');
            $sql = getenv('DB_CONNECTION_STRING');
            $this->connection = new \PDO($sql,'','');
            
            $this->logger->log('Connection initialized');
        } catch (\PDOException $e) {
            $this->logger->log($sql);   
            $this->logger->log('Connection failed: ' . $e->getMessage());
            die();
        }        
    }


    
    
    
    
    public function read()
    {
        
    }




    
    
    
    public function write(array $productStocks)
    {
        
    }

 
 
}
