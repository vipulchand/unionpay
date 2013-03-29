unionpay
========

UNION PAY
[UNION PAY]
https://online.unionpay.com/
requests:
https://online.unionpay.com/mer/pages/merser/helper.jsp?pid=1&cid=1
information for technique:
https://online.unionpay.com/mer/pages/merser/helper.jsp?pid=1&cid=1
11、防钓鱼接口有哪些特殊的技术要求？
（1）商户需在入网申请表中填写真实交易域名，如有多个，需全部填写。
（2）交易报文中customIp字段需上送客户真实IP，详情请参考接口规范文档中对customIp域的说明。
（3）UPOP会根据当前服务器时间减掉商户上送的订单时间来计算订单超时时间，所得时间间隔如果大于上送的transTimeout字段，则认为该笔订单超时。商户需对transTimeout字段上送一个合理值，建议300000毫秒。
[UNION PAY]