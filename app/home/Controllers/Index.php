<?php namespace App\Controllers;
use  \App\Models\IndexModel;

class Index extends BaseController
{    
    /**
     * @brief 构造函数
     * @param string 
     */
	private $data;//页面数据
	private $model;
	private $cacheName;//缓存文件名
	public function __construct()
	{	
		//缓存文件名 
		$this->cacheName = md5($_SERVER["QUERY_STRING"]);
		
		if ($output = cache($this->cacheName))
		{
			return $output;exit;
		}
		$this->model = new IndexModel();
		$area_id = $this->data['company'] = $this->model->getDefaultArea();
		$this->session = \Config\Services::session();
		//设置area_id
		$this->session->set(['area_id' =>$area_id]);
		//全局标签 数据
		$this->data['company'] = $this->model->getCompany($area_id);
		$this->data['site'] = $this->model->getSite($area_id);
		//$this->data['sorts'] = $this->model->getSorts($area_id);
		$this->route($_SERVER["QUERY_STRING"]);
		
	}
	public function index()
	{	
		$this->data['home'] = 1;//首页标记
		echo view('html/index.html',$this->data, ['cache'=>100,'cache_name'=>$this->cacheName]);
	}
	
	//列表页 $params[0] urlname; $params[1] id;$params[2]  页数
	public function list($params){
		if(count($params) == 1){
			$sort = $this->model->getSortByUrlname($params[0]);
		}else if(count($params) == 2){
			$sort = $this->model->getSortByUrlname($params[0]);
			//两个参数 可能是pm_modelid_id urlname_page
			if($sort['id']){
				$this->data['page'] = $params[1];
			}
		}else if(count($params) == 3){
			$sort = $this->model->getSortById($params[1]);
			$this->data['page'] = $params[2];
		}
		if(!$sort) exit('栏目不可用');
		if($sort['m_type']==1){//单页
			$this->page($params);
			return;
		}
		$parents = $this->model->getParentsorts($sort['id']);
		$this->data['sort'] = $sort;
		$this->data['topsort'] = $parents[0];//顶级分类
		array_pop($parents);//最后一个元素是当前分类，删除
		$this->data['parentsort'] = end($parents);//父分类 顶级分类无父分类
		
		//生成分页
		
		echo view('html/'.$sort['listtpl'],$this->data, ['cache'=>100,'cache_name'=>$this->cacheName]);
		exit;
	}
	//内容页 
	private function content($id){
		//获取content 相关数据
		$content = $this->model->getContent($id);
		if(!$content) exit('内容不可用');
		$prenext = $this->model->getPreNext($id);
		$this->data['pre'] = $prenext['pre'];
		$this->data['next'] = $prenext['next'];
		$sort = $this->model->getSortById($content['sort_id']);
		
		$this->data['content'] = $content;
		$parents = $this->model->getParentsorts($sort['id']);
		$this->data['sort'] = $sort;
		$this->data['topsort'] = $parents[0];//顶级分类
		array_pop($parents);//最后一个元素是当前分类，删除
		$this->data['parentsort'] = end($parents);//父分类 顶级分类无父分类
		echo view('html/'.$sort['contenttpl'],$this->data, ['cache'=>100,'cache_name'=>$this->cacheName]);
		exit;
	}
	//单页
	private function page($params){
		if($params[1]){
			$sort = $this->model->getSortById($params[1]);
		}else if($params[0]){
			$sort = $this->model->getSortByUrlname($params[0]);
		}
		if(!$sort){ // 内容不存在
			exit;
		}
		$content = $this->model->getPageContent($sort['id']);

		$this->data['sort'] = $sort;
		$this->data['content'] = $content;
		echo view('html/'.$sort['contenttpl'],$this->data, ['cache'=>100,'cache_name'=>$this->cacheName]);
		exit;
	}
	
	//处理路由 其它同类路由在app/home/config/routes.php里有一份处理CI自有模式和伪静态模式路由。
	private function route($str){
		if(!$str) return; //首页
		//去掉后缀
		$str = str_replace(".html","",$str);
		$str = str_replace(".htm","",$str);
		$str = trim($str, "/");
		//目录/ID模式模式处理
		if(strpos($str, '/')){//目录/ID模式
			$params = explode('/', $str);
			$this->content($params[1]); 
		}else if(strpos($str, '_')){ //列表页 单页 无自定义别名和有自定义别名的都可用
			$params = explode('_', $str);
			$this->list($params); 
		}else{//列表页 单页 ?list/ 有自定义别名
			$this->list(array($str)); 
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

}
