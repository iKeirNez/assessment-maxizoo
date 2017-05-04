<?php
require_once 'includes/database.php';
require_once 'includes/session.php';
require_once 'includes/form_input.php';

$eventId = null;
$eventName = null;

$errorMsg = null;

if ($loggedIn) {
    if (isset($_GET['id'])) {
        $eventId = $_GET['id'];

        $statement = $mysqli->prepare('SELECT `name`, locked FROM `events`
WHERE id = ?;');
        $statement->bind_param('i', $eventId);
        $statement->bind_result($eventName, $locked);
        $statement->execute();

        if ($statement->fetch()) {
            $statement->close();

            if (!$locked) {
                if ($hasSubmitted) {
                    $statement = $mysqli->prepare('INSERT INTO bookings (userId, eventId) VALUES (?, ?);');
                    $statement->bind_param('ii', $userId, $eventId);

                    if ($statement->execute()) {
                        header("Location: bookings.php?booked=$eventId");
                        die();
                    } else {
                        $errorMsg = 'Error whilst booking that event.';
                    }
                }

                $pageName = "Booking $eventName";
            } else {
                $errorMsg = 'That event is locked.';
            }
        } else {
            $errorMsg = 'An error occurred, perhaps that event doesn\'t exist or you\'ve already booked it?';
        }
    } else {
        $errorMsg = 'No event specified.';
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
<?php include('includes/head.php'); ?>

<body>
<?php include('includes/menu.php'); ?>
<div class="container">
    <?php if ($errorMsg == null): ?>
        <div class="col-md-6 col-md-offset-3">
            <h3 class="text-center"><?= $eventName ?></h3>
            <div class="panel panel-primary spacer">
                <div class="panel-heading">Please confirm your booking</div>
                <div class="panel-body">
                    <form role="form" action="" method="post">
                        <fieldset>
                            <div class="form-group text-center">
                                <input type="submit" name="submit" value="Yes, book me in" class="btn btn-success">
                                <a class="btn btn-danger" href="view_event.php?id=<?= $eventId ?>">No, get me out</a>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger"><?= $errorMsg ?></div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php' ?>
</body>
</html>
