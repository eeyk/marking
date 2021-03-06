        {
            在'status'为false的时候返回一个'msg'提示错误
            为评委添加一个修改密码的功能
            未结束活动添加一个查看分组名称，参赛者姓名，分数，排名 没有排名，测试通过
            未评分选手返回对应的未评分评委的数量 测试通过
            要返回选手的排名
            要添加选手分组的名字字段    测试通过
            返回所有选手信息时，将同一组别的放在一起    测试通过
        }

### 用户主页面接口(从哪个页面触发这个接口  #从主页面即'xxx.com/'这里直接由后台跳转)
Route::get('/','SessionsController@index')->name('index');

response:

    用户未登录：return redirect()->guest('login'); #跳转至登陆页面
    用户为评委：return redirect('getAllPlayers');  #跳转至该评委隶属的活动页面
    用户为主办方： return redirect('admin');       #跳转至管理员页面



### 用户登录(是否需要判断用户的身份，即为评委或管理员 #等商量下看是否需要区分裁判登陆页面和管理员登录页面)
Route::post('/login', 'SessionsController@store')->name('login');

request post:

    {
      "account":"xxxx",
      "password":"xxxx",
    }

response: json

成功时：

    'status':true;
    'url':跳转链接
失败时：

     'status':false;
     'url':跳转链接

### 退出登录(✔)
Route::delete('logout','SessionsController@destroy')->name('logout');

response:

    return redirect('login');



### 获取所有选手（该评委对应的选手,也是评委的主页面）
Route::get('/getAllPlayers','UsersController@getAllPlayers')->name('getAllPlayers');

response:view('getAllPlayers')

{
  "players": {
    "group1": [
      {
        "id": 1,
        "name": "选手测试2",
        "details": "测试细节",
        "score": 78.82,
        "isMarking": "true",
        "group": 1,
        "img": "img/276f31ab2a0beede004197b524df176b.png",
        "groupName": null
      },
      {
        "id": 2,
        "name": "测试2",
        "details": "details2",
        "score": 0,
        "isMarking": "0",
        "group": 1,
        "img": "",
        "groupName": null
      }
    ],
    "group2": [
      {
        "id": 3,
        "name": "选手3",
        "details": "细节3",
        "score": 0,
        "isMarking": "0",
        "group": 2,
        "img": "",
        "groupName": null
      },
      {
        "id": 4,
        "name": "选手4",
        "details": "细节4",
        "score": 7.5,
        "isMarking": "0",
        "group": 2,
        "img": "",
        "groupName": null
      }
    ]
  },
  "activity": {
    "id": 1,
    "name": "测试活动",
    "details": "111",
    "img": "0"
  }


### 获取选手详情(是否要获取选手的img #url已经补上)
Route::get('player/{id}','UsersController@show')->name('playerDetail');

request post:

    {
      'id' :1         #要查看的选手id
    }

response:view('player')

{
  "player": {
    "details": "details1",
    "id": 1,
    "name": "测试1",
    "isMarking": "",
    "img": "",
    "score":
  }
}



### 评分(✔)
Route::post('marking/{id}','UsersController@postScore')->name('marking');

request post:

    {
        'id' :1         #选手id
        'score':80      #给选手评的分数
    }

response: json

成功时：

    'status':true;
    'url':
失败时：

     'status':false;
     'url':



### 查看单组排名(单组排名是一组里面的各个队的排名还是每个选手的排名 #选手和队只是不同的说法)
Route::get('groupRank/{id}/{group}','UsersController@groupRank')->name('groupRank');

request post:

   {
     'id' :1         #要查看的活动id
     'group':1       #要查看的组别
   }

response:view('groupRank')

{
  "playerRank": [
    {
      "name": "测试1",
      "score": 78.82
    },
    {
      "name": "测试2",
      "score": 0
    }
  ]
}




### 查看组别混合排名(✔)
Route::get('rankall/{id}','UsersController@rankAll')->name('rankAll');

request post:

    {
      'id' :1         #要查看的活动id
    }

response:view(rankAll)

{
  "playerRank": [
    {
      "name": "测试1",
      "score": 78.82
    },
    {
      "name": "选手4",
      "score": 7.5
    },
    ...
  ]
}



### 管理员主页面(管理员主页面是哪一个 )
Route::get('admin','AdminController@admin')->name('admin');

response: view('admin')

{
  "activities": [
    {
      "id": 1,
      "name": "测试活动",
      "details": "111",
      "img": "0"
    }
  ],
  "oldActivities": [
    {
      "name": "admin",
      "id": 2,
      "details": "123456",
      "img": ""
    }
  ],

}


### 创建活动提交(缺少图片img #已经补上)
Route::post('create/activity','AdminController@postCreateActivity')->name('createActivity');

require post

    {
      'name' :xx      #活动名字
      'details':xx     #活动描述
      'img':
    }

response: json

    "url": "",
    "status": true



### 创建选手(✔)
Route::post('createPlayer','AdminController@createPlayer')->name('createPlayer');

require post

    {
      'id':1     #活动id
      一份规范填写的excel
    }

response :  json

    "url": "",
    "status": true



### 创建评委(✔)
Route::post('createUser','AdminController@createUser')->name('createUser');

require post

    {
      'id':1     #活动id
      一份规范填写的excel
    }

response :  json

    "url": "",
    "status": true



### 获取更新活动页面(✔)
Route::get('update/activity/{id}','AdminController@getUpdateActivity')->name('updateActivity');

require post

    {
      'id':1     #活动id
    }
response: view('updateActivity')

{
  "activity": {
    "id": 1,
    "name": "测试活动",
    "details": "111",
    "img": "0"
  }
}


### 更新活动提交(✔)
Route::post('update/activity/{id}','AdminController@updateActivity')->name('updateActivity');

require post

    {
      'id':1         #活动id
      'name':xx      #活动名字
      'details':xx   #活动信息
      'img':xx       #活动照片
    }
response:

"url":
"status":true



### 获取更新选手页面(✔)
Route::get('update/player/{id}','AdminController@getUpdatePlayer')->name('updatePlayer');

require post

    {
      'id':1     #选手id
    }
response: view('updatePlayer')

{
  "player": {
    "activity_id": "1",
    "id": 1,
    "name": "测试1",
    "details": "details1",
    "score": 78.82,
    "isMarking": 1,
    "group": "1",
    "img": ""
  }
}


### 更新选手提交(选手照片是否是在excel文件里上传 #应该是需要手动为每个选手添加图片的)
Route::poat('update/player/{id}','AdminController@updatePlayer')->name('updatePlayer');

require post

    {
      'id':1         #选手id
      'name':xx      #选手名字
      'details':xx   #选手信息
      'url':xx       #选手照片
    }
response:

"url":
"status":true


### 获取更新评委页面(✔)
Route::get('update/user/{id}','AdminController@getUpdateUser')->name('updateUser');

require post

    {
      'id':1         #评委id
    }
response: view('updateUser')

{
  "user": {
    "id": 1,
    "activity_id": "",
    "name": "admin",
    "details": "",
    "weight": 0,
    "level": ""
  }
}


### 更新评委提交(✔)
Route::post('update/user/{id}','AdminController@updateUser')->name('updateUser');

require post

    {
      'id':1         #评委id
      'name':xx      #评委名字
      'details':xx   #评委信息
      'weight':xx    #评委权重
      'account':xx   #评委账号
      'password':xx  #评委密码
    }
response:

"url":
"status":true



### 查看未结束活动信息(评委和选手是否一一列出 #按照UI图来确定)
Route::get('activity/{id}','AdminController@showActivity')->name('showActivity');

require post：

    {
      'id':1     #活动id
    }
response: view('activity')

{

  "activity": [
    {
      "id": 1,
      "name": "测试活动",
      "details": "111",
      "img": "0"
    }
  ],
  "users": [
    {
      "id": 2,
      "name": "excel1"
      "weight":
    },
    {
      "id": 3,
      "name": "excel2"
      "weight":
    },
    ...
  ],
  "players": [
    {
      "id": 1,
      "name": "测试1",
      "score": 78.82
    },
    {
      "id": 2,
      "name": "测试2",
      "score": 0
    },
    ...
  ]
}



### 查看所有已评分选手(✔)
Route::get('marked/player','AdminController@markedPlayer')->name(markedPlayer);

require post:

    {
      'id':1    #活动id
    }

response: view(markedPlayer)

{
  "players": [
    {
      "id": 1,
      "name": "测试1",
      "score": 78.82
    }
  ]
}


### 查看已评分选手得分详细(✔)
Route::get('marked/player/detail','AdminController@markedPlayerDetail')->name(markedPlayerDetail);

require post:

    {
      'id':1    #选手id
    }

response: view(markedPlayerDetail)

{
    "player": {
      "id": 1,
      "name": "选手测试2",
      "score": 78.82
    },
  "score": [
    {
      "user_id": "2",
      "score": 50,
      "weight": 0.5,
      "name": "excel1"
    },
    {
      "user_id": "3",
      "score": 70,
      "weight": 0.5,
      "name": "excel2"
    },
    ...
  ]
}



### 查看所有未完成评分选手(✔)
Route::get('unMarked/player','AdminController@unMarkedPlayer')->name(unMarkedPlayer);

require post:

    {
      'id':1    #活动id
    }

response: view(unMarkedPlayer)

{
  "players": [
    {
      "id": 2,
      "name": "测试2"
      "num": "3" 表示有3个评委没有对他评分
    },
    {
      "id": 3,
      "name": "选手3"
      "num": "3" 表示有3个评委没有对他评分
    },
    {
      "id": 4,
      "name": "选手4"
      "num": "3" 表示有3个评委没有对他评分
    }
  ]
}



### 查看未完成评分选手得分详细(✔)
Route::get('unMarked/player/detail','AdminController@unMarkedPlayerDetail')->name(unMarkedPlayerDetail);

require post:

    {
      'id':1    #选手id
    }

response: view(unMarkedPlayerDetail)

{
    "player": {
      "id": 2,
      "name": "测试2",
      "score": 0
    },
  "users": [
    {
      "id": 2,
      "name": "excel1",
      "weight": 0.5,
      "isMarking": false,
      "score": 0
    },
    {
      "id": 3,
      "name": "excel2",
      "weight": 0.5,
      "isMarking": false,
      "score": 0
    },
    ...
  ]
}


### 结束活动(✔)
Route::delete('delete/{id}','AdminController@destroy')->name('deleteActivity');

require post：

    {
      'id':1     #活动id
    }
response:   json

    status:true,
    url:



### 恢复活动(✔)
Route::post('restore/{id}','AdminController@restore')->name('restoreActivity');

require post：

    {
      'id':1     #活动id
    }

response:

    status:true,
    url:





###以下待议(活动创建成功是该简单提示成功还是弹出对话框提供活动二维码，另二维码是在前端生成还是后台生成  #以下功能暂时不考虑)



### 分享活动
Route::get('share','AdminController@share')->name('share');

require post:

    {
      'id':1        #活动id
    }

response:

json:

    {
      'password':xx  #评委登陆密码
      'url':xx       #登陆地址
      'img':xx       #二维码的地址
    }



### 保存二维码
Route::get('saveImg','AdminController@saveImg')->name('ssaveImg')

response:

    return response()->download($img);
