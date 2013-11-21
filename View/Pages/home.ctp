<?php

echo "<h1>Hello!</h1>";

echo $this->Html->link(__("Find Bot"), array('controller' => 'bots', 'action' => 'index'));

echo $this->Html->link(__("Make Bot"), array('controller' => 'bots', 'action' => 'add'));