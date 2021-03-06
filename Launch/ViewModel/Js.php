<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\Launch\ViewModel;

use Magento\Framework\Serialize\Serializer\JsonHexTag;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Adobe\Launch\Api\GetAllDatalayerEventsInterface;
use Adobe\Launch\Model\LaunchConfigProvider;

/**
 * View Model for the Launch related blocks. Provides module configuration.
 */
class Js implements ArgumentInterface
{
    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @var GetAllDatalayerEventsInterface
     */
    private $getAllDatalayerEvents;

    /**
     * @var JsonHexTag
     */
    private $json;

    /**
     * @param LaunchConfigProvider $launchConfigProvider
     * @param GetAllDatalayerEventsInterface $getAllDatalayerEvents
     * @param JsonHexTag $json
     */
    public function __construct(
        LaunchConfigProvider $launchConfigProvider,
        GetAllDatalayerEventsInterface $getAllDatalayerEvents,
        JsonHexTag $json
    ) {
        $this->launchConfigProvider = $launchConfigProvider;
        $this->getAllDatalayerEvents = $getAllDatalayerEvents;
        $this->json = $json;
    }

    /**
     * Get Launch script url.
     *
     * @return string
     */
    public function getScriptUrl(): ?string
    {
        return $this->launchConfigProvider->getScriptUrl();
    }

    /**
     * Get JS Datalayer object name.
     *
     * @return string
     */
    public function getDatalayerName(): ?string
    {
        return $this->launchConfigProvider->getDatalayerName();
    }

    /**
     * Return all events stored in the datalayer.
     *
     * @return string
     */
    public function getDatalayerEvents(): string
    {
        return $this->json->serialize($this->getAllDatalayerEvents->execute());
    }
}
