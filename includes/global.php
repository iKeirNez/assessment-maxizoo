<?php
$siteName = 'MaxiZoo';

$userColumns = array('username' => 20, 'first_name' => 20, 'last_name' => 20,
    'password' => 25, 'interest_area' => 20);

// key = db value (shouldn't change), value = display name
$userInterests = array('music' => 'Music', 'computing' => 'Computing', 'cars' => 'Cars');

function getUserInterest($interestKey) {
    global $userInterests;
    return array_key_exists($interestKey, $userInterests) ? $userInterests[$interestKey] : $interestKey;
}
