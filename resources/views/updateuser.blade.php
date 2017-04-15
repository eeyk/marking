
      <h5>更新用户资料</h5>



        @include('errors')
        @include('msg')
        <form method="POST" action="{{ route('updateUser', $user->id )}}">
            {{ method_field('PATCH') }}
            {{ csrf_field() }}

             <label for="name">名称：</label>
              <input type="text" name="name" class="form-control" value="{{ $user->name }}">

              <label for="name">权重：</label>
              <input type="text" name="weight" class="form-control" value="{{ $user->weight }}">

              <label for="name">描述：</label>
              <input type="text" name="details" class="form-control" value="{{ $user->details }}">

              <label for="text">帐号：</label>
              <input type="text" name="account" class="form-control" value="{{ old('account') }}" >



              <label for="password">密码：</label>
              <input type="password" name="password" class="form-control" value="{{ old('password') }}">



              <label for="password_confirmation">确认密码：</label>
              <input type="password" name="password_confirmation" class="form-control" value="{{ old('password_confirmation') }}">


            <button type="submit" class="btn btn-primary">更新</button>
        </form>