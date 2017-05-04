<?php
require_once '../includes/database.php';
require_once '../includes/session.php';
require_once '../includes/form_input.php';

/** @var array $formErrorMap */

/**
 * Parses a string from the Bootstrap DateTime picker into a DateTime object.
 *
 * @param string $pickerString
 * @return DateTime $dateTime
 */
function parseDateTime($pickerString) {
    return DateTime::createFromFormat('d/m/Y g:i a', $pickerString);
}

$editExisting = isset($_GET['id']);
$id = $editExisting ? $_GET['id'] : null;
$delete = $editExisting && isset($_GET['delete']) && boolval($_GET['delete']);
// confirm is done via post to prevent malicious links forcing an event to be deleted
$confirm = isset($_POST['confirm']) && boolval($_POST['confirm']);

// pre-fill values (for fields below)
$pfName = isset($_POST['name']) ? $_POST['name'] : '';
$pfDateTime = isset($_POST['date_time']) ? parseDateTime($_POST['date_time']) : null;
$pfVenue = isset($_POST['venue']) ? $_POST['venue'] : '';
$pfCost = isset($_POST['cost']) ? $_POST['cost'] : '';
$pfLocked = isset($_POST['locked']) ? boolval($_POST['locked']) : false;

$errorMsg = null;

if ($admin) {
    if ($delete) {
        if ($confirm) {
            $statement = $mysqli->prepare('DELETE FROM `events` WHERE id = ?;');
            $statement->bind_param('i', $id);

            if ($statement->execute()) {
                header('Location: /events.php?deleted=1');
                die();
            } else {
                $errorMsg = 'Failed to delete event.';
            }
        }
    } else if ($hasSubmitted) {
        $dateTime = parseDateTime($_POST['date_time']);
        $cost = floatval($_POST['cost']);
        $locked = isset($_POST['locked']) ? boolval($_POST['locked']) : false;

        foreach ($_POST as $key => $value) {
            if ($key == 'cost' && !is_numeric($value)) {
                $formErrorMap[$key] = 'Not a valid cost.';
            } else if (empty($value)) {
                $formErrorMap[$key] = 'Please enter a value.';
            } else if ($key == 'date_time' && $dateTime == null) {
                $formErrorMap[$key] = 'Invalid date/time.';
            } else if ($key == 'locked' && isset($_POST['locked']) && !filter_var($_POST['locked'], FILTER_VALIDATE_BOOLEAN)) {
                $errorMsg = 'Error parsing \'locked\' value.';
            }
        }

        if (count($formErrorMap) == 0) {
            $lockedInt = intval($locked);
            $dateTimeString = $dateTime->format('Y-m-d H:i:s');

            if ($editExisting) {
                $statement = $mysqli->prepare('UPDATE events SET `name` = ?, `time` = ?, venue = ?, cost = ?, locked = ? WHERE id = ?;');
                $statement->bind_param('sssdii', $_POST['name'], $dateTimeString, $_POST['venue'], $cost, $lockedInt, $id);
                $statement->execute();
            } else {
                $statement = $mysqli->prepare('INSERT INTO events (`name`, `time`, venue, cost, locked) VALUES (?, ?, ?, ?, ?);');
                $statement->bind_param('sssdi', $_POST['name'], $dateTimeString, $_POST['venue'], $cost, $lockedInt);
                $statement->execute();
                $id = $mysqli->insert_id;
                $editExisting = true;
            }

            header("Location: /view_event.php?id=$id&edited=1");
            die();
        }
    } else if ($editExisting) {
        $statement = $mysqli->prepare('SELECT `name`, `time`, venue, cost, locked FROM events WHERE id = ?;');
        $statement->bind_param('i', $id);
        $statement->bind_result($pfName, $pfDateTime, $pfVenue, $pfCost, $pfLocked);
        $statement->execute();

        if ($statement->fetch()) {
            // turn into DateTime object instead of String
            $pfDateTime = new DateTime($pfDateTime);
        } else {
            $errorMsg = "Event #$id doesn't exist, this event will be created instead of updated.";
            $editExisting = false;
        }
    }

    $pageName = ($editExisting ? ($delete ? 'Delete' : 'Modify') : 'Create') . ' Event';
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
    <div class="row">
        <?php if (!$admin): ?>
            <div class="alert alert-danger">You do not have administrative permission.</div>
        <?php else: ?>
            <div class="col-sm-6 col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong><?= $pageName ?></strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-10 col-md-offset-1 ">
                                <?php if ($errorMsg != null): ?>
                                    <div class="alert alert-danger"><?= $errorMsg ?></div>
                                <?php endif; ?>

                                <?php if (!$delete): ?>
                                    <form role="form" action="?<?= $editExisting && !empty($id) ? "id=$id" : '' ?>" method="post">
                                        <fieldset>
                                            <div class="form-group <?= hasInputError('name') ? 'has-error' : '' ?>">
                                                <?php if (hasInputError('name')): ?>
                                                    <span class="help-block"><?= getInputError('name') ?></span>
                                                <?php endif; ?>
                                                <div class="input-group">
												<span class="input-group-addon">
													<i class="glyphicon glyphicon-tag"></i>
												</span>
                                                    <input class="form-control" placeholder="Name" name="name" type="text" value="<?= $pfName ?>" <?= !$editExisting ? 'autofocus' : '' ?>>
                                                </div>
                                            </div>
                                            <div class="form-group <?= hasInputError('date_time') ? 'has-error' : '' ?>">
                                                <?php if (hasInputError('date_time')): ?>
                                                    <span class="help-block"><?= getInputError('date_time') ?></span>
                                                <?php endif; ?>
                                                <div class='input-group'>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                                    <input type='text' placeholder="Date" class="form-control" name="date_time" id="datetimepicker" />
                                                </div>
                                            </div>
                                            <div class="form-group <?= hasInputError('venue') ? 'has-error' : '' ?>">
                                                <?php if (hasInputError('venue')): ?>
                                                    <span class="help-block"><?= getInputError('venue') ?></span>
                                                <?php endif; ?>
                                                <div class="input-group">
												<span class="input-group-addon">
													<i class="glyphicon glyphicon-globe"></i>
												</span>
                                                    <input class="form-control" placeholder="Venue" name="venue" type="text" value="<?= $pfVenue ?>">
                                                </div>
                                            </div>
                                            <div class="form-group <?= hasInputError('cost') ? 'has-error' : '' ?>">
                                                <?php if (hasInputError('cost')): ?>
                                                    <span class="help-block"><?= getInputError('cost') ?></span>
                                                <?php endif; ?>
                                                <div class="input-group">
												<span class="input-group-addon">
													<i class="glyphicon glyphicon-gbp"></i>
												</span>
                                                    <input class="form-control" placeholder="Cost" name="cost" type="text" value="<?= $pfCost ?>">
                                                </div>
                                            </div>
                                            <div class="checkbox">
                                                <label><input name="locked" type="checkbox" value="true" <?= $pfLocked ? 'checked' : ''?>>Locked</label>
                                            </div>
                                            <div class="form-group">
                                                <input type="submit" name="submit" class="btn btn-lg btn-primary btn-block" value="Save">
                                            </div>
                                        </fieldset>
                                    </form>
                                <?php else: ?>
                                    <form role="form" action="?id=<?= $id ?>&delete=1" method="post">
                                        <fieldset>
                                            <div class="form-group text-center">
                                                <input type="hidden" name="confirm" value="1">
                                                <input type="submit" name="submit" value="Delete Event" class="btn btn-danger">
                                                <a class="btn btn-default" href="/view_event.php?id=<?= $id ?>">Keep Event</a>
                                            </div>
                                        </fieldset>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php' ?>
<script type="text/javascript">
    $(function () {
        $('#datetimepicker').datetimepicker({
            format: 'DD/MM/YYYY hh:mm a',
            defaultDate: <?= $pfDateTime != null ? "moment('" . $pfDateTime->format('Y-m-d H:i:s') . "')" : 'null' ?>,
            sideBySide: true
        });
    });
</script>
</body>
</html>
