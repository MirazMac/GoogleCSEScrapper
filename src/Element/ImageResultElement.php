<?php

namespace MirazMac\GoogleCSE\Element;

/**
 * ImageResultElement, contains a image result
 *
 * @package MirazMac\GoogleCSE
 * @since 0.1
 */
class ImageResultElement
{
    /**
     * Parsed result data from \MirazMac\GoogleCSE\Scrapper
     *
     * @var array
     */
    protected $result;

    /**
     * Constructor
     *
     * @param array $result Parsed result data from \MirazMac\GoogleCSE\Scrapper
     */
    public function __construct(array $result)
    {
        $this->result = $result;
    }

    /**
     * Get the HTML formatted result title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->result['title'];
    }

    /**
     * Get the raw result title
     *
     * @return string
     */
    public function getRawTitle()
    {
        return $this->result['titleNoFormatting'];
    }

    /**
     * Get the HTML formatted result content/description
     *
     * @return string
     */
    public function getContent()
    {
        return $this->result['content'];
    }

    /**
     * Get the raw result content/description
     *
     * @return string
     */
    public function getRawContent()
    {
        return $this->result['contentNoFormatting'];
    }

    /**
     * Get the actual URL of the image
     *
     * @return string
     */
    public function getRawURL()
    {
        return $this->result['url'];
    }

    /**
     * Get the actual URL of the image
     *
     * @return string
     */
    public function getURL()
    {
        return $this->result['url'];
    }

    /**
     * Get the webpage URL where the image is present
     *
     * @return string
     */
    public function getContextURL()
    {
        return $this->result['originalContextUrl'];
    }

    /**
     * Get the unscaped result URL
     *
     * @return string
     */
    public function getUnscapedURL()
    {
        return $this->result['unescapedUrl'];
    }

    /**
     * Get the visible part of result URL
     *
     * @return string
     */
    public function getVisibleURL()
    {
        return $this->result['visibleUrl'];
    }

    /**
     * Get image width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->result['width'];
    }

    /**
     * Get image height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->result['height'];
    }

    /**
     * Get image thumbnail URL
     *
     * @return string
     */
    public function getThumbnailURL()
    {
        return $this->result['tbUrl'];
    }

    /**
     * Get image thumbnail height
     *
     * @return integer
     */
    public function getThumbnailHeight()
    {
        return $this->result['tbHeight'];
    }

    /**
     * Get image thumbnail width
     *
     * @return integer
     */
    public function getThumbnailWidth()
    {
        return $this->result['tbWidth'];
    }
}
