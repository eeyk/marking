
      <h5>更新用户资料</h5>



        @include('errors')
        @include('msg')

            <form method="POST" action="{{ route('updatePlayer', $player->id )}}" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <label for="name">名称：</label>
                 <input type="text" name="name" class="form-control" value="{{ $player->name }}">

                 <label for="name">描述：</label>
                 <input type="text" name="details" class="form-control" value="{{ $player->details }}">
                  <h1>上传图片</h1>
                <input type="file" name="file"><br/><br/>
                <button type="submit">上传图片</button>
            </form>
