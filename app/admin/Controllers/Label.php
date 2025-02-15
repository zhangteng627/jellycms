<?php
/**
 * @file Label.php
 * @brief 标签管理
 * @author 无双
 * @date 2017-08-06
 * @version 3.8.1
 */
namespace App\Controllers;
use \App\Models\LabelModel;
class Label extends BaseController
{
    private $model;
    public function __construct()
    {
		$this->model = new LabelModel();
    }

    public function index()
    {		
		$labelList = $this->model->getList();
		$data = [
			'list' => $labelList,
		];
        return view('label.html', $data);
    }
	
    public function getList()
    {
		$list = $this->model->getList();
		$data = [
			"code" => 0,
			"msg" => "",
			"count" => count($list),
			"data" => $list,
		];
		return json_encode($data);
    }

  	//添加 编辑都在此处处理
    public function edit()
    {
		$post = post();
		if(!$post['name'] || !$post['type']){
			$rdata = [
				"code" => 0,
				"msg" => "参数不足",
			];
			return json_encode($rdata);
		}
		$data = $post;
		if(is_array($data['content'])){
			$data['content'] = implode(',', $data['content']);
		}
		// 统一value格式
		if(is_array(preg_split('/,|\r\n|\n/', $data['value']))){
			$data['value'] = implode(',',preg_split('/,|\r\n|\n/', $data['value']));
		}
		// 校验 输入规则
		$input_check = $this->model->inputRules($data);
		if($input_check !== true){
			exit($input_check);
		}
		// 校验该标签是否已存在
		$check = $this->model->checkEdit($data);
		if(!$data['id']){
			unset($data['id']);
			if(count($check)>0){
				$rdata = [
					"code" => 0,
					"msg" => "该标签已存在",
				];
				return json_encode($rdata);
			}
			$data['create_user'] = $this->session->id;
			$data['create_time'] = date('Y-m-d H:i:s',time());
			$data['status'] = 1;
		}else{
			if(count($check)>0 && $check[0]['id']!=$post['id']){
				$rdata = [
					"code" => 0,
					"msg" => "该标签已存在",
				];
				return json_encode($rdata);
			}
			$data['update_user'] = $this->session->id;
			$data['update_time'] = date('Y-m-d H:i:s',time());
		}
		if($this->model->edit($data)){
			$this->log('label', '[自定义标签]添加/编辑:'.$data['name']."[ID:".$post['id']."]");
			$rdata = [
				"code" => 1,
				"msg" => "操作成功",
			];
			return json_encode($rdata);			
		}else{
			$rdata = [
				"code" => 0,
				"msg" => "操作失败",
			];
			return json_encode($rdata);
		}
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
		if($this->model->edit($data)){
			$this->log('label', "[定制标签]删除:[ID:".$id."]");
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
		if($this->model->edit($data)){
			$this->log('label', "[定制标签]修改状态:[ID:".$post['id']."]");
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