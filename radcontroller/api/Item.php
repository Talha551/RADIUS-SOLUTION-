<?php

   

require APPPATH . 'libraries/REST_Controller.php';

     

class Item extends REST_Controller {

    

	  /**

     * Get All Data from this method.

     *

     * @return Response

    */

    public function __construct() {

       parent::__construct();

       $this->load->database();

    }

       

    /**

     * Get All Data from this method.

     *

     * @return Response

    */

	public function test1()

	{

        $username = $this->input->post("username");
        echo($username);
        exit;

        //if($username <> ""){

            $data = $this->db->get_where("rm_users", ['username' => $username])->row_array();

        //}else{

        //    $data = $this->db->get_where("rm_users", ['username' => "u2nasir3"])->row_array();

        //}

     

        $this->response($data);

	}

      

    /**

     * Get All Data from this method.

     *

     * @return Response

    */

    public function index_post1()

    {

        $input = $this->input->post();

        $this->db->insert('items',$input);

     

        $this->response(['Item created successfully.'], REST_Controller::HTTP_OK);

    } 

     

    /**

     * Get All Data from this method.

     *

     * @return Response

    */

    public function index_put($id)

    {

        $input = $this->put();

        $this->db->update('items', $input, array('id'=>$id));

     

        $this->response(['Item updated successfully.'], REST_Controller::HTTP_OK);

    }

     

    /**

     * Get All Data from this method.

     *

     * @return Response

    */

    public function index_delete($id)

    {

        $this->db->delete('items', array('id'=>$id));

       

        $this->response(['Item deleted successfully.'], REST_Controller::HTTP_OK);

    }

    public function user_get($username = 0)

	{

        if(!empty($username)){

            $data = $this->db->get_where("rm_users", ['username' => $username])->row_array();

        }else{

            $data = $this->db->get("rm_users")->result();

        }

     

        $this->response($data, REST_Controller::HTTP_OK);

	}

    	

}