<?php
class Request
{
    private $getArray = null;
    private $postArray = null;

    public function __construct()
    {
        $this->getArray = $_GET;
        $this->postArray = $_POST;
    }

    public function getQuery($camp_name){
        return $this->getArray[$camp_name];
    }

    public function getPost($camp_name){
        return $this->postArray[$camp_name];
    }
}
?>