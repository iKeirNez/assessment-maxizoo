<?php

$hasSubmitted = isset($_POST['submit']);
$formErrorMap = array();

/**
 * @param String $inputId
 * @return mixed
 */
function getInputError($inputId) {
    global $formErrorMap;
    return array_key_exists($inputId, $formErrorMap) ? $formErrorMap[$inputId] : null;
}

/**
 * @param String $inputId
 * @return bool
 */
function hasInputError($inputId) {
    global $hasSubmitted;
    $inputError = getInputError($inputId);
    return $hasSubmitted && !empty($inputError) && $inputError != null;
}