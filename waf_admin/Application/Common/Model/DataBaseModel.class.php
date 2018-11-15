<?php
namespace Common\Model;

class DataBaseModel extends BaseModel{
      
	protected function initDbConfig()
	{
		$this->setDbConfig('DB_MYSQL_DEFAULT');
	}
	  
}