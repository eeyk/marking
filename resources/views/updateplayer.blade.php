
      <h5>更新用户资料</h5>



        @include('errors')
        @include('msg')

        <form method="POST" action="{{ route('updatePlayer', $player->id )}}">
            {{ method_field('PATCH') }}
            {{ csrf_field() }}

             <label for="name">名称：</label>
              <input type="text" name="name" class="form-control" value="{{ $player->name }}">

              <label for="name">描述：</label>
              <input type="text" name="details" class="form-control" value="{{ $player->details }}">


            <button type="submit" class="btn btn-primary">更新</button>
        </form>
