<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    private $_model = null;

    /**
     * 初始化.
     */
    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '京西商城',
            'register'    => '用户注册',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME])?$meta_titles[ACTION_NAME]:'京西商城';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Article');

        /*-----主页内容静态缓存（配置文件已配置）-----*/
        // 将商品分类写入静态缓存，有则显示无则缓存再显示
        if(!$categories = S('goods_categories')){
            $categories =  D('GoodsCategory')->getList();
            S('goods_categories',$categories);
        }
        $this->assign('categories', $categories);
        // 将帮助文章写入静态缓存，有则显示无则缓存再显示
        if(!$help_articles=S('help_articles')){
            $help_articles = $this->_model->getHelpArticleList();
            S('help_articles',$help_articles);
        }
        $this->assign('help_articles',$help_articles);
        /*-----主页内容静态缓存-----*/

        // 由于只有是主页才显示无限级分类列表
        if(ACTION_NAME == 'index'){
            $this->assign('show_category', true);
        }else{
            $this->assign('show_category', false);
        }
    }

    /**
     * 首页显示
     */
    public function index(){
        // 得到推荐商品的数据
        $goods_model = D('Goods');
        $goods_list['best_list'] = $goods_model->getGoodsListByGoodsStatus(1);
        $goods_list['new_list'] = $goods_model->getGoodsListByGoodsStatus(2);
        $goods_list['hot_list'] = $goods_model->getGoodsListByGoodsStatus(4);
        $this->assign($goods_list);
        $this->display();
    }

    /**
     * 不同商品页的显示
     */
    public function goods($id){
        $goods_model = D('Goods');
        if(!$row = $goods_model->getGoodsInfo($id)){
            $this->error(get_error($goods_model->getError()),U('index'));
        }
        $this->assign('row',$row);
        $this->display();
    }

    /**
     * 获取到点击次数.
     * @param integer $goods_id 商品id
     */
    public function getGoodsClickTimes($goods_id){
        $goods_model = D('Goods');
        $click_times = $goods_model->getGoodsClickFromRedis($goods_id);
        $data =['click_times'=>$click_times];
        die(json_encode($data));
    }

    /**
     * 添加到购物车
     * @param integer $goods_id 商品id.
     * @param integer $amount   购买数量.
     */
    public function add2Car($goods_id,$amount) {
        //区分是否是已登录状态
        $shopping_car_model = D('ShoppingCar');
        $shopping_car_model->add2Car($goods_id,$amount);
        $this->success('添加购物车成功',U('ShoppingCar/flow1'));
    }
}