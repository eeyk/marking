


// **************************************文件导入及图片预览组件函数***************************************
// 上传预览图片的 js 文件(上传文件的 input 和预览的区域都需要在 form 内)



// 上传图片( point 为自定义有点击上传功能的元素, idOrClassForInput 为上传文件的 input )
function uploadImg(point,idOrClassForInput){
	var $form = $(point).parents("form");
	$form.find(idOrClassForInput).click();
}

//上传文件的预览( idOrClassForPreview 为图片预览的区域, point、idOrClassForInput 为上传文件的 input )
function showFile(point,idOrClassForPreview,idOrClassForInput){
	var $form = $(point).parents("form");
	if (restrictFile(point,idOrClassForInput,"img")) {
		var file = ($form.find(idOrClassForInput).prop("files"))[0];
		var reader = new FileReader();
        reader.readAsDataURL(file);  
        reader.onload = function(){
			$form.find(idOrClassForPreview).css({"background-image":"url(" + this.result + ")","backgroundSize":"100% auto"});
			$form.find(idOrClassForPreview).html("");
        }
	};
}

//限制上传文件格式仅为要求的格式( point、idOrClassForInput 为上传文件的 input )
function restrictFile(point,idOrClassForInput,fileType){
	var $form = $(point).parents("form");
	var filepath = $form.find(idOrClassForInput).val();
   	var extStart = filepath.lastIndexOf(".");
    var ext = filepath.substring(extStart, filepath.length).toUpperCase();
    if ((fileType == "img" && ext != ".BMP" && ext != ".PNG" && ext != ".GIF" && ext != ".JPG" && ext != ".JPEG") || (fileType == "word" && ext != ".DOCX") || (fileType == "excel" && ext != ".XLS" && ext != ".XLSX")) {
    	alertBox("仅限于上传类型为“" + fileType + "”的文件!");
		resetFileInput(point,idOrClassForInput);
		$form.find(idOrClassForInput).val("");
    	return false;
    };
    return true;
}

//清除file控件里的文件
function resetFileInput(point,idOrClassForInput){
	var $form = $(point).parents("form");
	var $oldInput = $form.find(idOrClassForInput);
	$oldInput.after($oldInput.clone().val(""));
	$oldInput.remove();
}