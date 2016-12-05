<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sat, 19 Mar 2011 16:50:45 GMT
 */

if( !defined( 'NV_IS_MOD_NVTOOLS' ) )
	die( 'Stop!!!' );

define( 'NV_ADMIN', true );

$data_system['author_name'] = 'VINADES.,JSC';
$data_system['author_email'] = 'contact@vinades.vn';
define( 'AUTHOR_FILEHEAD', "/**\n * @Project NUKEVIET 4.x\n * @Author " . $data_system['author_name'] . " (" . $data_system['author_email'] . ")\n * @Copyright (C) " . gmdate( "Y" ) . " " . $data_system['author_name'] . ". All rights reserved\n * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/\n * @Createdate " . gmdate( "D, d M Y H:i:s" ) . " GMT\n */" );

$modname = $nv_Request->get_title( 'modname', 'get,post' );

if( $nv_Request->isset_request( 'submit', 'post,get' ) )
{
	$moddata = preg_replace( '#-#', '_', $modname );

	$export_structure = $nv_Request->get_array( 'export_structure', 'post,get' );
	$export_data = $nv_Request->get_array( 'export_data', 'post,get' );
	$data_config = $nv_Request->get_int( 'data_config', 'post,get', 0 );
	$folder = $nv_Request->get_int( 'folder', 'post,get', 1 );

	$content_action = "<?php\n\n";
	$content_action .= AUTHOR_FILEHEAD . "\n\n";
	$content_action .= "if ( ! defined( 'NV_IS_FILE_MODULES' ) ) die( 'Stop!!!' );\n\n";
	$content_action .= "\$sql_drop_module = array();\n";

	if( !empty( $export_structure ) )
	{
		$sql_create = $sql_drop = "";
		foreach( $export_structure as $key => $tablename )
		{
			$setlang = preg_match( "/" . $db_config['prefix'] . "\_([a-z]{2}+)\_/", $tablename ) ? 1 : 0;

			$sql_drop .= "\$sql_drop_module[] = \"DROP TABLE IF EXISTS \" . \$db_config['prefix'] . \"";
			$name = '';
			if( $setlang and preg_match( "/" . $db_config['prefix'] . "\_([a-z]{2}+)\_" . $moddata . "\_([a-z0-9\_]*)/", $tablename, $match ) )
			{
				$sql_drop .= "_\" . \$lang . \"";
				$name = $match[2];
			}
			elseif( preg_match( "/" . $db_config['prefix'] . "\_" . $moddata . "\_([a-z0-9\_]*)/", $tablename, $match ) )
			{
				$name = $match[1];
			}
			if( !empty( $name ) )
			{
				$name = '_' . $name;
			}
			$sql_drop .= "_\" . \$module_data . \"" . $name . "\";\n";
			$resulttab = $db->query( 'SHOW  CREATE TABLE ' . $tablename );
			while( $row = $resulttab->fetch( ) )
			{
				preg_match( "/^(CREATE TABLE `?[^` ]+`? .*?\()([^\;]+)\)([^\)]*)\;?$/im", $row['create table'], $matches );
				$matches[2] = str_replace( '`', '', $matches[2] );
				$temp = "\$sql_create_module[] = \"CREATE TABLE \" . \$db_config['prefix'] . \"";
				if( $setlang )
				{
					$temp .= "_\" . \$lang . \"";
				}
				$temp .= "_\" . \$module_data . \"" . $name . "(\n" . $matches[2] . "\n) ENGINE=MyISAM\";";
				$sql_create .= preg_replace( "/(\r\n)+|(\n|\r)+/", "\r\n", $temp ) . "\n";
			}
			$sql_create .= "\n";
		}
		$content_action .= $sql_drop;
		$content_action .= "\n";
		$content_action .= "\$sql_create_module = \$sql_drop_module;\n";
		$content_action .= $sql_create;
	}
	if( !empty( $export_data ) )
	{
		$sql_insert = '';
		foreach( $export_structure as $key => $tablename )
		{
			$setlang = preg_match( "/" . $db_config['prefix'] . "\_([a-z]{2}+)\_/", $tablename ) ? 1 : 0;

			$name = '';
			$match;
			if( $setlang and preg_match( "/" . $db_config['prefix'] . "\_([a-z]{2}+)\_" . $moddata . "\_([a-z0-9\_]*)/", $tablename, $match ) )
			{
				$name = $match[2];
			}
			elseif( preg_match( "/" . $db_config['prefix'] . "\_" . $moddata . "\_([a-z0-9\_]*)/", $tablename, $match ) )
			{
				$name = $match[1];
			}
			if( !empty( $name ) )
			{
				$name = '_' . $name;
			}
			$array_column = array( );
			$_sql = ' SHOW COLUMNS FROM ' . $tablename;
			$_query = $db->query( $_sql );
			while( $row = $_query->fetch( ) )
			{
				$array_column[] = $row['field'];
			}
			$array_column = implode( ', ', $array_column );

			$_sql = 'SELECT * FROM ' . $tablename;
			$_query = $db->query( $_sql );
			while( $rows = $_query->fetch( ) )
			{
				$rows = str_replace( '"', '\"', $rows );
				$row = implode( "', '", $rows );
				$sql_insert .= "\$sql_create_module[] = \"INSERT INTO \" . \$db_config['prefix'] . \"";
				if( $setlang )
				{
					$sql_insert .= "_\" . \$lang . \"";
				}
				$sql_insert .= "_\" . \$module_data . \"" . $name . " (" . $array_column . ") ";
				$sql_insert .= "VALUES('" . $row . "')\";\n";
			}
			$sql_insert .= "\n";
		}
		$content_action .= $sql_insert;
	}
	if( $data_config == 1 )
	{
		$sql_config = '';
		$_sql = 'SELECT * FROM ' . NV_CONFIG_GLOBALTABLE . ' WHERE module=' . $db->quote( $modname );
		$_query = $db->query( $_sql );
		while( $row = $_query->fetch( ) )
		{
			$sql_config .= "\$sql_create_module[] = \"INSERT INTO \" . NV_CONFIG_GLOBALTABLE . \"";
			$sql_config .= "(lang, module, config_name, config_value) ";
			$sql_config .= "VALUES ('\" . \$lang . \"', '\" . \$module_name . \"', '" . $row['config_name'] . "', '" . $row['config_value'] . "')\";\n";
		}
		$content_action .= $sql_config;
	}
	if( $folder == 2 )
	{
		file_put_contents( NV_ROOTDIR . '/modules/' . $modname . '/action_mysql.php', trim( $content_action ) );
	}
	else
	{
		file_put_contents( NV_ROOTDIR . '/' . NV_TEMP_DIR . '/action_mysql.php', trim( $content_action ) );
	}

}

$page_title = $lang_module['SiteTitleModule'];
$key_words = $module_info['keywords'];

$xtpl = new XTemplate( $op . ".tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );

$xtpl->assign( 'MODNAME', $modname );

if( preg_match( '/^[a-zA-Z0-9\_\-]+$/', $modname ) )
{
	$modname = preg_replace( '#-#', '_', $modname );
	$export_structure = $nv_Request->get_array( 'export_structure', 'post,get' );
	$export_data = $nv_Request->get_array( 'export_data', 'post,get' );
	$data_config = $nv_Request->get_int( 'data_config', 'post,get', 0 );
	$folder = $nv_Request->get_int( 'folder', 'post,get', 1 );

	$array_structure = array( );
	if( !empty( $export_structure ) )
	{
		foreach( $export_structure as $value )
		{
			$array_structure[$value] = $value;
		}
	}
	$array_data = array( );
	if( !empty( $export_data ) )
	{
		foreach( $export_data as $value )
		{
			$array_data[$value] = $value;
		}
	}
	$i = 1;
	$result = $db->query( 'SHOW TABLE STATUS LIKE ' . $db->quote( '%' . $db_config['prefix'] . '_' . NV_LANG_DATA . '_' . $modname . '%' ) );
	while( $item = $result->fetch( ) )
	{
		$item['stt'] = $i;
		++$i;
		$item['checked'] = ( isset( $array_structure[$item['name']] )) ? 'checked=\"checked\"' : '';
		$item['checkeddata'] = ( isset( $array_data[$item['name']] )) ? 'checked=\"checked\"' : '';
		$xtpl->assign( 'ITEM', $item );
		$xtpl->parse( 'main.form.item' );
	}
	$result = $db->query( 'SHOW TABLE STATUS LIKE ' . $db->quote( '%' . $db_config['prefix'] . '_' . $modname . '%' ) );
	while( $item = $result->fetch( ) )
	{
		$item['stt'] = $i;
		++$i;
		$item['checked'] = ( isset( $array_structure[$item['name']] )) ? 'checked=\"checked\"' : '';
		$item['checkeddata'] = ( isset( $array_data[$item['name']] )) ? 'checked=\"checked\"' : '';
		$xtpl->assign( 'ITEM', $item );
		$xtpl->parse( 'main.form.item' );
	}
	$checkedconfig = ($data_config == 1) ? 'checked=\"checked\"' : '';
	$xtpl->assign( 'CHECKEDCONFIG', $checkedconfig );

	$array_folder = array( );
	$array_folder[1] = 'Thư mục tmp';
	$array_folder[2] = 'Thư mục của module';

	foreach( $array_folder as $key => $title )
	{
		$xtpl->assign( 'OPTION', array(
			'key' => $key,
			'title' => $title,
			'checked' => ($key == $folder) ? ' checked="checked"' : ''
		) );
		$xtpl->parse( 'main.form.folder' );
	}
	$xtpl->parse( 'main.form' );
}
else
{
	$modules_exit = nv_scandir( NV_ROOTDIR . '/modules', $global_config['check_module'] );
	foreach( $modules_exit as $mod_i )
	{
		$xtpl->assign( 'MODNAME', array(
			'value' => $mod_i,
			'selected' => ($modname == $mod_i) ? ' selected="selected"' : ''
		) );
		$xtpl->parse( 'main.tablename.modname' );
	}
	$xtpl->parse( 'main.tablename' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
