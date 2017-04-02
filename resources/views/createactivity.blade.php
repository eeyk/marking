<form method="POST" action="{{route('createActivity')}}">
    {!! csrf_field() !!}

    <div>
        Name
        <input type="text" name="name" value="{{ old('name') }}">
    </div>

    <div>
        描述
        <input type="text" name="details" value="{{ old('details') }}">
    </div>

    <div>
        <button type="submit">Register</button>
    </div>
</form>
@include('errors')
@include('msg')
