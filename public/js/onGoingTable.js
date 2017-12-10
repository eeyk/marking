


// 结束活动
function ifEndAct(actId){
	useMsgBox({warning:"是否结束活动?",ifno:true});
	$("#yes").attr("onclick","endAct(\'"+actId+"\')");
}

// 点击确认按钮结束活动
function endAct(actId){
	submit({method:"DELETE",target:"delete/"+actId,falseMsg:"结束活动异常!",success_func_name:endAct_redirect});
	// 关闭弹窗
	closeMsgBox();
}

// 结束活动success
function endAct_redirect(data){
	if (data.status == true) {
		window.location.href = data.url;
	}else {
		useMsgBox({warning:"结束活动失败!"});
	}
}



// 查看正举办活动信息
function lookOngoingAct(id){
	submit({method:"GET",target:"activity/"+id,falseMsg:"结束活动异常!",success_func_name:look});
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