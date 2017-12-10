<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}" />
	<title>创建活动</title>
	<link rel="stylesheet" type="text/css" href="/css/mainStyle.css">
	<link rel="stylesheet" type="text/css" href="/css/createAct.css">
	<script type="text/javascript" src="/js/jquery-2.1.4.min.js"></script>
	<style type="text/css">
		#newAct{
			color:#333;
			background-color:#ebf7f8;
		}
		#newAct span{
			content: url("/img/now.png");
		}
	</style>
</head>
<body>

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
			<!-- 右侧主体 -->
			<div class="rightside">
				<div class="mainContain">
					<form id="myForm">
						<div id="uploadPic" onclick="importClick(3)">
							<span>上传图片</span>
						</div>
						<input id="imgField" class="file" type="file" name="img" onchange="showFile(3)"/>
						<ul id="actMes">
							<li id="title1">
								<span>活动信息</span>
							</li>
							<li>
								<label>活动名称</label>
								<input id="name" class="required" name="name" placeholder="必填项" />
								<img src="/img/modify.png">
							</li>
							<li id="des">
								<label><span>活动简介</span></label>
								<textarea placeholder="请输入活动简介" id="description" name="description"></textarea>
							</li>
						</ul>
					</form>
					<span id="save" onclick="saveModify()">创建活动</span>
				</div>
				<div class="clearFloat"></div>
			</div>
		</div>
	</div>
	<script  type="text/javascript" src="/js/main.js"></script>
	<script  type="text/javascript" src="/js/createAct.js"></script>
</body>
</html>
