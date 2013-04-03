[About Order Status:]

web address: http://sjolzy.cn/Magento-default-order-status.html

当前位置: 首页 > PHP | 工作经验 > Magento默认订单状态

1
Magento默认订单状态 29 October 2010 11:57 Friday by 小屋
标签: 状态	magento	订单	const	state

require_once('app/Mage.php');
umask(0);
Mage::app('default');

$order = Mage::getModel('sales/order');
$order->loadByIncrementId(100000001);  // 100000001为订单编号

// 获取订单状态
$status = $order->getStatus();
$state  = $order->getState();

echo $status;
echo "\r\n";
echo $state;

// 设置订单状态
$order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING);
$order->save();

Magento订单 有两个状态变量：state和status，这让人困惑，只有测试下了，于是下了个单，然后在Magneto后台处理订单，得出下面的Magento订单状态值。

1. 新订单
state  : new
status : pending

2. 配送后
state  : processing
status : processing

3. 收款后
state  : processing
status : processing

4. 订单完成
state  : complete
status : complete

5. 订单取消
state  : canceled
status : canceled

6. 订单关闭
state  : closed
status : closed

7. 订单挂起
state  : holded
status : holded

Magento订单状态 是定义在Magento代码文件app\code\core\Mage\Sales\Model\Order.php中定义了订单的状态常量：

/**
 * Order model
 *
 * Supported events:
 *  sales_order_load_after
 *  sales_order_save_before
 *  sales_order_save_after
 *  sales_order_delete_before
 *  sales_order_delete_after
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order extends Mage_Sales_Model_Abstract
{

    /**
     * Order states
     */
    const STATE_NEW             = 'new';
    const STATE_PENDING_PAYMENT = 'pending_payment';
    const STATE_PROCESSING      = 'processing';
    const STATE_COMPLETE        = 'complete';
    const STATE_CLOSED          = 'closed';
    const STATE_CANCELED        = 'canceled';
    const STATE_HOLDED          = 'holded';
    const STATE_PAYMENT_REVIEW  = 'payment_review'; // added magento 1.4

/**
     * Order flags
     */
    const ACTION_FLAG_CANCEL    = 'cancel';
    const ACTION_FLAG_HOLD      = 'hold';
    const ACTION_FLAG_UNHOLD    = 'unhold';
    const ACTION_FLAG_EDIT      = 'edit';
    const ACTION_FLAG_CREDITMEMO= 'creditmemo';
    const ACTION_FLAG_INVOICE   = 'invoice';
    const ACTION_FLAG_REORDER   = 'reorder';
    const ACTION_FLAG_SHIP      = 'ship';
    const ACTION_FLAG_COMMENT   = 'comment';

    // ...
}
</core@magentocommerce.com>

其中，pending_payment, payment_review 是支付（Paypal, Amazon Pay）过程中引入的订单状态。
作者: Sjolzy | Google+
地址: http://sjolzy.cn/Magento-default-order-status.html

--EOF--

[Magento orders: states and statuses]

Magento orders have different states for following their process (billed, shipped, refunded...) in the order Workflow.
These states are not visible in Magento back office. In fact, it is orders statuses that are displayed in back office and not their states.

Each state can have one or several statuses and a status can have only one state.
By default, statuses and states have often the same name, that is why it is a little confusing. Here is the list of statuses and states available by default.
->
For adding new status to a state, you just need to declare it in config.xml file
<config>
  ...
  <global>
    <sales>
      <order>
        <!-- Statuses declaration -->
        <statuses>
          <my_processing_status translate="label"><label>My Processing Status</label></my_status>
        </statuses>
        <!-- Linking Status to a state -->
        <states>
          <processing>
            <statuses>
              <my_processing_status/>
            </statuses>
          </processing>
        </states>
      </order>
    </sales>
  </global>
</config>

When we want to modify order status in some code,
we have to be sure that current order state allows status wanted. It is possible to change both state and status with setState method
->
$order = Mage::getModel('sales/order')->loadByIncrementId('100000001');
$state = 'processing';
$status = 'my_processing_status';
$comment = 'Changing state to Processing and status to My Processing Status';
$isCustomerNotified = false;
$order->setState($state, $status, $comment, $isCustomerNotified);
$order->save();
[Magento orders: states and statuses]

[Magento Order Workflow]
Webaddress: http://www.magentocommerce.com/knowledge-base/entry/order-workflow/
1. New Order -> Pending Payment -> Processing -> Complete -> Closed
2. New Order -> Canceled
3. New Order -> On Hold
[Magento Order Workflow]


[Basic Definition]
1.Subtotal/小计   ¥0.01
Price: 0.01
Qty: 1
Qty to invoice: 1
->Subtotal: 0.01
Tax Amount: 0.00
Discount Amount: 0.00
->Row Total: 0.01
付款前得汇总

2.Grand Total/累计   ¥0.01
Subtotal - paid amount - refund amount - shipping amount - shipping refund = Order Grand Total
[Basic Definition]

[Sample Data]
array(149) {
["entity_id"]=> string(2) "22"
["state"]=> string(3) "new"
["status"]=> string(7) "pending"
["coupon_code"]=> NULL
["protect_code"]=> string(6) "858bc1"
["shipping_description"]=> NULL
["is_virtual"]=> string(1) "1"
["store_id"]=> string(1) "1"
["customer_id"]=> string(2) "13"
["base_discount_amount"]=> string(6) "0.0000"
["base_discount_canceled"]=> NULL
["base_discount_invoiced"]=> NULL
["base_discount_refunded"]=> NULL
["base_grand_total"]=> string(6) "0.0100"
["base_shipping_amount"]=> string(6) "0.0000"
["base_shipping_canceled"]=> NULL
["base_shipping_invoiced"]=> NULL
["base_shipping_refunded"]=> NULL
["base_shipping_tax_amount"]=> string(6) "0.0000"
["base_shipping_tax_refunded"]=> NULL
["base_subtotal"]=> string(6) "0.0100"
["base_subtotal_canceled"]=> NULL
["base_subtotal_invoiced"]=> NULL
["base_subtotal_refunded"]=> NULL
["base_tax_amount"]=> string(6) "0.0700"
["base_tax_canceled"]=> NULL
["base_tax_invoiced"]=> NULL
["base_tax_refunded"]=> NULL
["base_to_global_rate"]=> string(6) "1.0000"
["base_to_order_rate"]=> string(6) "1.0000"
["base_total_canceled"]=> NULL
["base_total_invoiced"]=> NULL
["base_total_invoiced_cost"]=> NULL
["base_total_offline_refunded"]=> NULL
["base_total_online_refunded"]=> NULL

["base_total_paid"]=> NULL 100.0700

["base_total_qty_ordered"]=> NULL
["base_total_refunded"]=> NULL
["discount_amount"]=> string(6) "0.0000"
["discount_canceled"]=> NULL
["discount_invoiced"]=> NULL
["discount_refunded"]=> NULL

["grand_total"]=> string(6) "100.0700"

["shipping_amount"]=> string(6) "0.0000"
["shipping_canceled"]=> NULL
["shipping_invoiced"]=> NULL
["shipping_refunded"]=> NULL
["shipping_tax_amount"]=> string(6) "0.0000"
["shipping_tax_refunded"]=> NULL
["store_to_base_rate"]=> string(6) "1.0000"
["store_to_order_rate"]=> string(6) "1.0000"
["subtotal"]=> string(6) "0.0100"
["subtotal_canceled"]=> NULL
["subtotal_invoiced"]=> NULL
["subtotal_refunded"]=> NULL

["tax_amount"]=> string(6) "0.0700"

["tax_canceled"]=> NULL
["tax_invoiced"]=> NULL
["tax_refunded"]=> NULL
["total_canceled"]=> NULL
["total_invoiced"]=> NULL
["total_offline_refunded"]=> NULL
["total_online_refunded"]=> NULL

["total_paid"]=> NULL

["total_qty_ordered"]=> string(6) "1.0000"
["total_refunded"]=> NULL
["can_ship_partially"]=> NULL
["can_ship_partially_item"]=> NULL
["customer_is_guest"]=> string(1) "0"
["customer_note_notify"]=> string(1) "1"
["billing_address_id"]=> string(2) "22"
["customer_group_id"]=> string(1) "1"
["edit_increment"]=> NULL
["email_sent"]=> string(1) "1"
["forced_shipment_with_invoice"]=> NULL
["payment_auth_expiration"]=> NULL
["quote_address_id"]=> NULL
["quote_id"]=> string(3) "105"
["shipping_address_id"]=> NULL
["adjustment_negative"]=> NULL
["adjustment_positive"]=> NULL
["base_adjustment_negative"]=> NULL
["base_adjustment_positive"]=> NULL
["base_shipping_discount_amount"]=> string(6) "0.0000"
["base_subtotal_incl_tax"]=> string(6) "0.0100"
["base_total_due"]=> string(6) "0.0100"
["payment_authorization_amount"]=> NULL
["shipping_discount_amount"]=> string(6) "0.0000"
["subtotal_incl_tax"]=> string(6) "0.0100"
["total_due"]=> string(6) "0.0100" ["weight"]=> string(6) "0.0000"
["customer_dob"]=> NULL
["increment_id"]=> string(9) "100000022"
["applied_rule_ids"]=> NULL
["base_currency_code"]=> string(3) "CNY"
["customer_email"]=> string(18) "aiuhio@outlook.com"
["customer_firstname"]=> string(7) "Maxwell"
["customer_lastname"]=> string(3) "Shi"
["customer_middlename"]=> NULL
["customer_prefix"]=> NULL
["customer_suffix"]=> NULL
["customer_taxvat"]=> NULL
["discount_description"]=> NULL
["ext_customer_id"]=> NULL
["ext_order_id"]=> NULL
["global_currency_code"]=> string(3) "CNY"
["hold_before_state"]=> NULL
["hold_before_status"]=> NULL
["order_currency_code"]=> string(3) "CNY"
["original_increment_id"]=> NULL
["relation_child_id"]=> NULL
["relation_child_real_id"]=> NULL
["relation_parent_id"]=> NULL
["relation_parent_real_id"]=> NULL
["remote_ip"]=> string(9) "127.0.0.1"
["shipping_method"]=> NULL
["store_currency_code"]=> string(3) "CNY"
["store_name"]=> string(31) "mo-commerce.com.dev Demo 中文"
["x_forwarded_for"]=> NULL
["customer_note"]=> NULL
["created_at"]=> string(19) "2013-04-02 10:27:11"
["updated_at"]=> string(19) "2013-04-02 10:27:15"
["total_item_count"]=> string(1) "1"
["customer_gender"]=> NULL
["hidden_tax_amount"]=> string(6) "0.0000"
["base_hidden_tax_amount"]=> string(6) "0.0000"
["shipping_hidden_tax_amount"]=> string(6) "0.0000"
["base_shipping_hidden_tax_amnt"]=> string(6) "0.0000"
["hidden_tax_invoiced"]=> NULL
["base_hidden_tax_invoiced"]=> NULL
["hidden_tax_refunded"]=> NULL
["base_hidden_tax_refunded"]=> NULL
["shipping_incl_tax"]=> string(6) "0.0000"
["base_shipping_incl_tax"]=> string(6) "0.0000"
["coupon_rule_name"]=> NULL
["paypal_ipn_customer_notified"]=> string(1) "0"
["gift_message_id"]=> NULL
["alipay_ipn_customer_notified"]=> string(1) "0"
["affiliateplus_discount"]=> NULL
["base_affiliateplus_discount"]=> NULL
["groupdeals_coupon_from"]=> NULL
["groupdeals_coupon_to"]=> NULL
["groupdeals_coupon_to_email"]=> NULL
["groupdeals_coupon_message"]=> NULL
["wapalipay_ipn_customer_notified"]=> string(1) "0"
["customer_mobile"]=> string(11) "18621798517"
["payment_authorization_expiration"]=> NULL
["forced_do_shipment_with_invoice"]=> NULL
["base_shipping_hidden_tax_amount"]=> string(6) "0.0000" }
[Sample Data]

[MAGENTO TAX]
Webaddress: http://www.magentocommerce.com/knowledge-base/entry/configuring-general-tax-settings
[MAGENTO TAX]

[MAGENTO UPDATE ORDER]
Table:sales_flat_order
Column:
base_subtotal_invoiced
base_tax_invoiced
base_total_invoiced

base_total_paid
total_paid

[MAGENTO UPDATE ORDER]


<config>
    <modules>
        <Inchoo_Invoicer>
            <version>1.0.0.0</version>
        </Inchoo_Invoicer>
    </modules>
    <global>
        <models>
            <inchoo_invoicer>
                <class>Inchoo_Invoicer_Model</class>
            </inchoo_invoicer>
        </models>
        <events>
            <sales_order_save_after>
                <observers>
                    <inchoo_invoicer_automatically_complete_order>
                        <class>inchoo_invoicer/observer</class>
                        <method>automaticallyInvoiceShipCompleteOrder</method>
                    </inchoo_invoicer_automatically_complete_order>
                </observers>
            </sales_order_save_after>
        </events>
    </global>
</config>
