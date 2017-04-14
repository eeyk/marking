<h1>已经结束的活动</h1>
@foreach ($oldActivities as $oldActivity)
{{$oldActivity->name}}<a href="{{ route('restoreActivity', $oldActivity->id ) }}">恢复活动<br></a>
@endforeach
