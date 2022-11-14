<?php
$EXT_CONF['forward_auth'] = array(
	'title' => 'Forward Authentication Extension',
	'description' => 'This extension provide login via Forward Authentication Header',
	'disable' => true,
	'version' => '1.1.0',
	'releasedate' => '2022-11-14',
	'author' => array('name'=>'Eweol', 'email'=>'eweol@outlook.com', 'company'=>'Unimain'),
	'config' => array(
		'forward_authEnable' => array(
			'title'=>'Enable ForwardAuth Login',
			'type'=>'checkbox',
		),
		'usernameHeader' => array(
			'title'=>'Header Name which provide Username to login',
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
		'file' => 'class.forward_auth.php',
		'name' => 'SeedDMS_ForwardAuth'
	),
);
?>
