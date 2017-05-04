<?php

/**
 * @param DateTime $dateTime
 * @return string
 */
function formatDateTime($dateTime) {
    return $dateTime->format('d/m/y H:i');
}

/**
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function startsWith($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
}