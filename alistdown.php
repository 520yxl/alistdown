<?php
//alist网址
$alist_url='https://www.xxx.com';
// alist账户
$Username = 'admin';
// alist密码
$Password = 'admin123456';
// alist后台令牌
$alisttoken = "alist-66a103dc";

function alist_post($url,$paras ){
    $host = $url;
    $method = "POST";
    $headers = array();
    array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
    $querys = "";
    $bodys = $paras;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $host);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
    return curl_exec($curl);
}
//接收post参数。
$path = $_GET["d"];
$password = $_GET["p"];
//获取token
if(empty($alisttoken)){
    $token = alist_post($alist_url."/api/auth/login","Password=".$Password."&Username=".$Username);
    $token = json_decode($token,true)['data']['token'];
    
}else{
    $token = $alisttoken;
}


if(empty($path)){
    print_r("文件路径不能空");
}else{
    $path=urlencode($path);
$raw_url=alist_post($alist_url."/api/fs/get","Authorization=".$token."&password=".$password."&path=".$path."&refresh=true");
    // echo $raw_url;
$code=json_decode($raw_url,true)['message'];
if ($code !== "success") {
    echo $raw_url;
}else {
    $raw_url=json_decode($raw_url,true)['data']['raw_url'];
    Header( "HTTP/1.1 301 Moved Permanently" );
    Header("Location:$raw_url"); 
    exit();
}

}
?>
