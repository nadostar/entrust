<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <a href="#">
                    <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">Admin</strong>
                    </span> <span class="text-muted text-xs block">Dev <b class="caret"></b></span> </span> </a>
                </div>
            </li>
            <li class="<?php if($target['tree'] == 'Dashboard') es('active')?>">
                <a href="<?php url('support/dashboard/', true, false); ?>"><i class="fa fa-dashboard"></i> <span class="nav-label">Dashboard</span></a>
            </li>
            <li class="<?php if($target['tree'] == 'FindUsers') es('active')?>">
                <a href="<?php url('support/findUsers/', true, false); ?>"><i class="fa fa-search"></i> <span class="nav-label">Find Users</span></a>
            </li>
            <li class="<?php if($target['tree'] == 'FindSurvey') es('active')?>">
                <a href="<?php url('support/findSurvey/', true, false); ?>"><i class="fa fa-search"></i> <span class="nav-label">Find Survey</span></a>
            </li>
            <?php 
                $menu_array = $LOGIN_USER->getEnableSystemMenu();
                if(count($menu_array) > 0):
            ?>
            <li class="<?php if($target['tree'] == 'System') es('active')?>">
                <a href="#"><i class="fa fa-gears"></i> <span class="nav-label">System</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <?php foreach ($menu_array as $menu): ?>
                    <li class="<?php if($target['menu'] == $menu['id']) es('active')?>"><a href="<?php es(Env::APP_URL.$menu['url']); ?>"><?php es($menu['name']); ?></a></li>    
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>

            <?php 
                $menu_array = $LOGIN_USER->getEnableSurveyMenu();
                if(count($menu_array) > 0):
            ?>
            <li class="<?php if($target['tree'] == 'Survey') es('active')?>">
                <a href="#"><i class="fa fa-files-o"></i> <span class="nav-label">Survey</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <?php foreach ($menu_array as $menu): ?>
                    <li class="<?php if($target['menu'] == $menu['id']) es('active')?>"><a href="<?php es(Env::APP_URL.$menu['url']); ?>"><?php es($menu['name']); ?></a></li>    
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>

            <?php 
                $menu_array = $LOGIN_USER->getEnableEntrustMenu();
                if(count($menu_array) > 0):
            ?>
            <li class="<?php if($target['tree'] == 'Entrust') es('active')?>">
                <a href="#"><i class="fa fa-users"></i> <span class="nav-label">Entrust</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <?php foreach ($menu_array as $menu): ?>
                    <li class="<?php if($target['menu'] == $menu['id']) es('active')?>"><a href="<?php es(Env::APP_URL.$menu['url']); ?>"><?php es($menu['name']); ?></a></li>    
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>

            <?php 
                $menu_array = $LOGIN_USER->getEnableLogMenu();
                if(count($menu_array) > 0):
            ?>
            <li class="<?php if($target['tree'] == 'Logs') es('active')?>">
                <a href="#"><i class="fa fa-inbox"></i> <span class="nav-label">Logs</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <?php foreach ($menu_array as $menu): ?>
                    <li class="<?php if($target['menu'] == $menu['id']) es('active')?>"><a href="<?php es(Env::APP_URL.$menu['url']); ?>"><?php es($menu['name']); ?></a></li>    
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div id="page-wrapper" class="gray-bg dashbard-1">
    <div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
        <form role="search" class="navbar-form-custom" action="search_results.html">
            <div class="form-group">
                <input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
            </div>
        </form>
    </div>

    <ul class="nav navbar-top-links navbar-right">
        <li>
            <a href="#" data-toggle="modal" data-target="#password-dialog" class="btn"> Password</a>
        </li>
        <li>
            <a href="<?php url('support/logout/', true, false); ?>">
                <i class="fa fa-sign-out"></i> Log out
            </a>
        </li>
    </ul>
</nav>
</div>

    <div id="password-dialog" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Admin Password</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form role="form">
                                <div class="form-group"><label>Current Password</label> <input type="password" name="password1" class="form-control"></div>
                                <div class="form-group"><label>New Password</label> <input type="password" name="password2" class="form-control"></div>
                                <div class="form-group"><label>Confirm Password</label> <input type="password" name="password2" class="form-control"></div>
                                <div>
                                    <button class="btn btn-lg btn-rounded btn-primary btn-block" type="submit"><strong>SaveChanges</strong></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>