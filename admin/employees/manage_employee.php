
<style>
	.select2-container--default .select2-selection--single{
		height:calc(2.25rem + 2px) !important;
	}
</style>
<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
    $user = $conn->query("SELECT * FROM users where id ='{$_GET['id']}'");
    foreach($user->fetch_array() as $k =>$v){
        $meta[$k] = $v;
    }
	$meta_qry = $conn->query("SELECT * FROM employee_meta where user_id = '{$_GET['id']}' ");
	while($row = $meta_qry->fetch_assoc()){
        $meta[$row['meta_field']] = $row['meta_value'];
    }

}
$department_qry = $conn->query("SELECT id,name FROM department_list");
$dept_arr = array_column($department_qry->fetch_all(MYSQLI_ASSOC),'name','id');
$designation_qry = $conn->query("SELECT id,name FROM designation_list");
$desg_arr = array_column($designation_qry->fetch_all(MYSQLI_ASSOC),'name','id');
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-body">
		<div class="container-fluid">
			<div id="msg"></div>
			<form action="" id="manage-user">	
				<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
				<input type="hidden" name="type" value="3">
				<div class="row">
					<div class="col-6">
						<div class="form-group">
							<label for="employee_id">Employee ID</label>
							<input type="text" name="employee_id" id="employee_id" class="form-control rounded-0" value="<?php echo isset($meta['employee_id']) ? $meta['employee_id']: '' ?>" required>
						</div>
						<div class="form-group">
							<label for="firstname">First Name</label>
							<input type="text" name="firstname" id="firstname" class="form-control rounded-0" value="<?php echo isset($meta['firstname']) ? $meta['firstname']: '' ?>" required>
						</div>
						<div class="form-group">
							<label for="middlename">Middle Name</label>
							<input type="text" name="middlename" id="middlename" class="form-control rounded-0" value="<?php echo isset($meta['middlename']) ? $meta['middlename']: '' ?>" required>
						</div>
						<div class="form-group">
							<label for="lastname">Last Name</label>
							<input type="text" name="lastname" id="lastname" class="form-control rounded-0" value="<?php echo isset($meta['lastname']) ? $meta['lastname']: '' ?>" required>
						</div>
						<div class="form-group">
							<label for="dob">DOB</label>
							<input type="date" name="dob" id="dob" class="form-control rounded-0" value="<?php echo isset($meta['dob']) ? date("Y-m-d",strtotime($meta['dob'])): '' ?>" required>
						</div>
						<div class="form-group">
							<label for="contact">Contact #</label>
							<input type="text" name="contact" id="contact" class="form-control rounded-0" value="<?php echo isset($meta['contact']) ? $meta['contact']: '' ?>" required>
						</div>
						<div class="form-group">
							<label for="address">Address</label>
							<textarea rows="3" name="address" id="address" class="form-control rounded-0" style="resize:none !important" required><?php echo isset($meta['address']) ? $meta['address']: '' ?></textarea>
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="department_id">Department</label>
							<select name="department_id" id="department_id" class="form-control select2bs4 select2 rounded-0" data-placeholder="Please Select Department here" reqiured>
								<option value="" disabled <?php echo !isset($meta['department_id']) ? 'selected' : '' ?>></option>
								<?php foreach($dept_arr as $k=>$v): ?>
									<option value="<?php echo $k ?>" <?php echo (isset($meta['department_id']) && $meta['department_id'] == $k) ? 'selected' : '' ?>><?php echo $v ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<label for="designation_id">Designation</label>
							<select name="designation_id" id="designation_id" class="form-control select2bs4 select2 rounded-0" data-placeholder="Please Select Designation here" reqiured>
								<option value="" disabled <?php echo !isset($meta['designation_id']) ? 'selected' : '' ?>></option>
								<?php foreach($desg_arr as $k=>$v): ?>
									<option value="<?php echo $k ?>" <?php echo (isset($meta['designation_id']) && $meta['designation_id'] == $k) ? 'selected' : '' ?>><?php echo $v ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<label for="username">Username</label>
							<input type="text" name="username" id="username" class="form-control rounded-0" value="<?php echo isset($meta['username']) ? $meta['username']: '' ?>" required  autocomplete="off">
						</div>
						<div class="form-group">
							<label for="" class="control-label">Avatar</label>
							<div class="custom-file">
								<input type="hidden" name="avatar" value="<?php echo isset($meta['avatar']) ? $meta['avatar']: '' ?>">
							<input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
							<label class="custom-file-label" for="customFile">Choose file</label>
							</div>
						</div>
						<div class="form-group d-flex justify-content-center">
							<img src="<?php echo validate_image(isset($meta['avatar']) ? $meta['avatar'] :'') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="card-footer">
			<div class="col-md-12">
				<div class="row">
					<button class="btn btn-sm btn-primary mr-2" form="manage-user">Save</button>
					<a class="btn btn-sm btn-secondary" href="./?page=employees">Cancel</a>
				</div>
			</div>
		</div>
</div>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$('#manage-user').submit(function(e){
		e.preventDefault();
var _this = $(this)
		start_loader()
		$.ajax({
			url:_base_url_+'classes/Master.php?f=save_employee',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
		    dataType: 'json',
			error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
			success:function(resp){
				if(typeof resp =='object' && resp.status == 'success'){
					location.href = './?page=employees/records&id='+resp.id;
				}else if(resp.status == 'failed' && !!resp.msg){
					var el = $('<div>')
						el.addClass("alert alert-danger err-msg").text(resp.msg)
						_this.prepend(el)
						el.show('slow')
						$("html, body").animate({ scrollTop: 0 }, "fast");
				}else{
					alert_toast("An error occured",'error');
					console.log(resp)
				}
                end_loader()
			}
		})
	})
	$(function(){
		$('.select2').select2()
		$('.select2-selection').addClass('form-control rounded-0')
	})

</script>