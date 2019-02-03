<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<?php
    include("response.php");
    $newObj = new Employee();
    $emps = $newObj->getEmployees();
 ?>
 <table id="employee_grid" class="table" width="100%" cellspacing="0">
	<thead>
		<tr>
			<th>Number</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Birth Date</th>
			<th>Gender</th>
			<th>Hired on</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($emps as $key => $emp) :?>
		<tr>
			<td><?php echo $emp['emp_no'] ?></td>
			<td><?php echo $emp['first_name'] ?></td>
			<td><?php echo $emp['last_name'] ?></td>
			<td><?php echo $emp['birth_date'] ?></td>
			<td><?php echo $emp['gender'] ?></td>
			<td><?php echo $emp['hire_date'] ?></td>
			<td><div class="btn-group" data-toggle="buttons"><a href="#" target="_blank" class="btn btn-warning btn-xs">Edit</a><a href="#" target="_blank" class="btn btn-danger btn-xs">Delete</a><a href="#" target="_blank" class="btn btn-primary btn-xs">View</a></div></td>
		</tr>
	<?php endforeach;?>
	</tbody>
</table>