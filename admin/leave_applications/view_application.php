<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT l.*,concat(u.lastname,' ',u.firstname,' ',u.middlename) as `name`,lt.code,lt.name as lname from `leave_applications` l inner join `users` u on l.user_id=u.id inner join `leave_types` lt on lt.id = l.leave_type_id  where l.id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
    $lt_qry = $conn->query("SELECT meta_value FROM `employee_meta` where user_id = '{$user_id}' and meta_field = 'employee_id' ");
    $employee_id = ($lt_qry->num_rows > 0) ? $lt_qry->fetch_array()['meta_value'] : "N/A";
}
?>
<style>
    p,label{
        margin-bottom:5px;
    }
    #uni_modal .modal-footer{
        display:none !important;
    }
    
</style>
<div class="container-fluid">
    <?php if($_settings->userdata('type') != 3): ?>
    <p class="m-0"><b>Employee ID: </b> <br><span class="mx-2"><?php echo $employee_id ?></span></p>
    <p class="m-0"><b>Name: </b> <br><span class="mx-2"><?php echo $name ?></span></p>
    <?php endif; ?>
    <p class="m-0"><b>Leave Type: </b> <br><span class="mx-2"><?php echo $code.' - '.$lname ?></span></p>
    <p class="m-0"><b>Date: </b> <br><span class="mx-2">
    <?php
        if($date_start == $date_end){
            echo date("Y-m-d", strtotime($date_start));
        }else{
            echo date("Y-m-d", strtotime($date_start)).' - '.date("Y-m-d", strtotime($date_end));
        }
    ?>
    </span></p>
    <p class="m-0"><b>Days of Leave: </b> <br><span class="mx-2"><?php echo $leave_days ?></span></p>
    <p class="m-0"><b>Reason: </b><br><span class="mx-2"><?php echo $reason ?></span></p>
    <p><b>Status</b><br>
    <?php if($status == 1): ?>
        <span class="badge badge-success mx-2">Approved</span>
    <?php elseif($status == 2): ?>
        <span class="badge badge-danger mx-2">Denied</span>
    <?php elseif($status == 3): ?>
        <span class="badge badge-danger mx-2">Cancelled</span>
    <?php else: ?>
        <span class="badge badge-primary mx-2">Pending</span>
    <?php endif; ?>
    </p>
    <div class="w-100 d-flex justify-content-end mb-2">
        <button class="btn btn-flat btn-sm btn-default bg-black" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
    </div>
</div>
