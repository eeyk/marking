    @include('errors')
    @include('msg')
    <label for="name">名称：</label>
   {{ $activity->name }}<br>

    <label for="name">描述：</label>
    {{ $activity->details }}<br>
    <a href="{{ route('updateActivity', $activity->id ) }}">编辑活动<br></a>
    <form action="{{ route('deleteActivity') }}" method="POST">
      {{ csrf_field() }}
      {{ method_field('DELETE') }}
      <input type="hidden" name="id" value="{{$activity->id}}">
      <button class="btn btn-block btn-danger" type="submit" name="button">结束该活动</button>
    </form>
    <ul class="users">
      <h2>活动裁判</h2>
  @foreach ($users as $user)
    <li>
      {{ $user->name }}<a href="{{ route('updateUser', $user->id )}}" class="username">编辑裁判信息</a>
    </li>
  @endforeach
  <h2>选手</h2>
  @foreach ($players as $player)
    <li>
      {{ $player->name }}<a href="{{ route('updatePlayer', $player->id )}}" class="username">编辑选手信息</a>
    </li>
  @endforeach
<a href="{{ route('rankAll', $activity->id )}}" >查看排名</a>
</ul>

<h1>添加裁判</h1>
    <form method="POST" action="{{route('createUser')}}" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <input type="hidden" name="id" value="{{$activity->id}}">
        <input type="file" name="file"><br/><br/>
        <button type="submit">添加裁判</button>

    </form>
    <h1>添加选手</h1>
        <form method="POST" action="{{route('createPlayer')}}" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <input type="hidden" name="id" value="{{$activity->id}}">
            <input type="file" name="file"><br/><br/>
            <button type="submit">添加选手</button>
        </form>
