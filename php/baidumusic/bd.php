<?php
// http://api.94qing.com/
header("Content-Type: text/html; charset=utf-8");

$keyword='音乐大约在冬季 齐秦';   
$aa = substr($keyword,strpos($keyword,'音乐')+strlen('音乐'));//一个汉字2个位置
$song = substr($aa,0,strpos($aa,' ' ));
$singer = substr($aa,strpos($aa,' ' ));
$songstr = trim((string)$song);
$singerstr = trim((string)$singer);
$test1=getMusic($songstr,$singerstr);
echo $test1;
return;
$test2=getMusicUrl($test1);
$test3=getMusic($songstr,$singerstr);
$test4=getHQMusicUrl($test3);
$musicArray = array(Title=>$songstr, Description=>'歌手:'.$singerstr, MusicUrl=>$test2,HQMusicUrl=>$test4);
$resultStr = transmitMusic($object, $musicArray, $funcFlag);
echo $resultStr;

//音乐点播
function read_child($node)
{
    global $musicstr ;
    $children = $node->childNodes; //获得$node的所有子节点
    foreach($children as $e) //循环读取每一个子节点
    {
        /*if($e->nodeType == XML_TEXT_NODE) //如果子节点为文本型则输出
        {
        echo $e->nodeValue.---------.
        ;
        }*/
        if($e->nodeType == XML_ELEMENT_NODE&&$e->nodeName=='encode') //如果子节点为文本型则输出
        {
        $musicstr.=$e->nodeValue;
        }
        if($e->nodeType == XML_ELEMENT_NODE&&$e->nodeName=='decode') //如果子节点为文本型则输出
        {
        $musicstr.=$e->nodeValue.'|';
        }
        if($e->nodeType == XML_ELEMENT_NODE) //如果子节点为节点对象，则调用函数处理
        {
            read_child($e); //注意这里的是因为的这些方法都是写在微信的那个wechatCallbackapiTest类中的，所以得加才能调用到这些函数。
        }
    }
    return  $musicstr ;
}
 
function getMusic($song,$singer)
{
$dom = new DomDocument(); //创建 DOM对象
$myurl ='http://box.zhangmen.baidu.com/x?op=12&count=1&title='.$song.'$$'.$singer.'$$$$';
//echo $myurl;
$dom->load($myurl); //读取 XML文件
$root = $dom->documentElement; //获取 XML数据的根
//echo $root;
return read_child($root);
//return $b; //调用 read_child函数读取根对象
}
 
function getMusicUrl($url)
{
//  echo strpos($url,&).musciURL;
    return substr($url,0,strpos($url,'&'));
}
function getHQMusicUrl($url)
{
    //echo  strripos($url,&).HQmusicURL;
    return substr($url,strripos($url,http),strripos($url,'&')-strripos($url,http));
}

function transmitMusic($object, $musicArray, $flag = 0)
{
    $itemTpl = '<music><title></title>
        <description><!--[CDATA[%s]]--></description>
        <musicurl><!--[CDATA[%s]]--></musicurl>
        <hqmusicurl><!--[CDATA[%s]]--></hqmusicurl>
    </music>';
 
    $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);
 
    $textTpl = '<xml>
        <tousername><!--[CDATA[%s]]--></tousername>
        <fromusername><!--[CDATA[%s]]--></fromusername>
        <createtime>%s</createtime>
        <msgtype><!--[CDATA[music]]--></msgtype>
        $item_str
        <funcflag>%d</funcflag>
        </xml>';
 
    $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $flag);
    return $resultStr;
}    



?>