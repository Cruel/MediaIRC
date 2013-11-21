<ul class="footer-links nav nav-pills">
  <li><?php echo $this->Html->link(__("Home"), array('controller' => 'pages', 'action' => 'display', 'home')) ?></li>
  <li><?php echo $this->Html->link(__("About"), array('controller' => 'pages', 'action' => 'display', 'about')) ?></li>
  <li><?php echo $this->Html->link(__("Commands"), array('controller' => 'pages', 'action' => 'display', 'commands')) ?></li>
  <li><?php echo $this->Html->link(__("Terms of Use"), array('controller' => 'pages', 'action' => 'display', 'terms')) ?></li>
  <li><?php echo $this->Html->link(__("Legal"), array('controller' => 'pages', 'action' => 'display', 'legal')) ?></li>
  <li class="copyleft">
    <div>
	  	Copyleft
		<!--[if lte IE 8]><span style="filter: FlipH; -ms-filter: "FlipH"; display: inline-block;"><![endif]-->
		<span style="-moz-transform: scaleX(-1); -o-transform: scaleX(-1); -webkit-transform: scaleX(-1); transform: scaleX(-1); display: inline-block;">
			&copy;
		</span>
		<!--[if lte IE 8]></span><![endif]-->
		MediaIRC. No rights reserved. <?php echo date("Y") ?>
	</div>
	<div>All logged media is "property" of respective copyright owners.</div>
  </li>
</ul>