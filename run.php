<?php
set_time_limit(0);
ignore_user_abort(true);
require dirname(__FILE__).'/settings.php';
$username = urlencode('');
$password = urlencode('');
$ol=curl_init('http://www.acfun.cn/online.aspx');
curl_setopt($ol,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ol,CURLOPT_COOKIE,$cookie);
$online=curl_exec($ol);
curl_close($ol);
if(json_decode($online,1)["success"]!=1){
$url = 'http://www.acfun.cn/login.aspx';
$data=["username"=>$username,"password"=>$password];
$ch = curl_init($url);
curl_setopt($ch,CURLOPT_HEADER,1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_REFERER,'http://www.acfun.cn/login/');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
$content = curl_exec($ch);
curl_close($ch);
preg_match_all('/Set-Cookie:(.*;)/iU',$content,$str);
foreach ($str[1] as $key) {
    if (strpos($key,'deleted') == false){
        $cookie1 .= $key;
    }
}
file_put_contents(dirname(__FILE__).'/settings.php','<?php'."\r\n".'$cookie='."'".$cookie1."';");
echo "===============================<br />已更新cookie,下次执行时将会继续挂机(签到不受影响)<br />";
}else{echo "===============================<br />level:".json_decode($online,1)["level"].'<br />online:'.json_decode($online,1)["duration"].'s<br />';}
$curl = curl_init('http://www.acfun.cn/webapi/record/actions/signin?channel=0');
curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
curl_setopt($curl, CURLOPT_REFERER,'http://www.acfun.cn/menber/');
curl_setopt($curl,CURLOPT_COOKIE,$cookie);
curl_setopt($curl, CURLOPT_POST,1);
$sign = curl_exec($curl);
curl_close($curl);
if(json_decode($sign,1)["code"] === 410004){echo 'sign:今天您已经签到过了<br />===============================';}
elseif(json_decode($sign,1)["code"] === 200){echo 'sign:签到成功<br />===============================';}
else{echo 'sign:未知错误#'.json_decode($sign,1)["code"].'<br />===============================';}