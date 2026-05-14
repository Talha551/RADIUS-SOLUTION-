<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-users"></i> Sub-Reseller Services</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Sub-Reseller Services</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">All Sub-Reseller Services</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th>Srvid</th>
                                <th>Service Name</th>
                                <th>RadSrvID</th>
                                <th>Manager</th>
                                <th>Base Price</th>
                                <th>Cost Price</th>
                                <th>Sale Price</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($subResellerServices)) { foreach($subResellerServices as $service) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($service->srvid); ?></td>
                                <td><?php echo htmlspecialchars($service->srvname); ?></td>
                                <td><?php echo htmlspecialchars($service->radsrvid); ?></td>
                                <td><?php echo htmlspecialchars($service->managername); ?></td>
                                <td><?php echo htmlspecialchars($service->baseprice); ?></td>
                                <td><?php echo htmlspecialchars($service->costprice); ?></td>
                                <td><?php echo htmlspecialchars($service->saleprice); ?></td>
                            </tr>
                        <?php } } else { ?>
                            <tr><td colspan="7" class="text-center">No sub-reseller services found.</td></tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div> 