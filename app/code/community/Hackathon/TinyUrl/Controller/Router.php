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
    const XML_PATH_TINY_URL_ROUTER = 'catalog/tinyurl/router';

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
        Mage::dispatchEvent('cms_controller_router_match_before', array(
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
        if(Mage::getStoreConfig(self::XML_PATH_TINY_URL_ROUTER) &&
            count($iarray) > 1 && $iarray[0] == Mage::getStoreConfig(self::XML_PATH_TINY_URL_ROUTER) &&
            $iarray[1] == (int) $iarray[1]) {
            $request->setModuleName('catalog')
                ->setControllerName('product')
                ->setActionName('view')
                ->setParam('id', (int) $iarray[1]);
            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $identifier
            );

            return true;
        }
        return false;
    }
}