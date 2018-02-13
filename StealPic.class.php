<?php
//图片盗链类
require "http.php"; //引入http连接类
class StealPic
{
    protected $http; //http连接对象
    protected $url; //盗取链接
    protected $referer; //伪造referer信息
    protected $imgInfo; //获取的图片信息
    protected $error; //错误信息
    protected $ext; //获取图片的后缀
    static protected $allow_ext = array("jpg","jpeg","gif","png"); //允许链接后缀类型

    public function __construct($url,$referer){
        /*判断链接是否为图片*/
        $ext = substr($url,strrpos($url,".")+1); //获取链接后缀
        if(!in_array($ext,SELF::$allow_ext)){
            $this->error = "错误的链接类型";
            $this->imgInfo = -1; //若链接类型错误返回-1
            return;
        }

        $this->ext = $ext;
        $this->http = new Http($url);
    }

    /**
     * 拼接referer头
     * @param $referer 设置的refer头信息
     */
    protected function setReferer($referer){
        $this->referer = "Referer:".$referer;
    }

    /**
     * 获取错误信息
     */
    public function getError(){
        return $this->error;
    }

    /**
     * 获取盗取图片信息
     */
    public function getImg(){
        $this->http->setHead($this->referer);
        $this->imgInfo = substr(strstr($this->http->get(),"\r\n\r\n"),4);
        return $this->imgInfo;
    }
}

//$stealPic = new StealPic("http://imgsa.baidu.com/forum/w%3D580/sign=56819b08b7315c6043956be7bdb0cbe6/c5c451da81cb39db0082504bdb160924ab183068.jpg","http://tieba.baidu.com/p/5338203749");
//file_put_contents("images/beautiful.jpg",$stealPic->getImg());