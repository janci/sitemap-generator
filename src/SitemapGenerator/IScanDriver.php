<?php
/**
 * This file is part of the SitemapGenerator
 *
 * Copyright (c) 2013 Ing. Jan Svantner (http://www.janci.net)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace SitemapGenerator;

/**
 * Interface IScanDriver described driver to gets html content for scanning.
 *
 * @package SitemapGenerator
 * @author Ing. Jan Svantner <posta.janci@gmail.com>
 */
interface IScanDriver {

    /**
     * Create new scan driver instance by gets url of site.
     * @param $url
     * @return IScanDriver
     */
    public static function fromUrl($url);

    /**
     * Returns full url path with domain.
     * This address will be use as unique site identifier.
     * @return string
     */
    public function getUrl();

    /**
     * Returns content for source site url.
     * @return string
     */
    public function getContent();

    /**
     * Scan driver can decide about correct input data. If scan driver detect
     * not valid input source, scanning can be refused.
     * @return bool
     */
    public function validate();

} 