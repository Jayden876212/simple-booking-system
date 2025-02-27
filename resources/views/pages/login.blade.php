@extends("layouts.default")

@section("content")

@section("title", $page_title)

<article class="container-fluid">
    <section class="col-md-5 mx-auto">
        <form class="card" action="" method="POST">
            @csrf

            <h1 class="card-header text-center">Login</h1>
            <div class="card-body">
                <div class="mb-3">
                    <label for="usernameInput" class="form-label">Username:</label>
                    <input type="text" class="form-control" id="usernameInput" name="username" required minlength="1" maxlength="">
                    @error("username")
                        <div class="alert alert-danger m-2" role="alert">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="passwordInput" class="form-label">Password:</label>
                    <input type="password" class="form-control" id="passwordInput" name="password" required minlength="1" maxlength="">
                    @error("password")
                        <div class="alert alert-danger m-2" role="alert">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="card-footer container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <a href="{{ url("account/register") }}"><button type="button" class="btn btn-primary text-center w-100">Sign Up</button></a>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success text-center w-100" name="login" value="true">Login</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</article>

@stop