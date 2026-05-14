<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-md-8 mx-auto">
                    <h1 class="mb-4" style="font-size:2rem;"><i class="fas fa-server"></i> <?php echo isset($nas) ? 'Edit NAS' : 'Add New NAS'; ?></h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <?php if(validation_errors()): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo validation_errors(); ?>
                        </div>
                    <?php endif; ?>
                    <?php if($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo isset($nas) ? 'Edit NAS' : 'Add New NAS'; ?></h3>
                        </div>
                        <form method="post" action="" autocomplete="off">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nasname">NAS Name (IP Address)</label>
                                    <input type="text" class="form-control" id="nasname" name="nasname" value="<?php echo isset($nas) ? htmlspecialchars($nas->nasname) : set_value('nasname'); ?>" required>
                                </div>
                                    </div>
                                    <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shortname">Short Name</label>
                                    <input type="text" class="form-control" id="shortname" name="shortname" value="<?php echo isset($nas) ? htmlspecialchars($nas->shortname) : set_value('shortname'); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <select class="form-control" id="type" name="type">
                                        <option value="0" <?php echo (isset($nas) && $nas->type == '0') ? 'selected' : ''; ?>>Mikrotik</option>
                                        <option value="1" <?php echo (isset($nas) && $nas->type == '1') ? 'selected' : ''; ?>>StarOS</option>
                                        <option value="2" <?php echo (isset($nas) && $nas->type == '2') ? 'selected' : ''; ?>>ChilliSpot</option>
                                        <option value="3" <?php echo (isset($nas) && $nas->type == '3') ? 'selected' : ''; ?>>Cisco</option>
                                        <option value="4" <?php echo (isset($nas) && $nas->type == '4') ? 'selected' : ''; ?>>pFSense</option>
                                        <option value="5" <?php echo (isset($nas) && $nas->type == '5') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                <div class="form-group">
                                    <label for="secret">Secret</label>
                                    <input type="text" class="form-control" id="secret" name="secret" value="<?php echo isset($nas) ? htmlspecialchars($nas->secret) : set_value('secret'); ?>" required>
                                </div>
                                    </div>
                                    <div class="col-md-4">
                                <div class="form-group">
                                    <label for="community">Community</label>
                                    <input type="text" class="form-control" id="community" name="community" value="<?php echo isset($nas) ? htmlspecialchars($nas->community) : set_value('community'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="ports">Ports</label>
                                            <input type="number" class="form-control" id="ports" name="ports" value="<?php echo isset($nas) ? htmlspecialchars($nas->ports) : set_value('ports'); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input type="text" class="form-control" id="description" name="description" value="<?php echo isset($nas) ? htmlspecialchars($nas->description) : set_value('description'); ?>">
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                <div class="form-group">
                                    <label for="starospassword">StarOS Password</label>
                                    <input type="text" class="form-control" id="starospassword" name="starospassword" value="<?php echo isset($nas) ? htmlspecialchars($nas->starospassword) : set_value('starospassword'); ?>">
                                </div>
                                    </div>
                                    <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ciscobwmode">Cisco BW Mode</label>
                                    <select class="form-control" id="ciscobwmode" name="ciscobwmode">
                                        <option value="0" <?php echo (isset($nas) && $nas->ciscobwmode == '0') ? 'selected' : ''; ?>>No</option>
                                        <option value="1" <?php echo (isset($nas) && $nas->ciscobwmode == '1') ? 'selected' : ''; ?>>Yes</option>
                                    </select>
                                </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                <div class="form-group">
                                    <label for="apiusername">API Username</label>
                                    <input type="text" class="form-control" id="apiusername" name="apiusername" value="<?php echo isset($nas) ? htmlspecialchars($nas->apiusername) : set_value('apiusername'); ?>">
                                </div>
                                    </div>
                                    <div class="col-md-4">
                                <div class="form-group">
                                    <label for="apipassword">API Password</label>
                                    <input type="text" class="form-control" id="apipassword" name="apipassword" value="<?php echo isset($nas) ? htmlspecialchars($nas->apipassword) : set_value('apipassword'); ?>">
                                </div>
                                    </div>
                                    <div class="col-md-4">
                                <div class="form-group">
                                    <label for="enableapi">Enable API</label>
                                    <select class="form-control" id="enableapi" name="enableapi">
                                        <option value="0" <?php echo (isset($nas) && $nas->enableapi == '0') ? 'selected' : ''; ?>>No</option>
                                        <option value="1" <?php echo (isset($nas) && $nas->enableapi == '1') ? 'selected' : ''; ?>>Yes</option>
                                    </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <a href="<?php echo base_url('Network_controller/nasList'); ?>" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
