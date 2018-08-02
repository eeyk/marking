


// ***********************************************登录函数************************************************
// 主要包含以下几个功能:  1.调整登录框  2.验证部分;  3.登录;



// 1.调整登录框-------------------------------------------------------------------------------------------
function adjustPosition(){
	// 平稳退化，检测#centerBox是否存在
	if ($("#centerBox").length <= 0) {
		return false;
	};

	if ( ($(window).height() - parseInt($("#centerBox").css("height"))) / 2 > 10 ) {
		$("#centerBox").css({"margin-top":($(window).height() - parseInt($("#centerBox").css("height"))) / 2 + "px", "margin-left":($(window).width() - parseInt($("#centerBox").css("width"))) / 2 + "px"});
	} else{
		$("#centerBox").css({"margin-top":"10px", "margin-left":($(window).width() - parseInt($("#centerBox").css("width"))) / 2 + "px"});
	};

	return true;
}

// 页面加载时先将登录框隐藏，调整好位置后再显示，避免出现抖动影响用户体验
$("#centerBox").hide();
function windowOnloadAdjustPosition(){
	if (adjustPosition()) {
		$("#centerBox").show();
	};
}

// window.onload时触发
addLoadEvent(windowOnloadAdjustPosition);

// 窗口大小改变时触发
$(window).resize(adjustPosition);



// 2.验证部分---------------------------------------------------------------------------------------------
function validate(){
    if ($("#account").val() == "") {
    	alertBox("请输入用户名!");
    	return false;
    } else if ($("#password").val() == "") {
    	alertBox("请输入密码!");
    	return false;
    } else{
    	return true;
    };
}



// 3.登录-------------------------------------------------------------------------------------------------
// 登录框聚焦时按回车响应登录操作
$("#loginForm").keydown(function() {
    if (event.keyCode == "13") {  //keyCode=13是回车键；数字不同代表监听的按键不同
    	$("#loginBtn").click();
    }
});

$("#loginBtn").click(function(){
	if (validate()) {
		var msg = new FormData($("#loginForm")[0]);
		submit("/login","POST",msg,loginRedirect);
	}
})

// 登录对接success后执行的函数
function loginRedirect(data){
	if (data.result == "success") {
		window.location.href = data.url;
	}else if (data.result == "pswError"){
		alertBox("密码错误，请重新输入!");
	}else if (data.result == "non-existent") {
		alertBox("该帐号不存在!");
	} else if (data.result == "identityAbnormality") {
		alertBox("登录身份异常!");
	} else {
		alertBox("异常错误!");
	}
}



// *********************************添加新函数时注意在该文件头部说明处补充********************************