<label for="name">名称：</label>
{{ $activity->name }}<br>

<label for="name">描述：</label>
{{ $activity->details }}<br>
<ul class="users">
<h2>选手</h2>
@foreach ($playerRank as $player)

  {{ $player->name }}
  {{$player->score}}<br>

@endforeach

</ul>
