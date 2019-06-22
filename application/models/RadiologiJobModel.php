<?php 
class RadiologiJobModel extends CI_Model{
    protected $table = 'radiologi_job';

    function getAll() {
        return $this->db->get($this->table)->result_array();
    }

    function getById($id=0) {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row_array();
    }

    function add($data=0) {
        if($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        }
    }

    function edit($id=0, $data=0) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    function delete($id=0) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }
}
