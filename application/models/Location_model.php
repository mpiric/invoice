<?php 

class Location_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
    public function get_all_countries()
    {
        $sqlAllCountries = $this->db->get_where('location', array('location_type' => 0));
        $sqlAllCountriesResult = $sqlAllCountries->result_object();
        return $sqlAllCountriesResult;
    }

    public function get_details_by_location_type_and_id($locationType,$location_id)
    {
        $qry = $this->db->get_where('location', array('location_type' => $locationType,'parent_id'=>$location_id));
        //echo $this->db->last_query();die;
        $result = $qry->result_object();
        return $result;        
    }

} 

?>