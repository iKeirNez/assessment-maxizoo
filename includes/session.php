<?php
require_once 'database.php';

session_start();

$loggedIn = isset($_SESSION['userId']);
$userId = null;
$username = null;
$firstName = null;
$lastName = null;
$interestArea = null;
$admin = false;

function loadSession($userId) {
    global $mysqli, $username, $firstName, $lastName, $interestArea, $admin;

    $statement = $mysqli->prepare('SELECT username, first_name, last_name, interest_area, admin FROM users WHERE id = ?;');

    try {
        $statement->bind_param('i', $userId);
        $statement->bind_result($username, $firstName, $lastName, $interestArea, $admin);

        if ($statement->execute()) {
            return $statement->fetch();
        } else {
            die('Error whilst fetching user data: ' . $statement->error);
        }
    } finally {
        $statement->close();
    }
}

if ($loggedIn) {
    $userId = $_SESSION['userId'];

    if (!loadSession($userId)) {
        // kick user out if no longer exists in db
        session_unset();
        session_destroy();
        header('Location: /login.php');
    }
}