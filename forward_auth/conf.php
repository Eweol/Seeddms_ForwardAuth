<?php
$EXT_CONF['foward_auth'] = array(
	'title' => 'Forward Authentication Extension',
	'description' => 'This extension provide login via Forward Authentication Header',
	'disable' => true,
	'version' => '1.0.0	',
	'releasedate' => '2022-10-23',
	'author' => array('name'=>'Eweol', 'email'=>'eweol@outlook.com'),
	'config' => array(
		'foward_authEnable' => array(
			'title'=>'Enable ForwardAuth Login',
			'type'=>'checkbox',
		),
		'usernameHeader' => array(
			'title'=>'Header Name which holds Username to login',
			'type'=>'input',
			'size'=>60,
		),
	),
	'constraints' => array(
		'depends' => array('php' => '5.6.40-', 'seeddms' => '5.1.0-'),
	),
	'icon' => 'icon.svg',
	'changelog' => 'changelog.md',
	'class' => array(
		'file' => 'class.foward_auth.php',
		'name' => 'SeedDMS_ExtForwardAuth'
	),
);
?>
