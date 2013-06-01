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
    public function getTinyUrl($productId)
    {
        $productId = (int) $productId;
        $tinyurlPrefix = Mage::getStoreConfig(Hackathon_TinyUrl_Controller_Router::XML_PATH_TINY_URL_ROUTER);
        return Mage::getUrl($tinyurlPrefix . '/' . $productId);
    }
}