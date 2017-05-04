<?php
require_once 'global.php';
require_once 'session.php';
/** @var string $siteName */
?>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/index.php"><?= $siteName ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <!-- Apply class 'active' for active links -->
                <li><a href="/index.php">Home</a></li>
                <?php if ($loggedIn): ?>
                    <li><a href="/events.php">Events</a></li>
                    <li><a href="/bookings.php">Bookings</a></li>
                <?php endif; ?>
                <?php if ($admin): ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Administration <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="/admin/modify_event.php">Create Event</a></li>
                            <li><a href="/admin/users.php">View Users</a></li>
                            <li><a href="/admin/bookings.php">Manage Bookings</a></li>
                            <!-- <li role="separator" class="divider"></li>
                            <li class="dropdown-header">Nav header</li> -->
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if ($loggedIn): ?>
                    <li><a href="/edit_profile.php"><?= "$firstName $lastName" ?></a></li>
                    <li><a href="/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="/register.php">Register</a></li>
                    <li><a href="/login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>