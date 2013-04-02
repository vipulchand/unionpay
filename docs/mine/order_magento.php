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
