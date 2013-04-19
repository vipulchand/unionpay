 Magento 覆写

发布时间:
    2010年08月23日
作者:
    2BETOP

Magento的覆写很重要，在项目开发过程中经常需要修改核心的东西，但为了不影响magento系统升级，
我们不能直接对magento的核心代码修改，我们只能覆写。下面介绍各个部件的覆写方法。

如要覆写magento核心模块里面的php文件即app/code/core目录下的php文件，
可以把core目录下的文件直接复制到 local目录 下，比如覆写/core/Mage/Catalog/Model/Product.php文件，
那么把product复制放到local保持路径一至 /local/Mage/Catalog/Model/Product.php，然后Magento就会用local中这个文件而不是core里面的。
顺 序是local→community→core→lib，意思是用这种方法连lib里面的文件都可以覆写。但 controller是不能用这种方法覆写的。
这个覆写之所以起作用是因为Magento set_include_path的时候先加的是local,然后是community,然后是core,再然后才是lib。（见 app/Mage.php文件）
但这种覆写方式不好，尽量不用

接下来说的覆写方法才是比较合理的。
etc的覆写

etc其实不需要覆写，如你要改其它模块的配置信息，直接在自己的模块配置文件里面改就行了，一样可以改过来。

来个例子吧，比如你要修改config/global/customer/address/formats/html的值，你不需要非得在customer模块中改。在任意的etc/config.xml文件都可以改 如下代码就OK。

<global>
        <customer>
            <address>
                <formats>
                    <html>
                        <defaultFormat><![CDATA[
<strong>Character Name:</strong> {{var character_name}}<br />
<strong>Name:</strong> {{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}<br />
<strong>Country:</strong> {{var country}}<br />
{{depend telephone}}<strong>Tel: </strong> {{var telephone}}{{/depend}}
                ]]></defaultFormat>
                    </html>
                </formats>
            </address>
        </customer>
     </global>


Block, Model, Helper的覆写

Block, Model, Helper的覆写比较简单,而且方式一样，这里面只示例block的覆写 比如就在Helloworld模块的上，覆写page/html block。 在etc/config.xml的config/global/blocks里面添加以下代码

<page>
    <!-- override "page/html" block. -->
    <rewrite>
        <html>Namespace_Helloworld_Block_Page_Html</html>
    </rewrite>
</page>

然后在此模块目录下的Block目录下新建Page目录，然后新建Html.php文件，文件内容为

<?php

class Namespace_Helloworld_Block_Page_Html extends Mage_Page_Block_Html
{
    //override goes here.
    //在这里面可以尽情重写，
}

用这种覆写方式的好处是，这里用到了继承，也就是没必要重写的可以不写出来，直接继承父类就行了。
controller的覆写

controller有两种覆写方法，假如我们想在浏览产品分类的时候用到的不是Mage_Catalog模块下的category控制器，
而是Namespace_Helloworld模块下的catalog_category控制器。
方法一

修改etc/config文件，在config/global里面加上改下代码

<rewrite>
    <helloworld_catalog_category>
        <from><![CDATA[#^(/)?catalog/category/#]]></from>
        <to><![CDATA[helloworld/catalog_category/]]></to>
    </helloworld_catalog_category>
</rewrite>

这种方法实际上是正则替换把catalog/category/*替换成了helloworld/catalog_category/* 然后在Helloworld模块目录下的controllers目录下，新建Catalog目录，然后新建CategoryController.php 文件，内容为

<?php
  require_once 'Mage/Catalog/controllers/CategoryController.php';

  class Namespace_Helloworld_Catalog_CategoryController extends Mage_Catalog_CategoryController {
    /**
     * Category view action
     */
    public function viewAction()
    {
       echo 'controller override success';
       parent::viewAction();
    }

  }
?>

方法二

方法二与方法一的唯一不同是etc/config.xml的写法不一样，方法二的写法如下 在config下加下面的代码。

<admin>
  <routers>
    <adminhtml>
      <args>
        <modules>
          <Namespace_Helloworld before="Mage_Adminhtml">Namespace_Helloworld</Namespace_Helloworld>
        </modules>
      </args>
    </adminhtml>
  </routers>
 </admin>