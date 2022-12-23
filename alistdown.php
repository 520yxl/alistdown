<?php
//alist网址
$alist_url='https://www.xxx.com';
// alist账户
$Username = 'admin';
// alist密码
$Password = 'admin123456';

function teacher_curl($url, $paras = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if (isset($paras['Header'])) {
        $Header = $paras['Header'];
    } else {
        $Header[] = "Accept:*/*";
        $Header[] = "Accept-Encoding:gzip,deflate,sdch";
        $Header[] = "Accept-Language:zh-CN,zh;q=0.8";
        $Header[] = "Connection:close";
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $Header);
    if (isset($paras['ctime'])) { // 连接超时
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $paras['ctime']);
    } else {
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    }
    if (isset($paras['rtime'])) { // 读取超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $paras['rtime']);
    }
    if (isset($paras['post'])) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paras['post']);
    }
    if (isset($paras['header'])) {
        curl_setopt($ch, CURLOPT_HEADER, true);
    }
    if (isset($paras['cookie'])) {
        curl_setopt($ch, CURLOPT_COOKIE, $paras['cookie']);
    }
    if (isset($paras['refer'])) {
        if ($paras['refer'] == 1) {
            curl_setopt($ch, CURLOPT_REFERER, 'http://m.qzone.com/infocenter?g_f=');
        } else {
            curl_setopt($ch, CURLOPT_REFERER, $paras['refer']);
        }
    }
    if (isset($paras['ua'])) {
        curl_setopt($ch, CURLOPT_USERAGENT, $paras['ua']);
    } else {
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36");
    }
    if (isset($paras['nobody'])) {
        curl_setopt($ch, CURLOPT_NOBODY, 1);
    }
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (isset($paras['GetCookie'])) {
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $result = curl_exec($ch);
        preg_match_all("/Set-Cookie: (.*?);/m", $result, $matches);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $headerSize); //状态码
        $body = substr($result, $headerSize);
        $ret = [
            "Cookie" => $matches, "body" => $body, "header" => $header, 'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);
        return $ret;
    }
    $ret = curl_exec($ch);
    if (isset($paras['loadurl'])) {
        $Headers = curl_getinfo($ch);
        if (isset($Headers['redirect_url'])) {
            $ret = $Headers['redirect_url'];
        } else {
            $ret = false;
        }
    }
    curl_close($ch);
    return $ret;
}


//获取token
$token=teacher_curl($alist_url."/api/auth/login",[
    'post'=>[
        'Username'=> $Username,
        'Password'=> $Password
    ]
]);

$token=json_decode($token,true)['data']['token'];
// print($token);

//接收post参数。
$path = $_GET["d"];
$password = $_GET["p"];
if(empty($path)){
    print_r("文件路径不能空");
}else{
$raw_url=teacher_curl($alist_url."/api/fs/get",[
    'post'=>[
        'Authorization'=> $token,
        'path'=> $path,
        'password'=>$password
    ]
]);
$code=json_decode($raw_url,true)['message'];
// print_r($code);
if ($code == 'password is incorrect or you have no permission') {
    print_r("缺失密码参数");
    // print_r($code);
}elseif ($code="success") {
    $raw_url=json_decode($raw_url,true)['data']['raw_url'];
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header("Location:$raw_url"); 
    exit();
}

// print_r($raw_url);
}
?>