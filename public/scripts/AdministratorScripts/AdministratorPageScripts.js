


// **********************************************管理员主页函数*******************************************
// 主要包含以下几个功能:  1.页面头部根据搜索框的输入的关键词进行查找;  2.页面加载完成后获取管理员信息;
//                        3.活动图片上传预览;  4.Excel文件相关函数;  5.创建活动;
//                        6.活动操作（结束、恢复、查看、修改、更新、删除）  7.管理员查看、修改评委
//                        8.管理员查看、修改选手信息和得分情况



// 1.页面头部根据搜索框的输入的关键词进行查找-------------------------------------------------------------
function sureSearch(){
	if ($("#createActPart").css("display") == "block") {  // 正在创建活动
		var $form = $("#createActPart");
		if (($form.find(".actName").val() != "") || ($form.find(".actDetails").val() != "") || ($form.find(".actImgInput").val() != "") || ($form.find("#playerFile").val() != "") || ($form.find("#userFile").val() != "")) {  // 表单正处于编辑当中
			confirmBox("活动尚未创建，是否放弃编辑?","searchActInfoFromCreateAct()");
		} else{
			return true;
		};
	} else if ($("#renewActBtn").css("display") == "block") {  // 正在修改活动	
		confirmBox("活动尚未保存，是否放弃修改?","searchActInfoFromActInfo()");
	} else{
		return true;
	};
}

// 放弃正在创建的活动，查询其他活动
function searchActInfoFromCreateAct(){
	// 清空创建活动的表单，防止临时标记的选项不断点击循环激活弹框
	clearCreateActForm();
	$("#searchImg").click();
}

// 放弃正在修改的活动，查询其他活动
function searchActInfoFromActInfo(){
	$("#cancelModifyActBtn").click();
	$("#searchImg").click();
}

function search(keyWord){
	var msg = new FormData();
	msg.append("name","" + keyWord);
	submit("/searchActivity","POST",msg,searchActSuccess);
}

// 搜索活动与后台对接成功
function searchActSuccess(data){
	if (data.result == "success") {

		if ($("#searchInput").val() != "") {
			$("#searchKeyWordInput").val($("#searchInput").val());
		};
		$("#searchInput").val("");

		// 平稳退化，检测#actList是否存在
		if ($("#actList").length > 0) {

			// 其他模块隐藏
			$("#menu li.checked").removeClass("checked").addClass("unchecked").attr("clickable","on");
			$("#actInformationForAdminLi").attr("clickable","off");
			$("#createActPart").hide();
			$("#onGoingActPart,#finishActPart").hide();
			$("#actMsgPartNav").hide();
			$("#userPart").hide();
			$("#userMsg").hide();
			$("#rankingPart").hide();

			$("#actList").html("");
			createActCard(data.onGoingActivities);
			createActCard(data.finishActivities);
			$("#actList").show();
		};
	} else if (data.result == "non-existent") {
		alertBox("搜索不到符合要求的活动!");
	} else{
		alertBox("搜索活动失败!");
	};
}



// 2.页面加载完成后获取管理员信息-------------------------------------------------------------------------
function showIdentityMsg(data){
	$("#helloName span").text(data.name);
}



// 3.活动图片上传预览-------------------------------------------------------------------------------------
$(".modifiable").attr("onclick","uploadImg(this,'.actImgInput')");
$(".actImgInput").attr("onchange","showFile(this,'.actImg','.actImgInput')");



// 4.Excel文件相关函数------------------------------------------------------------------------------------
// 绑定excel文件导入的处理函数
$("#playerFile").attr("onchange","fileStatusChange(this,'player')");
$("#userFile").attr("onchange","fileStatusChange(this,'user')");

// 检测导入的文件是否为excel类型
function checkExcel(point){
	// 平稳退化，检测point指针所指对象是否存在
	var pointId = $(point).attr("id");
	if ($("#" + pointId).length <= 0) {
		return false;
	};

	if (restrictFile(point,"#" + pointId,"excel")) {
		return true;
	} else{
		return false;
	};
}

// 检测文件是否导入成功修改后边的状态符
function fileStatusChange(point,type){
	// 平稳退化，检测point指针所指对象是否存在
	var pointId = $(point).attr("id");
	if ($("#" + pointId).length <= 0) {
		return false;
	};

	if (restrictFile(point,"#" + pointId,"excel")) {
		var statusImgId = type + "FileUploadStatusImg";
		// 平稳退化，检测文件导入状态符是否存在
		if ($("#" + statusImgId).length <= 0) {
			return false;
		};


		if ($(point).val() != "") {
			$("#" + statusImgId).removeClass("fileUploadStatusImgEmpty").addClass("fileUploadStatusImgNon-empty");
		} else{
			$("#" + statusImgId).removeClass("fileUploadStatusImgNon-empty").addClass("fileUploadStatusImgEmpty");
		};
	};
}



// 5.创建活动---------------------------------------------------------------------------------------------
$("#createActBtn").click(function(){
	confirmBox("确定要创建新活动吗?","updateActSureFun()");
})

// 检测活动表单是否填写完整
function ifCreateActFormComplete(formId,operationType){
	var $form = $(formId);
	if ($form.find(".actName").val() == "") {
		alertBox("请填写活动名称!");
		return false;
	} else if ($form.find(".actDetails").val() == "") {
		alertBox("请填写活动简介!");
		return false;
	};

	switch(operationType){
		case "create":{
			if ($form.find(".actImgInput").val() == "") {
				alertBox("请导入活动图片!");
				return false;
			} else if ($form.find("#playerFile").val() == "") {
				alertBox("请导入选手名单!");
				return false;
			} else if ($form.find("#userFile").val() == "") {
				alertBox("请导入评委名单!");
				return false;
			} else{
				return true;
			};
		}  break;
		case "modify":{
			return true;
		}  break;
	}
}

// 确定创建活动
function updateActSureFun(){
	// 检测活动表单是否填写完整
	if (!ifCreateActFormComplete("#createActPart","create")) {
		return false;
	};
	
	var $form = $("#createActPart");
	var msg = new FormData($form[0]);
	submit("/create/activity","POST",msg,createActSuccess);
}

// 创建活动对接成功
function createActSuccess(data){
	if (data.result == "success") {
		clearCreateActForm();
		submit("/activity/" + data.id,"GET",{},viewActSuccessFun);
	} else if (data.result == "nameExists") {
		alertBox("该活动名称已存在!");
	} else {
		alertBox("创建活动失败!");
	};
}



// 清空创建活动表单内容
function clearCreateActForm(){
	var $form = $("#createActPart");
	$form.find(":input[name]").val("");
	$form.find(".fileUploadStatusImg").removeClass("fileUploadStatusImgNon-empty").addClass("fileUploadStatusImgEmpty");
	$form.find(".actImg").css("background-image","url('#')").text("点击插入图片");
}



// 6.活动操作（结束、恢复、查看、修改、更新、删除）-------------------------------------------------------
// 修改活动状态（结束、恢复活动）
function modifyActState(point,operationType){

	var operationName,operationUrl;
	var targetState = $(point).attr("targetState");
	console.log(targetState);
	switch(targetState){
		case "finish":{
			operationName = "结束";
			operationUrl = "/finishActivity";
		}  break;
		case "restore":{
			operationName = "恢复";
			operationUrl = "/restoreActivity";
		}  break;
	}
	var actId = $(point).parent("form").find(".actId").val();
	confirmBox("确定要" + operationName + "活动吗?","sureModify(" + actId + ",'" + operationUrl + "','" + operationType + "')");
}

// 确认要修改活动状态
function sureModify(id,operationUrl,operationType){
	console.log(operationType);
	var msg = new FormData();
	msg.append("id","" + id);
	if (operationType == "actList") {
		submit(operationUrl,"POST",msg,actListModifyActSuccessFun);
	} else if (operationType == "actForm") {
		submit(operationUrl,"POST",msg,actFormModifyActSuccessFun);
	};
}

// 修改活动状态与后台对接成功
// 成功函数1
function actListModifyActSuccessFun(data){
	if (data.result == "success") {
		if ($("#menu li.checked").length > 0) {
			$("#menu li.checked").attr("clickable","on").click();
		} else{
			search($("#searchKeyWordInput").val());
		};
	} else{
		alertBox("操作失败!");
	};
}
// 成功函数2
// 保存原来正在查看的原表单id
var originViewActId;
function actFormModifyActSuccessFun(data){
	if (data.result == "success") {
		submit("/activity/" + originViewActId,"GET",{},viewActSuccessFun);
	} else{
		alertBox("操作失败!");
	};
}



// 查看活动详情
function viewAct(point){
	var $form = $(point).parent("form");
	var actId = $form.find(".actId").val();
	submit("/activity/" + actId,"GET",{},viewActSuccessFun);
}

// 查看活动详情与后台对接成功
function viewActSuccessFun(data) {

	if (data.result == "success") {
		// 根据活动类型选择活动表单
		var formId;
		if (data.targetActivity.actType == "onGoing") {
			formId = "#onGoingActPart";
		} else if (data.targetActivity.actType == "finish") {
			formId = "#finishActPart";
		};

		$(formId).find(".actId").val(data.targetActivity.id);
		$(formId).find(".actImgInput").val("");
		$(formId).find(".actImg").css({"background-image":"url(" + data.targetActivity.url + "?t=" + new Date().getTime() + ")"})
		$(formId).find(".actName").val(data.targetActivity.name);
		$(formId).find(".actDetails").val(data.targetActivity.details);

		setPrevNextAct(data.prevActivity,data.nextActivity);

		$("#onGoingActPart,#finishActPart").removeClass("showActForm");
		$(formId).addClass("showActForm");
		$("#actInformationForAdminLi").attr("clickable","on").click();
	} else if (data.result == "non-existent") {
		alertBox("该活动不存在!");
	} else {
		alertBox("查看活动失败!");
	};
}

// 为“上一个活动”和“下一个活动”赋值
function setPrevNextAct(prevActivity,nextActivity){
	if (prevActivity) {
		$("#prevActA").removeClass("notHadActClickable").addClass("hadActClickable");
		$("#prevActA").find("input").val(prevActivity.id);
		$("#prevActA").find("span").text(prevActivity.name);
	} else{
		$("#prevActA").removeClass("hadActClickable").addClass("notHadActClickable");
		$("#prevActA").find("input").val("");
		$("#prevActA").find("span").text("没有了");
	};
	if (nextActivity) {
		$("#nextActA").removeClass("notHadActClickable").addClass("hadActClickable");
		$("#nextActA").find("input").val(nextActivity.id);
		$("#nextActA").find("span").text(nextActivity.name);
	} else{
		$("#nextActA").removeClass("hadActClickable").addClass("notHadActClickable");
		$("#nextActA").find("input").val("");
		$("#nextActA").find("span").text("没有了");
	};
}

// 点击上一个活动/下一个活动
$("#actMsgPartNav a").click(function(){
	$("#actMsgPartNav a").attr("ifClick","off");
	$(this).attr("ifClick","on");

	var actId = $("#actMsgPartNav a[ifClick='on']").find("input").val();
	if ($("#renewActBtn").css("display") == "block" && actId != "") {  // 正在修改活动	
		confirmBox("活动尚未保存，是否放弃修改?","switchActInfoFromActInfo()");
	} else{
		clickPrevNextAct();
	};

	// 阻止a标签默认跳转
	return false;
})

function switchActInfoFromActInfo(){
	$("#cancelModifyActBtn").click();
	clickPrevNextAct();
}

function clickPrevNextAct(){
	var actId = $("#actMsgPartNav a[ifClick='on']").find("input").val();
	if (actId != "") {
		submit("/activity/" + actId,"GET",{},viewActSuccessFun);
	};
}

// 更新活动
$("#modifyActBtn").click(function(){
	$form = $("#onGoingActPart");
	$form.find(".actImg").addClass("modifiable").attr("onclick","uploadImg(this,'.actImgInput')");
	$form.find(".actName,.actDetails").removeAttr("readonly");

	$form.find(".onGoingActBtnGroup1").hide();
	$form.find(".onGoingActBtnGroup2").show();

	// 保存原表单信息
	var imgUrl = $form.find(".actImg").css("backgroundImage").split("(")[1].split(")")[0];
	getOriginActMsg($form.serializeJSON(),imgUrl);
})

//获取某即将被修改表单的原信息
var originActMsg;
function getOriginActMsg(someoneMsg,imgUrl){
	// 存储表单修改前的原值
	originActMsg = someoneMsg;
	originActMsg.url = imgUrl;
}

$("#renewActBtn").click(function(){
	confirmBox("确认要更新活动信息吗?","sureModifyAct()");
})

function sureModifyAct(){
	// 检测活动表单是否填写完整
	if (!ifCreateActFormComplete("#onGoingActPart","modify")) {
		return false;
	};
	
	var $form = $("#onGoingActPart");
	var msg = new FormData($form[0]);
	submit("/update/activity","POST",msg,updateActSuccess);
}

// 更新活动信息后台对接成功
function updateActSuccess(data){
	if (data.result == "success") {
		$form = $("#onGoingActPart");
		$form.find(".actImg").removeClass("modifiable").removeAttr("onclick");
		$form.find(".actName,.actDetails").attr("readonly","readonly");
		$form.find(".onGoingActBtnGroup2").hide();
		$form.find(".onGoingActBtnGroup1").show();
	} else{
		alertBox("更新活动信息失败!");
	};
}

// 取消修改活动信息
$("#cancelModifyActBtn").click(function(){
	// 输入框以及按钮组恢复原样式
	recoveryStyle();

	// 返回原值
	$form.find("input[name='id']").val(originActMsg.id);
	$form.find("input[name='name']").val(originActMsg.name);
	$form.find("textarea[name='details']").val(originActMsg.details);
	$form.find(".actImgInput").val("");
	$form.find(".actImg").css({"background-image":"url(" + originActMsg.url + ")"});
})

// 输入框以及按钮组恢复原样式
function recoveryStyle(){
	// 平稳退化，检测point指针所指对象是否存在
	if ($("#onGoingActPart").length <= 0) {
		return false;
	};
	$form = $("#onGoingActPart");
	$form.find(".actImg").removeClass("modifiable").removeAttr("onclick");
	$form.find(".actName,.actDetails").attr("readonly","readonly");
	$form.find(".onGoingActBtnGroup2").hide();
	$form.find(".onGoingActBtnGroup1").show();
}



// 结束活动、恢复活动
$("#endingActBtn,#restoreActBtn").click(function(){
	originViewActId = $(this).parent("form").find("input[name='id']").val();
	modifyActState(this,'actForm');
})



// 删除前原活动所属类型
var originActListType;
// 删除活动
$(".deleteActBtn").click(function(){
	var $form = $(this).parent("form");

	if ($form.find("#endingActBtn").length > 0) {
		originActListType = "#onGoingActivityLi";
	} else{
		originActListType = "#finishActivityLi";
	};

	var actId = $form.find(".actId").val();
	confirmBox("确定要删除活动吗?","sureDelete(" + actId + ")");
})

function sureDelete(id){
	submit("/deleteActivity/" + id,"POST",{},deleteActSuccessFun);
}

// 删除活动后台对接成功
function deleteActSuccessFun(data){
	if (data.result == "success") {
		$(originActListType).click();
	} else{
		alertBox("删除活动失败!");
	};
}



// 7.管理员查看、修改评委---------------------------------------------------------------------------------
// 活动详细信息内点击“查看评审的评委”按钮
$(".viewUsers").click(function(){
	var msg = new FormData();
	var actId = $(".showActForm").find(".actId").val();
	msg.append("id","" + actId);
	submit("/getUserTable","POST",msg,getUserTableSuccess);
})

// 查看评审的评委与后台对接成功
function getUserTableSuccess(data){
	if (data.result == "success") {

		// 清空评委表单里原有的评委信息
		$("#userTable").find("tr[id!='userTableHead']").remove();

		// 在表格内循环依次生成各评委信息
		var len = data.users.length;
		for (var i = 0; i < len; i++) {
			var $tr = $("<tr></tr>");
			$("#userTable").append($tr);
			var $td1 = $("<td><a class='userNameInTable' href=''>" + data.users[i].name + "</a><input type='hidden' value='" + data.users[i].id + "' /></td>");
			var $td2 = $("<td>" + data.users[i].weight + "</td>");
			$tr.append($td1).append($td2);
			$td1.find("a").attr("onclick","viewUser(this);return false;");
		};
		
		$("#actMsgPartNav,.showActForm").hide();
		$("#userInformation").hide();
		$(".userMsgOperationBtn").hide();
		$("#userTable").show();
		$(".userPartThirdNav").hide();
		$("#userMsgToUserTable").removeClass("userListNavClickable").attr("ifClick","off");
		$("#userPart").show();
		
		
	} else{
		alertBox("查看评委失败!");
	};
}

// 选手排名头部导航条的a链接点击无效避免其默认跳转
$("#userNav").find("a").click(function(){
	return false;
})

// 从评委列表返回活动详细信息
$("#userTableToActPart span").click(function(){
	$("#userPart").hide();
	$(".userPartThirdNav").hide();
	$("#actMsgPartNav,.showActForm").show();
})

// 从评委详细信息返回评委列表
$("#userMsgToUserTable span").click(function(){
	$(".userPartThirdNav").hide();
	$("#userInformation").hide();
	$(".userMsgOperationBtn").hide();
	$("#userMsgToUserTable").removeClass("userListNavClickable").attr("ifClick","off");
	$("#userTable").show();
})

// 点击评委姓名查看其详细信息
function viewUser(point){
	var userId = $(point).siblings("input").val();
	submit("/user/" + userId,"GET",{},viewUserSuccessFun);
}

// 查看评委详细信息与后台对接成功
function viewUserSuccessFun(data){
	if (data.result == "success") {

		// 判断该评委所属活动的类型
		var actType;
		if ($(".showActForm").attr("id") == "onGoingActPart") {
			actType = "onGoing";
			// 修改评委、恢复默认按钮显示
			$(".userMsgOperationBtn").show();
		} else if ($(".showActForm").attr("id") == "finishActPart") {
			actType = "finish";
		};
		setUserTable(data.user,actType);

		$("#userTable").hide();
		$("#userMsgToUserTable").addClass("userListNavClickable").attr("ifClick","on");
		$(".userPartThirdNav").show();
		$("#userInformation").show();
	} else{
		alertBox("查看评委信息失败!");
	};
}

// 为评委详细信息表单赋值
function setUserTable(user,actType){
	$table = $("#userInformation");
	$table.find("input[name='id']").val("" + user.id);
	$table.find("input[name='name']").val("" + user.name);
	$table.find("input[name='account']").val("" + user.account);
	$table.find("input[name='password']").val("" + user.password);
	$table.find("input[name='weight']").val(user.weight);
	$table.find("select[name='weight']").val(user.weight);

	if (actType == "onGoing") {
		$table.find("input[name]").removeAttr("disabled").addClass("userModifiableInput");
		$table.find("input[name='weight']").hide();
		$table.find("select[name='weight']").show();
	} else if (actType == "finish") {
		$table.find("input[name]").attr("disabled","disabled").removeClass("userModifiableInput");
		$table.find("input[name='weight']").show();
		$table.find("select[name='weight']").hide();
	};
}

// 点击修改评委信息按钮
$("#modifyUserMsg").click(function(){
	confirmBox("确定要修改评委信息吗?","sureModifyUserMsg()");
})

// 确认修改评委信息
function sureModifyUserMsg(){

	var $user = $("#userInformation");
	var name = $user.find("input[name='name']").val();
	var account = $user.find("input[name='account']").val();
	var password = $user.find("input[name='password']").val();

	if (name == "") {
		alertBox("评委姓名不能为空!");
		return false;
	} else if (account == "") {
		alertBox("评委帐号不能为空!");
		return false;
	} else if (password == "") {
		alertBox("评委密码不能为空!");
		return false;
	} else{
		var id = $user.find("input[name='id']").val();
		var weight = $user.find("select[name='weight']").val();
	};

	var msg = new FormData();
	msg.append("id","" + id);
	msg.append("name","" + name);
	msg.append("account","" + account);
	msg.append("password","" + password);
	msg.append("weight","" + weight);
	submit("/update/user","POST",msg,modifyUserSuccess);
}

// 修改评委信息与后台对接成功
function modifyUserSuccess(data){
	if (data.result == "success") {
		alertBox("评委信息已更新!");
	} else{
		alertBox("修改评委失败!");
	};
}

// 点击评委信息恢复默认按钮
$("#userMsgRestoreDefault").click(function(){
	confirmBox("确定要将评委信息表单恢复默认吗?","sureRestoreUserMsgDefault()");
})

// 确认将评委信息恢复默认
function sureRestoreUserMsgDefault(){
	var userId = $("#userId").val();
	submit("/user/" + userId,"GET",{},userMsgRestoreDefaultSuccessFun);
}

// 评委信息恢复默认与后台对接成功
function userMsgRestoreDefaultSuccessFun(data){
	if (data.result == "success") {
		setUserTable(data.user,"onGoing");
	} else{
		alertBox("恢复默认失败!");
	};
}



// 8.管理员查看、修改选手信息和得分情况-------------------------------------------------------------------
// 活动详细信息内点击“查看活动排名”按钮
$(".viewFinalRankings").click(function(){
	var msg = new FormData();
	var actId = $(".showActForm").find(".actId").val();
	msg.append("id","" + actId);
	submit("/getPlayerTable","POST",msg,getPlayerTableSuccess);
})

// 查看活动排名与后台对接成功
function getPlayerTableSuccess(data){
	if (data.result == "success") {
		// 清空排名列表里原有的排名信息
		$("#rankingListTable").find("tr[id!='playerTableHead']").remove();

		// 在表格内循环依次生成各选手信息
		var len = data.players.length;
		for (var i = 0; i < len; i++) {
			if (data.players[i].isMarking) {
				var $tr = $("<tr class='playerTr markedPlayer'></tr>");
				var $td3 = $("<td class='markTd'>是</td>");
			} else{
				var $tr = $("<tr class='playerTr unmarkedPlayer'></tr>");
				var $td3 = $("<td class='markTd'>否</td>");
			};
			$("#rankingListTable").append($tr);

			var $idInput =$("<input name='id' type='hidden' value='" + data.players[i].id + "' />")
			var $td1 = $("<td>" + data.players[i].rank + "</td>");
			var $td2 = $("<td>" + data.players[i].name + "</td>");
			var $td4 = $("<td>" + data.players[i].score + "</td>");
			$tr.append($idInput).append($td1).append($td2).append($td3).append($td4);

			$tr.attr("onclick","viewPlayer(this)");
		};

		$("#actMsgPartNav,.showActForm").hide();
		$("#playerInformation").hide();
		$(".rankingPartThirdNav").hide();
		$("#playerMsgToRankingTable").removeClass("rankingNavClickable").attr("ifClick","off");
		$("#rankingList").show();
		$("#actRanking").click();
		$("#rankingPart").show();
	} else{
		alertBox("查看活动排名失败!");
	};
}

// 选手排名头部导航条的a链接点击无效避免其默认跳转
$("#rankingNav").find("a").click(function(){
	return false;
})

// 从排名列表返回活动详细信息
$("#rankingTableToActPart span").click(function(){
	$("#rankingPart").hide();
	$(".rankingPartThirdNav").hide();
	$("#actMsgPartNav,.showActForm").show();
})

// 从选手详细信息返回排名列表
$("#playerMsgToRankingTable span").click(function(){
	$(".rankingPartThirdNav").hide();
	$("#playerInformation").hide();
	$("#playerMsgToRankingTable").removeClass("userListNavClickable").attr("ifClick","off");
	$("#rankingList").show();
})

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

// 点击某个选手查看详情
function viewPlayer(point){
	var playerId = $(point).find("input[name='id']").val();
	submit("/player/" + playerId,"GET",{},viewPlayerSuccessFun);
}

// 查看选手详情与后台对接成功
function viewPlayerSuccessFun(data){
	if (data.result == "success") {

		// 为选手基本信息赋值
		setPlayerBasicMsg(data.player);

		if ($(".showActForm").attr("id") == "onGoingActPart") {
			$("#playerImg").attr("on-off","on");
			$("#playerName").removeAttr("readonly");
			$("#playerDetails").removeAttr("readonly");
			$(".playerMsgOperationBtn").show();
		} else if ($(".showActForm").attr("id") == "finishActPart") {
			$("#playerImg").attr("on-off","off");
			$("#playerName").attr("readonly","readonly");
			$("#playerDetails").attr("readonly","readonly");
			$(".playerMsgOperationBtn").hide();
		};

		$("#rankingList").hide();
		$("#playerMsgToRankingTable").addClass("rankingNavClickable").attr("ifClick","on");
		$(".rankingPartThirdNav").show();
		$("#playerInformation").show();

		submit("/player/detail/" + data.player.id,"GET",{},viewPlayerDetailsSuccess);
	} else{
		alertBox("查看选手详情失败!");
	};
}

// 查看选手得分详细与后台对接成功
function viewPlayerDetailsSuccess(data){
	if (data.result == "success") {
		if (data.player.score) {
			$("#scoreSpan").text(data.player.score);
		} else{
			$("#scoreSpan").text("");
		};
		if (data.player.rank) {
			$("#rankSpan").text(data.player.rank);
		} else{
			$("#rankSpan").text("");
		};

		$table = $("#detailedScoreOfPlayer");
		$table.find(".userScoreTr").remove();
		var scoresLen = data.scores.length;
		var usersLen = data.users.length;
		for (var i = 0; i < scoresLen; i++) {
			var $tr = $("<tr class='userScoreTr'></tr>");
			var $td1 = $("<td>" + data.scores[i].name + "</td>");
			var $td2 = $("<td>" + data.scores[i].weight + "</td>");
			var $td3 = $("<td>" + data.scores[i].score + "</td>");
			$table.append($tr);
			$tr.append($td1).append($td2).append($td3);
		};
		for (var i = 0; i < usersLen; i++) {
			var $tr = $("<tr class='userScoreTr'></tr>");
			var $td1 = $("<td>" + data.users[i].name + "</td>");
			var $td2 = $("<td>" + data.users[i].weight + "</td>");
			var $td3 = $("<td class='no-score'>未评分</td>");
			$table.append($tr);
			$tr.append($td1).append($td2).append($td3);
		};
	} else{
		alertBox("获取选手得分详细失败!");
	};
}

// 为选手基本信息赋值
function setPlayerBasicMsg(player){
	$("#playerId").val(player.id);
	if (player.url) {
		$("#playerImg").text("");
		$("#playerImg").css({"background-image":"url(" + player.url + "?t=" + new Date().getTime() + ")"});
	} else{
		$("#playerImg").css({"background-image":"url('')"});
		$("#playerImg").text("上传照片");
	};
	$("#playerImgInput").val("");
	$("#playerName").val(player.name);
	$("#playerDetails").val(player.details);
}



// 选手详细信息插入图片
$("#playerImg").attr("onclick","uploadPlayerImg(this)");
$("#playerImgInput").attr("onchange","showFile(this,'#playerImg','#playerImgInput')");

function uploadPlayerImg(point){
	if ($(point).attr("on-off") == "on") {
		uploadImg(point,"#playerImgInput");
	};
}

// 提交修改选手信息
$("#modifyPlayerMsg").click(function(){
	confirmBox("确认修改选手信息吗?","sureModifyPlayerMsg()");
})

// 确定修改选手信息
function sureModifyPlayerMsg(){
	var name = $("#playerName").val();
	var details = $("#playerDetails").val();

	if (name == "") {
		alertBox("选手姓名不能为空!");
		return false;
	} else if (details == "") {
		alertBox("选手信息不能为空!");
		return false;
	};

	var $form = $("#playerInformation");
	var msg = new FormData($form[0]);
	submit("/update/player","POST",msg,modifyPlayerSuccess);
}

// 修改选手信息与后台对接成功
function modifyPlayerSuccess(data){
	if (data.result == "success") {
		alertBox("修改选手信息成功!");
	} else{
		alertBox("修改选手信息失败!");
	};
}

// 恢复默认选手信息
$("#playerMsgRestoreDefault").click(function(){
	confirmBox("确认要将选手信息恢复默认吗?","sureRestorePlayerMsgDefault()")
})

// 确认将选手信息恢复默认
function sureRestorePlayerMsgDefault(){
	var playerId = $("#playerId").val();
	submit("/player/" + playerId,"GET",{},playerMsgRestoreDefaultSuccessFun);
}

// 选手信息恢复默认与后台对接成功
function playerMsgRestoreDefaultSuccessFun(data){
	if (data.result == "success") {
		// 为选手基本信息赋值
		setPlayerBasicMsg(data.player);
	} else{
		alertBox("恢复默认失败!");
	};
}



// *********************************添加新函数时注意在该文件头部说明处补充********************************