<?php

// BlocNote model
class BlocNote extends JsonFileModelAbstract
{

    public function __construct($db_file = NOTES_DB_FILE) 
    {
        $this->db_file = $db_file;
        $this->_readDb();
    }

    public function readAll() 
    {
        if ($data = $this->_readAll()) {
            return $data;
        }
        return false;
    }

    public function read($id = null) 
    {
        if ($data = $this->_read($id)) {
            return $data;
        }
        return false;
    }

    public function update($id = null, array $data = null) 
    {
        if ($this->_update($id, $data)) {
            RESTapi::getInstance()->info('OK - entry updated');
            return $data;
        }
        return false;
    }

    public function create(array $data = null) 
    {
        if ($this->_create($data)) {
            RESTapi::getInstance()->info('OK - entry created');
            return $data;
        }
        return false;
    }

    public function delete($id = null) 
    {
        if ($this->_delete($id)) {
            RESTapi::getInstance()->info('OK - entry deleted');
            return $id;
        }
        return false;
    }

}

