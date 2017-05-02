<!DOCTYPE html>
<html>
<?php include __DIR__ . '/__header.php'; ?>
<head>
</head>

<body>
<div id="wrapper">
<?php include __DIR__ . '/__navbar.php'; ?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>{{__("Admin Management")}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/">Home</a>
            </li>
            <li>
                <a>{{__("Settings")}}</a>
            </li>
            <li class="active">
                <strong>{{__("Admin Management")}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInUp">
    <div class="row">
        <div class="col-lg-12">
            <div class="mail-box-header">
                <h2>
                    {{__("Admin Groups")}} ({{admin_groups.length}})
                </h2>
                <div class="mail-tools tooltip-demo m-t-md">
                    <div class="btn-group pull-right">
                        <button class="btn btn-white btn-sm"><i class="fa fa-arrow-left"></i></button>
                        <button class="btn btn-white btn-sm"><i class="fa fa-arrow-right"></i></button>
                    </div>
                    <button class="new-group btn btn-primary" data-toggle="tooltip" data-placement="left" title="Refresh inbox"><i class="fa fa-edit"></i> {{__("Create Admin Group")}}</button>
                </div>
            </div>
            <div class="mail-box">
                <table class="table table-hover table-mail ">
                <thead>
                <tr>
                    <th>{{__("ID")}}</th>
                    <th>{{__("Name")}}</th>
                    <th>{{__("Description")}}</th>
                </tr>
                </thead>
                <tbody>
                {% for admin_group in admin_groups %}
                <tr class="group read" data-group="{{admin_group.id}}">
                    <td class="mail-ontact"><a href="#">{{admin_group.id}}</a></td>
                    <td>{{admin_group.name}}</td>
                    <td>{{admin_group.description}}</td>
                </tr>
                {% endfor %}
                </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="group-viewer" class="row"></div>

    <div class="row">
        <div class="col-lg-12">
            <div class="mail-box-header">
                <h2>
                    {{__("Admin Users")}} ({{admin_users.length}})
                </h2>
                <div class="mail-tools tooltip-demo m-t-md">
                    <div class="btn-group pull-right">
                        <button class="btn btn-white btn-sm"><i class="fa fa-arrow-left"></i></button>
                        <button class="btn btn-white btn-sm"><i class="fa fa-arrow-right"></i></button>
                    </div>
                    <button class="new-user btn btn-primary" data-toggle="tooltip" data-placement="left" title="Refresh inbox"><i class="fa fa-edit"></i> {{__("Create Admin User")}}</button>
                </div>
            </div>
            <div class="mail-box">
                <table class="table table-hover table-mail ">
                <thead>
                <tr>
                    <th>{{__("Email")}}</th>
                    <th>{{__("Name")}}</th>
                    <th>{{__("Group")}}</th>
                    <th>{{__("Created At")}}</th>
                </tr>
                </thead>
                <tbody>
                {% for admin_user in admin_users %}
                <tr class="user read" data-user="{{admin_user.id}}">
                    <td class="mail-ontact"><a href="#">{{admin_user.email}}</a></td>
                    <td>{{admin_user.name}}</td>
                    <td>{{admin_user.group}}</td>
                    <td>{{admin_user.created_at|local}}</td>
                </tr>
                {% endfor %}
                </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="user-viewer" class="row"></div>
</div>


<?php include __DIR__ . '/__footer.php'; ?>
</div>
</body>
</html>