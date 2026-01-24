<div class="table-responsive">
	<table class="table table-bordered table-hover">
	<thead>
		<tr>
		<td class="text-center"><?php echo $column_customer_id; ?></td>
		<td class="text-center col-sm-4"><?php echo $column_cart; ?></td>
		<td class="text-center"><?php echo $column_work_log; ?></td>
		<td class="text-center col-sm-2"><?php echo $column_action; ?></td>
		</tr>
	</thead>
	<tbody>
		<?php if ($records) { ?>
		<?php foreach ($records as $record) { ?>
		<tr <?php echo ($record['is_abandoned'])? 'class="danger"' : '' ?> >
		<td>
			<table class="table table-bordered table-hover">
				<?php if ($type == 'customer' || $type == 'guest') { ?>
					<?php if (isset($record['customer_info']['firstname'])) { ?>	
					<tr class="hb_customer_name">
						<td class="text-center"><span data-toggle="tooltip" title="" data-original-title="Customer Name"><i class="fa fa-user"></i></span></td>
						<td><a href="<?php echo $record['customer_link']; ?>" target="_blank"><?php echo $record['customer_info']['firstname'].' '.$record['customer_info']['lastname']; ?> [<?php echo $record['customer_id']; ?>]</a></td>
					</tr>
					<?php } ?>
				<tr class="hb_email">
					<td class="text-center"><span data-toggle="tooltip" title="" data-original-title="Customer Email"><i class="fa fa-envelope"></i></span></td>
					<td><?php echo $record['customer_info']['email']; ?></td>
				</tr>

				<?php if (isset($record['customer_info']['telephone'])) { ?>
				<tr class="hb_phone">
					<td class="text-center"><span data-toggle="tooltip" title="" data-original-title="Customer Phone"><i class="fa fa-phone"></i></span></td>
					<td><?php echo $record['customer_info']['telephone']; ?></td>
				</tr>
				<?php } ?>

				<tr class="hb_since">
					<td class="text-center"><span data-toggle="tooltip" title="" data-original-title="Customer Since"><i class="fa fa-info"></i></span></td>
					<td><?php echo $record['customer_since']; ?></td>
				</tr>
				
				<tr class="hb_purchases">
					<td class="text-center"><span data-toggle="tooltip" title="" data-original-title="Total Previous Purchases"><i class="fa fa-money"></i></span></td>
					<td><?php echo $record['customer_purchases']; ?></td>
				</tr>
				<?php } ?>

				<tr class="hb_session">
					<td class="text-center"><span data-toggle="tooltip" title="" data-original-title="Customer Session ID"><i class="fa fa-street-view"></i></span></td>
					<td><?php echo $record['session_id']; ?></td>
				</tr>
				<tr class="hb_ip">
					<td class="text-center"><span data-toggle="tooltip" title="" data-original-title="Customer IP"><i class="fa fa-globe"></i></span></td>
					<td> <?php echo $record['ip']; ?></td>
				</tr>						
			</table>
		</td>
		<td class="col-sm-4">
			<table class="table table-bordered">
			<?php foreach ($record['cart'] as $cart) { ?>
				<tr>
					<td class="text-center"><img src="<?php echo $cart['image']; ?>"/></td>
					<td><span style="color:#3399CC; font-weight:bold;"><?php echo $cart['name']; ?></span> [<?php echo $cart['model']; ?>]
							<?php if ($cart['option']) { ?>
							<?php foreach ($cart['option'] as $option) { ?>
							<br /><small><b><?php echo $option['name']; ?></b>: <?php echo $option['value']; ?></small>
							<?php } ?>
							<?php } ?>
					</td>
					<td>x <?php echo $cart['quantity']; ?></td>
					<td><?php echo $cart['total']; ?></td>
				</tr>
			<?php } ?>
			</table>
			<?php if ($record['last_visited'] != '') { ?>
				<span class="hb_last_visited"><b><?php echo $text_last_visited; ?> :</b><?php echo $record['last_visited']; ?></span>
			<?php } ?>	
		</td>
		
		<td class="text-left">
			<table class="table table-bordered table-hover">
				<?php if ($type == 'customer' || $type == 'guest') { ?>
				<tr class="hb_first_email_sent">
					<td class="text-left col-sm-4"><?php echo $text_first_email_sent; ?></td>
					<td class="text-left"><?php echo $record['email_sent']; ?></td>
				</tr>
				<tr class="hb_email_viewed">
					<td class="text-left"><?php echo $text_email_viewed; ?></td>
					<td class="text-left"><?php echo $record['email_viewed']; ?></td>
				</tr>
				<tr class="hb_reminder">
					<td class="text-left"><?php echo $text_reminder; ?></td>
					<td class="text-left"><?php echo $record['reminder']; ?></td>
				</tr>
				<?php } ?>

				<tr class="hb_date_active">
					<td class="text-left"><?php echo $text_date_active; ?></td>
					<td class="text-left"><?php echo $record['date_added']; ?></td>
				</tr>
				<tr class="hb_date_modified">
					<td class="text-left"><?php echo $text_date_modified; ?></td>
					<td class="text-left"><?php echo $record['date_modified']; ?></td>
				</tr>
				<tr class="hb_time_inactive">
					<td class="text-left"><?php echo $text_time_inactive; ?></td>
					<td class="text-left"><?php echo $record['hours_inactive']; ?> HRS</td>
				</tr>
				<?php if ($record['last_visited'] != '') { ?>
				<tr class="hb_last_visited">
					<td class="text-left"><?php echo $text_last_visited; ?></td>
					<td class="text-left"><a href="<?php echo $record['last_visited']; ?>" target="_blank" data-toggle="tooltip" title="<?php echo $record['last_visited']; ?>" class="btn btn-sm btn-default"><?php echo $text_last_visited; ?>&nbsp;<i class="fa fa-external-link"></i></a></td>
				</tr>
				<?php } ?>	
			</table>
		</td>
		
		
		<td class="text-center col-sm-2">
		<?php if ($type == 'customer' || $type == 'guest') { ?>
			<?php if ($record['first_email'] == 0) { ?>
				<div class="col-sm-12" style="margin-bottom:10px;"><a class="btn btn-success col-sm-12 button-email" onclick="emailPreview('<?php echo $record['customer_id']; ?>', '<?php echo $record['session_id']; ?>',0);" ><i class="fa fa-envelope"></i> Send Email</a></div>
			<?php } else { ?>
				<div class="col-sm-12" style="margin-bottom:10px;"><a class="btn btn-warning col-sm-12 button-email" onclick="emailPreview('<?php echo $record['customer_id']; ?>', '<?php echo $record['session_id']; ?>', 1)" ><i class="fa fa-envelope"></i> Send Reminder Email</a></div>
			<?php } ?>
		<?php } ?>

		<div class="col-sm-12" style="margin-bottom:10px;"><a class="btn btn-danger col-sm-12" onclick="deleteCart('<?php echo $record['session_id']; ?>');" id="button-deletecart"><i class="fa fa-trash"></i> Delete Record</a></div>
		</td>
		</tr>
		<?php } ?>
		<?php } else { ?>
		<tr><td class="text-center" colspan="4">No Records Found</td></tr>
		<?php } ?>				
	</tbody>
	</table>
</div>
	<div class="row">
		<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
		<div class="col-sm-6 text-right"><?php echo $results; ?></div>
	</div>
</div>

