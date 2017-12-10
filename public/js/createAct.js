

//离开页面提示
$(window).bind('beforeunload',function(){ 
	return "您所编辑内容只有保存修改后才会上传到服务器，"; 
}); 

// 生成字符串名
function getName(preName,num){
	var numStr = ""+num;
	return preName.concat(numStr);
}

//保存二维码
function saveCode(){
	$.get("saveImg", function(data){
	  	$("#codeSaved").show();
	});
}

// 窗口大小变化，调整 遮罩层mask 大小
$(window).on("resize", function() {
    if($("#mask").length) {
        $("#mask").css({
            width: $(window).width(),
            height: $(window).height(),
            opacity: 0.25
        });
    }
});

//关闭创建活动成功弹框
function closeNewActMsg(url){
	$("#newActMsg").hide();
	$("#codeSaved").hide();
    window.location.href=url;
}

// 伪点击上传
function importClick(num) {
  	if (num==1) {
		$("#judgeField").click();
	} else if (num==2) {
		$("#playerField").click();
	} else{
		$("#imgField").click();
	};
}

//上传文件的预览和路径展示
function showFile(num){
	if (restrictFile(num)) {
		var file;
		if (num==1||num==2) {
			var fileShow;
			if (num==1) {
				file=document.getElementById("judgeField").files[0]; 
				fileShow="#jugdeFile";
			} else{
				file=document.getElementById("playerField").files[0]; 
				fileShow="#playerFile";
			};
            var reader=new FileReader();  
            reader.readAsDataURL(file);  
            reader.onload=function(e)  
            {  
                $(fileShow).val(this.result);
            }
		} else{
			file=document.getElementById("imgField").files[0];
			var reader=new FileReader();  
            reader.readAsDataURL(file);  
            reader.onload=function(e)  
            {
				$("#uploadPic").css({"background-image":"url(" + this.result + ")","backgroundSize":"250px 190px"});
            }
		};
	};
}

//限制上传文件格式
function restrictFile(which){
	switch(which){
		case 1:{
			var filepath = $("#judgeField").val();
		}break;
		case 2:{
			var filepath = $("#playerField").val();
		}break;
		default :{
			var filepath = $("#imgField").val();
		}
	}
   	var extStart = filepath.lastIndexOf(".");
    var ext = filepath.substring(extStart, filepath.length).toUpperCase();
    if (which==1||which==2) {
    	if (ext != ".XLSX") {
    		useMsgBox({warning:"仅限于上传后缀为“.xlsx”的Excel文件!"});
    		if (which==1) {
    			resetFileInput("#judgeField");
    			$("#jugdeFile").val("");
    		} else{
    			resetFileInput("#playerField");
    			$("#playerFile").val("");
    		};
        	return false;
    	};
    } else{
    	if (ext != ".BMP" && ext != ".PNG" && ext != ".GIF" && ext != ".JPG" && ext != ".JPEG") {
    		$("#msgBody").css({"height": "32px","padding":"29px 0","line-height":"16px"});
    		useMsgBox({warning:"仅限于上传后缀为“bmp,png,gif,jpeg,jpg”的图片文件!"});
    		resetFileInput("#imgField");
    		$("#imgField").val("");
        	return false;
    	};
    };
    return true;
}

//清除file控件里的文件
function resetFileInput(id){
	$(id).after($(id).clone().val(""));
	$(id).remove();
}

//点击创建活动按钮
function saveModify(){
	if (!inspect()) {
		return false;
	};
	$("#msgBody").css({"height": "32px","padding":"29px 0","line-height":"16px"});
	useMsgBox({warning:"请仔细核对活动信息，<br />是否确认开始举办活动?",ifno:true});
	var isClick = false;
   	$("#yes").click(function(){
   		submitActMsg();
   	});
}

//检查表单必填项项是否填写完整
function inspect(){
	if ($("#name").val() == "") {
		useMsgBox({warning:"请填写活动名称!"});
		return false;
	};
	if ($("#description").val() == "") {
		useMsgBox({warning:"请填写活动简介!"});
		return false;
	};
	if ($("#imgField").val() == "") {
		useMsgBox({warning:"请上传活动图片!"});
		return false;
	};
	return true;
}

//提交活动信息
function submitActMsg(){
	submit({method:"POST",target:"create/activity",id:"myForm",falseMsg:"创建活动发生异常异常!",success_func_name:create_redirect});
}

// 创建活动成功
function create_redirect(data){
	if (data.status == true) {
		window.location.href = data.url;
	}else {
		useMsgBox({warning:"创建活动失败!"});
	}
}