    @include('logout')

    <label for="name">名称：</label>
   {{ $activity->name }}<br>

    <label for="name">描述：</label>
    {{ $activity->details }}<br>
    <a href="{{ route('rankAll', $activity->id )}}" >查看排名</a>
  <h2>选手</h2>
  @foreach ($players as $player)

      <a href="{{ route('playerDetail', $player->id )}}" class="username">{{ $player->name }}</a>
      {{$player->isMarking}}
  @endforeach
  @include('errors')
  @include('msg')
