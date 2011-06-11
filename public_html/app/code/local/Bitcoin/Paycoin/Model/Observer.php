<?php

class Bitcoin_Paycoin_Model_Observer
{
    /**
     * Set forced canCreditmemo flag
     *
     * @param Varien_Event_Observer $observer
     * @return Bitcoin_Paycoin_Model_Observer
     */
    public function salesOrderBeforeSave($observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->getPayment()->getMethodInstance()->getCode() != 'paypal') {
            return $this;
        }

        if ($order->canUnhold()) {
            return $this;
        }

        if ($order->isCanceled() ||
            $order->getState() === Mage_Sales_Model_Order::STATE_CLOSED ) {
            return $this;
        }
        $order->addStatusHistoryComment('Paid with BTC');
        return $this;
    }

}
