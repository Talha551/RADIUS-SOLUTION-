  <!-- Modal Graph of Users-->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <p>Some text in the modal.</p>
                <div class="media-left">
                    <img src="http://203.135.57.66:8080/graphs/queue/<pppoe-u2nasir3>/daily.gif" class="img-responsive" alt="Cinque Terre" width="608" height="472"> 
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        
    </div>
</div>


<p>Daily data Usage Graph Interval 5 Minutes</p>
<div class="media-left">
    <?php 
        //Total Customers from Table
        $this->db->select('nasipaddress');
        $this->db->from('radacct');
        $this->db->where('username', $record->username);
        $this->db->order_by('radacctid', 'DESC');
        //echo $this->db->count_all_results();
        $query = $this->db->get();
        $result = $query->row();
    ?>
    <img src="http://<?php echo $result->nasipaddress; ?>:9080/graphs/queue/<pppoe-<?php echo $record->username; ?>>/daily.gif" class="img-responsive" alt="Cinque Terre" width="608" height="472"> 
    
</div>