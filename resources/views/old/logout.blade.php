<form action="{{ route('logout') }}" method="POST">
  {{ csrf_field() }}
  {{ method_field('DELETE') }}
  <button class="btn btn-block btn-danger" type="submit" name="button">退出</button>
</form>
