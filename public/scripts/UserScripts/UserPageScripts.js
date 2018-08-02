


// ***********************************************评委主页函数********************************************
// 主要包含以下几个功能:  1.页面加载完成后获取评委信息;  2.页面头部根据搜索框的输入的关键词进行查找;  
//                        3.评委获取活动详细信息;  4.评委查看活动结果;  5.评委查看选手信息;
//                        6.评委对选手进行评分;



// 1.页面加载完成后获取评委信息---------------------------------------------------------------------------
function showIdentityMsg(data){
	$("#helloName span").text(data.name);
	$("#userIdInput").val(data.id);
	$("#actIdInput").val(data.actId);
	userGetActMsg();
}



// 2.页面头部根据搜索框的输入的关键词进行查找-------------------------------------------------------------
function sureSearch(){
	return true;
}

function search(keyWord){
	var msg = new FormData();
	msg.append("name","" + keyWord);
	submit("/searchPlayer","POST",msg,searchPlayerSuccess);
}

// 搜索框查询选手与后台对接成功
function searchPlayerSuccess(data){
	if (data.result == "success") {
		// 其他模块隐藏
		$("#menu li.checked").removeClass("checked").addClass("unchecked").attr("clickable","on");
		$("#playerInformationForUserLi").attr("clickable","off");
		$("#actMsg").hide();
		$("#scoringPage").hide();
		$("#actRanking").click();
		$("#rankingList").show();

		// 循环生成选手列表
		generatingPlayerTable(data.players);
	} else if (data.result == "non-existent") {
		alertBox("搜索不到符合要求的选手!");
	} else{
		alertBox("搜索选手失败!");
	};
}



// 3.评委获取活动详细信息---------------------------------------------------------------------------------
function userGetActMsg(){
	// 清空原有活动信息内容
	$("#actName").find("span").text("");
	$("#actType").find("span").text("");
	$("#actImg").attr("src","");
	$("#detailsContent").text("");
	
	var actId = $("#actIdInput").val();	
	if (actId != "") {  // 防止页面尚未获取到评委所属活动id时就触发该函数;
		submit("/activityOfUser/" + actId,"GET",{},UserViewActSuccessFun);
	};
}

function UserViewActSuccessFun(data){
	if (data.result == "success") {
		$("#actName").find("span").text(data.activity.name);
		if (data.activity.actType == "onGoing") {
			$("#actType").find("span").removeClass("finish").addClass("onGoing").text("正举办");
		} else if (data.activity.actType == "finish") {
			$("#actType").find("span").removeClass("onGoing").addClass("finish").text("已结束");
		};
		$("#actImg").attr("src",data.activity.url);
		$("#detailsContent").text(data.activity.details);
	} else{
		alertBox("获取活动信息失败!");
	};
}



// 4.评委查看活动结果-------------------------------------------------------------------------------------
// 评委获取活动排名列表
function getActRanking(){
	var msg = new FormData();
	var actId = $("#actIdInput").val();
	msg.append("id","" + actId);
	submit("/getAllPlayers","POST",msg,getActRankingSuccess);
}

// 评委获取活动排名列表与后台对接成功
function getActRankingSuccess(data){
	if (data.result == "success") {
		// 循环生成选手列表
		generatingPlayerTable(data.players);
	} else{
		alertBox("获取排名信息失败!");
	};
}



// 循环生成选手列表
function generatingPlayerTable(players){
	// 清空排名列表里原有的排名信息
	$("#rankingListTable").find("tr[id!='playerTableHead']").remove();

	// 在表格内循环依次生成各选手信息
	var len = players.length;
	for (var i = 0; i < len; i++) {
		if (players[i].isMarking) {
			var $tr = $("<tr class='playerTr markedPlayer'></tr>");
			var $td3 = $("<td class='markTd'>是</td>");
		} else{
			var $tr = $("<tr class='playerTr unmarkedPlayer'></tr>");
			var $td3 = $("<td class='markTd'>否</td>");
		};
		$("#rankingListTable").append($tr);

		var $idInput =$("<input name='id' type='hidden' value='" + players[i].id + "' />")
		var $td1 = $("<td>" + players[i].rank + "</td>");
		var $td2 = $("<td>" + players[i].name + "</td>");
		var $td4 = $("<td>" + players[i].score + "</td>");
		$tr.append($idInput).append($td1).append($td2).append($td3).append($td4);

		$tr.attr("onclick","viewPlayer(this)");
	};
}



// “查看活动排名”点击活动排名
$("#actRanking").click(function(){
	if (!rankingPartShowTypeBtnUnclicked(this)) {
		return false;
	};

	$("#rankingListTable").find("tr").show();
})

// “查看活动排名”查看已评分选手
$("#showMarkedPlayer").click(function(){
	if (!rankingPartShowTypeBtnUnclicked(this)) {
		return false;
	};

	$("#rankingListTable").find("tr.unmarkedPlayer").hide();
	$("#rankingListTable").find("tr.markedPlayer").show();
})

// “查看活动排名”查看未评分选手
$("#showUnmarkedPlayer").click(function(){
	if (!rankingPartShowTypeBtnUnclicked(this)) {
		return false;
	};

	$("#rankingListTable").find("tr.markedPlayer").hide();
	$("#rankingListTable").find("tr.unmarkedPlayer").show();
})

// “查看活动排名”内检查三个列表类型按钮是否被点击
function rankingPartShowTypeBtnUnclicked(point){
	if ($(point).attr("clicked") == "on") {
		return false;
	};

	$(".rankingPartShowTypeBtn").attr("clicked","off");
	$(point).attr("clicked","on");

	return true;
}



// 6.评委查看选手信息-------------------------------------------------------------------------------------
// 评委进入评分页面，查看选手信息
function viewPlayer(point){
	var playerId = $(point).find("input[name='id']").val();
	submit("/playerOfUser/" + playerId,"GET",{},viewPlayerSuccessFun);
}

// 查看选手信息与后台对接成功
function viewPlayerSuccessFun(data) {

	if (data.result == "success") {
		$("#playerId").val(data.targetPlayer.id);
		$("#playerName").find("span").text(data.targetPlayer.name);
		$("#playerDetails").find("textarea").text(data.targetPlayer.details);
		$("#playerImg").attr("src",data.targetPlayer.url);
		$("#playerScoreInput").val(data.targetPlayer.score);
		$("#playerOriginScoreInput").val(data.targetPlayer.score);

		// 将左侧菜单栏中选手信息选项栏临时修改为可点击状态并进行点击
		$("#playerInformationForUserLi").attr("clickable","on").click();



		setPrevNextPlayer(data.prevPlayer,data.nextPlayer);
	} else if (data.result == "non-existent") {
		alertBox("该选手不存在!");
	} else {
		alertBox("查看选手失败!");
	};
}

// 为“上一个选手”和“下一个选手”赋值
function setPrevNextPlayer(prevPlayer,nextPlayer){
	if (prevPlayer) {
		$("#prevPlayerA").removeClass("notHadPlayerClickable").addClass("hadPlayerClickable");
		$("#prevPlayerA").find("input").val(prevPlayer.id);
		$("#prevPlayerA").find("span").text(prevPlayer.name);
	} else{
		$("#prevPlayerA").removeClass("hadPlayerClickable").addClass("notHadPlayerClickable");
		$("#prevPlayerA").find("input").val("");
		$("#prevPlayerA").find("span").text("没有了");
	};
	if (nextPlayer) {
		$("#nextPlayerA").removeClass("notHadPlayerClickable").addClass("hadPlayerClickable");
		$("#nextPlayerA").find("input").val(nextPlayer.id);
		$("#nextPlayerA").find("span").text(nextPlayer.name);
	} else{
		$("#nextPlayerA").removeClass("hadPlayerClickable").addClass("notHadPlayerClickable");
		$("#nextPlayerA").find("input").val("");
		$("#nextPlayerA").find("span").text("没有了");
	};
}

// 点击上一个选手/下一个选手
$("#scoringPageNav a").click(function(e){
	// 阻止a标签默认跳转
	e.preventDefault();

	$("#scoringPageNav a").attr("ifClick","off");
	$(this).attr("ifClick","on");

	clickPrevNextPlayer();

	// // 阻止a标签默认跳转
	// return false;
})

function clickPrevNextPlayer(){
	var playerId = $("#scoringPageNav a[ifClick='on']").find("input").val();
	if (playerId != "") {
		submit("/playerOfUser/" + playerId,"GET",{},viewPlayerSuccessFun);
	};
}



// 6.评委对选手进行评分-----------------------------------------------------------------------------------
// 点击提交分数按钮
$("#scoreBtn").click(function(){
	var newScore = $("#playerScoreInput").val();
	if (newScore != "" && (newScore >= 0 && newScore <= 100)) {
		confirmBox("是否提交新分数?","submitScore()");
	} else{
		alertBox("请输入正确的分数(0~100)!");
		var originScore = $("#playerOriginScoreInput").val();
		$("#playerScoreInput").val(originScore);
	};
})

// 确认要提交分数
function submitScore(){
	var playerId = $("#playerId").val();
	var score = $("#playerScoreInput").val();
	var msg = new FormData();
	msg.append("id",playerId);
	msg.append("score",score);
	submit("/marking","POST",msg,submitScoreSuccessFun);
}

// 提交分数与后台对接成功
function submitScoreSuccessFun(data){
	if (data.result == "success") {
		alertBox("评分成功!");
		// 给保存原始分数的input赋值新的分数
		var newOriginScore = $("#playerOriginScoreInputplayerScoreInput").val();
		$("#playerOriginScoreInput").val(newOriginScore);
	} else{
		alertBox("提交评分失败!");
		var originScore = $("#playerOriginScoreInput").val();
		$("#playerScoreInput").val(originScore);
	};
}



// *********************************添加新函数时注意在该文件头部说明处补充********************************


