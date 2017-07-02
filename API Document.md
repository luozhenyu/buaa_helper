## Version: 2017/06/11 04:15:24


#### ChangeLog:
* 2017/06/11 04:15:24
  * 添加用户信息对应表以及城市信息的JSON，通知分三步骤获取
  * 新增JSON Web Token认证方式，目前两种方式均可认证
* 2017/06/08 15:08:39
  * 新增删除设备和测试通知提醒功能，notifyDevice仅供测试使用
* 2017/06/08 14:03:07
  * 用户支持查询和修改头像和多种用户信息
  * 新增客户端接受推送的API，需要上传终端ID
* 2017/05/29 22:28:08
  * 通知增加important属性，true为必读通知，false为普通通知
* 2017/05/28 21:32:50
  * 增加delete项
  * 如果通知被删，delete=true，deleted_at=int，否则，delete=false，deleted_at=null
* 2017/05/28 19:57:39
  * time全替换成unix timestamp
  * 删除部分冗余信息
* 2017/05/28 15:56:34
  * Notification路由显示未删除和软删除的通知
  * 删除原有deleted路由
  * 如果deleted_at属性不为空则此通知已经删除
* 2017/05/27 17:15:42
  * 增加软删除功能
  * 增加deleted,delete,restore路由
  * 增加stared_at和read_at字段


#### Tips
* Domain: https://api.ourbuaa.com/
* 请先用测试号 user:10000,pwd:123456 测试所有功能

#### [JSON Web Token介绍](http://www.jianshu.com/p/576dbf44b2ae)
1. /jwt/login?user=&password=获取token
2. 将你的token添加到header里 `Authorization: Bearer paste_your_token`
3. 使用带有token的header请求资源
4. 如果token过期，可在2周内使用header访问/jwt/refresh更新token
5. 检查是否过期可以将token用点分割的第二部分，用base64解码，得到exp属性，从而判断


#### API
**HTTP_POST**

`$this->any('/login', 'APIController@login');`
* params:
  * user:id,email,phone
  * password:string
* return:
  * errcode:integer
  * access_token:uuid
  * expires_in:seconds

`$this->any('/jwt/login', 'APIController@JWTLogin');`
* params:
  * user:id,email,phone
  * password:string
* return:
  * errcode:integer
  * token:string

`$this->any('/jwt/refresh', 'APIController@JWTRefresh');`
* header:
  * Authorization: Bearer paste_your_token
* return:
  * errcode:integer
  * token:string
  
`$this->any('/device', 'APIController@listDevice');`
* return:
  * errcode:integer
  * devices:
    * registrationID:string
    * updated_at:unix_timestamp
      
`$this->any('/device/create', 'APIController@createDevice');`
* params:
  * registration_id:string
* return:
  * errcode:integer
  * msg:string
    
`$this->any('/device/delete', 'APIController@deleteDevice');`
* params:
  * registration_id:string
* return:
  * errcode:integer
  * msg:string
  
`$this->any('/device/notify', 'APIController@notifyDevice');`
* params:
  * text:string
* return:
  * errcode:integer
  * msg:string  
    
`$this->any('/user/info', 'APIController@userInfo');`
* return:
  * errcode:integer
  * user:
    * avatar:string(url)
    * number:integer
    * name:string
    * department:integer
    * department_name:string
    * email:string
    * phone:integer
    * grade:integer
    * class:integer
    * political_status:integer
    * native_place:array(integer)
    * financial_difficulty:integer

`$this->any('/user/avatar', 'APIController@modifyUserAvatar');`
* 请先预处理图片，大小限制8MB，服务器将会把图片压缩为200px * 200px 的png格式，推荐处理图像为200px * 200px再上传。
* params:
  * upload:file
* return:
  * errcode:integer

`$this->any('/user/params', 'APIController@userParams');`
* return:
  * name:integer
  * display_name:string
  * property_values:
    * name:integer
    * display_name:string

`$this->any('/user/cities', 'APIController@cities');`
* return:
  * code:integer(province)
  * name:string
  * children:
    * code:integer(city)
    * name:string
    * children:
      * code:integer(area)
      * name:string
      * children:
    
`$this->any('/user/modify', 'APIController@modifyUserInfo');`
* params:
  * phone:integer(nullable)
  * email:string(nullable)
  * grade:integer(nullable)
  * class:integer(nullable)
  * political_status:integer(nullable)
  * native_place:array(integer) //parameters native_place=100100&native_place=100110&native_place=100111 will be treated as an array native_place=[100100,100110,100111]
  * financial_difficulty:integer(nullable)
* return:
  * errcode:integer

`$this->any('/notification', 'APIController@listNotification');`
* return:
  * errcode:integer
  * notifications:
    * id:integer
    * updated_at:unix_timestamp


`$this->any('/notification/{id}', 'APIController@showNotification');`
* url_params:
  * id:integer
* return:
  * errcode:integer
  * notification:
    * title:string
    * author:string
    * department:integer
    * department_name:string
    * department_avatar:string(url)
    * start_date:unix_timestamp
    * finish_date:unix_timestamp
    * excerpt:string
    * important:bool
    * read:bool
    * read_at:unix_timestamp
    * star:bool
    * stared_at:unix_timestamp
    * delete:bool
    * deleted_at:unix_timestamp

`$this->any('/notification/{id}/full', 'APIController@showFullNotification');`
* return:
  * errcode:integer
  * notification:
    * content:html
    * files:
          * hash:string
          * fileName:string
          * sha1:string
          * mime:string
          * size:integer
          * url:string

`$this->any('/notification/{id}/delete', 'APIController@deleteNotification');`
* url_params:
  * id:integer
* return:
  * errcode:integer
  * msg:’Deleted!’

`$this->any('/notification/{id}/restore', 'APIController@restoreNotification');`
* url_params:
  * id:integer
* return:
  * errcode:integer
  * msg:’Restored!’

`$this->any('/notification/{id}/read', 'APIController@read');`
* url_params:
  * id:integer
* return:
  * errcode:integer
  * msg:’Read!’

`$this->any('/notification/{id}/star', 'APIController@star');`
* url_params:
  * id:integer
* return:
  * errcode:OK
  * msg:’Stared!’

`$this->any('/notification/{id}/unstar', 'APIController@unstar');`
* url_params:
  * id:integer
* return:
  * errcode:integer
  * msg:’Unstared!’

`$this->any('/log', 'LogController@index');`
* return:
  * html 

`$this->any('/log/write', 'LogController@index');`
* params:
  * log:string
* return:
  * ok

`$this->any('/log/clear', 'LogController@index');`
* return:
  * ok 
#### ErrCode Reference
```php
//服务器异常
const SERVER_ERROR = -1;

//正常
const OK = 0;

//不合法
const CREDENTIALS_ERROR = 40001;
const ACCESS_TOKEN_INVALID = 40002;
const RESOURCE_NOT_FOUND = 40003;
const INDEX_ILLEGAL = 40004;
const FORM_ILLEGAL =40005;

//缺少参数
const USER_ID_MISSING = 41001;
const PASSWORD_MISSING = 41002;
const ACCESS_TOKEN_MISSING = 41003;
const INDEX_MISSING = 41004;
```