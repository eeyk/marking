    @include('errors')
    @include('msg')
    <label for="name">名称：</label>
    <input type="text" name="name" class="form-control" value="{{ $activity->name }}">

    <label for="name">描述：</label>
    <input type="text" name="details" class="form-control" value="{{ $activity->details }}">

    <ul class="users">
      <h2>活动裁判</h2>
  @foreach ($users as $user)
    <li>
      <a href="{{ route('updateUser', $user->id )}}" class="username">{{ $user->name }}</a>
    </li>
  @endforeach
  <h2>选手</h2>
  @foreach ($players as $player)
    <li>
      <a href="{{ route('updatePlayer', $player->id )}}" class="username">{{ $player->name }}</a>
    </li>
  @endforeach
<a href="{{ route('rankAll', $activity->id )}}" >查看排名</a>
</ul>

<h1>添加裁判</h1>
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
        <input type="hidden" name="id" value="{{$activity->id}}">
        <div>
            <button type="submit">Register</button>
        </div>
    </form>
