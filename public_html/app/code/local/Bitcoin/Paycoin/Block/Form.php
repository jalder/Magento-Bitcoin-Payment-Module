<?php 

$dir = Mage::getBaseDir();

require_once($dir.'/app/code/local/Bitcoin/Paycoin/Helper/jsonRPCClient.php');

class Bitcoin_Paycoin_Block_Form extends Mage_Payment_Block_Form
{
	
	private $rpcurl;
	
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paycoin/form.phtml');
        $this->rpcurl = $this->_getRpcUrl();
    }
    
    protected function _getRpcUrl()
    {
    	$rpcurl = Mage::getSingleton('paycoin/paycoin')->getConfig()->getRpcUrl();
        return $rpcurl;
    }
    
    protected function getAddress(){
    	$bitcoin = new jsonRPCClient($this->rpcurl);
    	
    	try {
			$bitcoin->getinfo();
		} catch (Exception $e) {
			$address = 'Error: Bitcoin server is down.  Please email system administrator regarding your order after confirmation.';
			return $address;
		}
    	
		$info = Mage::getSingleton('checkout/cart')->getCustomerSession()->getCustomer();
		if($info->email==''){
			
			$address = 'GuestCheckout-';
		}
		else{
			$address = $info->email.'-';		
		}

		$qid = Mage::getSingleton('checkout/cart')->getCheckoutSession()->getQuoteId();	
		$info = Mage::getSingleton('sales/quote')->load($qid);	
		$info->reserveOrderId();
		$address .= $info['reserved_order_id'];
		
		$address = $bitcoin->getaccountaddress($address);
		
    	return $address;
    }
    
    
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
    	$this->address = $this->getAddress();
        Mage::dispatchEvent('payment_form_block_to_html_before', array(
            'block'     => $this
        ));
        return parent::_toHtml();
    }
}