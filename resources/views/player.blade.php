<label for="name">名称：</label>
 <input type="text" name="name" class="form-control" value="{{ $data['name'] }}">

 <label for="name">描述：</label>
 <input type="text" name="details" class="form-control" value="{{ $data['details'] }}">
 <label for="name">评分与否：</label>
 <input type="text" name="details" class="form-control" value="{{ $data['isMarking'] }}">

<form method="POST" action="{{route('marking',$data['id'])}}">
  {{csrf_field()}}
    <div>
      评分
      <input type="text" name="score" placeholder="{{ $data['score'] }}">
      <input type="hidden" name="id" value="{{ $data['id'] }}">
     <button type="submit">提交</button>
   </div>
 </form>
