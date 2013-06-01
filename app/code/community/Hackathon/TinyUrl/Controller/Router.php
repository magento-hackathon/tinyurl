<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 01.06.13
 * Time: 12:18
 * To change this template use File | Settings | File Templates.
 */
class Hackathon_TinyUrl_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    const XML_PATH_TINY_URL_PRODUCT_PREFIX = 'catalog/tinyurl/product_prefix';
    const XML_PATH_TINY_URL_CATEGORY_PREFIX = 'catalog/tinyurl/category_prefix';
    const XML_PATH_TINY_URL_CMS_PAGE_PREFIX = 'catalog/tinyurl/cms_page_prefix';

    /**
     * Initialize Controller Router
     *
     * @param Varien_Event_Observer $observer
     */
    public function initControllerRouters($observer)
    {
        /* @var $front Mage_Core_Controller_Varien_Front */
        $front = $observer->getEvent()->getFront();

        $front->addRouter('tinyurl', $this);
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }

        $identifier = trim($request->getPathInfo(), '/');

        $condition = new Varien_Object(array(
            'identifier' => $identifier,
            'continue'   => true
        ));
        Mage::dispatchEvent('tinyurl_controller_router_match_before', array(
            'router'    => $this,
            'condition' => $condition
        ));
        $identifier = $condition->getIdentifier();

        if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return true;
        }

        if (!$condition->getContinue()) {
            return false;
        }

        $iarray = explode('/', $identifier);
        if(count($iarray) > 1) {
            if(Mage::getStoreConfig(self::XML_PATH_TINY_URL_PRODUCT_PREFIX) &&
                $iarray[0] == Mage::getStoreConfig(self::XML_PATH_TINY_URL_PRODUCT_PREFIX) &&
                $iarray[1] == (int) $iarray[1]) {
                $urlRewrite = Mage::getModel('core/url_rewrite')->getCollection()
                    ->addFieldToFilter('id_path', 'product/'.(int) $iarray[1])
                    ->addFieldToFilter('store_id', Mage::app()->getStore()->getId());
                if($urlRewrite->count()) {
                    $item = $urlRewrite->getFirstItem();
                    Mage::app()->getResponse()
                        ->setRedirect(Mage::getBaseUrl() . $item->getRequestPath());
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }
            } else if(Mage::getStoreConfig(self::XML_PATH_TINY_URL_CATEGORY_PREFIX) &&
                $iarray[0] == Mage::getStoreConfig(self::XML_PATH_TINY_URL_CATEGORY_PREFIX) &&
                $iarray[1] == (int) $iarray[1]) {
                $urlRewrite = Mage::getModel('core/url_rewrite')->getCollection()
                    ->addFieldToFilter('id_path', 'category/'.(int) $iarray[1])
                    ->addFieldToFilter('store_id', Mage::app()->getStore()->getId());
                if($urlRewrite->count()) {
                    $item = $urlRewrite->getFirstItem();
                    Mage::app()->getResponse()
                        ->setRedirect(Mage::getBaseUrl() . $item->getRequestPath());
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }
            } else if(Mage::getStoreConfig(self::XML_PATH_TINY_URL_CMS_PAGE_PREFIX) &&
                $iarray[0] == Mage::getStoreConfig(self::XML_PATH_TINY_URL_CMS_PAGE_PREFIX) &&
                $iarray[1] == (int) $iarray[1]) {
                $cmsPage = Mage::getModel('cms/page')->load((int) $iarray[1]);
                if($cmsPage->getId()) {
                    Mage::app()->getResponse()
                        ->setRedirect(Mage::getBaseUrl() . $cmsPage->getIdentifier());
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }
            }
        }
        return false;
    }
}