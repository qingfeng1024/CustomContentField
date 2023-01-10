<?php

namespace TypechoPlugin\CustomContentField\lib;

use Typecho\Plugin\Exception;

class Helper
{

    /**
     * @throws \Typecho\Db\Exception
     */
    public static function getContentFieldsById($cid, $fields = null)
    {
        $db = \Typecho\Db::get();
        $field = implode(',', (array)$fields);
        return $db->fetchRow($db->select($field)->from('table.contents')->where('cid = ?', $cid));
    }

    /**
     * @param $cid
     * @param $fields
     * @return mixed
     * @throws \Typecho\Db\Exception
     */
    public static function updateFields($cid, $fields)
    {
        $fields = array_filter($fields);
        if($fields){
            $db = \Typecho\Db::get();
            return $db->query($db->update('table.contents')->rows($fields)->where('cid = ?', $cid));
        }
    }

    /**
     * @param $type 'post|page'
     * @param $containLabel
     * @return array
     * @throws Exception
     */
    public static function getFields($type, $containLabel = false): array
    {
        $fields = [];
        switch ($type) {
            case 'post':
                $fieldStr = trim(\Typecho\Widget::widget('Widget_Options')->plugin('CustomContentField')->post_field);
                break;
            case 'page':
                $fieldStr = trim(\Typecho\Widget::widget('Widget_Options')->plugin('CustomContentField')->page_field);
                break;
            default:
                throw new Exception('type不正确');
        }
        if ($fieldStr) {
            foreach (explode(',', $fieldStr) as $field) {
                $fields[] = $containLabel ? $field : explode('|', $field)[0];
            }
        }
        return $fields;
    }

}
