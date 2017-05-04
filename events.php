<?php
require_once 'includes/database.php';
require_once 'includes/session.php';
require_once 'includes/util.php';

$successMsg = null;

if ($loggedIn) {
    $pageName = 'Events';

    if (isset($_GET['deleted']) && boolval($_GET['deleted'])) {
        $successMsg = 'Successfully deleted event.';
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
        <h2>Events <small>sorted by name ascending</small></h2>

        <?php
        $statement = $mysqli->prepare('SELECT id, `name`, `time`, venue, cost, locked FROM events ORDER BY `name` ASC;');
        $statement->bind_result($id, $name, $isoDateTime, $venue, $cost, $locked);
        $statement->execute();
        $statement->store_result();
        ?>

        <?php if ($successMsg != null): ?>
            <div class="alert alert-success"><?= $successMsg ?></div>
        <?php endif; ?>

        <?php if ($statement->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Date/Time</th>
                    <th>Venue</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php while ($statement->fetch()): ?>
                    <tr>
                        <td>
                            <?php if ($locked): ?>
                                <i class="fa fa-fw fa-lock"></i>
                            <?php endif; ?>
                        </td>
                        <td><?= $name ?></td>
                        <td><?= formatDateTime(new DateTime($isoDateTime)) ?></td>
                        <td><?= $venue ?></td>
                        <td><a href="view_event.php?id=<?= $id ?>">View Event</a></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>There are currently no events.</p>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-danger">You must be logged in to view this.</div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php' ?>
</body>
</html>
