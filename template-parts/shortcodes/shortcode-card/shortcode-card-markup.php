
<?php
 $isPost = $template_args['isPost'];
 $data = $template_args['card'];
?>



<div class="km-single-card<?php if($isPost) echo ' ksc-post'; ?>" <?php if($isPost) echo 'data-ksc-post="' . $data['id'] . '"'; ?>>

 <?php if($data['img']) : ?>
  <div class="kmsc-img" style="background-image:url(<?php echo $data['img']; ?>);">
    <img class="responsive-only" src="<?php echo $data['img']; ?>" alt="Illustrative Photo" />
  </div>
 <?php endif; ?>

 <div class="kmsc-content">

 <?php if($isPost) : ?>

   <h2><?php echo $data['title']; ?></h2>
   <p><?php echo $data['content']; ?></p>
   <a href="<?php echo $data['link'] ?>">Learn More</a>

  <?php else : ?>
 
    <?php echo $data['content']; ?>

  <?php endif; ?>

 </div>

</div>