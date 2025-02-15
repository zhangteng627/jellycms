<?php
namespace App\Models;
use CodeIgniter\Model;
class RoleModel extends Model
{
	protected $table      = 'role';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $useSoftDeletes = false;
	protected $allowedFields = [];
	protected $useTimestamps = false;
	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = true;
	protected $protectFields = false;
    // 获取角色列表
    public function getList()
    {
		$builder = $this->db->table('role');
		$result   = $builder->select('role.*, admin.name as create_user')
							->join('admin', 'role.create_user = admin.id', 'left')
							->where(['role.deleted'=>0])
							->get()
							->getResultArray();
		//转换为数组，便于前端表单填充
		foreach($result as $key=>$value){
			$result[$key]['areas_id'] = json_decode($result[$key]['areas_id'], true);
			$result[$key]['rules_id'] = json_decode($result[$key]['rules_id'], true);
		}
        return $result;
    }

    // 获取角色选择列表
    public function getSelect()
    {
		$sql = "SELECT * FROM ".$this->db->prefixTable('role')." where status=1";
		$result = $this->db->query($sql)->getResultArray();
        return $result;
    }
	public function edit($data){
		return $this->save($data);
	}
	// 校验角色是否存在
	public function roleCheck($name){
		$sql = "SELECT * FROM ".$this->db->prefixTable('role')." where name='".$name."' and deleted=0";
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
     
}

