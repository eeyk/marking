<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

	<!-- 与响应式相关的代码 -->
	<!-- user-scalable属性能够解决ipad切换横屏之后触摸才能回到具体尺寸的问题。 -->
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="true">

	<title>评分系统评委版</title>
	<link rel="stylesheet" type="text/css" href="../styles/UserStyles/UserPage.css">
</head>
<body>
	<div id="contain">
		<!-- 页面头部 -->
		<header id="pageHeader">
			<label class="pageHeaderPart sameFontSize" id="helloName">你好，<span></span>!</label>
			<input id="userIdInput" type="hidden" value="" />
			<input id="actIdInput" type="hidden" value="" />
			<input class="pageHeaderPart sameFontSize" id="cancellation" type="button" value="注销" />
			<label class="pageHeaderPart sameFontSize" id="searchBox">
				<input id="searchInput" class="sameFontSize" type="text" placeholder="请输入选手姓名">
				<span id="searchImg"></span>
			</label>
		</header>
		<!-- 页面头部以下 -->
		<div id="pageContainer">

			<!-- 左侧菜单栏 -->
			<ul id="menu">
				<li class="unchecked sameFontSize hoverable" id="actInformationForUserLi" clickable="on">
					<span></span>活动信息
				</li>
				<li class="unchecked sameFontSize hoverable" id="scoringAreaLi" clickable="on">
					<span></span>活动结果
				</li>
				<li class="unchecked  sameFontSize non-hoverable" id="playerInformationForUserLi" clickable="off">
					<span></span>选手信息
				</li>
			</ul>

			<!-- 右侧核心区域 -->
			<div id="corePart">

				<!-- 活动信息 -->
				<div id="actMsg" class="sameFontSize">
					<h2 id="actName">活动名称：<span></span></h2>
					<p id="actType">活动状态：<span class="onGoing"></span></p>
					<!-- <p id="actType">活动状态：<span class="finish">已结束</span></p> -->
					<img id="actImg" src="">
					<h2 id="h2OfActDetails">活动简介</h2>
					<article id="detailsContent" class="sameFontSize"></article>
				</div>

				<!-- 选手排名列表 -->
				<!-- 这里需要注意的是，评委版的选手排名列表模块的各部分代码都与管理员版的几乎一模一样（包括html、css、js），本来打算将css、js部分的代码合并统一编写，但考虑到往后需求可能会改变，故思考再三后放弃合并的想法，仍以“一式两份”的方式进行编写 -->
				<div id="rankingList" class="sameFontSize">
					<input id="actRanking" class="rankingPartShowTypeBtn" type="button" clicked="on" value="活动排名" />
					<input id="showMarkedPlayer" class="rankingPartShowTypeBtn" type="button" clicked="off" value="已评分选手" />
					<input id="showUnmarkedPlayer" class="rankingPartShowTypeBtn" type="button" clicked="off" value="未评分选手" />
					
					<!-- 选手排名列表内容 -->
					<table id="rankingListTable">
						<tr id="playerTableHead">
							<th>排名</th><th>姓名</th><th>已对该选手评分</th><th>加权分数</th>
						</tr>
						<!-- 单个选手信息示例 -->
						<!-- <tr class="playerTr markedPlayer">
							<input name="id" type="hidden" value="1" />
							<td>1</td><td>某某某</td><td class="markTd">是</td><td>98</td>
						</tr> -->
					</table>
				</div>

				<!-- 评分页面，含选手个人信息 -->
				<div id="scoringPage" class="sameFontSize">
					<!-- 评分页面(导航条查看上一个选手/下一个选手的导航条) -->
					<nav id="scoringPageNav">
						<a id="prevPlayerA" class="notHadPlayerClickable" ifClick="off" href="">
							&lt;&lt;&nbsp;&nbsp;上一选手：
							<span></span>
							<input type="hidden" value="" />
						</a>
						<a id="nextPlayerA" class="notHadPlayerClickable" ifClick="off" href="">
							下一选手：
							<span></span>
							&nbsp;&nbsp;&gt;&gt;
							<input type="hidden" value="" />
						</a>
					</nav>

					<!-- 选手个人信息 -->
					<form id="playerMsg">
						<h2 class="playerMsgH2">&gt;&gt;选手个人信息</h2>
						<input id="playerId" name="id" type="hidden" />
						<img id="playerImg" src="">
						<ul id="playerMsgUl">
							<li id="playerNameLi">
								<label id="playerName">
									<b>选手姓名</b>
									<span></span>
								</label>
							</li>
							<li id="playerDetailsLi">
								<label id="playerDetails">
									<b>选手信息</b>
									<textarea class="sameFontSize" disabled="disabled"></textarea>
								</label>
							</li>
						</ul>
						<h2 class="playerMsgH2">&gt;&gt;评分区域</h2>
						<label id="scoreLabel">
							<b>请输入分数:</b>
							<input id="playerScoreInput" type="text" />
							<input id="playerOriginScoreInput" type="hidden" />
							<span>分</span>
							<input id="scoreBtn" type="button" value="提交评分" />
						</label>
					</form>
				</div>



			</div>
		</div>
	</div>

	<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script type="text/javascript">
	if (typeof jQuery == "undefined") {
		document.write(unescape('%3Cscript type="text/javascript" src="../scripts/CommonScripts/jquery-2.1.4.min.js"%3E%3C/script%3E'));
	}
	</script><!-- *******************************CDN不可用时从本地服务器下载JQ****************************** -->
	<script type="text/javascript" src="../scripts/CommonScripts/alertBox.js"></script><!-- ***********弹框功能*********** -->
	<script type="text/javascript" src="../scripts/CommonScripts/CustomFunction.js"></script><!-- *******自定义函数******* -->
	<script type="text/javascript" src="../scripts/CommonScripts/HomePageScripts.js"></script><!-- ************************************************主页函数************************************************ -->
	<script type="text/javascript" src="../scripts/UserScripts/UserPageScripts.js"></script><!-- ***********************************************评委主页函数********************************************* -->
</body>
</html>