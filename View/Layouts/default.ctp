<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        MediaIRC - <?php echo $title_for_layout; ?>
    </title>
    <?php
    echo $this->Html->meta('favicon.png', '/favicon.png', array('type' => 'icon'));

    echo $this->Html->css('bootstrap.min');
    echo $this->Html->css('bootstrap-theme.min');
    echo $this->Html->css('colorbox');
    echo $this->Html->css('mediairc');

    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    ?>

</head>
<body>
<!--[if lt IE 7]>
	<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->

<div id="wrap">
	<div class="container">
		<?php echo $this->element("header") ?>
		<div id="content">
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
			<div class="clearfix"></div>
		</div>
	</div> <!-- /container -->
</div> <!-- /wrap -->

<div id="canvas"></div>

<div id="footer">
	<div class="container text-center">
		<?php echo $this->element("footer") ?>
	</div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<?php echo $this->Html->script('jquery.history'); ?>
<?php echo $this->Html->script('masonry.pkgd.min'); ?>
<?php echo $this->Html->script('jquery.colorbox-min'); ?>
<?php echo $this->Html->script('bootstrap.min'); ?>
<?php echo $this->Html->script('mediairc'); ?>
<?php echo $this->Html->script('protoclass'); ?>
<?php echo $this->Html->script('box2d'); ?>
<?php echo $this->Html->script('mediairc.box2d'); ?>
</body>
</html>
