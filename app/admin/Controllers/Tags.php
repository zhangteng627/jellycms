<?php
/**
 * @file Tags.php
 * @brief 文章内链
 * @author jellypa
 * @date 2020-06-02
 * @version 3.8.1
 */
namespace App\Controllers;
use \App\Models\TagsModel;

class Tags extends BaseController
{

    private $model;

    public function __construct()
    {
        $this->model = new TagsModel();
    }
    public function index()
    {
         return view('tags.html');
    }
    public function getList()
    {
		$get = $this->request->getGet();
		isset($get['page'])?$page = $get['page']:$page = 1;
		isset($get['limit'])?$limit = $get['limit']:$limit = 10; //默认单页数
		$res = $this->model->getList($page, $limit, $this->session->area_id);
		$data = [
			"code" => 0,
			"msg" => "",
			"count" => $res['total'],
			"data" => $res['list'],
		];
		return json_encode($data);
    }
    public function edit()
    {
		$post = post();
		if(!$post['name'] || !$post['link']){
			$rdata = [
				"code" => 0,
				"msg" => "参数不足",
			];
			return json_encode($rdata);
		}
		//新建内链同名检测
		if($this->model->check($post['name']) && !$post['id']){
			$rdata = [
				"code" => 0,
				"msg" => $post['name'].":内链已存在",
			];
			return json_encode($rdata);
		}
		$data = $post;
		$data['area_id'] = $this->session->area_id;
		if(!$post['id']){
			$data['create_user'] = $this->session->id;
			$data['create_time'] = date('Y-m-d H:i:s',time());
			$data['status'] = 1;
		}else{
			$data['update_user'] = $this->session->id;
			$data['update_time'] = date('Y-m-d H:i:s',time());
		}
		
		if($this->model->edit($data, $this->session->area_id)){
			$this->log("tags", "[文章内链]编辑/新增[ID:".$post['id']."]");
			$rdata = [
				"code" => 1,
				"msg" => "操作成功",
			];		
		}else{
			$rdata = [
				"code" => 0,
				"msg" => "操作失败",
			];
		}
		return json_encode($rdata);
    }
    public function del()
    {
		$id = post('id');
		if(!$id){
			$rdata = [
				"code" => 0,
				"msg" => "参数不足",
			];
			return json_encode($rdata);
		}
		$data = [
			'id' => $id,
			'deleted' => 1,
		];
		if($this->model->edit($data,$this->session->area_id)){
			$this->log("tags", "[文章内链]删除[ID:".$id."]");
			$rdata = [
				"code" => 1,
				"msg" => "操作成功",
			];
		}else{
			$rdata = [
				"code" => 0,
				"msg" => "操作失败",
			];
		}

		return json_encode($rdata);		
    }
    public function switch()
    {
		$post = post();
		$allowSwitch = ['status'];
		if(!$post['id'] || is_null($post['switchValue']) || !in_array($post['switchName'], $allowSwitch)){
			$rdata = [
				"code" => 0,
				"msg" => "参数不足",
			];
			return json_encode($rdata);
		}
		$data = [
			'id' => $post['id'],
			$post['switchName'] => (int)$post['switchValue'],
		];
		if($this->model->edit($data, $this->session->area_id)){
			$this->log("tags", "[文章内链]修改状态[ID:".$post['id']."]");
			$rdata = [
				"code" => 1,
				"msg" => "操作成功",
			];
		}else{
			$rdata = [
				"code" => 0,
				"msg" => "操作失败",
			];
		}
		return json_encode($rdata);
    }
}