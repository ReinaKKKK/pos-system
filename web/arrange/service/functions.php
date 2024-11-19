<?php

/**
 * Escapes special characters in a string for use in HTML.
 *
 * This function is a wrapper for htmlspecialchars to simplify usage.
 *
 * @param string $val The string to be escaped.
 *
 * @return string The escaped string.
 */
function h($val)
{
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}
