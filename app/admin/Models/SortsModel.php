<?php
namespace App\Models;
use CodeIgniter\Model;

class SortsModel extends Model
{
	protected $table      = 'sorts';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $useSoftDeletes = false;
	protected $allowedFields = [];
	protected $useTimestamps = false;
	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;
	protected $protectFields = false;
    public function getList()
    {
		$builder = $this->db->table('sorts');
		$result   = $builder->select('sorts.*, model.name as m_name')
							->join('model', 'model.id = sorts.model_id', 'left')
							->where(['sorts.deleted'=>0, 'area_id'=>session('area_id')])
							->orderBy('id', 'DESC')
							->get()
							->getResultArray();
							
        return $result;
    }

    // 获取内容栏目选择列表
    public function getSelect()
    {
		$builder = $this->db->table('sorts');
		$result   = $builder->select('id,name,pid')
							->where(['deleted'=>0])
							->get()
							->getResultArray();
		$tree = getTree($result);
        return $tree;
    }
    // 获取内容栏目选择列表
    public function getSelectByModelId($model_id)
    {
		$builder = $this->db->table('sorts');
		$result   = $builder->select('id,name,pid')
							->where(['deleted'=>0, 'model_id'=>$model_id])
							->get()
							->getResultArray();
		$tree = getTree($result);
        return $tree;
    }
	public function edit($data){
		
		return $this->save($data);
	}

	public function getChild($pid){
		$builder = $this->db->table('sorts');
		$result   = $builder->select('*')
							->where(['deleted'=>0, 'pid'=>$pid])
							->get()
							->getResultArray();
        return $result;
	}
	public function checkName($name, $area_id){
		$builder = $this->db->table('sorts');
		$result   = $builder->select('id,name,pid')
							->where(['deleted'=>0, 'name'=>$name, 'area_id'=>$area_id])
							->get()
							->getRowArray();
		return $result;
	}
}
