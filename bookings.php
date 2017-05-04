<?php
require_once 'includes/database.php';
require_once 'includes/session.php';
require_once 'includes/util.php';

$successMsg = null;

if ($loggedIn) {
    $pageName = 'Bookings';

    if (isset($_GET['booked'])) {
        $eventId = $_GET['booked'];
        $statement = $mysqli->prepare('SELECT `name` FROM `events` WHERE id = ?;');
        $statement->bind_param('i', $eventId);
        $statement->bind_result($eventName);
        $statement->execute();

        if ($statement->fetch()) {
            $successMsg = "Congratulations $firstName, you're going to <strong>$eventName</strong>.";
        }

        $statement->close();
    }
} else {
    $pageName = 'Access Denied';
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php'); ?>

<body>
<?php include('includes/menu.php'); ?>
<div class="container">
    <?php if ($loggedIn): ?>
        <h2>Bookings</h2>

        <?php if ($successMsg != null): ?>
            <div class="alert alert-success"><?= $successMsg ?></div>
        <?php endif; ?>

        <?php
        $statement = $mysqli->prepare('SELECT id, `name`, `time`, venue FROM `events`, bookings
WHERE events.id = bookings.eventId
      AND bookings.userId = ?;');
        $statement->bind_param('i', $userId);
        $statement->bind_result($eventId, $name, $isoDateTime, $venue);
        $statement->execute();
        $statement->store_result();
        ?>

        <?php if ($statement->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Date/Time</th>
                    <th>Venue</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php while ($statement->fetch()): ?>
                    <tr>
                        <td><?= $name ?></td>
                        <td><?= formatDateTime(new DateTime($isoDateTime)) ?></td>
                        <td><?= $venue ?></td>
                        <td><a href="view_event.php?id=<?= $eventId ?>">View Event</a></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You are not booked into any events.</p>
        <?php endif; ?>

        <?php $statement->close(); ?>
    <?php else: ?>
        <div class="alert alert-danger">You must be logged in to view this.</div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php' ?>
</body>
</html>
