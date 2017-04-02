<form method="POST" action="login">
    {!! csrf_field() !!}

    <div>
        Account
        <input type="text" name="account" value="{{ old('account') }}">
    </div>

    <div>
        Password
        <input type="password" name="password" id="password">
    </div>

    <div>
        <input type="checkbox" name="remember"> Remember Me
    </div>

    <div>
        <button type="submit">Login</button>
    </div>
</form>
@include('errors')
@include('msg')
