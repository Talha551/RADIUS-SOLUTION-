<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><i class="fas fa-tachometer-alt"></i> Accounts Dashboard <small class="text-muted">Control panel</small></h1>
          </div>
        </div>
      </div>
    </section>
    
    <section class="content">
      <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="info-box bg-info">
            <span class="info-box-icon"><i class="fa fa-money-bill"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Cash In Hand</span>
              <span class="info-box-number"><?php if($CashInHand->CashInHand>=0){ echo "Debit ".$CashInHand->CashInHand; }else{  echo "Credit (".abs($CashInHand->CashInHand).")"; } ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <span class="progress-description">
                <?php echo round((($CashInHand->CashInHand/($CashInHand->CashInHand+$CashInHand->pCashInHand))*100),0)."% Increased in last 30 days"; ?>
              </span>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fa fa-university"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Bank Deposit</span>
              <span class="info-box-number"><?php if($CashInHand->BankBalance>=0){ echo "Debit ".$CashInHand->BankBalance; }else{  echo "Credit (".abs($CashInHand->BankBalance).")"; } ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <span class="progress-description">
              <?php echo round((($CashInHand->CashInHand/($CashInHand->CashInHand+$CashInHand->pCashInHand))*100),0)."% Increased in last 30 days"; ?>
              </span>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="info-box bg-warning">
            <span class="info-box-icon"><i class="fa fa-credit-card"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Expenses This Month</span>
              <span class="info-box-number"><?php if($CashInHand->ExpBalance>=0){ echo "Debit ".$CashInHand->ExpBalance; }else{  echo "Credit (".abs($CashInHand->ExpBalance).")"; } ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <span class="progress-description">
                <?php echo round((($CashInHand->ExpBalance/($CashInHand->ExpBalance+$CashInHand->pExpBalance))*100),0)."% Increased in last 30 days"; ?>
              </span>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="info-box bg-danger">
            <span class="info-box-icon"><i class="fa fa-wallet"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Expenses Last Month</span>
              <span class="info-box-number"><?php echo $CashInHand->p_month_ExpBalance; ?></span>
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <span class="progress-description">
                <?php echo round((($CashInHand->pExpBalance/($CashInHand->ExpBalance+$CashInHand->pExpBalance))*100),0)."% of Current Month"; ?>
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <span class="info-box-icon bg-info elevation-1 mr-3"><i class="fa fa-balance-scale"></i></span>
                <div>
                  <span class="info-box-text">Sales Current</span>
                  <span class="info-box-number">
                  <?php 
                    $managername = $this->session->userdata ( 'name' );
                    $this->db->select_sum('amount');
                    $this->db->from('tbl_invoices');
                    $this->db->where('managername', $managername);
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
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <span class="info-box-icon bg-success elevation-1 mr-3"><i class="fa fa-balance-scale"></i></span>
                <div>
                  <span class="info-box-text">Profit/Loss Previous</span>
                  <span class="info-box-number">
                  <?php 
                    $managername = $this->session->userdata ( 'name' );
                    $this->db->select_sum('costprice');
                    $this->db->from('rm_users as A');
                    $this->db->join('tbl_services as B', 'A.srvid = B.radsrvid','left');
                    if($this->ismaster < 1){
                        $this->db->where('A.owner', $managername);
                        $this->db->where('B.managername', $managername);}
                    else{
                        $this->db->where('A.owner in (Select managername from rm_managers where mastername = "'.$managername.'")');
                        $this->db->where('B.managername', $managername);
                    }
                    $this->db->where('A.enableuser', 1);
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
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <span class="info-box-icon bg-warning elevation-1 mr-3"><i class="fa fa-opencart"></i></span>
                <div>
                  <span class="info-box-text">Opening Stock</span>
                  <span class="info-box-number"><?php echo 0; ?> GBs</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <span class="info-box-icon bg-danger elevation-1 mr-3"><i class="fa fa-truck"></i></span>
                <div>
                  <span class="info-box-text">Closing Stock</span>
                  <span class="info-box-number"><?php //echo $row->Uploads; ?> GBs</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Sales Trends</h3>
            </div>
            <div class="card-body">
              <div class="chart">
                <canvas id="bar-chart-grouped" width="700" height="300"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Expenses Trends</h3>
            </div>
            <div class="card-body">
              <div class="chart">
                <canvas id="pie-chart" width="700" height="300"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
</div>

<?php 

    $managername = $this->session->userdata ( 'name' );
    
    //$query = $this->db->query("SELECT date(acctstoptime) as date, round((((sum(`BaseTbl`.`acctoutputoctets`)/1024)/1024)/1024),0) as downloads, 
    //          round((((sum(`BaseTbl`.`acctinputoctets`)/1024)/1024)/1024),0) as Uploads 
    //          FROM `radacct` as `BaseTbl` LEFT JOIN `nas` as `NasTbl` ON `BaseTbl`.`nasipaddress` = `NasTbl`.`nasname` 
    //          LEFT JOIN `rm_users` as `UsrTbl` ON `BaseTbl`.`username` = `UsrTbl`.`username` WHERE `UsrTbl`.`owner` = '$managername' 
    //          and acctstoptime > now() - INTERVAL 6 day AND isnull(BaseTbl.acctstoptime) = false 
    //          GROUP BY date");

    //$query = $this->db->query("select MONTH(acctstarttime) as month, sum(HOUR(TIMEDIFF(acctstarttime, acctstoptime)) / 24) as days, 
    //          round((((sum(acctinputoctets)/1024)/1024)/1024),0) as uploads, 
    //          round((((sum(acctoutputoctets)/1024)/1024)/1024),0) as downloads 
    //          from radacct where acctstarttime > now() - INTERVAL 5 month 
    //          and username in (select username from rm_users where owner = '$managername') and isnull(acctstoptime)=false 
    //          group by month order by acctstarttime");

    if($this->ismaster > 0 || $managername == 'admin'){ 
        $where = " and owner IN (select managername from rm_managers where mastername = '".$managername."') "; }
        else{ $where = " and owner = '".$managername."'"; }

    $query = $this->db->query("select month(createdon) as month, count(username) as newcustomers, count(if(enableuser=0,1,NULL)) as disabled, 
              count(if(expiration <= current_date(),1,null)) as expired  from rm_users 
              where month(createdon) <= Month(current_date()) and month(createdon) >= MONTH(CURRENT_DATE())-5 
              ".$where." group by month(createdon)");

    $row   = $query->result();

    $newcustomers = "";
    $blocked = "";
    $months = "";

    if(!empty($row))
    {
        $row_count = 1;
        foreach($row as $record)
        { 
          if(!empty($newcustomers)){
            $months = $months . ", ".$record->month;
            $newcustomers = $newcustomers . ", " . $record->newcustomers;
            $blocked = $blocked . ", " . $record->disabled;
          }
          else
          {
            $months = $record->month;
            $newcustomers = $record->newcustomers;
            $blocked = $record->disabled;
          }
          

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
            label: "New",
            backgroundColor: "#3e95cd",
            data: [<?php echo $newcustomers; ?>]
            }, {
            label: "Blocked",
            backgroundColor: "#8e5ea2",
            data: [<?php echo $blocked; ?>]
            }
        ]
        },
        options: {
        title: {
            display: true,
            text: 'Monthly Customer Trends'
        }
        }
    });

    new Chart(document.getElementById("pie-chart"), {
    type: 'pie',
    data: {
      labels: ["Total", "expired", "blocked"],
      datasets: [
        {
          label: "Customers (Count)",
          backgroundColor: ["#3e95cd", "#8e5ea2","#c45850"],
          data: [<?php echo $total_customers ?>,<?php echo $expired_users ?>,<?php echo $blocked_users ?>,]
        }
      ]
    },
    options: {
      title: {
        display: true,
        text: 'Current Users Status (Live)'
      }
    }
    });



</script>