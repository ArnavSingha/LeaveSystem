<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<?php 
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] : date("Y-m-d",strtotime(date('Y-m-d').' -3 days'));
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] : date("Y-m-d");
?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Reports</h3>
		<!-- <div class="card-tools">
			<a href="?page=offenses/manage_record" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div> -->
	</div>
	<div class="card-body">
		<div class="">
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label for="date_start" class="control-label">Date Start</label>
                    <input type="date" class="form-control" id="date_start" value="<?php echo date("Y-m-d",strtotime($date_start)) ?>">
                </div>
            </div>
            <div class="col-4">
            <div class="form-group">
                    <label for="date_end" class="control-label">Date End</label>
                    <input type="date" class="form-control" id="date_end" value="<?php echo date("Y-m-d",strtotime($date_end)) ?>">
                </div>
            </div>
            <div class="col-2 row align-items-end pb-1">
                <div class="w-100">
                    <div class="form-group d-flex justify-content-between align-middle">
                        <button class="btn btn-flat btn-default bg-lightblue" type="button" id="filter"><i class="fa fa-filter"></i> Filter</button>
                        <button class="btn btn-flat btn-success" type="button" id="print"><i class="fa fa-print"></i> Print</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid" id="print_out">
			<table class="table table-hover table-stripped">
				<colgroup>
					<col width="5%">
					<col width="20%">
					<col width="20%">
					<col width="15%">
					<col width="10%">
					<col width="30%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Employee</th>
						<th>Leave Type</th>
						<th>Date</th>
						<th>Status</th>
						<th>Reason</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
                    $sql = "SELECT l.*,concat(u.lastname,', ',u.firstname,' ',u.middlename) as `name`,lt.code,lt.name as lname from `leave_applications` l inner join `users` u on l.user_id=u.id inner join `leave_types` lt on lt.id = l.leave_type_id where ((date(l.date_start) BETWEEN '$date_start'  and '$date_end') OR (date(l.date_end) BETWEEN '$date_start'  and '$date_end') ) order by unix_timestamp(l.date_start) asc,unix_timestamp(l.date_end) asc";
						$qry = $conn->query($sql);
						while($row = $qry->fetch_assoc()):
                            $lt_qry = $conn->query("SELECT meta_value FROM `employee_meta` where user_id = '{$row['user_id']}' and meta_field = 'employee_id' ");
							$row['employee_id'] = ($lt_qry->num_rows > 0) ? $lt_qry->fetch_array()['meta_value'] : "N/A";
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td>
                                <small><b>ID: </b><?php echo $row['employee_id'] ?></small><br>
                                <small><b>Name: </b><?php echo $row['name'] ?></small>
                            </td>
							<td><?php echo $row['code'] ?> - <?php echo $row['lname'] ?></td>
							<td>
                            <?php
                                if($row['date_start'] == $row['date_end']){
                                    echo date("Y-m-d", strtotime($row['date_start']));
                                }else{
                                    echo date("Y-m-d", strtotime($row['date_start'])).' - '.date("Y-m-d", strtotime($row['date_end']));
                                }
                            ?>
                            </td>
							<td class="text-center">
                            <?php if($row['status'] == 1): ?>
                                <span class="badge badge-success mx-2">Approved</span>
                            <?php elseif($row['status'] == 2): ?>
                                <span class="badge badge-danger mx-2">Denied</span>
                            <?php elseif($row['status'] == 3): ?>
                                <span class="badge badge-danger mx-2">Cancelled</span>
                            <?php else: ?>
                                <span class="badge badge-primary mx-2">Pending</span>
                            <?php endif; ?>
                            </td>
							<td><small><?php echo $row['reason'] ?></small></td>
						</tr>
					<?php endwhile; ?>
					<?php if($qry->num_rows <=0 ): ?>
                        <tr>
                            <th class="text-center" colspan='6'> No Records.</th>
                        </tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
        $('#filter').click(function(){
            location.replace("./?page=reports&date_start="+($('#date_start').val())+"&date_end="+($('#date_end').val()));
        })

        $('#print').click(function(){
            start_loader()
            var _h = $('head').clone()
            var _p = $('#print_out').clone();
            var _el = $('<div>')
            _el.append(_h)
            _el.append('<style>html, body, .wrapper {min-height: unset !important;}</style>')
            var rdate = "";
            if('<?php echo $date_start ?>' == '<?php echo $date_end ?>')
                rdate = "<?php echo date("M d, Y",strtotime($date_start)) ?>";
            else
                rdate = "<?php echo date("M d, Y",strtotime($date_start)) ?> - <?php echo date("M d, Y",strtotime($date_end)) ?>";
            _p.prepend('<div class="d-flex mb-3 w-100 align-items-center justify-content-center">'+
            '<img class="mx-4" src="<?php echo validate_image($_settings->info('logo')) ?>" width="50px" height="50px"/>'+
            '<div class="px-2">'+
            '<h3 class="text-center"><?php echo $_settings->info('name') ?></h3>'+
            '<h3 class="text-center">Leave Application Reports</h3>'+
            '<h4 class="text-center">as of</h4>'+
            '<h4 class="text-center">'+rdate+'</h4>'+
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