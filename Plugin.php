<?php

/**
 * 页面和文章自定义编辑字段插件<br>
 * <i>Typecho插件定制QQ86199786</i>
 *
 *
 * @package CustomContentField
 * @author 清风
 * @version 1.0.0
 * @link http://www.quhe.net
 */

namespace TypechoPlugin\CustomContentField;

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Text;
use TypechoPlugin\CustomContentField\lib\Helper;
use Widget\Contents\Post\Edit as PostEdit;
use Widget\Contents\Page\Edit as PageEdit;

class Plugin implements PluginInterface
{

    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     */
    public static function activate()
    {
        \Typecho\Plugin::factory('admin/write-post.php')->option = __CLASS__ . '::postRender';
        \Typecho\Plugin::factory('admin/write-page.php')->option = __CLASS__ . '::pageRender';
        \Typecho\Plugin::factory(PostEdit::class)->finishPublish = __CLASS__ . '::postFinish';
        \Typecho\Plugin::factory(PostEdit::class)->finishSave = __CLASS__ . '::postFinish';
        \Typecho\Plugin::factory(PageEdit::class)->finishPublish = __CLASS__ . '::pageFinish';
        \Typecho\Plugin::factory(PageEdit::class)->finishSave = __CLASS__ . '::pageFinish';
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     */
    public static function deactivate()
    {
    }

    /**
     * 获取插件配置面板
     *
     * @param Form $form 配置面板
     */
    public static function config(Form $form)
    {
        $postField = new Text(
            'post_field',
            null,
            null,
            _t('文章字段'),
            '1.格式：数据库字段名|显示标签名<br>2.多个用逗号","隔开<br>3.例子：views|浏览量,agree|点赞数'
        );

        $pageField = new Text(
            'page_field',
            null,
            null,
            _t('页面字段'),
            '1.格式：数据库字段名|显示标签名<br>2.多个用逗号","隔开<br>3.例子：views|浏览量,agree|点赞数'
        );

        $form->addInput($postField);
        $form->addInput($pageField);
    }

    /**
     * 个人用户的配置面板
     *
     * @param Form $form
     */
    public static function personalConfig(Form $form)
    {
    }

    /**
     * @param $page Widget\Contents\Page\Edit
     * @return void
     * @throws \Typecho\Plugin\Exception
     * @throws \Typecho\Db\Exception
     */
    public static function pageRender($page)
    {
        $pageField = trim(\Typecho\Widget::widget('Widget_Options')->plugin('CustomField')->page_field);
        if ($pageField) {
            $html = '<hr style="height: 1px;border:none;border-top: 1px dashed #009688;">';
            $fieldsValue = Helper::getContentFieldsById($page->cid, Helper::getFields('page'));
            foreach (explode(',', $pageField) as $field) {
                $fieldArr = explode('|', $field);
                $fieldLabel = $fieldArr[1] ?? $fieldArr[0];
                $fieldValue = $fieldsValue[$fieldArr[0]];
                $html .= <<<HTML
<section class="typecho-post-option">
    <label class="typecho-label">{$fieldLabel}</label>
    <p><input id="{$fieldArr[0]}" name="{$fieldArr[0]}" type="text" value="{$fieldValue}" class="w-100 text"/></p>
</section>
HTML;
            }
            $html .= '<hr style="height: 1px;border:none;border-top: 1px dashed #009688;">';
            echo $html;
        }

    }

    /**
     * @param $post Widget\Contents\Post\Edit
     * @return void
     * @throws \Typecho\Plugin\Exception
     * @throws \Typecho\Db\Exception
     */
    public static function postRender($post)
    {
        $postField = Helper::getFields('post', true);
        if ($postField) {
            $html = '<hr style="height: 1px;border:none;border-top: 1px dashed #009688;">';
            $fieldsValue = Helper::getContentFieldsById($post->cid, Helper::getFields('post'));
            foreach ($postField as $field) {
                $fieldArr = explode('|', $field);
                $fieldLabel = $fieldArr[1] ?? $fieldArr[0];
                $fieldValue = $fieldsValue[$fieldArr[0]];
                $html .= <<<HTML
<section class="typecho-post-option">
    <label class="typecho-label">{$fieldLabel}</label>
    <p><input id="{$fieldArr[0]}" name="{$fieldArr[0]}" type="text" value="{$fieldValue}" class="w-100 text"/></p>
</section>
HTML;
            }
            $html .= '<hr style="height: 1px;border:none;border-top: 1px dashed #009688;">';
            echo $html;
        }

    }

    /**
     * @param $contents
     * @param $edit PageEdit
     * @return array
     * @throws \Typecho\Plugin\Exception
     */
    public static function pageFinish($contents, $edit)
    {
        $customFiledInput = $edit->request->from(Helper::getFields('page'));
        Helper::updateFields($edit->cid, $customFiledInput);
    }

    /**
     * @param $contents
     * @param $edit PostEdit
     * @return array
     * @throws \Typecho\Plugin\Exception
     */
    public static function postFinish($contents, $edit)
    {
        $customFiledInput = $edit->request->from(Helper::getFields('post'));
        Helper::updateFields($edit->cid, $customFiledInput);
    }
}