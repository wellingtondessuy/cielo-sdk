<?php

namespace BraspagCielo\API30;

/**
 * Interface Environment
 *
 * @package Cielo\API30
 */
interface Environment
{
    /**
     * Gets the environment's Api URL
     *
     * @return string the Api URL
     */
    public function getApiUrl();

    /**
     * Gets the environment's Api Query URL
     *
     * @return string the Api Query URL
     */
    public function getApiQueryURL();

    /**
     * Gets the environment's Api Split URL
     *
     * @return string Api Split URL
     */
    public function getApiSplitURL();

    /**
     * Gets the environment's Api BraspagOauth2Server URL
     *
     * @return string Api BraspagOauth2Server URL
     */
    public function getBraspagOauth2ServerURL();
}
