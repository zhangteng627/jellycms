<?php
namespace App\Models;
use CodeIgniter\Model;

class ContentModel extends Model
{
	protected $table      = 'content';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $useSoftDeletes = true;
	protected $allowedFields = [ 'title'];
	protected $useTimestamps = false;
	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;
	protected $protectFields = false;
	//获取指定model下的内容列表  搜索关键词不区分大小写
    public function getList($keyword, $model_id, $page, $limit, $area_id)
    {
		$offset = ($page-1)*$limit;
		$builder = $this->db->table('content');
		if($keyword){
			$builder->like('content.title', $keyword, 'both', true, false);
		}
		$res   = $builder->select('content.*, model.urlname as m_urlname, model.id as model_id, sorts.urlname as urlname, sorts.name as sort_name, admin.name as create_user')
							->join('sorts', 'sorts.id = content.sorts_id', 'left')
							->join('model', 'model.id = sorts.model_id', 'left')
							->join('admin', 'admin.id = content.create_user', 'left')
							->where(['content.deleted'=>0, 'sorts.model_id'=>$model_id, 'sorts.area_id'=>$area_id])
							->orderBy('id', 'DESC')
							->get($limit, $offset)
							->getResultArray();
		foreach($res as $key=>$value){
			$res[$key]['link'] = $value['urlname']!=''?$GLOBALS['self_path'].url(array($value['urlname'], $value['id'])):$GLOBALS['self_path'].url(array($value['m_urlname'],$value['id']));
		}
		$builder = $this->db->table('content');
		if($keyword){
			$builder->like('content.title', $keyword, 'both', true, false);
		}
		$total = $builder->select('content.id')
							->join('sorts', 'sorts.id = content.sorts_id', 'left')
							->where(['content.deleted'=>0, 'sorts.model_id'=>$model_id, 'sorts.area_id'=>$area_id])
							->countAllResults(false);					
        $result['list'] = $res;
        $result['total'] = $total;
		return $result;
    }
	//获取指定sortsID下的内容列表 搜索关键词不区分大小写
    public function getListBySortsId($keyword, $sorts, $page, $limit, $area_id)
    {
		$offset = ($page-1)*$limit;
		$builder = $this->db->table('content');
		if($keyword){
			$builder->like('content.title', $keyword, 'both', true, false);
		}
		$res   = $builder->select('content.*, model.urlname as m_urlname, model.id as model_id, sorts.urlname as urlname, sorts.name as sort_name, admin.name as create_user')
							->join('sorts', 'sorts.id = content.sorts_id', 'left')
							->join('model', 'model.id = sorts.model_id', 'left')
							->join('admin', 'admin.id = content.create_user', 'left')
							->where(['content.deleted'=>0, 'sorts.id'=>$sorts, 'sorts.area_id'=>$area_id])
							->orderBy('id', 'DESC')
							->get($limit, $offset)
							->getResultArray();
		foreach($res as $key=>$value){
			$res[$key]['link'] = $value['urlname']!=''?url(array($value['urlname'], $value['id'])):url(array($value['m_urlname'],$value['id']));
		}
		$builder = $this->db->table('content');
		if($keyword){
			$builder->like('content.title', $keyword, 'both', true, false);
		}
		$total = $builder->select('content.id')
							->join('sorts', 'sorts.id = content.sorts_id', 'left')
							->where(['content.deleted'=>0, 'sorts.id'=>$sorts, 'sorts.area_id'=>$area_id])
							->countAllResults(false);					
        $result['list'] = $res;
        $result['total'] = $total;
		return $result;
    }

    public function getContent($id)
    {
		$builder = $this->db->table('content');
		$content   = $builder->select('content.*, sorts.model_id as model_id ')
							->join('sorts', 'sorts.id = content.sorts_id', 'left')
							->where(['content.deleted'=>0, 'content.id'=>$id])
							->get()
							->getRowArray();			
								
		//获取模型字段值					
		$builder = $this->db->table('content_ext');
		$extend   = $builder->select('content_ext.value, modelfield.name as m_name')
							->join('modelfield', 'modelfield.id = content_ext.modelfield_id', 'left')
							->where(['modelfield.deleted'=>0, 'content_ext.content_id'=>$id])
							->get()
							->getResultArray();
		foreach($extend as $key=>$value){
			$mvalue= explode(',',$extend[$key]['value']);
			$content[$extend[$key]['m_name']] = count($mvalue)>1?$mvalue:$extend[$key]['value'];
		 
		}
		$result = $content;
        return $result;
    }
	public function getCopyContent($id){
		$builder = $this->db->table('content');
		$result   = $builder->select('*')
							->where(['id'=>$id])
							->get()
							->getRowArray();
		return $result;
	}
	public function getContentExtend($id){
		$builder = $this->db->table('content_ext');
		$result   = $builder->select('*')
							->where(['deleted'=>0, 'content_id'=>$id])
							->get()
							->getResultArray();
		return $result;
	}
	public function getContentExtendBySortsId($sorts_id){
		$builder = $this->db->table('modelfield');
		$result   = $builder->select('modelfield.*')
							->join('sorts', 'modelfield.model_id = sorts.model_id', 'left')
							->where(['modelfield.deleted'=>0, 'sorts.id'=>$sorts_id])
							->get()
							->getResultArray();
		return $result;
	}
	public function insertExtendBatch($data){
		$builder = $this->db->table('content_ext');
		$result   = $builder->insertBatch($data);
		return $result;
	}
	public function updateExtendBatch($data){
		$builder = $this->db->table('content_ext');
		foreach($data as $key=>$value){
			$cententExt = 	$builder->select('*')
									->where(['content_id'=>$value['content_id'], 'modelfield_id'=>$value['modelfield_id']])
									->get()
									->getRowArray();
			if(empty($cententExt)){
				$res = $builder->insert($value);
			}else{
				$builder->where(['content_id'=>$value['content_id'], 'modelfield_id'=>$value['modelfield_id']]);
				$res = $builder->update($value);
			}

		}
		return $result;
	}
    public function getModelId($id)
    {
		$builder = $this->db->table('content');
		$result   = $builder->select('sorts.model_id as model_id')
							->join('sorts', 'sorts.id = content.sorts_id', 'left')
							->where(['content.deleted'=>0, 'content.id'=>$id])
							->get()
							->getRowArray();
        return $result;
    }
	public function getModelName($id){
		$builder = $this->db->table('model');
		$result   = $builder->select('*')
							->where(['deleted'=>0,'id'=>$id])
							->get()
							->getRowArray();
		return $result;
	}

	public function getModelFields($model_id){
		//获取模型扩展字段					
		$builder = $this->db->table('modelfield');
		$result   = $builder->select('*')
							->where(['deleted'=>0, 'model_id'=>$model_id])
							->get()
							->getResultArray();		
		//将value转换为数组					
		foreach($result as $key=>$value){
			$result[$key]['value'] = explode(',', $result[$key]['value']);
		}
		return $result;
	}
	
	public function edit($data){
		return $this->save($data);
	}
 

}