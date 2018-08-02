


// ************************************所有地方都可以用的一些公用函数*************************************
// 一些自定义的函数:  1.根据类名取元素数组函数getElementsByClassName;  2.共享onload函数addLoadEvent;
//                    3.目标元素节点后插入新元素的函数insertAfter;  4.统一对接函数;



// 1.根据类名取元素数组函数getElementsByClassName---------------------------------------------------------
function getElementsByClassName(node,classname){
	if (node.getElementsByClassName) {  //如果传入节点已经存在了适当的getElementsByClassName函数
		return node.getElementsByClassName(classname);
	} else{
		var results = new Array();
		var elems = node.getElementsByTagName("*");
		for (var i = 0; i < elems.length; i++) {
			if (elems[i].className.indexOf(classname) != -1) {
				results[results.length] = elems[i];
			};
		};
		return results;
	};
}



// 2.共享onload函数addLoadEvent---------------------------------------------------------------------------
function addLoadEvent(func){
	var oldonload = window.onload;
	if (typeof window.onload != "function") {
		window.onload = func;
	} else{
		window.onload = function(){
			oldonload();
			func();
		}
	};
}



// 3.目标元素节点后插入新元素的函数insertAfter------------------------------------------------------------
function insertAfter(newElement,targetElement){
	var parent = targetElement.parentNode;
	if (parent.lastChild == targetElement) {  //如果目标元素是parent的最后一个子元素，就用appendChild方法把新元素追加到parent元素上
		parent.appendChild(newElement);
	} else{  //如果不是，就把新元素插入到目标元素和目标元素的下一个兄弟元素之间
		parent.insertBefore(newElement,targetElement.nextSibling);
	};
}



// 4.统一对接函数-----------------------------------------------------------------------------------------
// 使用示例:
//          var msg = new FormData($form[0]);
//          msg.append("add_modify","add");
//          msg.append("id","");
//          ("http://localhost:8080/updateMemberAction","POST",msg,add_success);
function submit(myurl,mytype,msg,myfun){

	// // url前缀
	// var urlPrefix = "http://localhost:8080";

	$.ajax({
        // url: urlPrefix + myurl,  
        url: myurl,
        type: mytype,
        data: msg,
        dataType: "JSON",
        processData: false,  
        contentType: false,
        success: function(data) {
            myfun(data);
        },
        error: function (err) {
            console.log(err);
            alertBox("数据获取异常!");
        }
    })
}



// *********************************添加新函数时注意在该文件头部说明处补充********************************