<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
	<title>管理员主页</title>
	<link rel="stylesheet" type="text/css" href="../css/mainStyle.css">
	<script type="text/javascript" src="../js/jquery-2.1.4.min.js"></script>
	<style type="text/css">
		#allAct{
			color:#333;
			background-color:#ebf7f8;
		}
		#allAct span{
			content: url("../image/now.png");
		}
	</style>
</head>
<body class="table">
	<div id="container">
		<div class="topNav">
			<div class="topLeft">
				<span>{{$name}},您好！</span>
				<div id="menu">
						<div class="cap">
							<ul class="chmenu">
								<li id="modifyPsw">修改密码</li>
								<li id="exit">退出登录</li>
							<ul>
						</div>
				</div>
			</div>
			<div class="topRight">
				<input type="text" id="searchText" >
				<span id="search"></span>
			</div>
		</div>
		<div class="">
			<div class="leftside">
				<ul class="leftlist">
					<li id="allAct">
						<span></span>
						所有活动
					</li>
					<li id="newAct">
						<span></span>
						创建新活动
					</li>
					<li id="workAct">
						<span></span>
						未完成活动
					</li>
					<!-- <li id="waitAct">
						<span></span>
						未举办活动
					</li> -->
					<li id="overActiv">
						<span></span>
						已举办活动
					</li>
				</ul>
			</div>
			<!-- 自定义弹框 -->
			<div id="msgBox">
				<label class="msgHead"><span class="msgTip">提示</span><span id="msgClose" class="msgClose" onclick="closeMsgBox()"></span></label>
				<label id="msgBody" class="msgBody"></label>
				<label class="msgBottom"><span id="no" onclick="closeMsgBox()"> 取消 </span><span id="yes" onclick="closeMsgBox()"> 确定 </span></label>
			</div>
			<div class="rightside">
				<div class="mainContain">
					<!-- 应注意区别两种类型活动的查看信息按钮 -->
					<!-- 正举办 -->
					@foreach($activities  as $activity)
					<div class="children">
						<div class="chTop" style="background-image:url('{{$activity->img}}')">
							<div class="target_2">正举办</div>
						</div>
						<div class="chBot" id="111">
							<span>{{$activity->name}}</span>
							<div class="endBtn" onclick="ifEndAct({{$activity->id}})">结束活动</div>
							<div class="check ongoing_check" onclick="lookOngoingAct({{$activity->id}})">查看信息</div>
						</div>
					</div>
					@endforeach
					<!-- 已结束 -->
					@foreach($oldActivities  as $oldActivity)
					<div class="children">
						<div class="chTop" style="background-image:url('{{$oldActivity->img}}')">
							<div class="target_3">已结束</div>
						</div>
						<div class="chBot">
							<span>{{$oldActivity->name}}</span>
							<div class="recovery" onclick="ifRecoveryAct({{$oldActivity->id}})">恢复活动</div>
							<div class="check">查看信息</div>
						</div>
					</div>
					@endforeach
				</div>
				<div class="clearFloat"></div>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="../js/main.js"></script>
	<script type="text/javascript" src="../js/onGoingTable.js"></script>
	<script type="text/javascript" src="../js/overTable.js"></script>
</body>
</html>
