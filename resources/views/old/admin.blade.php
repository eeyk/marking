<a href="{{route('createActivity')}}">创建活动</a>
@include('logout')
<h1>正在进行的活动</h1>
@foreach ($activities as $activity)
{{$activity->name}}<a href="{{ route('rankAll', $activity->id ) }}">查看活动</a>
<a href="{{ route('showActivity', $activity->id ) }}">编辑活动<br></a>
@endforeach
@foreach ($oldActivities as $oldActivity)
<a href="{{ route('showOldActivity',$oldActivity->id)}}">{{$oldActivity->name}}<br></a>
@endforeach
@include('msg')
