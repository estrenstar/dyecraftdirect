<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
    <thead>
      <tr>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: center; padding: 7px; color: #222222;"><?php echo $text_product_image; ?></td>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;"><?php echo $text_product; ?></td>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: center; padding: 7px; color: #222222;"><?php echo $text_quantity; ?></td>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;"><?php echo $text_price; ?></td>
      </tr>
    </thead>
<tbody>
  <?php foreach ($products as $product) { ?>
  <tr>
	<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: center; padding: 7px;"><?php if ($product['image']) { ?>
	  <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-thumbnail" /></a>
	  <?php } ?></td>
	<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a> (<?php echo $product['model']; ?>)
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
	<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: center; padding: 7px;"><?php echo $product['quantity']; ?></td>
	<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $product['price']; ?></td>
  </tr>
  <?php } ?>
  </tbody>
  </table>