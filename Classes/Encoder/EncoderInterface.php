<?php

namespace Madj2k\AjaxApi\Encoder;
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Class EncoderInterface
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_AjaxApi
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
interface EncoderInterface
{

    /**
     * Sets status
     *
     * @param int $value
     * @return self
     */
    public function setStatus(int $value): self;


    /**
     * Sets message
     *
     * @param string  $id
     * @param string  $message
     * @param int $type
     * @return self
     */
    public function setMessage(string $id, string $message, int $type = 1): self;


    /**
     * Sets data
     *
     * @param mixed $data
     * @return self
     */
    public function setData($data): self;


    /**
     * Unsets data
     *
     * @return self
     */
    public function unsetData(): self;


    /**
     * Unset HTML
     *
     * @return $this
     */
    public function unsetHtml(): self;


    /**
     * Sets JavaScript
     *
     * @param boolean $before
     * @param string $javaScript
     * @return $this
     */
    public function setJavaScript(string $javaScript, bool $before = false): self;


    /**
     * Unsets JavaScript
     *
     * @return self
     */
    public function unsetJavaScript(): self;


    /**
     * Returns encoded string
     *
     * @return string
     */
    public function __toString(): string;

}
