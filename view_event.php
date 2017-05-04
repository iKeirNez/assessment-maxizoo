<?php
require_once 'includes/database.php';
require_once 'includes/session.php';

$id = null;
$name = '';
/** @var DateTime $dateTime */
$dateTime = null;
$venue = '';
$cost = '';
$locked = false;

$errorMsg = null;
$successMsg = null;

if ($loggedIn) {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        if (isset($_GET['edited']) && boolval($_GET['edited'])) {
            $successMsg = 'Successfully updated event.';
        }

        $statement = $mysqli->prepare('SELECT `name`, `time`, venue, cost, locked FROM `events` WHERE id = ?;');
        $statement->bind_param('i', $id);
        $statement->bind_result($name, $dateTime, $venue, $cost, $locked);
        $statement->execute();

        if ($statement->fetch()) {
            $pageName = $name;
            // convert date/time from string to DateTime object
            $dateTime = new DateTime($dateTime);
        } else {
            $pageName = 'Error';
            $errorMsg = 'Event not found.';
        }

        $statement->close();
    } else {
        $pageName = 'Error';
        $errorMsg = 'No event specified.';
    }
} else {
    $pageName = 'Access Denied';
    $errorMsg = 'You must be logged in to view this.';
}

?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php'); ?>

<body>
<?php include('includes/menu.php'); ?>
<div class="container">
    <?php if ($errorMsg != null): ?>
        <div class="alert alert-danger"><?= $errorMsg ?></div>
    <?php else: ?>
        <h2><?= $name ?> <?= $locked ? '<small><i class="fa fa-lock"></i> Locked</small>' : '' ?></h2>
        <?php if ($successMsg != null): ?>
            <div class="alert alert-success"><?= $successMsg ?></div>
        <?php endif; ?>
        <div class="row spacer">
            <div class="col-md-4">
                <section class="event-details">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Details</h3>
                        </div>
                        <div class="panel-body">
                            <ul class="list-unstyled">
                                <li><i class="fa fa-fw fa-building-o"></i><span class="sr-only">Venue: </span><?= $venue ?></li>
                                <li><i class="fa fa-fw fa-calendar"></i><span class="sr-only">Date: </span><?= $dateTime->format('jS F Y'); ?></li>
                                <li><i class="fa fa-fw fa-clock-o"></i><span class="sr-only">Time: </span><?= $dateTime->format('g:ia'); ?></li>
                                <li><i class="fa fa-fw fa-money"></i><span class="sr-only">Cost: </span>£<?= $cost ?></li>
                                <li><i class="fa fa-fw fa-lock"></i><span class="sr-only">Status: </span>Bookings <?= $locked ? 'Closed' : 'Open' ?></li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
            <?php if ($admin): ?>
                <div class="col-md-3 pull-right">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">Administration</h3>
                        </div>
                        <div class="panel-body text-center">
                            <a class="btn btn-default" href="admin/modify_event.php?id=<?= $id ?>" role="button">Modify Event</a>
                            <a class="btn btn-danger" href="admin/modify_event.php?id=<?= $id ?>&delete=1" role="button">Delete Event</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-info">
                    <div class="panel-heading">Booking</div>
                    <div class="panel-body text-center">
                        <?php
                        $statement = $mysqli->prepare('SELECT eventId FROM bookings WHERE userId = ? AND eventId = ?;');
                        $statement->bind_param('ii', $userId, $id);
                        $statement->execute();
                        ?>

                        <?php if ($statement->fetch()): ?>
                            <div class="alert alert-success">You're already booked in to this event.</div>
                        <?php elseif ($locked): ?>
                            <div class="alert alert-danger">This event is locked, no more bookings are being taken.</div>
                        <?php else: ?>
                            <a class="btn btn-success" href="book.php?id=<?= $id ?>" role="button">Book Now</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php' ?>
</body>
</html>