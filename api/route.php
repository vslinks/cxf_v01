<?php
/**
*路由实现，需再此处注册路由，才可访问
*/

Route::user([
	'/' => 'Users.index',
	'/goods' => 'Index.index',
	'/goods/index' => 'Index.index',
	'/god' => 'Index.doSave',

	

]); 
Route::admin([
	'/admin' => 'Index.index',
	'/login' => 'Index.Action',
]); 