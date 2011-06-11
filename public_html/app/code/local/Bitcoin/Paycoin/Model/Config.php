<?php 


class Bitcoin_Paycoin_Model_Config
{
	protected static $_methods;
	
    public function getRpcUrl($store=null)
    {
		$bitconf = $this->_getConf();
     
        return $bitconf['rpcurl'];
    }
	
    
    private function _getConf($store=null){
    	$config = Mage::getStoreConfig('payment', $store);
		$bitconf = $config['paycoin'];
		return $bitconf;
    }
    
}