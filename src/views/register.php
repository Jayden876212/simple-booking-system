<?php
    const PAGE_TITLE = "Sign Up";
    include_once "include/base.php";
?>

<article class="container-fluid">
    <section class="col-md-5 mx-auto">
        <form class="card" action="" method="POST">
            <h1 class="card-header text-center"><?=PAGE_TITLE?></h1>
            <div class="card-body">
                <div class="mb-3">
                    <label for="usernameInput" class="form-label">Username:</label>
                    <input type="text" class="form-control" id="usernameInput" name="username" required minlength="1" maxlength="<?=Account::MAX_USERNAME_LENGTH?>">
                </div>
                <div class="mb-3">
                    <label for="passwordInput" class="form-label">Password:</label>
                    <input type="password" class="form-control" id="passwordInput" name="password" required minlength="1" maxlength="<?=Account::MAX_PASSWORD_LENGTH?>">
                </div>
            </div>
            <div class="card-footer container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <a href="account/login"><button type="button" class="btn btn-primary text-center w-100">Log In</button></a>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success w-100" name="sign_up" value="true">Sign Up</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</article>

<?php include_once "include/footer.php" ?>