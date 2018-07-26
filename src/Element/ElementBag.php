<?php

namespace MirazMac\GoogleCSE\Element;

/**
 * ElementBag, Contains the Search Results
 *
 * @package MirazMac\GoogleCSE
 * @since 0.1
 */
class ElementBag
{
    /**
     * Contains parsed results from \MirazMac\GoogleCSE\Scrapper
     *
     * @var array
     */
    protected $bag = [];

    /**
     * Contains cursor/meta data about the results
     *
     * @var array
     */
    protected $cursorData = [];

    /**
     * Contains spelling data
     *
     * @var array
     */
    protected $spelling = [];

    /**
     * Create a new ElementBag
     *
     * @param array $parsedResults Parsed results from \MirazMac\GoogleCSE\Scrapper
     * @param array $cursorData    Cursor/meta data about the results
     * @param array $spelling      Spelling data
     */
    public function __construct(array $parsedResults, array $cursorData, $spelling = [])
    {
        $this->bag = $parsedResults;
        $this->cursorData = $cursorData;
        $this->spelling = $spelling;
    }

    /**
     * Returns all elements
     *
     * @return array
     */
    public function getAll()
    {
        return $this->bag;
    }

    /**
     * Returns pagination data
     *
     * @return array
     */
    public function getPages()
    {
        return isset($this->cursorData['pages']) ? $this->cursorData['pages'] : [];
    }

    /**
     * Returns current page index
     *
     * @return array
     */
    public function getCurrentPageIndex()
    {
        return isset($this->cursorData['currentPageIndex']) ? $this->cursorData['currentPageIndex'] : 0;
    }

    /**
     * Returns estimated result count
     *
     * @return integer
     */
    public function getEstimatedResultCount()
    {
        return isset($this->cursorData['estimatedResultCount']) ? $this->cursorData['estimatedResultCount'] : 0;
    }

    /**
     * Returns result count
     *
     * @return integer
     */
    public function getResultCount()
    {
        return isset($this->cursorData['resultCount']) ? $this->cursorData['resultCount'] : 0;
    }

    /**
     * Returns search time it took for the results
     *
     * @return integer|float
     */
    public function getSearchResultTime()
    {
        return isset($this->cursorData['searchResultTime']) ? $this->cursorData['searchResultTime'] : 0;
    }

    /**
     * Returns if there been a spelling mistake
     *
     * @return boolean
     */
    public function isSpellingMistake()
    {
        return !empty($this->spelling) ? true : false;
    }

    /**
     * Returns if there's a did you mean available
     *
     * @return boolean
     */
    public function hasDidYouMean()
    {
        return isset($this->spelling['type']) && $this->spelling['type'] === 'DYM' ? true : false;
    }

    /**
     * Returns raw corrected query ( if there's been a spelling mistake )
     *
     * @return string|boolean
     */
    public function getRawCorrectedQuery()
    {
        return isset($this->spelling['correctedQuery']) ? $this->spelling['correctedQuery'] : false;
    }

    /**
     * Returns HTML formatted corrected query ( if there's been a spelling mistake )
     *
     * @return string|boolean
     */
    public function getCorrectedQuery()
    {
        if (isset($this->spelling['anchor'])) {
            return $this->spelling['anchor'];
        } elseif (isset($this->spelling['correctedAnchor'])) {
            return $this->spelling['correctedAnchor'];
        }

        return false;
    }

    /**
     * Returns if there's been a spelling mistake and search was automatically
     * performed using corrected query
     *
     * @return boolean
     */
    public function hasCorrectedResults()
    {
        return isset($this->spelling['type']) && $this->spelling['type'] === 'SPELL_CORRECTED_RESULTS' ? true : false;
    }

    /**
     * Returns the raw original query ( if search results was corrected automatically )
     *
     * @return string|boolean
     */
    public function getRawOriginalQuery()
    {
        return isset($this->spelling['originalQuery']) ? $this->spelling['originalQuery'] : false;
    }

    /**
     * Returns the HTML formatted original query ( if search results was corrected automatically )
     *
     * @return string|boolean
     */
    public function getOriginalQuery()
    {
        return isset($this->spelling['originalAnchor']) ? $this->spelling['originalAnchor'] : false;
    }
}
