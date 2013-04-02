事件派送很简单，如以下代码
Mage::dispatchEvent('sales_quote_remove_item', array('quote_item' => $item));

Mage::addObserver来监听事件，但一般不那样做，基本上都是在etc/config.xml只添加的。
<events>
     <store_add>
         <observers>
             <catalog>
                 <type>singleton</type>
                 <class>catalog/observer</class>
                 <method>storeAdd</method>
             </catalog>
             <catalog_product_flat>
                 <type>singleton</type>
                 <class>catalog/product_flat_observer</class>
                 <method>storeAdd</method>
             </catalog_product_flat>
         </observers>
     </store_add>
 </events>

 当store_add事件派送之后，会有两个函数会执行，
 它们分别是catalog模块下的Model目录下的Observer.php文件里面的 storeAdd方法，
 和catalog模块下的Model/Product/Flat/Observer.php文件里面的StoreAdd方法
