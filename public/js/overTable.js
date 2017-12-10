


// 恢复活动
function ifRecoveryAct(actId){
	useMsgBox({warning:"是否结束活动?",ifno:true});
	$("#yes").attr("onclick","recoveryAct(\'"+actId+"\')");
}

// 点击确定恢复活动
function recoveryAct(actId){
	submit({method:"GET",target:"delete/"+actId,falseMsg:"恢复活动异常!",success_func_name:recoveryAct_redirect});
	// 关闭弹窗
	closeMsgBox();
}

// 恢复活动success
function recoveryAct_redirect(data){
	if (data.status == true) {
		window.location.href = data.url;
	}else {
		useMsgBox({warning:"恢复活动失败!"});
	}
}

// 查看已结束活动信息
function lookOngoingAct(id){
	submit({method:"GET",target:"oldActivity/"+id,falseMsg:"结束活动异常!",success_func_name:look});
}

function look(){
	if (data.status == true) {
		window.location.href = data.url;
	}else {
		useMsgBox({warning:"跳转失败!"});
	}
}

// //查看排名
// $("#range").click(respond(e,"rankall/{id}"))

// //查看信息
// $("#check").click(respond(e,"activity/{id}"))

// // 响应函数
// function respond(e,url){
// 	var httpRequest;
// 	function exit(e){
// 		httpRequest = new XMLHttpRequest();
// 		httpRequest.onreadystatechange = handleResponse;
// 		httpRequest.open("get",url);
// 		httpRequest.send({"id":""+@{{$myAct->id}}});
// 	}

// 	function handleResponse(){
// 		if(httpRequest.readyState == 4 && httpRequest.status == 200){
// 	        var text = httpRequest.responseText;
// 	        var resultJson = eval("("+text+")");//把响应内容对象转成javascript对象
// 	        var view = resultJson.view;//未完成活动视图
// 		}
// 	}
// })