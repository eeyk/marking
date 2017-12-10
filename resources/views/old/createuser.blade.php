<form method="POST" action="{{route('createUser')}}">
    {!! csrf_field() !!}

    <div>
        Name
        <input type="text" name="name" value="{{ old('name') }}">
    </div>

    <div>
        Account
        <input type="text" name="account" value="{{ old('account') }}">
    </div>

    <div>
        Password
        <input type="password" name="password">
    </div>

    <div>
        Confirm Password
        <input type="password" name="password_confirmation">
    </div>

    <div>
        权重
        <input type="float" name="weight" value="{{ old('weight') }}">
    </div>
    <div>
        描述
        <input type="text" name="details" value="{{ old('details') }}">
    </div>

    <input type="hidden" name="id" value="1">

    <div>
        <button type="submit">Register</button>
    </div>
</form>
@include('errors')
@include('msg')
