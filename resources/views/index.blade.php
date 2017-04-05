    @include('errors')
    @include('msg')
    <label for="name">名称：</label>
    <input type="text" name="name" class="form-control" value="{{ $activity->name }}">

    <label for="name">描述：</label>
    <input type="text" name="details" class="form-control" value="{{ $activity->details }}">

    <ul class="users">
  <h2>选手</h2>
  @foreach ($players as $player)

      <a href="{{ route('playerDetail', $player->id )}}" class="username">{{ $player->name }}</a>
      {{$player->isMarking}}
  @endforeach

</ul>
