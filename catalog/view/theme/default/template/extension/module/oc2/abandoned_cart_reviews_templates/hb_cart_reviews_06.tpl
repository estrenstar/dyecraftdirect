<?php if (!empty($reviews)) { ?>
<h3 style="Margin:0; line-height:29px; font-size:18px;font-style:normal;font-weight:normal;color:#888B8D;text-align: center; margin-bottom:20px;"><?php echo $text_reviews; ?></h3>

  <?php foreach ($reviews as $review) { ?>
    <table style="border-collapse: collapse; width: 100%; margin-bottom: 20px;">
      <tbody>
        <tr style="border:#d6d6d6 1px solid;">
        <td style="font-size: 12px;	text-align: center; padding: 7px; max-width:150px" valign="top">
          <center><img src="<?php echo $https_server; ?>image/catalog/hb_cart/44301521732133350.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></center><br>
          <img src="<?php echo $https_server; ?>image/catalog/hb_cart/stars-<?php echo $review['rating']; ?>.png" alt style="display:block;border:0; margin-bottom:5px; outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;">
        </td>
        <td style="font-size: 12px; text-align: left; padding: 7px;" valign="top">
          <span style="font-size: 12px; text-align: left; line-height: 18px;"><?php echo $review['text']; ?></span><br />
          <span style="font-size: 12px; text-align: left; color:#913500; line-height: 25px; font-style:italic;"><?php echo $review['author']; ?></span><br />
        </td>
        </tr>
      </tbody>
    </table>
  <?php } ?> 
<?php } ?>