<?php
require_once 'includes/global.php';
require_once 'includes/database.php';
require_once 'includes/session.php';
require_once 'includes/form_input.php';

/** @var array $userInterests */
/** @var array $userColumns */
/** @var array $formErrorMap */

$pageName = 'Register';
$errorMsg = null;

if (!$loggedIn) {
    if ($hasSubmitted) {
        foreach ($_POST as $key => $value) {
            if (empty($value)) {
                $formErrorMap[$key] = 'Please enter a value.';
            } else if ($key == 'interest_area' && !array_key_exists($value, $userInterests)) {
                $formErrorMap[$key] = 'Invalid selection.';
            } else if (array_key_exists($key, $userColumns) && strlen($value) > $userColumns[$key]) {
                $formErrorMap[$key] = "Must be $userColumns[$key] characters or less.";
            }
        }

        $inputValid = count($formErrorMap) == 0;

        if ($inputValid) {
            $statement = $mysqli->prepare('INSERT INTO users (username, first_name, last_name, `password`, interest_area) VALUES (?, ?, ?, ?, ?);');
            $statement->bind_param('sssss', $_POST['username'], $_POST['first_name'], $_POST['last_name'], $_POST['password'], $_POST['interest_area']);
            $statement->execute();
            header('Location: login.php');
            die();
        }
    }
} else {
    $errorMsg = 'You are already logged in.';
}

?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php'); ?>

<body>
<?php include("includes/menu.php"); ?>

<div class="container">
    <?php if ($errorMsg != null): ?>
        <div class="alert alert-danger"><?= $errorMsg ?></div>
    <?php else: ?>
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Register a new account</strong>
                </div>
                <div class="panel-body">
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
                                            <input class="form-control" placeholder="First Name" name="first_name" type="text" value="<?= $_POST['first_name'] ?>" autofocus required>
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
                                            <input class="form-control" placeholder="Last Name" name="last_name" type="text" value="<?= $_POST['last_name'] ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group <?= hasInputError('username') ? 'has-error' : '' ?>">
                                        <?php if (hasInputError('username')): ?>
                                            <span class="help-block"><?= getInputError('username') ?></span>
                                        <?php endif; ?>
                                        <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-at"></i>
                                        </span>
                                            <input class="form-control" placeholder="Username" name="username" type="text" value="<?= $_POST['username'] ?>" required>
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
                                                <option value="<?= $key ?>" <?= $key == $_POST['interest_area'] ? 'selected' : '' ?>><?= $value ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <input type="submit" name="submit" class="btn btn-lg btn-primary btn-block" value="Register">
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
