<table style="border-collapse: collapse; width: 100%; margin-bottom: 20px;">
<tbody>
  <?php foreach ($products as $product) { ?>
  <tr>
	<td style="font-size: 12px; text-align: center; padding: 7px;"><?php if ($product['image']) { ?>
	  <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-thumbnail" /></a>
	  <?php } ?></td>
	<td style="font-size: 12px;	text-align: left; padding: 7px;"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a> (<?php echo $product['model']; ?>)
	  <?php if (!$product['stock']) { ?>
	  <span class="text-danger">***</span>
	  <?php } ?>
	  <?php if ($product['option']) { ?>
	  <?php foreach ($product['option'] as $option) { ?>
	  <br />
	  <small><?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
	  <?php } ?>
	  <?php } ?>
	</td>
	<td style="font-size: 12px; text-align: right; padding: 7px;"><?php echo $product['price']; ?></td>
	<td style="font-size: 12px;	text-align: center; padding: 7px;">x<?php echo $product['quantity']; ?></td>
	
  </tr>
  <?php } ?>
  </tbody>
  </table>