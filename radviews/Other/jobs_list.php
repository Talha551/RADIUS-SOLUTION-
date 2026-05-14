<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Jobs Queue <small>List</small></h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Jobs Queue</li>
        </ol>
    </section>
    <section class="content">
        <div class="container-fluid">
            <?php if($this->session->flashdata('success')): ?>
                <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
            <?php endif; ?>
            <?php if($this->session->flashdata('error')): ?>
                <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-12">
                    <a href="<?php echo base_url('Other_controller/jobsAdd'); ?>" class="btn btn-success mb-2"><i class="fa fa-plus"></i> Add New Job</a>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Jobs List</h3>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>URL</th>
                                        <th>Active</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Interval (sec)</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($jobs)): foreach($jobs as $job): ?>
                                        <tr>
                                            <td><?php echo $job->jobid; ?></td>
                                            <td>
                                              <?php
                                                if ($job->jobtype == 0) echo 'Controller';
                                                elseif ($job->jobtype == 1) echo 'WebHook';
                                                elseif ($job->jobtype == 2) echo 'Backup';
                                                elseif ($job->jobtype == 3) echo 'CronJob';
                                                else echo 'Unknown';
                                              ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($job->jobname); ?></td>
                                            <td><a href="<?php echo htmlspecialchars($job->url); ?>" target="_blank"><?php echo htmlspecialchars($job->url); ?></a></td>
                                            <td><?php echo $job->active ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'; ?></td>
                                            <td><?php echo $job->jobstart; ?></td>
                                            <td><?php echo $job->jobend; ?></td>
                                            <td><?php echo $job->jobinterval; ?></td>
                                            <td><?php echo $job->createdDtm; ?></td>
                                            <td>
                                                <a href="<?php echo base_url('Other_controller/jobsEdit/'.$job->jobid); ?>" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>
                                                <a href="<?php echo base_url('Other_controller/jobsDelete/'.$job->jobid); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this job?');"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td colspan="10" class="text-center">No jobs found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 