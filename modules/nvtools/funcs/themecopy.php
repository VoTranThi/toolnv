<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sat, 19 Mar 2011 16:50:45 GMT
 */

if( ! defined( 'NV_IS_MOD_NVTOOLS' ) ) die( 'Stop!!!' );

$page_title = $lang_module['themecopy'];
$key_words = $module_info['keywords'];

$array_mod_title[] = array(
	'catid' => 0,
	'title' => $lang_module['themecopy'],
	'link' => $client_info['selfurl']
);

$xtpl = new XTemplate( "themecopy.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );

// Get theme config
$ini = file( NV_ROOTDIR . '/modules/' . $module_file . '/ini/theme.ini' );
$theme_syscfg = array();
$section = '';

foreach( $ini as $line )
{
	$line = trim( $line );

	if( ! empty( $line ) )
	{
		if( preg_match( '/^\[(.*?)\]$/', $line, $match ) )
		{
			$section = $match[1];
			continue;
		}

		if( ! empty( $section ) )
		{
			$theme_syscfg[$section][] = $line;
		}
	}
}

unset( $ini, $line, $section );

if( $nv_Request->isset_request( 'theme', 'get,post' ) )
{
	$theme = trim( $nv_Request->get_title( 'theme', 'get,post', '' ) );

	$array_layouts = array();
	$array_modules = array();
	$array_files = array();
	$array_modules_name = array();

	$array_files = nv_list_all_files( NV_ROOTDIR . '/themes/' . $theme );
	$array_layouts = nv_scandir( NV_ROOTDIR . '/themes/' . $theme . '/layout', "/^layout\.(.*?)\.tpl$/" );
	$array_modules_name = nv_scandir( NV_ROOTDIR . '/themes/' . $theme . '/modules', $global_config['check_module'] );

	foreach( $array_modules_name as $module )
	{
		$array_modules[$module] = array(
			'images' => array(),
			'css' => array(),
			'tpl' => array()
		);
	}

	foreach( $array_files as $file )
	{
		if( preg_match( "/^modules\/(.*?)\/(.*?)$/", $file, $m ) )
		{
			if( isset( $array_modules[$m[1]] ) )
			{
				$array_modules[$m[1]]['tpl'][] = $m[2];
			}
		}

		if( preg_match( "/^images\/(.*?)\/(.*?)$/", $file, $m ) )
		{
			if( isset( $array_modules[$m[1]] ) )
			{
				$array_modules[$m[1]]['images'][] = $m[2];
			}
		}
	}

	if( $nv_Request->isset_request( 'newthemename', 'post' ) )
	{
		$newthemename = $nv_Request->get_title( 'newthemename', 'post', '' );
		$layouts = $nv_Request->get_title( 'layouts', 'post', '' );
		$modules = $nv_Request->get_title( 'modules', 'post', '' );

		$layouts = explode( '|', $layouts );
		$modules = explode( '|', $modules );

		if( empty( $newthemename ) )
		{
			die( 'ERR|' . $lang_module['themecopy_new_warnning'] );
		}
		elseif( empty( $layouts ) )
		{
			die( 'ERR|' . $lang_module['themecopy_layout_warnning1'] );
		}
		elseif( ! preg_match( $global_config['check_theme'], $newthemename ) )
		{
			die( 'ERR|' . sprintf( $lang_module['themecopy_error_valid_name'], $newthemename ) );
		}
		elseif( file_exists( NV_ROOTDIR . '/themes/' . $newthemename ) )
		{
			die( 'ERR|' . $lang_module['themecopy_error_exists'] );
		}

		// Calculate all file
		$files_folders = array();

		foreach( $array_files as $file )
		{
			if( preg_match( "/^css\/(.*?)\.css$/", $file, $m ) )
			{
				if( ! isset( $array_modules[$m[1]] ) or in_array( $m[1], $modules ) )
				{
					if( $theme == 'default' and $m[1] != 'font-awesome.min' )
					{
						$files_folders[] = $file;
					}
				}
			}
			elseif( preg_match( "/^js\/(.*?)\.js$/", $file, $m ) )
			{
				if( ! isset( $array_modules[$m[1]] ) or in_array( $m[1], $modules ) )
				{
					$files_folders[] = $file;
				}
			}
			elseif( preg_match( "/^images\/(.*?)\/(.*?)$/", $file, $m ) )
			{
				if( ! isset( $array_modules[$m[1]] ) or in_array( $m[1], $modules ) )
				{
					$files_folders[] = $file;
				}
			}
			elseif( preg_match( "/^layout\/layout\.(.*?)\.tpl$/", $file, $m ) )
			{
				if( in_array( $m[1], $layouts ) )
				{
					$files_folders[] = $file;
				}
			}
			elseif( preg_match( "/^modules\/(.*?)\/(.*?)$/", $file, $m ) )
			{
				if( ! isset( $array_modules[$m[1]] ) or in_array( $m[1], $modules ) )
				{
					$files_folders[] = $file;
				}
			}
			elseif( $theme == 'default' and preg_match( "/^fonts\/(.*?)\.(eot|otf|svg|ttf|woff|woff2)$/", $file ) )
			{

			}
			elseif( $theme == 'default' and preg_match( "/^layout\/block\.(.*?)\.tpl$/", $file, $m ) )
			{
				if( $m[1] == 'default' or $m[1] == 'no_title' )
				{
					$files_folders[] = $file;
				}
			}
			elseif( $file != 'system/dump.tpl' )
			{
				$files_folders[] = $file;
			}
		}

		// Creat new theme folder
		$CreatNew = nv_mkdir( NV_ROOTDIR . '/themes', $newthemename );

		if( $CreatNew[0] == 0 )
		{
			die( 'ERR|' . $CreatNew[1] );
		}

		// Make folder
		$ftp_check_login = 0;

		if( $sys_info['ftp_support'] and intval( $global_config['ftp_check_login'] ) == 1 )
		{
			$ftp_server = nv_unhtmlspecialchars( $global_config['ftp_server'] );
			$ftp_port = intval( $global_config['ftp_port'] );
			$ftp_user_name = nv_unhtmlspecialchars( $global_config['ftp_user_name'] );
			$ftp_user_pass = nv_unhtmlspecialchars( $global_config['ftp_user_pass'] );
			$ftp_path = nv_unhtmlspecialchars( $global_config['ftp_path'] );
			// set up basic connection
			$conn_id = ftp_connect( $ftp_server, $ftp_port, 10 );
			// login with username and password
			$login_result = ftp_login( $conn_id, $ftp_user_name, $ftp_user_pass );
			if( ( ! $conn_id ) || ( ! $login_result ) )
			{
				$ftp_check_login = 3;
			}
			elseif( ftp_chdir( $conn_id, $ftp_path ) )
			{
				$ftp_check_login = 1;
			}
			else
			{
				$ftp_check_login = 2;
			}
		}

		// Tao thu muc
		foreach( $files_folders as $file )
		{
			$cp = '';
			$e = explode( '/', $file );
			$s = sizeof( $e ) - 1;

			if( $s > 0 )
			{
				foreach( $e as $i => $p )
				{
					if( $i != $s and ! empty( $p ) and ! is_dir( NV_ROOTDIR . '/themes/' . $newthemename . '/' . $cp . $p ) )
					{
						if( $ftp_check_login == 1 )
						{
							ftp_mkdir( $conn_id, NV_ROOTDIR . '/themes/' . $newthemename . '/' . $cp . $p );
							if( substr( $sys_info['os'], 0, 3 ) != 'WIN' ) ftp_chmod( $conn_id, 0777, NV_ROOTDIR . '/themes/' . $newthemename . '/' . $cp . $p );

							if( ! is_dir( NV_ROOTDIR . '/themes/' . $newthemename . '/' . $cp . $p ) )
							{
								die( 'ERR|' . $lang_module['themecopy_error_creat_folder'] . ' ' . $cp . $p );
							}
						}
						else
						{
							$CreatNew = nv_mkdir( NV_ROOTDIR . '/themes/' . $newthemename . '/' . $cp, $p );

							if( $CreatNew[0] == 0 )
							{
								die( 'ERR|' . $CreatNew[1] );
							}
						}
					}
					$cp .= $p . '/';
				}
			}
		}

		// Copy file
		foreach( $files_folders as $file )
		{
			if( ! nv_copyfile( NV_ROOTDIR . '/themes/' . $theme . '/' . $file, NV_ROOTDIR . '/themes/' . $newthemename . '/' . $file ) )
			{
				die( 'ERR|' . $lang_module['themecopy_error_copy_file'] . ' ' . $file );
			}
		}

		// Save to database
		try
		{
			$sql = 'INSERT INTO ' . $db_config['prefix'] . '_setup_extensions VALUES( 0, \'theme\', :title, 0, 0, :basename, :table_prefix, :version, ' . NV_CURRENTTIME . ', \'VINADES (contact@vinades.vn)\', :note )';
			$table_prefix = preg_replace( '/(\W+)/i', '_', $newthemename );
			$version = '4.0.0 ' . NV_CURRENTTIME;

			$sth = $db->prepare( $sql );
			$sth->bindParam( ':title', $newthemename, PDO::PARAM_STR );
			$sth->bindParam( ':basename', $newthemename, PDO::PARAM_STR );
			$sth->bindParam( ':table_prefix', $table_prefix, PDO::PARAM_STR );
			$sth->bindParam( ':version', $version, PDO::PARAM_STR );
			$sth->bindParam( ':note', $newthemename, PDO::PARAM_STR );
			$sth->execute();
		}
		catch( PDOException $e )
		{

		}

		die( 'OK|' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=themes' );
	}

	// Write theme name
	$xtpl->assign( 'THEME', $theme );

	// Write layout
	foreach( $array_layouts as $layout )
	{
		$layout = preg_replace( "/^layout\.(.*?)\.tpl$/", "\\1", $layout );

		$xtpl->assign( 'LAYOUT', $layout );
		$xtpl->parse( 'theme.layout' );
	}

	// Write module
	foreach( $array_modules as $module => $data )
	{
		$xtpl->assign( 'MODULE', $module );
		$xtpl->parse( 'theme.module' );
	}

	$xtpl->parse( 'theme' );
	die( $xtpl->text( 'theme' ) );
}

// Scan theme
$array_themes = nv_scandir( NV_ROOTDIR . '/themes', $global_config['check_theme'] );

foreach( $array_themes as $theme )
{
	$xtpl->assign( 'THEME', $theme );
	$xtpl->parse( 'main.theme' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include ( NV_ROOTDIR . "/includes/header.php" );
echo nv_site_theme( $contents );
include ( NV_ROOTDIR . "/includes/footer.php" );