<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->display();
    }
    public function top(){
        $this->display();
    }
    public function menu(){
        $menu=D('menu');
        $menus=$menu->getMenuList();
        //var_dump($menus);exit;
        $this->assign('menus',$menus);
        $this->display();
    }
    public function main(){
        $this->display();
    }

}