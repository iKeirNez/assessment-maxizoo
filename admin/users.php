<?php
require_once '../includes/global.php';
require_once '../includes/database.php';
require_once '../includes/session.php';
require_once '../includes/form_input.php';

/** @var array $formErrorMap */

if ($admin) {
    $pageName = 'Users';
} else {
    $pageName = 'Access Denied';
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('../includes/head.php'); ?>

<body>
<?php include('../includes/menu.php'); ?>
<div class="container">
    <?php if ($admin): ?>
        <?php
        $statement = $mysqli->prepare('SELECT id, username, first_name, last_name, interest_area, admin FROM users ORDER BY last_name DESC;');
        $statement->bind_result($id, $username, $firstName, $lastName, $interestArea, $admin);
        $statement->execute();
        ?>
        <h2>Users <small>sorted by last name descending</small></h2>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Username</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Interest Area</th>
                <th>Administrator</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($statement->fetch()): ?>
                <tr>
                    <td><?= $username ?></td>
                    <td><?= $firstName ?></td>
                    <td><?= $lastName ?></td>
                    <td><?= getUserInterest($interestArea) ?></td>
                    <td><?= $admin ? 'Yes' : 'No' ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-danger">You do not have administrative permission.</div>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php' ?>
</body>
</html>
