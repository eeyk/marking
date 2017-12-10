


window.onload=function(){
	center();//始终居中
	createCode();//更新二维码
};
window.onresize=center;

// 居中
function center(){
	$("#loginBox").show();
    var windowH = $(window).height();
    var windowW = $(window).width();
    $("#loginBox").css("margin-top", (windowH-300)/2/100+"rem");
    $("#loginBox").css("margin-left", (windowW-400)/2/100+"rem");
}

//登录
function login(){
	if (inspect()) {
		if (validate()) {
			submitLoginMsg();
		};
	};
	createCode();
    $("#verification").val("");
}

//检查表单必填项项是否填写完整
function inspect(){
	if ($("#account").val() == "") {
		useMsgBox({warning:"请输入帐号!"});
		return false;
	};
	if ($("#password").val() == "") {
		useMsgBox({warning:"请输入密码!"});
		return false;
	};
	if ($("#verification").val() == "") {
		useMsgBox({warning:"请输入验证码!"});
		return false;
	};
	return true;
}

var code; //在全局定义验证码
//产生验证码
function createCode(){
     code = "";
     var codeLength = 4;
     var random = new Array(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R',
     'S','T','U','V','W','X','Y','Z');
     for(var i = 0; i < codeLength; i++) {
        var index = Math.floor(Math.random()*36);
        code += random[index];
    }
    $("#verificationCode").val(code);
}

//校验验证码
function validate(){
    var inputCode = $("#verification").val().toUpperCase(); //取得输入的验证码并转化为大写
    if(inputCode != code ) {
        useMsgBox({warning:"验证码错误!"});
        return false;
    }
    else {
        return true;
    }
}

//提交登录信息
function submitLoginMsg(){
	// var login_msg = { account : $("#account").val() , password : $("#password").val() , rememberMe : $("#rememberMe").val() };
	// submit({method:"POST",target:"/login",msg:login_msg,falseMsg:"登录异常!",success_func_name:login_redirect});
	submit({method:"POST",target:"/login",id:"myForm",falseMsg:"登录异常!",success_func_name:login_redirect});
}

// 登录success
function login_redirect(data){
	if (data.status == true) {
		window.location.href = data.url;
	}else {
		useMsgBox({warning:"用户名密码错误，请重新输入!"});
	}
}
