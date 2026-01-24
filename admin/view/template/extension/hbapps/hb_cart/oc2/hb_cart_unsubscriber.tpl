<div class="table-responsive">
	<table class="table table-bordered table-hover">
	<thead>
		<tr>
		<td class="text-center"><?php echo $column_id; ?></td>
		<td class="text-left col-sm-6"><?php echo $column_email; ?></td>
		<td class="text-left"><?php echo $column_ip; ?></td>
		<td class="text-right"><?php echo $column_date_added; ?></td>
		<td class="text-center"><?php echo $column_action; ?></td>
		</tr>
	</thead>
	<tbody>
		<?php if ($records) { ?>
		<?php foreach ($records as $record) { ?>
		<tr>
			<td class="text-center"><?php echo $record['id']; ?></td>
			<td class="text-left"><?php echo $record['email']; ?></td>
			<td class="text-left"><?php echo $record['ip']; ?></td>
			<td class="text-right"><?php echo $record['date_added']; ?></td>
			<td class="text-center"><a class="btn btn-sm btn-danger" onclick="deleteUnsubscriber(<?php echo $record['id']; ?>);"><i class="fa fa-trash"></i> Delete</a></td>
		</tr>
		<?php } ?>
		<?php } else { ?>
			<tr><td class="text-center" colspan="5">No Records Found</td></tr>
		<?php } ?>				
	</tbody>
	</table>
</div>

<div class="row">
	<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
	<div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>



