<?php
interface Proto{
    function conn($url); //http连接
    function get(); //get请求
    function post($body); //post请求
    function close(); //关闭连接
}

class Http implements proto{
    const NEWLINE = "\r\n";
    protected $urlInfo = null; //url解析信息
    protected $line = array(); //请求行
    protected $version = "HTTP/1.1"; //http协议版本
    protected $header = array(); //请求头
    protected $body = ""; //请求主体
    protected $errorno = -1; //错误号
    protected $errstr = ""; //错误字符串
    protected $fs = null; //连接变量
    protected  $content = ""; //服务器端接收内容

    public function __construct($url){
        $this->conn($url);
        $this->setHead("Host:".$this->urlInfo["host"]);
    }

    public function conn($url){
        $this->urlInfo = parse_url($url); //解析url,获取url信息
        /*判断是否解析出端口，解析不出默认80端口*/
        if(!isset($this->urlInfo["port"])){
            $this->urlInfo["port"] = 80;
        }

        /*判断是否解析出主机，解析不出则默认本地主机*/
        if(!isset($this->urlInfo["host"])){
            $this->urlInfo["host"] = "localhost";
        }
        $this->fs = fsockopen($this->urlInfo["host"],$this->urlInfo["port"],$this->errorno,$this->errstr,3);
    }

    /*设置请求行*/
    protected function setLine($method){
        $this->line[0] = $method." ".$this->urlInfo["path"]."?".$this->urlInfo["query"]." ".$this->version;
    }

    /*设置请求头*/
    public function setHead($head){
        $this->header[] = $head;
    }

    /*设置请求主体*/
    protected function setBody($body){
        $this->body = http_build_query($body);
    }

    public function get(){
        $this->setLine("GET");
        $this->request();
        return $this->content;
    }

    /*发送请求*/
    protected function request(){
        /*构造请求信息*/
        $req = array_merge($this->line,$this->header,array(""),array($this->body),array(""));
        $reqstr = implode(Http::NEWLINE,$req);
        fwrite($this->fs,$reqstr); //写入请求
        /*获取服务器返回信息*/
        while(!feof($this->fs)){
            $this->content .= fread($this->fs,1024);
        }

        $this->close(); //关闭http连接
    }


    public function close(){
        fclose($this->fs);
    }

    public function post($body){
        $this->setLine("POST");
        $this->setHead("Content-type: application/x-www-form-urlencoded");
        $this->setBody($body);
        $this->setHead("Content-length: ".strlen($this->body));
        $this->request();
        return $this->content;
    }
}


