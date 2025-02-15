<?php
/**
 * @file Menu.php
 * @brief 菜单管理
 * @author 无双
 * @date 2019-09-06
 * @version 3.8.1
 */
namespace App\Controllers;
use  \App\Models\MenuModel;
use  \App\Models\RoleModel;

class Menu extends BaseController
{

    private $count;

    private $blank;

    private $outData = array();

    private $model;

    public function __construct()
    {
        $this->model = new MenuModel();
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
		$menuList = $this->model->getSelect();
		$roleList = $this->roleModel->getSelect();
		$data = [
			'menuList' =>$menuList,
			'roleList' =>$roleList,
		];
        return view('menu.html', $data);
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
		return json_encode($data, true);
    }
    public function edit()
    {
		$post = post();
		if(!$post['name'] || !$post['roles_id']){
			$rdata = [
				"code" => 0,
				"msg" => "菜单名 授权角色不能为空",
			];
			return json_encode($rdata);
		}
		foreach($post['roles_id'] as $key=>$value){
			$post['roles_id'][$key] = (int)$value;
		}
		$post['roles_id'] = json_encode($post['roles_id']);
	 
		$data = $post;
		if(!$post['id']){
			$data['create_user'] = $this->session->id;
			$data['create_time'] = date('Y-m-d H:i:s',time());
		}else{
			$data['update_user'] = $this->session->id;
			$data['update_time'] = date('Y-m-d H:i:s',time());
		}
		if($this->model->edit($data)){
			$this->log('menu', '[菜单]添加/编辑:'.$data['name']."[ID:".$post['id']."]");
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
	//编辑字段值
    public function changeValue()
    {
		$post = post();
		if(!$post['id'] || !$post['field']){
			$rdata = [
				"code" => 0,
				"msg" => "参数不足",
			];
			return json_encode($rdata);
		}
		$data = [
			'id' => $post['id'],
			$post['field'] => $post['value'],
		];
		if($this->model->edit($data)){
			$this->log('menu', '[菜单]编辑字段值为:'.$data['value']."[ID:".$post['id']."]");
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
		$ChildMenu = $this->model->getChildMenu($id);
		if(count($ChildMenu)>0){
			$rdata = [
				"code" => 0,
				"msg" => "有子菜单，无法删除",
			];
			return json_encode($rdata);
		}
		$data = [
			'id' => $id,
			'deleted' => 1,
		];
		if($this->model->edit($data)){
			$this->log('menu', '[菜单]删除:'."[ID:".$id."]");
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
			$this->log('menu', '[菜单]修改状态:'."[ID:".$post['id']."]");
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