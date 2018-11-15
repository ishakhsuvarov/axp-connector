<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\AxpConnector\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer for checkout success event
 */
class CheckoutOnepageControllerSuccessActionObserver implements ObserverInterface
{
    /**
     * @var string
     */
    const COOKIE_NAME = 'axpconnector_checkout_success';

    /**
     * Short duration, it just has to survive a page load
     */
    const COOKIE_DURATION_SECS = 180;

    /**
     * @var \Adobe\AxpConnector\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * CheckoutOnepageControllerSuccessActionObserver constructor.
     * @param \Adobe\AxpConnector\Helper\Data $helper
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        \Adobe\AxpConnector\Helper\Data $helper,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->helper = $helper;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * @inheritdoc
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        $datalayerContent = $this->helper->orderPlacedPushData($orderIds);
        $datalayerName = $this->helper->getDatalayerName();

        $cookieContent = [
            'datalayerName' => $datalayerName,
            'datalayerContent' => $datalayerContent
        ];

        if (count($datalayerContent) > 0) {
            $jsonContent = $this->helper->jsonify($cookieContent);
            $metadata = $this->cookieMetadataFactory
                ->createPublicCookieMetadata()
                ->setDuration(self::COOKIE_DURATION_SECS);
            $this->cookieManager->setPublicCookie(self::COOKIE_NAME, $jsonContent, $metadata);
        }
    }
}
