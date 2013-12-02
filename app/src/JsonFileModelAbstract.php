<?php

// default model
abstract class JsonFileModelAbstract
{

    protected $db_file  = null;
    protected $db       = array();

    abstract function __construct($db_file = NOTES_DB_FILE);
    abstract function readAll();
    abstract function create(array $data = null);
    abstract function read($id = null);
    abstract function update($id = null, array $data = null);
    abstract function delete($id = null);

    protected function _validate(array $data = null) 
    {
        return (bool) !empty($data);
    }

    protected function _readAll() 
    {
        return $this->db;
    }

    protected function _read($id = null, $return_array = false) 
    {
        $data = null;
        $index = null;
        foreach ($this->db as $i=>$item) {
            if ($item['id']==$id) {
                $data = $item;
                $index = $i;
            }
        }
        if (!empty($data)) {
            if (empty($data['id'])) {
                $data['id'] = $id;
            }
            return $return_array ? array($index, $data) : $data;
        } else {
            throw new Exception("Data not found (searching '$id')!");
        }
    }

    protected function _update($id = null, array $data = null, $return_array = false) 
    {
        try {
            list($index, $original_data) = $this->_read($id, true);
            if (!empty($index) && !empty($data)) {
                if (!$this->_validate($data)) {
                    throw new InvalidArgumentException("Data are empty!");
                }
                $this->db[$index] = $data;
                return $this->_writeDb();
            } else {
                return false;
            }
        } catch(Exception $e) {
            throw $e;
        }
    }

    protected function _create(array $data = null, $return_array = false) 
    {
        try {
            if (!$this->_validate($data)) {
                throw new InvalidArgumentException("Data are empty!");
            }
            if (empty($data['id'])) {
                $data['id'] = count($this->db)+1;
            }
            $this->db[] = $data;
            return $this->_writeDb();
        } catch(Exception $e) {
            throw $e;
        }
    }

    protected function _delete($id = null, $return_array = false) 
    {
        try {
            list($index, $data) = $this->_read($id, true);
            if (!empty($index) && !empty($data)) {
                unset($this->db[$index]);
                return $this->_writeDb();
            } else {
                return false;
            }
        } catch(Exception $e) {
            throw $e;
        }
    }

    protected function _readDb()
    {
        $ctt = file_get_contents($this->db_file);
        if ($ctt) {
            $this->db = json_decode($ctt, true);
        } else {
            throw new Exception("Can not read DB file '$this->db_file'!");
            return false;
        }
        return true;
    }
    
    protected function _writeDb()
    {
        if (!file_put_contents($this->db_file, json_encode($this->db))) {
            throw new Exception("Can not write in DB file '$this->db_file'!");
            return false;
        }
        return true;
    }
    
}
