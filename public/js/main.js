$("#menu").hover(showMe,hiddenMe);
function showMe(){
	$(".cap").show();
}
function hiddenMe(){
	$(".cap").hide();
}

$("#newAct").click(function(eve){
	var tar = eve.target;
	$("#newAct").css({"backgroud-color":"#ebf7f8"});
	$("newAct").find("span").css({"backgroud-image":"url('../image/create2.png')"});
})

//弹框提示
function useMsgBox(setting){	
	
	var defaultSetting={ 
	    warning:"异常错误!", 
	    ifno:false
	}; 
	$.extend(defaultSetting,setting); 
	if (defaultSetting.ifno) {
		$("#no").show();
	};

	$("#msgBox").show();
	$("#msgBody").html(defaultSetting.warning);
	mask();
	$('html,body').addClass('ovfHiden');
}

//关闭弹框
function closeMsgBox(){
	$("#msgBox").hide();
	$("#msgBody").html("");
	$("#msgBody").css({"height": "14px","padding":"38px 0","line-height": "14px"});
	$("#mask").remove();
	$('html,body').removeClass('ovfHiden');
}



//在页面内添加遮罩层
function mask() {
    $('<div id="mask" style="width: ' + ($(window).width()+100)/100 + 'rem; '
        + 'height: ' + ($(window).height()+100)/100 + 'rem;"></div>').appendTo("body");
}

// 浏览器窗口大小发生改变
$(window).resize(function(){
   if($("#mask").length > 0) {
   		$("#mask").css({"width":($(window).width()+100)/100+"rem","height":($(window).height()+100)/100+"rem"});
   }  
});

// 统一提交函数
function submit(param_list){

	function func_name(){};

	var default_param = { 
	    method:"POST", 
	    target:"#",
	    id:"",
	    falseMsg:"对接错误!",
	    success_func_name:func_name
	}; 
	$.extend(default_param , param_list);

	var data = new FormData($("#"+default_param.id)[0]);

	// reWirte:是否需要被覆盖;  realMethod:覆盖前方法;
	var reWirte = false;
	var realMethod = default_param.method;

	// 待完善
	switch(default_param.method){

		case "PATCH":
		case "DELETE": {
			reWirte = true;
			default_param.method = "GET";
		};  break;

		case "POST": {
			// token安全验证
			$.ajaxSetup({
			    headers: {
			        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			    }
			});
		};  break;

		default:{};
	}

	$.ajax({
		type: default_param.method,
		url: default_param.target,
		data: data,
		dataType: "json",
		cache:false,
	    processData:false,
	    contentType:false,

		// 覆盖标头
		beforeSend: function(reWirte,realMethod,request) {
	        if (reWirte == true) {
	        	request.setRequestHeader("X-HTTP-Method-Override",realMethod);
	        };
     	},

		success: function (data) {
			default_param.success_func_name(data);
		},
		// error:function(){
		// 	alert(default_param.falseMsg);
		// }
		error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(XMLHttpRequest.status);
            alert(XMLHttpRequest.readyState);
            alert(textStatus);
            // alert(XMLHttpRequest.responseText);
        }
	});
}

// 退出登录
$("#exit").click(function(){
	submit({method:"DELETE",target:"logout",falseMsg:"退出异常!",success_func_name:exit_redirect});
});

// 退出登录success
function exit_redirect(data){
	if (data.status == true) {
		window.location.href = data.url;
	}else {
		useMsgBox({warning:"退出登录失败!"});
	}
}