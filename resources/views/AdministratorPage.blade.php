<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

	<!-- 与响应式相关的代码 -->
	<!-- user-scalable属性能够解决ipad切换横屏之后触摸才能回到具体尺寸的问题。 -->
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="true">

	<title>评分系统管理员版</title>
	<link rel="stylesheet" type="text/css" href="../styles/AdministratorStyles/AdministratorPage.css">
</head>
<body>
	<div id="contain">

		<!-- 页面头部 -->
		<header id="pageHeader">
			<label class="pageHeaderPart sameFontSize" id="helloName">你好，<span></span>!</label>
			<input class="pageHeaderPart sameFontSize" id="cancellation" type="button" value="注销" />
			<label class="pageHeaderPart sameFontSize" id="searchBox">
				<input id="searchInput" class="sameFontSize" type="text" placeholder="请输入活动名称" />
				<input id="searchKeyWordInput" class="sameFontSize" type="hidden" />
				<span id="searchImg"></span>
			</label>
		</header>

		<!-- 页面头部以下 -->
		<div id="pageContainer">

			<!-- 左侧菜单栏 -->
			<ul id="menu">
				<li class="unchecked  sameFontSize hoverable" id="createActivityLi" clickable="on">
					<span></span>创建新活动
				</li>
				<li class="unchecked  sameFontSize hoverable" id="onGoingActivityLi" clickable="on">
					<span></span>正举办活动
				</li>
				<li class="unchecked  sameFontSize hoverable" id="finishActivityLi" clickable="on">
					<span></span>已结束活动
				</li>
				<li class="unchecked  sameFontSize non-hoverable" id="actInformationForAdminLi" clickable="off">
					<span></span>活动内容栏
				</li>
			</ul>

			<!-- 右侧核心区域 -->
			<div id="corePart">

				<!-- 创建活动模块 -->
				<form id="createActPart">
					<span class="actImg modifiable">点击插入图片</span>
					<input class="actImgInput" name="actImg" type="file" />
					<ul class="actBriefMsg sameFontSize">
						<li>
							<label>活动名称</label>
							<input class="actName sameFontSize" name="name" type="text" />
						</li>
						<li>
							<label>活动简介</label>
							<textarea class="actDetails sameFontSize" name="details"  placeholder="对活动内容作简要的介绍......"></textarea>
						</li>
					</ul>
					<ul id="fileField">
						<li>
							<label class="fileUploadTip sameFontSize">请导入选手文件</label>
							<label class="fileUpload sameFontSize">
								<span>选择文件</span>
								<input class="excelFileInput" name="playerFile" id="playerFile" type="file" />
							</label>
							<span class="fileUploadStatusImg fileUploadStatusImgEmpty" id="playerFileUploadStatusImg"></span>
						</li>
						<li>
							<label class="fileUploadTip sameFontSize">请导入评委文件</label>
							<label class="fileUpload sameFontSize">
								<span>选择文件</span>
								<input class="excelFileInput" name="userFile" id="userFile" type="file" />
							</label>
							<span class="fileUploadStatusImg fileUploadStatusImgEmpty" id="userFileUploadStatusImg"></span>
						</li>
					</ul>
					<input class="actOperationBtn" id="createActBtn" type="button" value="创建活动" />
				</form>



				<!-- 活动列表 -->
				<div id="actList">

					<!-- 活动卡片注释例子 -->
					<!-- <form class="actCard">
						<input id="actId" name="id" type="hidden" value="" />
						<label class="cardActImgPart">
							<span class="actType sameFontSize onGoingType">正举办</span>
							<span class="actType sameFontSize finishType">已结束</span>
						</label>
						<label class="cardActName">活动名称</label>
						<input class="cardBtn sameFontSize endingAct" type="button" value="结束活动" />
						<input class="cardBtn sameFontSize recoverAct" type="button" value="恢复活动" />
						<input class="cardBtn sameFontSize viewAct" type="button" value="查看详情" />
					</form> -->
				</div>



				<!-- 查看上一个活动/下一个活动的导航条 -->
				<nav id="actMsgPartNav" class="sameFontSize">
					<a id="prevActA" class="notHadActClickable" ifClick="off" href="">
						&lt;&lt;&nbsp;&nbsp;上一活动：
						<span></span>
						<input type="hidden" value="" />
					</a>
					<a id="nextActA" class="notHadActClickable" ifClick="off" href="">
						下一活动：
						<span></span>
						&nbsp;&nbsp;&gt;&gt;
						<input type="hidden" value="" />
					</a>
				</nav>



				<!-- 以下正举办活动和已结束活动两个表单有大部分代码相同，本来想整合在一起，但考虑到日后需求可能会变或者需要维护等其他各种情况，故放弃整合的想法 -->
				<!-- 正举办活动详细 -->
				<form id="onGoingActPart">
					<input class="actId" name="id" type="hidden" value="">
					<span class="actImg"></span>
					<input class="actImgInput" name="actImg" type="file"/>
					<ul class="actBriefMsg sameFontSize">
						<li>
							<label>活动名称</label>
							<input class="actName sameFontSize" name="name" type="text" readonly="readonly" />
						</li>
						<li>
							<label>活动简介</label>
							<textarea class="actDetails sameFontSize" name="details"  placeholder="对活动内容作简要的介绍......" readonly="readonly" ></textarea>
						</li>
					</ul>
					<ul class="actInformationFunBtnUl">
						<li>
							<input class="actInformationFunBtn sameFontSize viewUsers" type="button" value="查看评审的评委" />
						</li>
						<li>
							<input class="actInformationFunBtn sameFontSize viewFinalRankings" type="button" value="查看活动排名" />
						</li>
					</ul>
					<input class="actOperationBtn onGoingActBtnGroup1" id="modifyActBtn" type="button" value="修改活动" />
					<input class="actOperationBtn onGoingActBtnGroup2" id="renewActBtn" type="button" targetstate="finish" value="更新活动" />
					<input class="actOperationBtn onGoingActBtnGroup2" id="cancelModifyActBtn" type="button" value="取消修改" />
					<input class="actOperationBtn onGoingActBtnGroup1" id="endingActBtn" type="button" targetstate="finish" value="结束活动" />
					<input class="actOperationBtn onGoingActBtnGroup1 deleteActBtn" type="button" targetstate="finish" value="删除活动" />
				</form>



				<!-- 已结束活动详细 -->
				<form id="finishActPart">
					<input class="actId" name="id" type="hidden" value="">
					<span class="actImg"></span>
					<input class="actImgInput" name="actImg" type="file"/>
					<ul class="actBriefMsg sameFontSize">
						<li>
							<label>活动名称</label>
							<input class="actName sameFontSize" name="name" type="text" disabled="disabled" />
						</li>
						<li>
							<label>活动简介</label>
							<textarea class="actDetails sameFontSize" name="details"  placeholder="对活动内容作简要的介绍......" disabled="disabled"></textarea>
						</li>
					</ul>
					<ul class="actInformationFunBtnUl">
						<li>
							<input class="actInformationFunBtn sameFontSize viewUsers" type="button" value="查看评审的评委" />
						</li>
						<li>
							<input class="actInformationFunBtn sameFontSize viewFinalRankings" type="button" value="查看活动排名" />
						</li>
					</ul>
					<input class="actOperationBtn" id="restoreActBtn" type="button" targetstate="restore" value="恢复活动" />
					<input class="actOperationBtn deleteActBtn" type="button" targetstate="finish" value="删除活动" />
				</form>



				<!-- 评委内容块 -->
				<div id="userPart" class="sameFontSize">

					<!-- 评委列表头部导航条 -->
					<nav id="userNav">
						<a id="userTableToActPart" class="userListNavClickable" ifClick="on" href="">
							<span>活动详细信息</span>
						</a>
						<b class="userPartSecondNav">&nbsp;&nbsp;&gt;&nbsp;&nbsp;</b>
						<a id="userMsgToUserTable" class="userPartSecondNav" ifClick="off" href="">
							<span>评委列表</span>
						</a>
						<b class="userPartThirdNav">&nbsp;&nbsp;&gt;&nbsp;&nbsp;</b>
						<a id="userMsg" class="userPartThirdNav" ifClick="off" href="">
							<span>评委详细信息</span>
						</a>
					</nav>

					<!-- 评委列表表格 -->
					<table id="userTable">
						<tr id="userTableHead">
							<th>评委姓名</th><th>权重</th>
						</tr>
						<!-- 单个评委信息示例 -->
						<!-- <tr>
							<td>
								<a class="userNameInTable" href="">二营长</a>
								<input type="hidden" value="3" />
							</td>
							<td>B</td>
						</tr> -->
					</table>

					<!-- 评委个人详细信息 -->
					<table id="userInformation">
						<input id="userId" name="id" type="hidden" value="" />
						<tr>
							<th>评委姓名</th>
							<td>
								<input name="name" class="sameFontSize" type="text" value="" />
							</td>
						</tr>
						<tr>
							<th>帐号</th>
							<td>
								<input name="account" class="sameFontSize" type="text" value="" />
							</td>
						</tr>
						<tr>
							<th>密码</th>
							<td>
								<input name="password" class="sameFontSize" type="text" value="" />
							</td>
						</tr>
						<tr>
							<th>权重</th>
							<td>
								<select id="userWeightSelect" name="weight">
								  	<option value="A">A</option>
								  	<option value="B">B</option>
								  	<option value="C">C</option>
								</select>
								<input id="userWeightInput" name="weight" class="sameFontSize" type="text" value="" />
							</td>
						</tr>
					</table>
					<!-- 修改评委信息、恢复默认按钮组 -->
					<input id="modifyUserMsg" class="userMsgOperationBtn" type="button" value="提交修改" />
					<input id="userMsgRestoreDefault" class="userMsgOperationBtn" type="button" value="恢复默认" />
				</div>



				<!-- 选手排名内容块 -->
				<div id="rankingPart" class="sameFontSize">
					<!-- 选手排名头部导航条 -->
					<nav id="rankingNav">
						<a id="rankingTableToActPart" class="rankingNavClickable" ifClick="on" href="">
							<span>活动详细信息</span>
						</a>
						<b class="rankingPartSecondNav">&nbsp;&nbsp;&gt;&nbsp;&nbsp;</b>
						<a id="playerMsgToRankingTable" class="rankingPartSecondNav" ifClick="off" href="">
							<span>排名列表</span>
						</a>
						<b class="rankingPartThirdNav">&nbsp;&nbsp;&gt;&nbsp;&nbsp;</b>
						<a id="playerMsg" class="rankingPartThirdNav" ifClick="off" href="">
							<span>选手详细信息</span>
						</a>
					</nav>
					
					<!-- 选手排名列表 -->
					<div id="rankingList" class="sameFontSize">
						<input id="actRanking" class="rankingPartShowTypeBtn" type="button" clicked="on" value="活动排名" />
						<input id="showMarkedPlayer" class="rankingPartShowTypeBtn" type="button" clicked="off" value="已评分选手" />
						<input id="showUnmarkedPlayer" class="rankingPartShowTypeBtn" type="button" clicked="off" value="未评分选手" />
						
						<!-- 选手排名列表内容 -->
						<table id="rankingListTable">
							<tr id="playerTableHead">
								<th>排名</th><th>姓名</th><th>评委已完成评分</th><th>分数</th>
							</tr>
							<!-- 单个选手信息示例 -->
							<!-- <tr class="playerTr markedPlayer">
								<input name="id" type="hidden" value="1" />
								<td>1</td><td>某某某</td><td class="markTd">是</td><td>98</td>
							</tr> -->
						</table>
					</div>

					<!-- 选手详细信息 -->
					<form id="playerInformation" class="sameFontSize">
						<input id="playerId" name="id" type="hidden" value="" />
						<span id="playerImg" on-off="on"></span>
						<input id="playerImgInput" name="playerImg" type="file" value="" />
						<ul id="playerBasicInformation">
							<li id="playerNameLi">
								<span>选手姓名</span>
								<input id="playerName" class="sameFontSize" name="name" type="text" value="" />
							</li>
							<li id="playerDetailsLi">
								<span>选手信息</span>
								<textarea id="playerDetails" class="sameFontSize" name="details"></textarea>
							</li>
						</ul>
						<!-- 修改选手信息、恢复默认按钮组 -->
						<input id="modifyPlayerMsg" class="playerMsgOperationBtn" type="button" value="提交修改" />
						<input id="playerMsgRestoreDefault" class="playerMsgOperationBtn" type="button" value="恢复默认" />



						<!-- 选手得分详细表单 -->
						<label id="playerScoreAndRank">
							分数:<span id="scoreSpan"></span>
							排名:<span id="rankSpan"></span>
						</label>
						<table id="detailedScoreOfPlayer" class="sameFontSize">
							<tr id="detailedScoreTableHead">
								<th>评委姓名</th><th>权重</th><th>所评分数</th>
							</tr>
							<!-- 单个评委评分信息示例 -->
							<!-- <tr class="userScoreTr">
								<td>某某某</td><td>A</td><td>98</td>
							</tr>
							<tr class="userScoreTr">
								<td>某某某</td><td>A</td><td class="no-score">未评分</td>
							</tr> -->
						</table>
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
	<script type="text/javascript" src="../scripts/CommonScripts/jquery.serializejson.min.js"></script>
	<script type="text/javascript" src="../scripts/CommonScripts/alertBox.js"></script><!-- ****弹框功能**** -->
	<script type="text/javascript" src="../scripts/CommonScripts/insertPicPreview.js"></script><!-- *****************************************插入图片并实现预览函数***************************************** -->
	<script type="text/javascript" src="../scripts/CommonScripts/CustomFunction.js"></script><!-- 自定义函数 -->
	<script type="text/javascript" src="../scripts/CommonScripts/HomePageScripts.js"></script><!-- *****************************************************主页公共函数**************************************************** -->
	<script type="text/javascript" src="../scripts/AdministratorScripts/AdministratorPageScripts.js"></script><!-- *******************************************管理员主页函数****************************************** -->
</body>
</html>