<?php if($_settings->chk_flashdata('success')): ?>
    <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
    </script>
<?php endif;?>
<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
    $user = $conn->query("SELECT * FROM users where id ='{$_GET['id']}'");
    foreach($user->fetch_array() as $k =>$v){
        $$k = $v;
    }
    $name = ucwords($lastname.', '.$firstname.' '.$middlename);
	$meta_qry = $conn->query("SELECT * FROM employee_meta where user_id = '{$_GET['id']}' ");
	while($row = $meta_qry->fetch_assoc()){
        ${$row['meta_field']} = $row['meta_value'];
    }

}
$department_qry = $conn->query("SELECT id,name FROM department_list");
$dept_arr = array_column($department_qry->fetch_all(MYSQLI_ASSOC),'name','id');
$designation_qry = $conn->query("SELECT id,name FROM designation_list");
$desg_arr = array_column($designation_qry->fetch_all(MYSQLI_ASSOC),'name','id');
?>
<?php 
if(isMobileDevice()):
?>
<style>
    .info-table td{
        display:block !important;
        width:100% !important;
    }
</style>
<?php endif; ?>
<div class="card">
    <div class="card-body">
        <div class="w-100 d-flex justify-content-end mb-3">
            <?php if($_settings->userdata('type') != 3): ?>
            <a href="?page=employees/manage_employee&id=<?php echo $id ?>" class="btn btn-flat btn-primary"><span class="fas fa-edit"></span>  Edit Employee</a>
            <?php endif; ?>
            <a href="javascript:void(0)" class="btn btn-flat btn-success ml-3" id="print"><span class="fas fa-print"></span>  Print</a>
        </div>
        <div id="print_out">
        <table class="table info-table">
            <tr class='boder-0'>
                <td width="20%">
                    <div class="w-100 d-flex align-items-center justify-content-center">
                        <img src="<?php echo validate_image($avatar) ?>" alt="Employee Avatar" class="img-thumbnail" id="cimg">
                    </div>
                </td>
                <td width="80%" class='boder-0 align-bottom'>
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex w-max-100">
                                <label class="float-left w-auto whitespace-nowrap">Employee ID:</label>
                                <p class="col-md-auto border-bottom border-dark w-100"><b><?php echo $employee_id ?></b></p>
                            </div>
                            <div class="d-flex w-max-100">
                                <label class="float-left w-auto whitespace-nowrap">Name:</label>
                                <p class="col-md-auto border-bottom border-dark w-100"><b><?php echo $name ?></b></p>
                            </div>
                            <div class="row justify-content-between  w-max-100 mr-0">
                                <div class="col-6 d-flex w-max-100">
                                    <label class="float-left w-auto whitespace-nowrap">DOB: </label>
                                    <p class="col-md-auto border-bottom px-2 border-dark w-100"><b><?php echo date("M d, Y",strtotime($dob)) ?></b></p>
                                </div>
                                <div class="col-6 d-flex w-max-100">
                                    <label class="float-left w-auto whitespace-nowrap">Contact No.: </label>
                                    <p class="col-md-auto border-bottom px-2 border-dark w-100"><b><?php echo $contact ?></b></p>
                                </div>
                            </div>
                            <div class="d-flex w-max-100">
                                <label class="float-left w-auto whitespace-nowrap">Address:</label>
                                <p class="col-md-auto border-bottom border-dark w-100"><b><?php echo $address ?></b></p>
                            </div>
                            <div class="row justify-content-between  w-max-100  mr-0">
                                <div class="col-6 d-flex w-max-100">
                                    <label class="float-left w-auto whitespace-nowrap">Department: </label>
                                    <p class="col-md-auto border-bottom px-2 border-dark w-100"><b><?php echo $dept_arr[$department_id] ?></b></p>
                                </div>
                                <div class="col-6 d-flex w-max-100">
                                    <label class="float-left w-auto whitespace-nowrap">Designation: </label>
                                    <p class="col-md-auto border-bottom px-2 border-dark w-100"><b><?php echo $desg_arr[$designation_id] ?></b></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <hr class="border-dark">
        <div class="row">
            <div class="col-md-4 col-sm-12">
                <div class="callout border-0">
                    <?php if($_settings->userdata('type') != 3): ?>
                    <div class="float-right">
                        <button class="btn btn-sm btn-default bg-lightblue rounded-circle text-center" type="button" id="manage_leave"><span class="fa fa-cog"></span></button>
                    </div>
                    <?php endif; ?>

                    <h5 class="mb-2">Leave Credits</h5>
                    <table class="table table-hover ">
                        <colgroup>
                            <col width="70%">
                            <col width="15%">
                            <col width="15%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="py-1 px-2">Type</th>
                                <th class="py-1 px-2">Allowable</th>
                                <th class="py-1 px-2">Available</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        if(isset($leave_type_ids) && !empty($leave_type_ids)):
                        $leave_type_credits = isset($leave_type_credits) ? json_decode($leave_type_credits) : array();
                        $ltc = array();
                        foreach($leave_type_credits as $k=> $v){
                            $ltc[$k] = $v;
                        }
                        $lt = $conn->query("SELECT * FROM `leave_types` where `id` in ({$leave_type_ids}) order by code asc ");
                        while($row=$lt->fetch_assoc()):
                            $used = $conn->query("SELECT SUM(`leave_days`) as total FROM `leave_applications` where user_id = '{$id}' and status = 1 and date_format(date_start,'%Y') = '".date('Y')."' and date_format(date_end,'%Y') = '".date('Y')."' and leave_type_id = '{$row['id']}' ")->fetch_array()['total'];
                            $allowed = (isset($ltc[$row['id']])) ? $ltc[$row['id']] : 0;
                            $available =  $allowed - $used;
                        ?>
                        <tr>
                            <td><?php echo $row['code'] ?></td>
                            <td><?php echo number_format($allowed) ?></td>
                            <td><?php echo number_format($available,1) ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-8 col-sm-12">
                <div class="callout border-0">
                    <h5>Records</h5>
                    <table class="table-stripped table">
                        <colgroup>
                                <col width="30%">
                                <col width="20%">
                                <col width="10%">
                                <col width="40%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="p-1">Leave Type</th>
                                <th class="p-1">Date</th>
                                <th class="p-1">Days</th>
                                <th class="p-1">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $la = $conn->query("SELECT l.*,lt.code, lt.name FROM `leave_applications` l inner join `leave_types` lt on l.leave_type_id = lt.id where l.status = 1 and l.user_id = '{$id}' and (date_format(l.date_start,'%Y') = '".date("Y")."' or date_format(l.date_end,'%Y') = '".date("Y")."')  order by unix_timestamp(l.date_start) asc,unix_timestamp(l.date_end) asc ");
                            while($row = $la->fetch_assoc()):
                            ?>
                            <tr>
                                <td class="p-1"><?php echo $row['code'].' - '.$row['name'] ?></td>
                                <td class="p-1">
                                    <?php
                                    if($row['date_start'] == $row['date_end']){
                                        echo date("Y-m-d", strtotime($row['date_start']));
                                    }else{
                                        echo date("Y-m-d", strtotime($row['date_start'])).' - '.date("Y-m-d", strtotime($row['date_end']));
                                    }
                                    ?>
                                </td>
                                <td class="p-1"><?php echo $row['leave_days'] ?></td>
                                <td class="p-1"><small><i><?php echo $row['reason'] ?></i></small></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('#manage_leave').click(function(){
            uni_modal('<i class="fa fa-cog"></i> Manage Leave Credits of <?php echo $name ?>','employees/manage_leave_type.php?id=<?php echo $id ?>');
        })
        $('#print').click(function(){
            start_loader()
            var _h = $('head').clone()
            var _p = $('#print_out').clone();
            var _el = $('<div>')
            _el.append(_h)
            _el.append('<style>html, body, .wrapper {min-height: unset !important;}.btn{display:none !important}</style>')
            _p.prepend('<div class="d-flex mb-3 w-100 align-items-center justify-content-center">'+
            '<img class="mx-4" src="<?php echo validate_image($_settings->info('logo')) ?>" width="50px" height="50px"/>'+
            '<div class="px-2">'+
            '<h3 class="text-center"><?php echo $_settings->info('name') ?></h3>'+
            '<h3 class="text-center">Employee\'s Leave Information Year(<?php echo date("Y") ?>)</h3>'+
            '</div>'+
            '</div><hr/>');
            _el.append(_p)
            var nw = window.open("","_blank","width=1200,height=1200")
                nw.document.write(_el.html())
                nw.document.close()
                setTimeout(() => {
                    nw.print()
                    setTimeout(() => {
                        nw.close()
                        end_loader()
                    }, 300);
                }, 500);
        })
    })
</script>