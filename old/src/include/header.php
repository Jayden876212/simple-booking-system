<?php require_once "include/utils.php"; ?>

<header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?=HOST?><?=WORKING_DIRECTORY?>/home">Simple Booking System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?=PAGE_TITLE == "Home" ? "active" : "" ?>" <?=PAGE_TITLE == "Home" ? "aria-current='page'" : "" ?> href="<?=HOST?><?=WORKING_DIRECTORY?>/home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?=((PAGE_TITLE == "Sign Up") OR (PAGE_TITLE == "Log In")) ? "active" : "" ?>" <?=((PAGE_TITLE == "Sign Up") OR (PAGE_TITLE == "Log In")) ? "aria-current='page'" : "" ?> href="<?=HOST?><?=WORKING_DIRECTORY?>/account/login">Account</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?=PAGE_TITLE == "Bookings" ? "active" : "" ?>" <?=PAGE_TITLE == "Bookings" ? "aria-current='page'" : "" ?> href="<?=HOST?><?=WORKING_DIRECTORY?>/bookings">Bookings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?=PAGE_TITLE == "Orders" ? "active" : "" ?>" <?=PAGE_TITLE == "Orders" ? "aria-current='page'" : "" ?> href="<?=HOST?><?=WORKING_DIRECTORY?>/bookings/orders">Orders</a>
                </li>
            </ul>
            <div class="btn-group dropstart">
                <button
                    type="button"
                    class="btn 
                        <?php if (isset($session->username)): ?>btn-success<?php else: ?>btn-outline-secondary<?php endif ?>
                    dropdown-toggle"
                    data-bs-toggle="dropdown" aria-expanded="false"
                >
                    <?php if (isset($session->username)): ?>
                        <i class="fa-solid fa-user"></i>
                        <?=$session->username?>
                    <?php else: ?>
                        <i class="fa-regular fa-user"></i>
                        Not logged in
                    <?php endif ?>
                </button>
                <ul class="dropdown-menu">
                    <?php if (isset($session->username)): ?>
                        <li><a class="dropdown-item" href="<?=HOST?><?=WORKING_DIRECTORY?>/account/logout">Log Out</a></li>
                        <li><a class="dropdown-item" href="<?=HOST?><?=WORKING_DIRECTORY?>/account/delete">Delete Account</a></li>
                    <?php else: ?>
                        <li><a class="dropdown-item" href="<?=HOST?><?=WORKING_DIRECTORY?>/account/login">Log In</a></li>
                        <li><a class="dropdown-item" href="<?=HOST?><?=WORKING_DIRECTORY?>/account/register">Sign Up</a></li>
                    <?php endif ?>
                </ul>
            </div>
        </div>
    </nav>
    <?php if (isset($_REQUEST["alert"])): ?>
        <?php if (isset($_REQUEST["error"])): ?>
            <div class="alert alert-danger m-2" role="alert">
                <?=$_REQUEST["alert"]?>
            </div>
        <?php else: ?>
            <div class="alert alert-success m-2" role="alert">
                <?=$_REQUEST["alert"]?>
            </div>
        <?php endif ?>
    <?php endif ?>
</header>