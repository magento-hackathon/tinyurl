<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 01.06.13
 * Time: 14:24
 * To change this template use File | Settings | File Templates.
 */
class Hackathon_TinyUrl_Helper_Data extends Mage_Core_Helper_Abstract
{
    const TYPE_PRODUCT = 1;
    const TYPE_CATEGORY = 2;
    const TYPE_CMS_PAGE = 3;

    public function getTinyUrl($id, $type)
    {
        $id = (int) $id;
        $prefix = '';
        switch($type) {
            case self::TYPE_PRODUCT:
                $prefix = Mage::getStoreConfig(Hackathon_TinyUrl_Controller_Router::XML_PATH_TINY_URL_PRODUCT_PREFIX);
                break;
            case self::TYPE_CATEGORY:
                $prefix = Mage::getStoreConfig(Hackathon_TinyUrl_Controller_Router::XML_PATH_TINY_URL_CATEGORY_PREFIX);
                break;
            case self::TYPE_CMS_PAGE:
                $prefix = Mage::getStoreConfig(Hackathon_TinyUrl_Controller_Router::XML_PATH_TINY_URL_CMS_PAGE_PREFIX);
                break;
            default:
                $prefix = Mage::getStoreConfig(Hackathon_TinyUrl_Controller_Router::XML_PATH_TINY_URL_PRODUCT_PREFIX);
        }
        return Mage::getUrl($prefix . '/' . $id);
    }
}