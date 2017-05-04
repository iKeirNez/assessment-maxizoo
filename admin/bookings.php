<?php
require_once '../includes/database.php';
require_once '../includes/session.php';
require_once '../includes/form_input.php';
require_once '../includes/util.php';

$successMsg = null;
$errorMsg = null;

if ($loggedIn) {
    $pageName = 'System Bookings';

    if ($hasSubmitted) {
        $deleteBookings = array();

        foreach ($_POST as $key => $value) {
            if (startsWith($key, 'del-')) {
                $parts = explode('-', substr_replace($key, '', 0, strlen('del-')));

                if (count($parts) == 2) {
                    // ensure both parts are integers to ensure/minimise sql injection possibilities
                    $userId = filter_var($parts[0], FILTER_VALIDATE_INT);
                    $eventId = filter_var($parts[1], FILTER_VALIDATE_INT);

                    // !== to prevent automatic type conversion
                    if ($userId !== false || $eventId !== false) {
                        array_push($deleteBookings, "(userId = $userId AND eventId = $eventId)");
                    }
                }
            }
        }

        $statement = $mysqli->prepare('DELETE FROM bookings WHERE ' . implode(' OR ', $deleteBookings) . ';');

        if ($statement->execute()) {
            $successMsg = 'Successfully deleted ' . count($deleteBookings) . ' booking(s).';
        } else {
            $errorMsg = 'Error whilst deleting bookings.';
        }
    }
} else {
    $pageName = 'Access Denied';
}

if ($errorMsg != null) {
    $pageName = 'Error';
}

?>
<!DOCTYPE html>
<html lang="en">
<?php include('../includes/head.php'); ?>

<body>
<?php include('../includes/menu.php'); ?>
<div class="container">
    <h2>System Bookings</h2>

    <?php if ($errorMsg != null): ?>
        <div class="alert alert-danger"><?= $errorMsg ?></div>
    <?php elseif ($successMsg != null): ?>
        <div class="alert alert-success"><?= $successMsg ?></div>
    <?php endif; ?>

    <?php
    $statement = $mysqli->prepare('SELECT `events`.id, users.id, `events`.name, users.first_name, users.last_name, users.username
FROM bookings, users, `events`
WHERE bookings.userId = users.id
      AND bookings.eventId = `events`.id;');
    $statement->bind_result($eventId, $userId, $eventName, $firstName, $lastName, $username);
    $statement->execute();
    $statement->store_result();
    ?>

    <?php if ($statement->num_rows > 0): ?>
        <form role="form" action="" method="post">
            <fieldset>
                <input class="btn btn-danger" type="submit" name="submit" value="Delete Selected">
                <table class="table table-striped spacer">
                    <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Customer Name</th>
                        <th>Customer Username</th>
                        <th>Select</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($statement->fetch()): ?>
                        <tr>
                            <td><?= $eventName ?></td>
                            <td><?= "$firstName $lastName" ?></td>
                            <td><?= $username ?></td>
                            <td>
                                <input name="del-<?= $userId ?>-<?= $eventId ?>" value="" type="checkbox">
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </fieldset>
        </form>
    <?php else: ?>
        <p>No system bookings found.</p>
    <?php endif;?>
</div>
<?php include '../includes/footer.php' ?>
</body>
</html>
