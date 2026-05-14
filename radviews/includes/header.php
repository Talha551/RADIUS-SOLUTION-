<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $pageTitle; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/adminlte.min.css">
  <!-- Custom styles -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/custom.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  
  <style>
    .error {
      color: red;
      font-weight: normal;
    }

    .center-info {
      text-align: center;
    }

    .dataTables_info {
      float: none;
      /* Override default float */
    }

    #columnToggle label {
      display: inline-block;
      /* Makes the label and checkbox pairs flow in a line */
      margin-right: 10px;
      /* Adds some spacing between the checkbox pairs */
    }

    #columnToggle input[type="checkbox"] {
      margin-right: 5px;
      /* Adds some spacing between the checkbox and its label text */
    }

    .dataTables_length select {
      font-family: Arial, sans-serif;
      /* Example: match your form input font */
      font-size: 14px;
      /* Match your form input font size */
      padding: 6px 12px;
      /* Match your form input padding */
      border: 1px solid #ccc;
      /* Match your form input border */
      border-radius: 4px;
      /* Match your form input border radius */
      /* Add other styles as needed to match your form inputs */
    }

    .table-container {
      max-height: 450px;
      overflow-x: auto;
    }
  </style>
  <script src="<?php echo base_url(); ?>assets/plugins/jquery/jquery.min.js"></script>
  <script>var baseURL = "<?php echo base_url(); ?>";</script>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  <!-- DataTables CSS and JS - Uncomment if needed -->
  <!--
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
  -->


</head>



<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- User Account Menu -->
        <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
            <img src="<?php echo base_url(); ?>assets/dist/img/avatar.png" class="user-image img-circle elevation-2" alt="User Image" />
            <span class="d-none d-md-inline"><?php echo $name.($this->session->userdata('login_type') == 'profile' ? " (".$this->session->userdata('profileid').")" : ""); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <!-- User image -->
            <li class="user-header bg-primary">
              <img src="<?php echo base_url(); ?>assets/dist/img/avatar.png" class="img-circle elevation-2" alt="User Image" />
              <p>
                <?php echo $name.($this->session->userdata('login_type') == 'profile' ? " (".$this->session->userdata('profileid').")" : ""); ?>
                <small><?php echo $role_text; ?></small>
              </p>
              <p>Balance: Rs.
                <?php
                $managername = $this->session->userdata('name');
                //Total Customers from Table
                $this->db->select_sum('amount');
                $this->db->from('tbl_invoices');
                $this->db->where('managername', $managername);
                //echo $this->db->count_all_results();
                $query = $this->db->get();
                $result = $query->row();
                if ($query->num_rows() > 0) {
                  $manager_balance_info = $result->amount;
                  echo $result->amount;
                } else {
                  echo 0;
                }
                ?>
              </p>
            </li>
            <!-- Menu Footer-->
            <li class="user-footer">
              <a href="<?php echo base_url(); ?>profile" class="btn btn-warning btn-flat"><i class="fa fa-user-circle"></i> Profile</a>
              <a href="<?php echo base_url(); ?>logout" class="btn btn-default btn-flat float-right"><i class="fa fa-sign-out-alt"></i> Sign out</a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="<?php echo base_url(); ?>" class="brand-link">
        <span class="brand-text font-weight-light"><b>RadSpot </b>Billing</span>
      </a>

      <?php
          $managername = $this->session->userdata('name');
          //Total Customers from Table
          $this->db->select('*');
          $this->db->from('rm_managers');
          $this->db->where('managername', $managername);
          //echo $this->db->count_all_results();
          $query = $this->db->get();
          $managerPermissions = $query->row();

          $perm_listusers = $managerPermissions->perm_listusers;
          $perm_createusers = $managerPermissions->perm_createusers;
          $perm_editusers = $managerPermissions->perm_editusers;
          $perm_edituserspriv = $managerPermissions->perm_edituserspriv;
          $perm_deleteusers = $managerPermissions->perm_deleteusers;
          $perm_listmanagers = $managerPermissions->perm_listmanagers;
          $perm_createmanagers = $managerPermissions->perm_createmanagers;
          $perm_editmanagers = $managerPermissions->perm_editmanagers;
          $perm_deletemanagers = $managerPermissions->perm_deletemanagers;
          $perm_listservices = $managerPermissions->perm_listservices;
          $perm_createservices = $managerPermissions->perm_createservices;
          $perm_editservices = $managerPermissions->perm_editservices;
          $perm_deleteservices = $managerPermissions->perm_deleteservices;
          $perm_listonlineusers = $managerPermissions->perm_listonlineusers;
          $perm_listinvoices = $managerPermissions->perm_listinvoices;
          $perm_trafficreport = $managerPermissions->perm_trafficreport;
          $perm_addcredits = $managerPermissions->perm_addcredits;
          $perm_negbalance = $managerPermissions->perm_negbalance;
          $perm_listallinvoices = $managerPermissions->perm_listallinvoices;
          $perm_showinvtotals = $managerPermissions->perm_showinvtotals;
          $perm_logout = $managerPermissions->perm_logout;
          $perm_cardsys = $managerPermissions->perm_cardsys;
          $perm_editinvoice = $managerPermissions->perm_editinvoice;
          $perm_allusers = $managerPermissions->perm_allusers;
          $perm_allowdiscount = $managerPermissions->perm_allowdiscount;
          $perm_enwriteoff = $managerPermissions->perm_enwriteoff;
          $perm_accessap = $managerPermissions->perm_accessap;
          $perm_cts = $managerPermissions->perm_cts;

      ?>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
            <li class="nav-header">MAIN NAVIGATION</li>


            <li class="nav-header">DASHBOARD</li>
            <li class="nav-item">
              <a href="<?php echo base_url(); ?>dashboard" class="nav-link <?php echo ($this->uri->segment(1) == 'dashboard' || $this->uri->segment(1) == '') ? 'active' : ''; ?>">
                <i class="nav-icon far fa-circle text-danger"></i>
                <p class="text">Users & Expiry</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo base_url(); ?>Network_controller/nasList" class="nav-link <?php echo ($this->uri->segment(1) == 'network') ? 'active' : ''; ?>">
                <i class="nav-icon far fa-circle text-warning"></i>
                <p>Network</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo base_url(); ?>Network_controller/userDashboard" class="nav-link <?php echo ($this->uri->segment(1) == 'accounts') ? 'active' : ''; ?>">
                <i class="nav-icon far fa-circle text-info"></i>
                <p>Connectivity Status </p>
              </a>
            </li>
            

            <li class="nav-header">Subscribers</li>
              <li class="nav-item">
                <a href="<?php echo base_url(); ?>subscribers_controller/subscribersListView" class="nav-link <?php echo ($this->uri->segment(1) == 'search' || $this->uri->segment(1) == '') ? 'active' : ''; ?>">
                  <i class="nav-icon far fa-circle text-danger"></i>
                  <p class="text">Search</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo base_url(); ?>onlineusers" class="nav-link <?php echo ($this->uri->segment(1) == 'AddNew') ? 'active' : ''; ?>">
                  <i class="nav-icon far fa-circle text-info"></i>
                  <p>Online Users</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?php echo base_url(); ?>Network_controller/searchUserDetails" class="nav-link <?php echo ($this->uri->segment(1) == 'searchUserDetails') ? 'active' : ''; ?>">
                  <i class="nav-icon far fa-circle text-warning"></i>
                  <p>Radius Accounting</p>
                </a>
              </li>

              <!-- Users Menu -->
              <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-users"></i>
                  <p>
                    Subscribers Manage
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>

                <ul class="nav nav-treeview">

                  <?php if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1, 2)) 
                            || $this->session->userdata('login_type') == 'manager') { ?>

                
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>usersListing/0" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Manage Users</p>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>usersAddNew" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Add User</p>
                      </a>
                    </li>
                    
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>userQuickEdit" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Bulk Recharge</p>
                      </a>
                    </li>
                    
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>Userslist/usersAssignRegion" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Assign Region/Location</p>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>usersGroup" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Users Group</p>
                      </a>
                    </li>

                    <?php } ?>

                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>Invoices/activationTicket_list" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Activation Ticket</p>
                      </a>
                    </li>

                    <?php if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1, 2)) 
                            || $this->session->userdata('login_type') == 'manager') { ?>

                    <li class="nav-item has-treeview">
                      <a href="#" class="nav-link">
                        <i class="fas fa-circle-o nav-icon"></i>
                        <p>
                          Import Users
                          <i class="fas fa-angle-left right"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        <li class="nav-item">
                          <a href="<?php echo base_url(); ?>import/ImportUsers/logs" class="nav-link">
                            <i class="fas fa-circle-o nav-icon"></i>
                            <p>Import History</p>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="<?php echo base_url(); ?>import/ImportUsers/" class="nav-link">
                            <i class="fas fa-circle-o nav-icon"></i>
                            <p>Users Import</p>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <?php } ?>
                  
                  

                </ul>

                
                
            </li>
            
            <?php if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1, 2)) 
                          || $this->session->userdata('login_type') == 'manager') { ?>
            <!-- Hotspot Menu -->
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-rss"></i>
                <p>
                  Hotspot WiFi
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>serieslist" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Batch List</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>cardsListing" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Cards List</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>Invoices/cardscollection_list" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Collection</p>
                  </a>
                </li>

                <?php
                //if($managerInfo->perm_createservices == 1)
                if ($this->session->userdata('name') == 'admin' || $this->ismaster > 0 || $perm_cardsys == 1 && $perm_createusers == 1) { ?>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>genusers" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Generate Cards</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>importcards" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Import Cards</p>
                    </a>
                  </li>

                  <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                      <i class="fas fa-circle-o nav-icon"></i>
                      <p>
                        Dynamic Wallet Fix
                        <i class="fas fa-angle-left right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="<?php echo base_url(); ?>cardfixownership/1" class="nav-link">
                          <i class="fas fa-circle-o nav-icon"></i>
                          <p>One Day</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="<?php echo base_url(); ?>cardfixownership/3" class="nav-link">
                          <i class="fas fa-circle-o nav-icon"></i>
                          <p>3 Days</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="<?php echo base_url(); ?>cardfixownership/7" class="nav-link">
                          <i class="fas fa-circle-o nav-icon"></i>
                          <p>7 Days</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="<?php echo base_url(); ?>cardfixownership/15" class="nav-link">
                          <i class="fas fa-circle-o nav-icon"></i>
                          <p>15 Days</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="<?php echo base_url(); ?>cardfixownership/30" class="nav-link">
                          <i class="fas fa-circle-o nav-icon"></i>
                          <p>30 Days</p>
                        </a>
                      </li>
                    </ul>
                  </li>

                  <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                      <i class="fas fa-circle-o nav-icon"></i>
                      <p>
                        Re-Check API Users
                        <i class="fas fa-angle-left right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="<?php echo base_url(); ?>cardrecheck/1" class="nav-link">
                          <i class="fas fa-circle-o nav-icon"></i>
                          <p>One Day</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="<?php echo base_url(); ?>cardrecheck/3" class="nav-link">
                          <i class="fas fa-circle-o nav-icon"></i>
                          <p>3 Days</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="<?php echo base_url(); ?>cardrecheck/7" class="nav-link">
                          <i class="fas fa-circle-o nav-icon"></i>
                          <p>7 Days</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="<?php echo base_url(); ?>cardrecheck/15" class="nav-link">
                          <i class="fas fa-circle-o nav-icon"></i>
                          <p>15 Days</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="<?php echo base_url(); ?>cardrecheck/30" class="nav-link">
                          <i class="fas fa-circle-o nav-icon"></i>
                          <p>30 Days</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                <?php }
                ?>
              </ul>
            </li>
            <?php } ?>

            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-shopping-bag"></i>
                <p>
                  Package Management
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>

              <?php if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1)) 
                          || $this->session->userdata('login_type') == 'manager') { ?>
              <ul class="nav nav-treeview">

                <?php if ($this->session->userdata('name') == 'admin') { ?>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>Services_controller/servicesListingview" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Packages List</p>
                    </a>
                  </li>
                <?php } ?>

                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>serviceslist" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Package Management</p>
                  </a>
                </li>
                <?php if ($this->session->userdata('name') == 'admin') { ?>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>spList" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Service Profiles</p>
                    </a>
                  </li>
                <?php } ?>

                <?php if ($this->session->userdata('name') == 'admin') { ?>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>spList1" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Radius Attributes</p>
                    </a>
                  </li>
                <?php } ?>
              </ul>
              <?php } ?>
            </li>

            <?php
            //if($managerInfo->perm_createservices == 1)
            if ($this->session->userdata('name') == 'admin' || $this->perm_listmanagers == 1) {
            ?>


              <?php if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1)) 
                                        || $this->session->userdata('login_type') == 'manager') { ?>
         
              <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
                  <p>
                    Resellers
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>resellerListing" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Reseller Listing</p>
                    </a>
                  </li>

                  <?php if($this->perm_createmanagers == 1){ ?>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>credit" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Credit Reseller</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>Reseller_controller/profileListing" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Login Profiles</p>
                    </a>
                  </li>
                  <?php } ?>

                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>managerGroup" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Groups Mapping</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>other/mac_check_execute" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Apply MAC Bindings</p>
                    </a>
                  </li>
                </ul>
              </li>
              <?php } ?>


            <?php } ?>

            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="fas fa-print nav-icon"></i>
                <p>
                  Billing
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>invoiceListing" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                    <p>Recharge/Invoices</p>
                  </a>
                </li>
                
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>Api_apps" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                    <p>Agents</p>
                  </a>
                </li>

                <?php if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1)) 
                          || $this->session->userdata('login_type') == 'manager') { ?>
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>Invoices/invcollection_list" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                    <p>Bills Collection</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>Invoices/walletListing" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                    <p>Payment Wallets</p>
                  </a>
                </li>
                
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>smsReport" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                    <p>Import Collection</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>easypaisalist" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                    <p>Easypaisa Invoices</p>
                  </a>
                </li>
                <?php } ?>
              </ul>
            </li>
            

            <!-- Network Menu -->
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-wifi"></i>
                <p>
                  Network Management
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>

              <?php if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1, 3)) 
                          || $this->session->userdata('login_type') == 'manager') { ?>

                <ul class="nav nav-treeview">

                  <?php if($this->session->userdata('name') == 'admin'){ ?>
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>Network_controller/addNas" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Add NAS</p>
                      </a>
                    </li>
                  
                  
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>snmp_controller/snmpCacheList" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>SNMP Monitoring</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>Network_controller/mappedDeviceList" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Mapped Devices</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>Network_controller/acsDeviceList" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>ACS Mapping</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>wireguard" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Wireguard</p>
                    </a>
                  </li>
                  
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>userQuickEdit" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Network Overview</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>usersGroup" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Network Report</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>Network_controller/segmentList" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Segments</p>
                    </a>
                  </li>

                  <?php } ?>
                  
                </ul>
              <?php } ?>
            </li>

            <!-- Mikrotik Menu -->
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-wifi"></i>
                <p>
                  Mikrorik Management
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>

              <?php if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1, 3)) 
                          || $this->session->userdata('login_type') == 'manager') { ?>

              <ul class="nav nav-treeview">

                <?php if($this->session->userdata('name') == 'admin'){ ?>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>mikrotik/interface_monitor" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Interface Monitor</p>
                    </a>
                  </li>
                
                
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>snmp_controller/snmpCacheList" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Mikrotik Hotspot Monitor</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>Network_controller/mappedDeviceList" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Configure Mikrotik</p>
                  </a>
                </li>

                <?php } ?>
                
              </ul>

                
              <?php } ?>
            </li>

            <?php if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1)) 
                          || $this->session->userdata('login_type') == 'manager') { ?>
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="fas fa-print nav-icon"></i>
                <p>
                  Reports
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>salesReport" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                    <p>Sales Report</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>radiusReport" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                    <p>Connection Report</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>userFairUseReport" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                    <p>FUP Bandwidth Report</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>smsReport" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                    <p>SMS Logs Report</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>easypaisalist" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                    <p>Easy Paisa List</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>easypaisaCollectionReport" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                    <p>EasyPaisa Collection</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo base_url(); ?>indicatorReport" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                    <p>Indicators Report</p>
                  </a>
                </li>
              </ul>
            </li>
            <?php } ?>
            
            <!-- Accounts Menu -->
            <?php
            if ($this->accountsmanager == 1 || $managername == 'admin') {
            ?>
              <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                  <i class="fas fa-university nav-icon"></i>
                  <p>
                    Accounts
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>


                <?php if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1)) 
                          || $this->session->userdata('login_type') == 'manager') { ?>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>accountsdashboard" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                      <p>Dashboard</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>accountslist" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                      <p>Accounts List</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>jvslist" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                      <p>Transactions List</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>stockList" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                      <p>Stock Management</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>stockLedgerReport" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                      <p>Stock Ledger</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>wallerReport" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                      <p>Wallet Ledger</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>ledgerReport" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                      <p>Accounts Ledger</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>trialBalance" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                      <p>Financial Report</p>
                    </a>
                  </li>
                </ul>
                <?php } ?>
              </li>

              <?php } ?>

              <!-- DMA Control Panel -->
               
              <?php

              if ($this->session->userdata('isaccountmanager') == 1 || $managername == 'admin') {
              ?>

                <?php if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1)) 
                          || $this->session->userdata('login_type') == 'manager') { ?>
                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                    <i class="fas fa-cogs nav-icon"></i>
                    <p>
                      Control Panel
                      <i class="fas fa-angle-left right"></i>
                    </p>
                  </a>

                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>main" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Main</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>naslist" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>List NAS</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>dyndnslisting" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Dynamic DNS NAS</p>
                      </a>
                    </li>

                    <li class="nav-item has-treeview">
                      <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                          Profile Settings
                          <i class="fas fa-angle-left right"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        <li class="nav-item">
                          <a href="<?php echo base_url(); ?>profilelist" class="nav-link">
                            <i class="fas nav-icon"></i>
                            <p>Profile List</p>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="<?php echo base_url(); ?>profileadd" class="nav-link">
                            <i class="fas nav-icon"></i>
                            <p>Profile Add</p>
                          </a>
                        </li>
                      </ul>
                    </li>

                    <li class="nav-item has-treeview">
                      <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                          Traffic Reprot
                          <i class="fas fa-angle-left right"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        <li class="nav-item">
                          <a href="<?php echo base_url(); ?>findtrafficdata" class="nav-link">
                            <i class="fas nav-icon"></i>
                            <p>Find Traffic Data</p>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="<?php echo base_url(); ?>trafficsummery" class="nav-link">
                            <i class="fas nav-icon"></i>
                            <p>Traffic Summery</p>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="<?php echo base_url(); ?>overalltrafficreport" class="nav-link">
                            <i class="fas nav-icon"></i>
                            <p>Overall Traffic Report</p>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>usersimport" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                        <p>Users Import</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>import/ImportUsers/logs" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                        <p>Import Logs</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>systemsettings" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                        <p>System Settings</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>radiusonline" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                        <p>Radius Online Users</p>
                      </a>
                    </li>
                  </ul>
              
                </li>
              <?php } ?>
            <?php }

            //if($managerInfo->perm_createservices == 1)
            if ($this->session->userdata('name') == 'admin') {
            ?>
              <li class="nav-item has-treeview">

                <a href="#" class="nav-link">
                  <i class="fas fa-cog nav-icon"></i>
                  <p>
                    Settings
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>

                <?php if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1)) 
                          || $this->session->userdata('login_type') == 'manager') { ?>

                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>settingsList" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                      <p>Settings List</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>Other_controller/jobsList" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                      <p>CronJobs</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url(); ?>managerGroup" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                      <p>Default Entries</p>
                    </a>
                  </li>
                </ul>

                <?php } ?>

              </li>
            <?php } 

            if ($managername == 'admin' || $this->ismaster > 0) {
            ?>
              

              <!-- Payment System -->
              <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                  <i class="fas fa-shopping-bag nav-icon"></i>
                  <p>
                    Payment System
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>

                <?php if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1)) 
                          || $this->session->userdata('login_type') == 'manager') { ?>
                <ul class="nav nav-treeview">
                  <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                      <p>
                        Easy Paisa
                        <i class="fas fa-angle-left right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="<?php echo base_url(); ?>easypaisalist" class="nav-link">
                        <i class="far nav-icon"></i>
                          <p>Generate List</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="<?php echo base_url(); ?>epimport" class="nav-link">
                        <i class="far nav-icon"></i>
                          <p>Import List</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                </ul>
                <?php } ?>
              </li>
            <?php } ?>

            <!-- Support Menu -->
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="fas fa-share nav-icon"></i>
                <p>
                  Support
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">


              <?php if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(1, 2)) 
                          || $this->session->userdata('login_type') == 'manager') { ?>
                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                    <i class="fas fa-circle-o nav-icon"></i>
                    <p>
                      Whatsapp Messages
                      <i class="fas fa-angle-left right"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>whatsapp/connect" class="nav-link">
                        <i class="fas fa-circle-o nav-icon"></i>
                        <p>Connect Whatsapp</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>whatsapp/alerts" class="nav-link">
                        <i class="fas fa-circle-o nav-icon"></i>
                        <p>Alerts Setting</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>whatsapp/logs" class="nav-link">
                        <i class="fas fa-circle-o nav-icon"></i>
                        <p>Incoming Logs</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo base_url(); ?>" class="nav-link">
                        <i class="fas fa-circle-o nav-icon"></i>
                        <p>Outgoing Alerts</p>
                      </a>
                    </li>
                  </ul>
                </li>
                <?php } ?>

                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                    <i class="fas fa-circle-o nav-icon"></i>
                    <p>
                      Complaints
                      <i class="fas fa-angle-left right"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="#" class="nav-link">
                        <i class="fas fa-circle-o nav-icon"></i>
                        <p>New Ticket</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="#" class="nav-link">
                        <i class="fas fa-circle-o nav-icon"></i>
                        <p>View Ticket</p>
                      </a>
                    </li>
                  </ul>
                </li>

                
              </ul>


              <li class="nav-header">Rad Spot</li>
                
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon far fa-circle text-info"></i>
                    <p>User Guide</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon far fa-circle text-info"></i>
                    <p>Documentation</p>
                  </a>
                </li>


            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>