<?php

namespace MirazMac\GoogleCSE\Element;

/**
 * RichSnippetElement, wraps rich snippet data into a dot notation accessible interface
 *
 * @package MirazMac\GoogleCSE
 * @since 0.1
 */
class RichSnippetElement
{
    /**
     * Array storage
     *
     * @var array
     */
    protected $storage = [];

    /**
     * Constructor
     *
     * @param array $storage the array to access dot notation from
     */
    public function __construct(array $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Fetch value from the array storage via dot notation
     *
     * @param  string $key     The array key, dot notation supported
     * @param  mixed $default  Fallback value
     * @return mixed
     *
     * @author Selvin Ortiz
     * @see https://selvinortiz.com/blog/traversing-arrays-using-dot-notation
     */
    public function get($key, $default = null)
    {
        $data = $this->storage;
        // @assert $key is a non-empty string
        // @assert $data is a loopable array
        // @otherwise return $default value
        if (!is_string($key) || empty($key) || !count($data)) {
            return $default;
        }

        // @assert $key contains a dot notated string
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);

            foreach ($keys as $innerKey) {
                // @assert $data[$innerKey] is available to continue
                // @otherwise return $default value
                if (!array_key_exists($innerKey, $data)) {
                    return $default;
                }

                $data = $data[$innerKey];
            }

            return $data;
        }

        // @fallback returning value of $key in $data or $default value
        return array_key_exists($key, $data) ? $data[$key] : $default;
    }
}
