@extends("layouts.default")

@section("content")

@section("title", "Sign Up")

<article class="container-fluid">
    <section class="col-md-5 mx-auto">
        <form class="card" action="" method="POST">
            @csrf

            <h1 class="card-header text-center">Sign Up</h1>
            <div class="card-body">
                <div class="mb-3">
                    <label for="usernameInput" class="form-label">Username:</label>
                    <input type="text" class="form-control" id="usernameInput" name="username" required minlength="1" maxlength="">
                </div>
                <div class="mb-3">
                    <label for="passwordInput" class="form-label">Password:</label>
                    <input type="password" class="form-control" id="passwordInput" name="password" required minlength="1" maxlength="">
                </div>
            </div>
            <div class="card-footer container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <a href=""><button type="button" class="btn btn-primary text-center w-100">Log In</button></a>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success w-100" name="sign_up" value="true">Sign Up</button>
                    </div>
                </div>
            </div>

            @error("username")
                <div class="alert alert-danger">
                    {{ $message }}
                </div>
            @enderror
        </form>
    </section>
</article>

@stop