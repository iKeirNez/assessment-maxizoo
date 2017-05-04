<?php

require_once 'includes/database.php';
require_once 'includes/session.php';
require_once 'includes/form_input.php';

/** @var array $formErrorMap */

$pageName = 'Login';
$error = null;

$pfUsername = isset($_POST['username']) ? $_POST['username'] : '';

if (!$loggedIn) {
    if ($hasSubmitted) {
        foreach ($_POST as $key => $value) {
            if (empty($value)) {
                $formErrorMap[$key] = 'Please enter a value.';
            }
        }

        $inputValid = count($formErrorMap) == 0;

        if ($inputValid) {
            $statement = $mysqli->prepare('SELECT id FROM users WHERE username = ? AND `password` = ?;');
            $statement->bind_param('ss', $_POST['username'], $_POST['password']);
            $statement->bind_result($userId);
            $statement->execute();

            if ($statement->fetch()) {
                $_SESSION['userId'] = $userId;
                echo 'Logged in successfully, redirecting...';
                header('Location: index.php');
                die();
            } else {
                $error = 'Invalid credentials.';
            }
        }
    }
} else {
    echo 'Already logged in, redirecting...';
    header('Location: index.php');
    die();
}

?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php') ?>

<body>
<?php include('includes/menu.php'); ?>

<div class="container" style="margin-top:40px">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Sign in to continue</strong>
                </div>
                <div class="panel-body">
                    <form role="form" action="" method="post">
                        <fieldset>
                            <div class="row">
                                <div class="col-sm-12 col-md-10  col-md-offset-1 ">
                                    <?php if ($error != null): ?>
                                        <div class="alert alert-danger"><?= $error ?></div>
                                    <?php endif; ?>
                                    <div class="form-group <?= hasInputError('username') ? 'has-error' : '' ?>">
                                        <?php if (hasInputError('username')): ?>
                                            <span class="help-block"><?= getInputError('username') ?></span>
                                        <?php endif; ?>
                                        <div class="input-group">
												<span class="input-group-addon">
													<i class="glyphicon glyphicon-user"></i>
												</span>
                                            <input class="form-control" placeholder="Username" name="username" type="text" value="<?= $pfUsername ?>" autofocus>
                                        </div>
                                    </div>
                                    <div class="form-group <?= hasInputError('password') ? 'has-error' : '' ?>">
                                        <?php if (hasInputError('password')): ?>
                                            <span class="help-block"><?= getInputError('password') ?></span>
                                        <?php endif; ?>
                                        <div class="input-group">
												<span class="input-group-addon">
													<i class="glyphicon glyphicon-lock"></i>
												</span>
                                            <input class="form-control" placeholder="Password" name="password" type="password" value="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" name="submit" class="btn btn-lg btn-primary btn-block" value="Sign in">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="panel-footer ">
                    Don't have an account! <a href="register.php"> Sign Up Here </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php' ?>
</body>
