<?php

namespace MirazMac\GoogleCSE\Element;

/**
 * WebResultElement, contains a web result
 *
 * @package MirazMac\GoogleCSE
 * @since 0.1
 */
class WebResultElement
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
        r($result);
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
     * Get the actual URL of the result
     *
     * @return string
     */
    public function getRawURL()
    {
        return $this->result['url'];
    }

    /**
     * Get the HTML formatted result URL
     *
     * @return string
     */
    public function getURL()
    {
        return $this->result['formattedUrl'];
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
     * Get Google cache URL if present false otherwise
     *
     * @return string|boolean
     */
    public function getCacheURL()
    {
        return isset($this->result['cacheUrl']) ?  $this->result['cacheUrl'] : false;
    }

    /**
     * Get click tracking URL
     *
     * @return string
     */
    public function getTrackingURL()
    {
        return $this->result['clicktrackUrl'];
    }

    /**
     * Get the rich snippet object
     *
     * @return object|boolean If there's rich snippet return MirazMac\GoogleCSE\Element FALSE otherwise
     */
    public function getRichSnippet()
    {
        return isset($this->result['richSnippet']) ?  $this->result['richSnippet'] : false;
    }
}
