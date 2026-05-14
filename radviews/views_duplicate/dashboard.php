<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-tachometer" aria-hidden="true"></i> Dashboard
        <small>Control panel</small>
      </h1>
    </section>
    
    <section class="content">
        <div class="row">
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-aqua">
                <div class="inner">
                  <h3><?php 
                      header("Refresh: 30");
                      $managername = $this->session->userdata ( 'name' );
                      //Total Customers from Table
                      $this->db->select('username');
                      $this->db->from('rm_users');
                      if($managername == 'admin'){
                        $this->db->where('enableuser=0 and acctype=0 and expiration >"'.$curDate.'"'');
                      }
                        else{
                        $this->db->where('enableuser=1 and acctype=0 and owner = "'.$managername.'"');
                      }
                      echo $this->db->count_all_results();
                    ?></h3>
                  <p>Total Customers</p>
                </div>
                <div class="icon">
                  <i class="ion ion-bag"></i>
                </div>
                <a href="<?php echo base_url(); ?>usersListing" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-green">
                <div class="inner">
                  <h3>
                    <?php 
                      //$curDate = strtotime(date("d-m-Y"));
                      $curDate = date("y-m-d");
                      header("Refresh: 30");
                      $managername = $this->session->userdata ( 'name' );
                      //Total Customers from Table
                      $this->db->select('username');
                      $this->db->from('radacct');
                      $this->db->where('acctstoptime is null', null, false);
                      if($managername <> 'admin'){
                        $this->db->where("username IN (Select username from rm_users where owner='".$managername."')", null, false);
                      }
                      echo $this->db->count_all_results();
                    ?>
                  </h3>
                  <p>Online Customers</p>
                </div>
                <div class="icon">
                  <i class="ion ion-stats-bars"></i>
                </div>
                <a href="<?php echo base_url(); ?>onlineusers" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-yellow">
                <div class="inner">
                <h3>
                    <?php 
                      //$curDate = strtotime(date("d-m-Y"));
                      $curDate = date("y-m-d");
                      header("Refresh: 30");
                      $managername = $this->session->userdata ( 'name' );
                      //Total Customers from Table
                      $this->db->select('username');
                      $this->db->from('rm_users');
                      if($managername == 'admin'){
                        $this->db->where('enableuser=1 and acctype=0 and expiration >"'.$curDate.'"');
                      }
                      else{
                        $this->db->where('enableuser=1 and acctype=0 and owner = "'.$managername.'" and expiration >"'.$curDate.'"');
                      }
                      echo $this->db->count_all_results();
                    ?>
                  </h3>
                  <p>Active Customers</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person-add"></i>
                </div>
                <a href="<?php echo base_url(); ?>userListing" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-red">
                <div class="inner">
                <h3>
                    <?php 
                      //$curDate = strtotime(date("d-m-Y"));
                      $curDate = date("y-m-d");
                      header("Refresh: 30");
                      $managername = $this->session->userdata ( 'name' );
                      //Total Customers from Table
                      $this->db->select('username');
                      $this->db->from('rm_users');
                      if($managername == 'admin'){
                        $this->db->where('enableuser=1 and acctype=0 and expiration <="'.$curDate.'"');
                      }
                      else{
                        $this->db->where('enableuser=1 and acctype=0 and owner = "'.$managername.'" and expiration <="'.$curDate.'"');
                      }

                      echo $this->db->count_all_results();


                      $this->db->select('username');
                      $this->db->from('rm_users');
                      if($managername == 'admin'){
                        $this->db->where('enableuser=0 and acctype=0');
                      }
                      else{
                        $this->db->where('enableuser=0 and acctype=0 and owner = "'.$managername.'"');
                      }


                      echo "/".$this->db->count_all_results();
                    ?>
                  </h3>
                  <p>Expired/Blocked Users</p>
                </div>
                <div class="icon">
                  <i class="ion ion-pie-graph"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
          </div>

          <div class="row">
              <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
              <span class="info-box-icon bg-aqua"><i class="fa fa-money"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">Balance</span>
              <span class="info-box-number">
              <?php 
                      $managername = $this->session->userdata ( 'name' );
                      //Total Customers from Table
                      $this->db->select_sum('amount');
                      $this->db->from('tbl_invoices');
                      $this->db->where('managername', $managername);
                      //echo $this->db->count_all_results();
                      $query = $this->db->get();
                      $result = $query->row();
                      if ($query->num_rows() > 0){
                        echo $result->amount;
                      } else {
                          echo 0;
                      }
              ?>
              </span>
              </div>
              </div>
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
              <span class="info-box-icon bg-green"><i class="fa fa-shopping-cart"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">Monthly Cost of Sales</span>
              <span class="info-box-number">
              <?php 
                      $managername = $this->session->userdata ( 'name' );
                      //Total Customers from Table
                      $this->db->select_sum('costprice');
                      $this->db->from('rm_users as A');
                      $this->db->join('tbl_services as B', 'A.srvid = B.radsrvid','left');
                      $this->db->where('A.owner', $managername);
                      $this->db->where('B.managername', $managername);
                      $this->db->where('A.enableuser', 1);
                      //echo $this->db->count_all_results();
                      $query = $this->db->get();
                      $result = $query->row();
                      if ($query->num_rows() > 0){
                        echo $result->costprice;
                      } else {
                          echo 0;
                      }
              ?>
              </span>
              </div>
              </div>
              </div>

              <?php 

                  $managername = $this->session->userdata ( 'name' );
                  $query = $this->db->query("SELECT round((((sum(`BaseTbl`.`acctoutputoctets`)/1024)/1024)/1024),0) as downloads, 
                            round((((sum(`BaseTbl`.`acctinputoctets`)/1024)/1024)/1024),0) as Uploads 
                            FROM `radacct` as `BaseTbl` LEFT JOIN `nas` as `NasTbl` ON `BaseTbl`.`nasipaddress` = `NasTbl`.`nasname` 
                            LEFT JOIN `rm_users` as `UsrTbl` ON `BaseTbl`.`username` = `UsrTbl`.`username` WHERE `UsrTbl`.`owner` = '$managername' 
                            AND isnull(BaseTbl.acctstoptime) = true limit 1");
                  $row   = $query->row();

              ?>

              <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
              <span class="info-box-icon bg-yellow"><i class="fa fa-cloud-download"></i></span>
              <div class="info-box-content">
              <span class="info-box-text">Active Session Downloads</span>
              <span class="info-box-number"><?php echo $row->downloads; ?> GBs</span>
              </div>
              </div>
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
              <span class="info-box-icon bg-red"><i class="fa fa-cloud-upload"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">Active Session Uploads</span>
              <span class="info-box-number"><?php echo $row->Uploads; ?> GBs</span>
              </div>
              </div>
              </div>
          </div>

          <div class="box box-primary">


        <div class="col-md-6">
            <div class="box">

                <div class="box-header with-border">
                    <h3 class="box-title">Data Chart</h3>


                </div>
                <div class="box-body">
                    <div class="chart">
                        <canvas id="bar-chart-grouped" width="700" height="300"></canvas>
                    </div>
                </div>

            </div>

        </div>

        <div class="col-md-6">
            <div class="box">

                <div class="box-header with-border">
                    <h3 class="box-title">Data Chart</h3>
                </div>
                <div class="box-body">
                    <div class="chart">
                        <canvas id="line-chart1" width="700" height="300"></canvas>
                    </div>
                </div>

            </div>

        </div>

    </div>
    </div>
  </section>

<?php 

    $managername = $this->session->userdata ( 'name' );
    $query = $this->db->query("SELECT date(acctstoptime) as date, round((((sum(`BaseTbl`.`acctoutputoctets`)/1024)/1024)/1024),0) as downloads, 
              round((((sum(`BaseTbl`.`acctinputoctets`)/1024)/1024)/1024),0) as Uploads 
              FROM `radacct` as `BaseTbl` LEFT JOIN `nas` as `NasTbl` ON `BaseTbl`.`nasipaddress` = `NasTbl`.`nasname` 
              LEFT JOIN `rm_users` as `UsrTbl` ON `BaseTbl`.`username` = `UsrTbl`.`username` WHERE `UsrTbl`.`owner` = '$managername' 
              and acctstoptime > now() - INTERVAL 6 day AND isnull(BaseTbl.acctstoptime) = false 
              GROUP BY date");

    $query = $this->db->query("select MONTH(acctstarttime) as month, sum(HOUR(TIMEDIFF(acctstarttime, acctstoptime)) / 24) as days, 
              round((((sum(acctinputoctets)/1024)/1024)/1024),0) as uploads, 
              round((((sum(acctoutputoctets)/1024)/1024)/1024),0) as downloads 
              from radacct where acctstarttime > now() - INTERVAL 5 month 
              and username in (select username from rm_users where owner = '$managername') and isnull(acctstoptime)=false 
              group by month order by acctstarttime");

    $row   = $query->result();

    $chartDownload = "";
    $chartUpload = "";
    $months = "";

    if(!empty($row))
    {
        $row_count = 1;
        foreach($row as $record)
        { 
          if(!empty($chartDownload)){
            $months = $months . ", ".$record->month;
            $chartDownload = $chartDownload . ", " . $record->downloads;
            $chartUpload = $chartUpload . ", " . $record->uploads;
          }
          else
          {
            $months = $record->month;
            $chartDownload = $record->downloads;
            $chartUpload = $record->uploads;
          }
          
          //echo $record->downloads;
          $row_count++;
        }
    }
                          

?>

<script type="text/javascript">

    today = new Date();
    var dd = today.getDate();

    new Chart(document.getElementById("bar-chart-grouped"), {
        type: 'bar',
        data: {
        labels: [<?php echo $months; ?>],
        datasets: [
            {
            label: "Download",
            backgroundColor: "#3e95cd",
            data: [<?php echo $chartDownload; ?>]
            }, {
            label: "Upload",
            backgroundColor: "#8e5ea2",
            data: [<?php echo $chartUpload; ?>]
            }
        ]
        },
        options: {
        title: {
            display: true,
            text: 'Monthly Downloads (GBs)'
        }
        }
    });

    new Chart(document.getElementById("line-chart"), {
    type: 'line',
    data: {
        labels: [1,25,50,1750,1800,1850,1900,1950,1999,2050],
        datasets: [{ 
            data: [86,114,106,106,107,111,133,221,783,2478],
            label: "Online",
            borderColor: "#3e95cd",
            fill: false
        }, { 
            data: [282,350,411,502,635,809,947,1402,3700,5267],
            label: "Expired",
            borderColor: "#8e5ea2",
            fill: false
        }, { 
            data: [168,170,178,190,203,276,408,547,675,734],
            label: "Active",
            borderColor: "#3cba9f",
            fill: false
        }, { 
            data: [40,20,10,16,24,38,74,167,508,784],
            label: "Blocked",
            borderColor: "#e8c3b9",
            fill: false
        }, { 
            data: [6,3,2,2,7,26,82,172,312,433],
            label: "Total",
            borderColor: "#c45850",
            fill: false
        }
        ]
    },
    options: {
        title: {
        display: true,
        text: 'Online Customers (Users)'
        }
    }
    });



</script>
