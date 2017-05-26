<?php

namespace App\Func;


final class ErrCode
{
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

}