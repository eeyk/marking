


// ***********************************管理员主页和评委主页一些公用函数************************************
// 主要包含以下几个功能:  1.页面加载完成后获取登录者信息;  2.注销登录，返回登录页面;
//                        3.页面头部搜索框相关函数;  4.左侧菜单栏相关函数;  5.获取活动列表相关函数；



// 1.页面加载完成后获取登录者信息-------------------------------------------------------------------------
addLoadEvent(function(){
	getIdentityMsg();
})

function getIdentityMsg(){
	submit("/getIdentityAction","GET",{},showIdentityMsg);
}



// 2.注销登录，返回登录页面-------------------------------------------------------------------------------
$("#cancellation").click(function(){
    confirmBox("确认要注销吗?","cancellationfun()");
})

function cancellationfun(){
    window.location.href = "/logout";
}



// 3.页面头部搜索框相关函数-------------------------------------------------------------------------------
// 页面头部搜索框聚焦时按回车响应查询操作
$("#searchInput").keydown(function() {
    if (event.keyCode == "13") {  //keyCode=13是回车键；数字不同代表监听的按键不同
    	// 活动查询前的检查，避免编辑未保存等情况
    	if (!sureSearch()) {
    		return false;
    	};
    	
    	var keyWord = this.value;
        if (checkedKeyword(keyWord)) {
        	// 不同html页面关联的js文件不同，因而search函数也不同
        	search(keyWord);
        };
    }
});

// 页面头部点击搜索图标响应查询操作
$("#searchImg").click(function(){
	// 活动查询前的检查，避免编辑未保存等情况
	if (!sureSearch()) {
		return false;
	};
	
	// 平稳退化，检测#searchInput是否存在
	if ($("#searchInput").length <= 0) {
		return false;
	};

	var keyWord = $("#searchInput").val();
	if (checkedKeyword(keyWord)) {
		// 不同html页面关联的js文件不同，因而search函数也不同
    	search(keyWord);
    };
});

// 页面头部根据搜索框的输入内容是否为空
function checkedKeyword(keyWord){
	// 文本框失焦，避免出现弹框或其他情况下仍能继续输入的情况
	$("#searchInput").blur();

	if (keyWord != "") {
		return true;
	} else{
		alertBox("请输入要查询的关键词!");
		return false;
	};
}



// 4.左侧菜单栏相关函数-----------------------------------------------------------------------------------
// 点击左侧menu菜单
$("#menu li").click(function(){
	// 检测所点击的选项是否原本就被选中
	if (ifchecked(this)) {
		return false;
	};
	// 检测所点击的选项是否是不可点击状态下的活动内容栏或选手信息栏
	var clickMenuLiId = $(this).attr("id");
	if ((clickMenuLiId == "actInformationForAdminLi" || clickMenuLiId == "playerInformationForUserLi") && $(this).attr("clickable") == "off") {
		return false;
	};

	if (($(".checked").length > 0) && ($(".checked").attr("id") == "createActivityLi")) {  // 原选中选项为创建活动
		var $form = $("#createActPart");
		if (($form.find(".actName").val() != "") || ($form.find(".actDetails").val() != "") || ($form.find(".actImgInput").val() != "") || ($form.find("#playerFile").val() != "") || ($form.find("#userFile").val() != "")) {  // 表单正处于编辑当中
			// 利用临时标记标明欲切换的模块
			$("#menu li").removeClass("switchFromCreateAct");
			$(this).addClass("switchFromCreateAct");
			
			confirmBox("活动尚未创建，是否放弃编辑?","switchModuleFromCreateAct()");
		} else{
			// 切换模块
			switchModule(this);
		};
	} else if (($(".checked").length > 0) && ($(".checked").attr("id") == "actInformationForAdminLi")) {  // 原选中选项为活动详细信息
		if ($("#renewActBtn").css("display") == "block") {  // 正在修改活动
			// 利用临时标记标明欲切换的模块
			$("#menu li").removeClass("switchFromActInfo");
			$(this).addClass("switchFromActInfo");
			
			confirmBox("活动尚未保存，是否放弃修改?","switchModuleFromActInfo()");
		} else{
			// 切换模块
			switchModule(this);
		};
	} else{
		// 切换模块
		switchModule(this);
	};
})

// 放弃编辑创建活动后跳到其他的模块
function switchModuleFromCreateAct(){
	// 清空创建活动的表单，防止临时标记的选项不断点击循环激活弹框
	clearCreateActForm();
	$(".switchFromCreateAct").click();
}

// 放弃编辑更新活动后跳到其他的模块
function switchModuleFromActInfo(){
	// 先取消修改，防止不断因为正处于修改而循环激活弹框
	$("#cancelModifyActBtn").click();
	$(".switchFromActInfo").click();
}

// 页面加载完成后默认点击左侧menu菜单的第一个选项
addLoadEvent(function(){
	$("#menu li:first-of-type").click();
})

// 检测左侧菜单栏所点击的选项是否原本就被选中及是否可以重新选中
function ifchecked(point){
	var thisClass = $(point).attr("class");
	var thisClickable = $(point).attr("clickable");
	if (thisClass.indexOf("unchecked") == -1 && thisClass.indexOf("effectiveClick") == -1 && thisClickable != "on") {
		return true;
	};
}

// 点击左侧menu菜单时更换样式的函数
function menuLiStyleChange(point){
	$("#menu li.checked").removeClass("checked").addClass("unchecked");
	$(point).removeClass("unchecked").addClass("checked");
}

// 根据菜单点击的选项切换模块
function switchModule(point){
	// 更换样式
	menuLiStyleChange(point);

	$(".hoverable").attr("clickable","on");
	$(point).attr("clickable","off");

	// 清空搜索框
	$("#searchInput").val("");
	$("#searchKeyWordInput").val("");

	// 平稳退化，检测#searchInput是否存在
	if ($("#actList").length > 0) {
		$("#actList").html("");
	};

	switch($(point).attr("id")){
		// 管理员页面的菜单栏
		// 创建新活动
		case "createActivityLi":{
			$("#actList").hide();
			$("#onGoingActPart,#finishActPart").hide();
			$("#actMsgPartNav").hide();
			$("#userPart").hide();
			$("#userMsg").hide();
			$("#rankingPart").hide();
			recoveryStyle();
			$("#createActPart").show();
		}  break;
		// 正举办活动
		case "onGoingActivityLi":{
			$("#createActPart").hide();
			$("#onGoingActPart,#finishActPart").hide();
			$("#actMsgPartNav").hide();
			$("#userPart").hide();
			$("#userMsg").hide();
			$("#rankingPart").hide();
			recoveryStyle();
			getActList("onGoing");
			$("#actList").show();
		}  break;
		// 已结束活动
		case "finishActivityLi":{
			$("#createActPart").hide();
			$("#onGoingActPart,#finishActPart").hide();
			$("#actMsgPartNav").hide();
			$("#userPart").hide();
			$("#userMsg").hide();
			$("#rankingPart").hide();
			recoveryStyle();
			getActList("finish");
			$("#actList").show();
		}  break;
		// 活动内容栏
		case "actInformationForAdminLi":{
			$("#createActPart").hide();
			$("#actList").hide();
			$("#onGoingActPart,#finishActPart").hide();
			$("#userPart").hide();
			$("#userMsg").hide();
			$("#rankingPart").hide();
			$("#actMsgPartNav").show();
			$(".showActForm").show();
		}  break;
		// 评委页面的菜单栏
		// 评委活动信息
		case "actInformationForUserLi":{
			$("#rankingList").hide();
			$("#scoringPage").hide();
			userGetActMsg();
			$("#actMsg").show();
		}  break;
		// 活动结果
		case "scoringAreaLi":{
			$("#actMsg").hide();
			$("#scoringPage").hide();
			getActRanking();
			$("#rankingList").show();
		}  break;
		// 选手信息
		case "playerInformationForUserLi":{
			$("#rankingList").hide();
			$("#scoringPage").show();
		}  break;
	}
}



// 5.获取活动列表相关函数---------------------------------------------------------------------------------
// 获取相应类型的活动列表
function getActList(listType){
	submit("/getActivityList/" + listType,"GET",{},getActListSuccess);
}

// 获取相应类型的活动列表对接成功
function getActListSuccess(data){
	createActCard(data.activities);
}

// 在活动列表里创建活动卡片
function createActCard(activities){

	var len = activities.length;

	// 右上角活动类型小部件、按钮功能类型
	var $actType,$funcBtn,funcBtnClass;
	// 如果有活动的话，根据第一项活动的活动类型判断出该组活动属于“正举办”还是“已结束”，从而确定零部件类型
	if (len > 0) {
		if (activities[0].actType == "onGoing") {
			$actType = $("<span class='actType onGoingType sameFontSize'>正举办</span>");
			$funcBtn = $("<input class='cardBtn sameFontSize endingAct' type='button' targetState='finish' value='结束活动' />");
			funcBtnClass = "endingAct";
		} else if (activities[0].actType == "finish") {
			$actType = $("<span class='actType finishType sameFontSize'>已结束</span>");
			$funcBtn = $("<input class='cardBtn sameFontSize recoverAct' type='button' targetState='restore' value='恢复活动' />");
			funcBtnClass = "recoverAct";
		};
	};

	for (var i = 0; i < len; i++) {
		// 创建form表单
		var $form = $("<form  class='actCard'></form>");
	    $("#actList").append($form);

	    // 载入活动信息
	    $form.append("<input class='actId' name='id' type='hidden' value='" + activities[i].id + "' />");

	    var $cardActImgPart = $("<label class='cardActImgPart'></label>");
	    $cardActImgPart.css({"background-image":"url(" + activities[i].url + "?t=" + new Date().getTime() + ")"});
	    $cardActImgPart.append($actType.clone());
	    $form.append($cardActImgPart);

	    $form.append("<label class='cardActName'>" + activities[i].name + "</label>");
	    $form.append($funcBtn.clone().addClass(funcBtnClass));
	    $form.append("<input class='cardBtn sameFontSize viewAct' type='button' value='查看详情' />");
	};

	$("." + funcBtnClass).attr("onclick","modifyActState(this,'actList')");
	$(".viewAct").attr("onclick","viewAct(this)");
}



// *********************************添加新函数时注意在该文件头部说明处补充********************************
