<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-cog"></i> MikroTik Router Configuration Test
                        <small>Debug and Setup</small>
                    </h1>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            
            <!-- Router Status -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Router Configuration Status</h3>
                        </div>
                        <div class="card-body">
                            <?php if(!$hasEnableApi): ?>
                                <div class="alert alert-danger">
                                    <h5><i class="icon fas fa-exclamation-triangle"></i> Database Setup Required!</h5>
                                    <p>The <code>enableapi</code> column is missing from your <code>nas</code> table.</p>
                                    <p><strong>Solution:</strong> Run this SQL command in your database:</p>
                                    <pre><code>ALTER TABLE nas ADD COLUMN enableapi TINYINT(1) DEFAULT 0;</code></pre>
                                    <p>After adding the column, update your router:</p>
                                    <pre><code>UPDATE nas SET enableapi = 1 WHERE id = 244;</code></pre>
                                </div>
                            <?php elseif(empty($routers)): ?>
                                <div class="alert alert-warning">
                                    <h5><i class="icon fas fa-exclamation-triangle"></i> No Enabled Routers Found!</h5>
                                    <p>You need to configure at least one router in the <code>nas</code> table with <code>enableapi = 1</code></p>
                                    <p><strong>Solution:</strong> Run this SQL command:</p>
                                    <pre><code>UPDATE nas SET enableapi = 1 WHERE id = your_router_id;</code></pre>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <h5><i class="icon fas fa-check"></i> Found <?php echo count($routers); ?> Enabled Router(s)</h5>
                                    <p>You can now use the interface monitor.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- All Routers Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">All Routers in Database</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>IP Address</th>
                                        <th>API Username</th>
                                        <th>API Password</th>
                                        <th>Enable API</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($allRouters as $router): ?>
                                    <tr>
                                        <td><?php echo $router->id; ?></td>
                                        <td><?php echo $router->shortname; ?></td>
                                        <td><?php echo $router->nasname; ?></td>
                                        <td><?php echo $router->apiusername; ?></td>
                                        <td><?php echo str_repeat('*', strlen($router->apipassword)); ?></td>
                                        <td>
                                            <?php if(isset($router->enableapi)): ?>
                                                <?php if($router->enableapi == 1): ?>
                                                    <span class="badge badge-success">Enabled</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Disabled</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Column Missing</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $router->description; ?></td>
                                        <td>
                                            <?php if(isset($router->enableapi) && $router->enableapi == 1): ?>
                                                <span class="badge badge-success">Ready</span>
                                            <?php elseif(isset($router->enableapi)): ?>
                                                <span class="badge badge-warning">Not Enabled</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Need Setup</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Configuration Instructions -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Configuration Instructions</h3>
                        </div>
                        <div class="card-body">
                            <h5>To enable a router for API access:</h5>
                            <ol>
                                <li>Make sure your MikroTik router has API access enabled</li>
                                <li>Update the <code>nas</code> table with correct credentials:</li>
                                <pre><code>UPDATE nas SET 
    enableapi = 1,
    apiusername = 'your_api_username',
    apipassword = 'your_api_password'
WHERE id = your_router_id;</code></pre>
                                <li>Ensure the router IP (<code>nasname</code>) is reachable</li>
                                <li>Verify API credentials work with your MikroTik</li>
                            </ol>
                            
                            <h5>MikroTik Router Setup:</h5>
                            <ol>
                                <li>Enable API access in your MikroTik router</li>
                                <li>Create an API user with appropriate permissions</li>
                                <li>Ensure the router IP is accessible from your web server</li>
                                <li>Test connection using the "Test Connection" button</li>
                            </ol>
                            
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Quick Test</h6>
                                <p>You can test a router connection by visiting: <code><?php echo base_url('Mikrotik/Mikrotik_api/debugRouters'); ?></code></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <a href="<?php echo base_url('Mikrotik/Mikrotik_api'); ?>" class="btn btn-primary">
                                <i class="fas fa-network-wired"></i> Go to Interface Monitor
                            </a>
                            <a href="<?php echo base_url('Mikrotik/Mikrotik_api/debugRouters'); ?>" class="btn btn-info">
                                <i class="fas fa-bug"></i> Debug Routers
                            </a>
                            <a href="<?php echo base_url('naslistview'); ?>" class="btn btn-secondary">
                                <i class="fas fa-cog"></i> Manage NAS/Routers
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
</div>
