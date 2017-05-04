<?php
require_once 'includes/global.php';
require_once 'includes/database.php';
require_once 'includes/session.php';
require_once 'includes/form_input.php';

/** @var array $userInterests */
/** @var integer $userId */
/** @var array $formErrorMap */
/** @var array $userColumns */
/** @var string $firstName */
/** @var string $lastName */
/** @var string $interestArea */

$pfFirstName = null;
$pfLastName = null;
$pfInterestArea = null;

$successMsg = null;
$errorMsg = null;

if ($loggedIn) {
    if ($hasSubmitted) {
        $pfFirstName = isset($_POST['first_name']) ? $_POST['first_name'] : '';
        $pfLastName = isset($_POST['last_name']) ? $_POST['last_name'] : '';
        $pfInterestArea = isset($_POST['interest_area']) ? $_POST['interest_area'] : '';

        foreach ($_POST as $key => $value) {
            if (empty($value) && $key != 'password') { // password can be empty if user wants to leave it unchanged
                $formErrorMap[$key] = 'Please enter a value.';
            } else if ($key == 'interest_area' && !array_key_exists($value, $userInterests)) {
                $formErrorMap[$key] = 'Invalid selection.';
            } else if (array_key_exists($key, $userColumns) && strlen($value) > $userColumns[$key]) {
                $formErrorMap[$key] = "Must be $userColumns[$key] characters or less.";
            }
        }

        $inputValid = count($formErrorMap) == 0;

        if ($inputValid) {
            if (isset($_POST['password']) && !empty($_POST['password'])) {
                $statement = $mysqli->prepare('UPDATE users SET first_name = ?, last_name = ?, `password` = ?, interest_area = ? WHERE id = ?;');
                $statement->bind_param('ssssi', $_POST['first_name'], $_POST['last_name'], $_POST['password'], $_POST['interest_area'], $userId);
            } else {
                $statement = $mysqli->prepare('UPDATE users SET first_name = ?, last_name = ?, interest_area = ? WHERE id = ?;');
                $statement->bind_param('sssi', $_POST['first_name'], $_POST['last_name'], $_POST['interest_area'], $userId);
            }

            if ($statement->execute() && loadSession($userId)) {
                $successMsg = 'Successfully updated user profile.';
            } else {
                $errorMsg = 'Error whilst updating user profile.';
            }
        }
    } else {
        $pfFirstName = $firstName;
        $pfLastName = $lastName;
        $pfInterestArea = $interestArea;
    }

    $pageName = 'Edit Profile';
} else {
    $errorMsg = 'You must be logged in to view this.';
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
    <?php if ($errorMsg != null): ?>
        <div class="alert alert-danger"><?= $errorMsg ?></div>
    <?php else: ?>
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Edit profile</strong>
                </div>
                <div class="panel-body">
                    <?php if ($successMsg != null): ?>
                        <div class="alert alert-success"><?= $successMsg ?></div>
                    <?php endif; ?>
                    <form role="form" action="" method="post">
                        <fieldset>
                            <div class="row">
                                <div class="col-sm-12 col-md-10  col-md-offset-1">
                                    <div class="form-group <?= hasInputError('first_name') ? 'has-error' : '' ?>">
                                        <?php if (hasInputError('first_name')): ?>
                                            <span class="help-block"><?= getInputError('first_name') ?></span>
                                        <?php endif; ?>
                                        <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                        </span>
                                            <input class="form-control" placeholder="First Name" name="first_name" type="text" value="<?= $pfFirstName ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group <?= hasInputError('last_name') ? 'has-error' : '' ?>">
                                        <?php if (hasInputError('last_name')): ?>
                                            <span class="help-block"><?= getInputError('last_name') ?></span>
                                        <?php endif; ?>
                                        <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                        </span>
                                            <input class="form-control" placeholder="Last Name" name="last_name" type="text" value="<?= $pfLastName ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group <?= hasInputError('password') ? 'has-error' : '' ?>">
                                        <?php if (hasInputError('password')): ?>
                                            <span class="help-block"><?= getInputError('password') ?></span>
                                        <?php endif; ?>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-key"></i>
                                            </div>
                                            <input class="form-control" placeholder="Password" name="password" type="password" required>
                                        </div>
                                    </div>

                                    <div class="form-group <?= hasInputError('interest_area') ? 'has-error' : '' ?>">
                                        <?php if (hasInputError('interest_area')): ?>
                                            <span class="help-block"><?= getInputError('interest_area') ?></span>
                                        <?php endif; ?>

                                        <select class="form-control" name="interest_area">
                                            <option value="default" selected>Please select interest area</option>

                                            <?php foreach ($userInterests as $key => $value): ?>
                                                <option value="<?= $key ?>" <?= $key == $pfInterestArea ? 'selected' : '' ?>><?= $value ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <input type="submit" name="submit" class="btn btn-lg btn-primary btn-block" value="Save">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php' ?>
</body>
</html>
