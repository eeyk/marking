


// *********************************************弹框组件函数**********************************************
// 自定义弹框 alertBox confirmBox 的js文件



$(function(){
	createAlertBox();
	$("#mask").css({"margin-top":"-" + $(".header").css("height")});
	$("#alertBox").css({"margin-top":($(window).height() - parseInt($("#alertBox").css("height"))) / 2 + "px", "margin-left":($(window).width() - parseInt($("#alertBox").css("width"))) / 2 + "px"});
	$(".alertBoxClose").attr("onclick","closeAlertBox()");
	$("#mask").attr("onclick","borderFlash()");
});

$(window).resize(function(){
   $("#alertBox").css({"margin-top":($(window).height() - parseInt($("#alertBox").css("height"))) / 2 + "px", "margin-left":($(window).width() - parseInt($("#alertBox").css("width"))) / 2 + "px"});
});

// 生成自定义弹框 alertBox
function createAlertBox(){
	var $div = $("<div id='mask'><ul id='alertBox' class='originBorder'><li id='alertBoxHeader'><label id='alertBoxClose' class='alertBoxClose'><span></span></label></li><li id='alertBoxBody'></li><li id='alertBoxFooter'><input class='alertBoxBtn alertBoxClose alertSure' id='alertBoxSure' type='button' value='确定'/><input class='alertBoxBtn alertBoxClose' id='alertBoxCancel' type='button' value='取消' /><input id='funBtn' type='hidden' /></li></ul></div>");
	$("body").append($div);
}

// alert 类型弹框关闭弹框
function closeAlertBox(){
	$("#mask").hide();
}

// confirm 类型弹框时点击确定时总的函数
function clickSure(){
	closeConfirmBox();
	// 点击隐藏按钮，执行具体函数
	$("#funBtn").click();
}

// confirm 类型弹框关闭弹框，并将样式和功能恢复为 alert 弹框类型
function closeConfirmBox(){
	$(".alertBoxClose").attr("onclick","closeAlertBox()");
	$("#mask").hide();
	$("#alertBoxSure").removeClass("confirmSure");
	$("#alertBoxCancel").hide();
}

// 相当于浏览器的 alert
function alertBox(msg){
	// 使页面的input框失焦
	$("input").blur();

	$("#alertBoxBody").text(msg);
	$("#mask").show();
}

// 相当于浏览器的 confirm , sureFunStr 为点击确定时具体要执行的函数
function confirmBox(msg,sureFunStr){
	// 使页面的input框失焦
	$("input").blur();

	$(".alertBoxClose").attr("onclick","closeConfirmBox()");
	$("#alertBoxSure").attr("onclick","clickSure()");
	$("#alertBoxSure").addClass("confirmSure");
	$("#alertBoxCancel").show();
	$("#alertBoxBody").text(msg);
	$("#mask").show();
	// 将隐藏按钮的点击事件设置为具体函数
	$("#funBtn").attr("onclick",sureFunStr);
}

function borderFlash(){
	setTimeout(function(){ $("#alertBox").addClass("flashBorder"); }, 100);
    setTimeout(function(){ $("#alertBox").removeClass("flashBorder"); }, 200);
    setTimeout(function(){ $("#alertBox").addClass("flashBorder"); }, 300);
    setTimeout(function(){ $("#alertBox").removeClass("flashBorder"); }, 400);
}