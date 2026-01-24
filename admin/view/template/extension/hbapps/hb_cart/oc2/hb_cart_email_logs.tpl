		 <div class="table-responsive">
			<table class="table table-bordered table-hover">
			<thead>
				<tr>
				<td class="text-center">ID</td>
				<td class="text-left">Email</td>
				<td class="text-center">Customer Notified</td>
				<td class="text-center">Total Reminders</td>
				<td class="text-center">Email Viewed</td>
				<td class="text-center">Conversion</td>
				<td class="text-right">Order ID</td>
				<td class="text-right">Order Total</td>
				<td class="text-right">Date Added</td>
				<td class="text-right">Date Modified</td>
			  </tr>
			</thead>
			<tbody>
				<?php if ($records) { ?>
				<tr class="success">
					<td class="text-center"></td>
					<td class="text-left">Unique Emails: <?php echo $total_unique_emails; ?></td>
					<td class="text-center">Total Notifications: <?php echo $total_notified; ?></td>
					<td class="text-center">Total Reminders: <?php echo $total_reminder_email; ?></td>
					<td class="text-center">Noticed: <?php echo $total_email_viewed; ?></td>
					<td class="text-center">Conversions: <?php echo $total_responded; ?> (<?php echo $conversion_rate; ?>%)</td>
					<td class="text-right">Total Orders: <?php echo $total_orders; ?></td>
					<td class="text-right">Total Sales: <?php echo $total_sales; ?></td>
					<td class="text-right"></td>
					<td class="text-right"></td>
				</tr>
				<?php foreach ($records as $record) { ?>
				<tr>
					<td class="text-center"><?php echo $record['id']; ?></td>
					<td class="text-left"><?php echo $record['email']; ?></td>
					<td class="text-center"><?php echo $record['first_email']; ?></td>
					<td class="text-center"><?php echo $record['reminder']; ?></td>
					<td class="text-center"><?php echo $record['email_viewed']; ?></td>
					<td class="text-center"><?php echo $record['conversion']; ?></td>
					<td class="text-right"><?php echo $record['order_id']; ?></td>
					<td class="text-right"><?php echo $record['totals']; ?></td>
					<td class="text-right"><?php echo $record['date_added']; ?></td>
					<td class="text-right"><?php echo $record['date_modified']; ?></td>
				</tr>
				<?php } ?>
				<?php } else { ?>
					<tr><td class="text-center" colspan="10">No Records Found</td></tr>
				<?php } ?>				
			</tbody>
			</table>
		</div>
			<div class="row">
			  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
			  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
			</div>
       </div>

