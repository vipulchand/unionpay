Magento如何新加收费

Webaddress:
http://www.2betop.net/node/3


发布时间:
    2010年08月26日
作者:
    2BETOP

Magento系统默认有Subtotal，Grand Total，税收收费，运费收费，打折折扣费（这个可能是负值）等等，如何添加一种收费呢？

比如现在我有这么一个需求，如果选择的付款方式是ips_payment的话，
如果购物总金额大于120，则另收总金额的10%，如果没有超过就统一收30的金额。
下面的内容将介绍如何实现这个需求。

magento默认有按照运输方式的不同设置不同的运费，但选择不同的付款方式另收费用貌似还没这功能。所以这里面自己来添加。

1. 先创建一个模块，这就不介绍如何创建模块了，主要讲重点。

2.在etc/config.xml加上以下设置，在global下加

<sales>
    <quote>
        <totals>
            <ips_total>
                <class>ips/quote_address_total_ips</class>
                <after>subtotal,freeshipping,tax_subtotal,shipping</after>
                <before>grand_total</before>
            </ips_total>
        </totals>
    </quote>
</sales>

这里面有点很重要，因为需求是总金额的10%，所以这里面设置顺序非常重要，一定要在所有的收费之下 ，grand_total之上，要不在下面的model里面没法算对所有总金额。

3.创建如上代码设置的class的model.代码如下

<?php
class Mage_Ips_Model_Quote_Address_Total_Ips extends
 Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function __construct()
    {
        $this->setCode('ips_total');
    }

    /**
     * Collect totals information about ips
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Total_Shipping
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        if($address->getAddressType()!='shipping') return $this;
        try{
            $quote = $address->getQuote();
            if(!$quote)return $this;
            if($quote->getPaymentsCollection()->count()==0)return $this;
            $payment = $quote->getPayment();
            if($payment && $payment->getMethod() == 'ips_payment') {

                $TotalAmount = 0;
                $ipsAmount = 0;
                foreach($address->getAllTotalAmounts() as $amount)
        $TotalAmount+= $amount;

                if($TotalAmount >= 120.00) {
                    $ipsAmount =   $TotalAmount * 0.1;
                } else {
                    $ipsAmount = 30;
                }

                $ipsAmount =  $quote->getStore()
                    ->roundPrice($ipsAmount);


                $this->_setAmount($ipsAmount)
             ->_setBaseAmount($ipsAmount);
            }

        } catch(Exception $e) {
            Mage::throwException($e->getMessage());
            //do nothing.
        }
        return $this;
    }

    /**
     * Add ips totals information to address object
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Total_Shipping
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        parent::fetch($address);
        $amount = $address->getTotalAmount($this->getCode());
        if ($amount!=0) {
            $address->addTotal(array(
                'code'=>$this->getCode(),
                'title'=>Mage::helper('ips')->__('Ips Cost'),
                'value'=>$amount
            ));
        }

        return $this;
    }

    /**
     * Get Shipping label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('ips')->__('Ips Cost');
    }
}
