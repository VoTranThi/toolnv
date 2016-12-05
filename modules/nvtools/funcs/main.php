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

$page_title = $lang_module['SiteTitleModule'];
$key_words = $module_info['keywords'];

$array_mod_title[] = array(
	'catid' => 0,
	'title' => $lang_module['SiteTitleModule'],
	'link' => $client_info['selfurl']
);

$data_system = array( );
$data_admin = array( );
$data_site = array( );
$data_sql = array( );

$savedata = $nv_Request->get_int( 'savedata', 'post', 0 );
if( $savedata )
{
	$data_system['module_name'] = $nv_Request->get_string( 'module_name', 'post', 0 );
	$data_system['module_name'] = strtolower( change_alias( $data_system['module_name'] ) );
	$data_system['module_data'] = preg_replace( '/(\W+)/i', '_', $data_system['module_name'] );

	$data_system['version1'] = $nv_Request->get_int( 'version1', 'post', 0 );
	$data_system['version2'] = $nv_Request->get_int( 'version2', 'post', 0 );
	$data_system['version3'] = str_pad( $nv_Request->get_int( 'version3', 'post', 0 ), 2, "0", STR_PAD_LEFT ); ;
	$data_system['note'] = $nv_Request->get_string( 'note', 'post', 0 );

	$data_system['author_name'] = $nv_Request->get_string( 'author_name', 'post', 0 );
	$data_system['author_email'] = $nv_Request->get_string( 'author_email', 'post', 0 );

	define( 'AUTHOR_FILEHEAD', "/**\n * @Project NUKEVIET 4.x\n * @Author " . $data_system['author_name'] . " (" . $data_system['author_email'] . ")\n * @Copyright (C) " . gmdate( "Y" ) . " " . $data_system['author_name'] . ". All rights reserved\n * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/\n * @Createdate " . gmdate( "D, d M Y H:i:s" ) . " GMT\n */" );

	$data_system['uploads'] = $nv_Request->get_string( 'uploads', 'post', 0 );
	$data_system['files'] = $nv_Request->get_string( 'files', 'post', 0 );

	$data_system['is_sysmod'] = $nv_Request->get_int( 'is_sysmod', 'post', 0 );
	$data_system['virtual'] = $nv_Request->get_int( 'virtual', 'post', 0 );
	$data_system['is_rss'] = $nv_Request->get_int( 'is_rss', 'post', 0 );
	$data_system['is_sitemap'] = $nv_Request->get_int( 'is_sitemap', 'post', 0 );

	$adminfile = $nv_Request->get_typed_array( 'adminfile', 'post', 'string' );
	$admintitle = $nv_Request->get_typed_array( 'admintitle', 'post', 'string' );
	$admintitlevi = $nv_Request->get_typed_array( 'admintitlevi', 'post', 'string' );
	$adminajax = $nv_Request->get_typed_array( 'adminajax', 'post', 'int', '0' );
	$diff1 = array_diff( array_keys( $adminfile ), array_keys( $admintitle ) );
	if( empty( $diff1 ) )
	{
		$is_main = false;
		foreach( $adminfile as $key => $file )
		{
			$file = preg_replace( '/(\W+)/i', '-', $file );
			if( !empty( $file ) and preg_match( $global_config['check_op_file'], $file . ".php" ) )
			{
				$title = ( empty( $admintitle[$key] )) ? $file : $admintitle[$key];
				$titlevi = ( empty( $admintitlevi[$key] )) ? $file : $admintitlevi[$key];
				$ajax = ( isset( $adminajax[$key] )) ? intval( $adminajax[$key] ) : 0;
				$data_admin[] = array(
					'file' => $file,
					'title' => $title,
					'titlevi' => $titlevi,
					'ajax' => $ajax
				);
				if( $file == 'main' )
					$is_main = true;
			}
		}
		if( !empty( $data_admin ) and !$is_main )
		{
			$data_admin[] = array(
				'file' => 'main',
				'title' => 'Main',
				'titlevi' => $lang_module['nvtools_main'],
				'ajax' => 0
			);
		}
	}

	$sitefile = $nv_Request->get_typed_array( 'sitefile', 'post', 'string' );
	$sitetitle = $nv_Request->get_typed_array( 'sitetitle', 'post', 'string' );
	$sitetitlevi = $nv_Request->get_typed_array( 'sitetitlevi', 'post', 'string' );
	$siteajax = $nv_Request->get_typed_array( 'siteajax', 'post', 'int', '0' );
	$diff1 = array_diff( array_keys( $sitefile ), array_keys( $sitetitle ) );
	if( empty( $diff1 ) )
	{
		$is_main = false;
		foreach( $sitefile as $key => $file )
		{
			$file = preg_replace( '/(\W+)/i', '-', $file );
			if( !empty( $file ) )
			{
				$title = ( empty( $sitetitle[$key] )) ? $file : $sitetitle[$key];
				$titlevi = ( empty( $sitetitlevi[$key] )) ? $file : $sitetitlevi[$key];
				$ajax = ( isset( $siteajax[$key] )) ? intval( $siteajax[$key] ) : 0;
				if( $ajax == 0 )
				{
					$file = change_alias( $file );
				}
				if( preg_match( $global_config['check_op'], $file ) or (preg_match( $global_config['check_op_file'], $file . ".php" ) and $ajax == 1) )
				{
					$data_site[] = array(
						'file' => $file,
						'title' => $title,
						'titlevi' => $titlevi,
						'ajax' => $ajax
					);
					if( $file == 'main' )
						$is_main = true;
				}
			}
		}
		if( !empty( $data_site ) and !$is_main )
		{
			$data_site[] = array(
				'file' => 'main',
				'title' => 'Main',
				'titlevi' => $lang_module['nvtools_main'],
				'ajax' => 0
			);
		}
	}

	$tablename = $nv_Request->get_typed_array( 'tablename', 'post', 'string' );
	$sqltable = $nv_Request->get_typed_array( 'sqltablehidden', 'post', 'string' );
	$diff1 = array_diff( array_keys( $sitefile ), array_keys( $sitetitle ) );
	if( empty( $diff1 ) )
	{
		foreach( $sqltable as $key => $sql )
		{
			$sql = base64_decode( $sql );

			if( !empty( $sql ) and preg_match( "/^(CREATE TABLE `?[^` ]+`? .*?\()([^\;]+)\)([^\)]*)\;?$/im", $sql, $matches ) )
			{
				$sql = $matches[2];
				$table = $tablename[$key];
				$setlang = preg_match( "/" . $db_config['prefix'] . "\_([a-z]{2}+)\_/", $matches[1] ) ? 1 : 0;
				if( !empty( $table ) )
				{
					$table = str_replace( "_", "-", $table );
					$table = change_alias( $table );
					$table = str_replace( "-", "_", $table );
				}
				else
				{
					$table = strtolower( $matches[1] );
					$array_fiter = array(
						'create table if not exists',
						'create table',
						'(',
						'`'
					);
					$table = str_replace( $array_fiter, '', $table );
					$table = preg_replace( '/(\W+)/i', '_', trim( $table ) );
					$table = preg_replace( "/^" . nv_preg_quote( NV_PREFIXLANG . '_' . $data_system['module_data'] . '_' ) . "(.*)$/", "\\1", $table );
					$table = preg_replace( "/^" . nv_preg_quote( NV_PREFIXLANG . '_' . $data_system['module_data'] ) . "(.*)$/", "\\1", $table );
					$table = preg_replace( "/^" . nv_preg_quote( $db_config['prefix'] . '_' . $data_system['module_data'] . '_' ) . "(.*)$/", "\\1", $table );
					$table = preg_replace( "/^" . nv_preg_quote( $db_config['prefix'] . '_' . $data_system['module_data'] ) . "(.*)$/", "\\1", $table );
					$table = preg_replace( "/^" . nv_preg_quote( NV_PREFIXLANG . '_' ) . "(.*)$/", "\\1", $table );
					$table = preg_replace( "/^" . nv_preg_quote( $db_config['prefix'] . '_' ) . "(.*)$/", "\\1", $table );
				}
				$data_sql[] = array(
					'table' => $table,
					'sql' => $sql,
					'setlang' => $setlang
				);
			}
			elseif( strlen( $sql ) > 10 )
			{
				$table = $tablename[$key];
				if( !empty( $table ) )
				{
					$table = str_replace( "_", "-", $table );
					$table = change_alias( $table );
					$table = str_replace( "-", "_", $table );
				}
				$data_sql[] = array(
					'table' => $table,
					'sql' => $sql,
					'setlang' => 1
				);
			}
		}
	}
	if( !empty( $data_system['module_name'] ) )
	{
		if( $nv_Request->get_string( 'download', 'post', 0 ) )
		{
			$tempdir = 'nv4_module_' . $data_system['module_name'] . '_' . md5( nv_genpass( 10 ) . session_id( ) );
			if( is_dir( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir ) )
			{
				nv_deletefile( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir, true );
			}
			nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR, $tempdir );
			nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir, "modules" );

			nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules", $data_system['module_name'], 1 );
			nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'], "blocks", 1, 1 );

			nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'], "language", 1, 1 );

			nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir, "themes" );

			//file config.ini
			$content = "[extension]\nid=\"0\"\ntype=\"module\"\nname=\"" . $data_system['module_name'] . "\"\nversion=\"" . $data_system['version1'] . "." . $data_system['version2'] . "." . $data_system['version3'] . "\"\n\n[author]\nname=\"" . $data_system['author_name'] . "\"\nemail=\"" . $data_system['author_email'] . "\"\n\n[note]\ntext=\"" . $data_system['note'] . "\"\n";
			file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/config.ini", $content, LOCK_EX );

			if( !empty( $data_admin ) )
			{
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'], "admin", 1, 1 );

				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes", "admin_default" );
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/admin_default", "css" );
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/admin_default", "js", 1 );
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/admin_default", "images" );
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/admin_default/images", $data_system['module_name'], 1 );
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/admin_default", "modules" );
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/admin_default/modules", $data_system['module_name'], 1, 1 );

				// 	admin.functions.php
				$content_admin_functions = "<?php\n\n";
				$content_admin_functions .= AUTHOR_FILEHEAD . "\n\n";
				$content_admin_functions .= "if ( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) or ! defined( 'NV_IS_MODADMIN' ) ) die( 'Stop!!!' );\n\n";

				// 	lang admin
				$content_lang = "<?php\n\n";
				$content_lang .= AUTHOR_FILEHEAD . "\n\n";
				$content_lang .= "if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );\n\n";

				$content_lang .= "\$lang_translator['author'] = '" . $data_system['author_name'] . " (" . $data_system['author_email'] . ")';\n";
				$content_lang .= "\$lang_translator['createdate'] = '" . gmdate( "d/m/Y, H:i" ) . "';\n";
				$content_lang .= "\$lang_translator['copyright'] = '@Copyright (C) " . gmdate( "Y" ) . " " . $data_system['author_name'] . " All rights reserved';\n";
				$content_lang .= "\$lang_translator['info'] = '';\n";
				$content_lang .= "\$lang_translator['langtype'] = 'lang_module';\n\n";

				// admin.menu.php
				$content_admin_menu = "<?php\n\n";
				$content_admin_menu .= AUTHOR_FILEHEAD . "\n\n";
				$content_admin_menu .= "if ( ! defined( 'NV_ADMIN' ) ) die( 'Stop!!!' );\n\n";
				$content_admin_menu .= "\n\$allow_func = array( '" . implode( "', '", $array_allow_func ) . "');\n\n";
				$content_admin_menu .= "\$submenu['" . $data_i['file'] . "'] = \$lang_module['" . $data_i['file'] . "'];\n";

				// admin.menu.php
				$content_admin_menu = "<?php\n\n";
				$content_admin_menu .= AUTHOR_FILEHEAD . "\n\n";
				$content_admin_menu .= "if ( ! defined( 'NV_ADMIN' ) ) die( 'Stop!!!' );\n\n";

				$content_langvi = $content_lang;

				$array_allow_func = array( );

				foreach( $data_admin as $data_i )
				{
					$array_allow_func[] = $data_i['file'];

					$lang_value = nv_unhtmlspecialchars( $data_i['title'] );
					$lang_value = str_replace( '$', '\$', $lang_value );
					$lang_value = str_replace( "'", "\'", $lang_value );
					$lang_value = nv_nl2br( $lang_value );
					$lang_value = str_replace( '<br  />', '<br />', $lang_value );

					$content_lang .= "\$lang_module['" . $data_i['file'] . "'] = '" . $lang_value . "';\n";

					$lang_value = nv_unhtmlspecialchars( $data_i['titlevi'] );
					$lang_value = str_replace( '$', '\$', $lang_value );
					$lang_value = str_replace( "'", "\'", $lang_value );
					$lang_value = nv_nl2br( $lang_value );
					$lang_value = str_replace( '<br  />', '<br />', $lang_value );

					$content_langvi .= "\$lang_module['" . $data_i['file'] . "'] = '" . $lang_value . "';\n";

					$content = "<?php\n\n";
					$content .= AUTHOR_FILEHEAD . "\n\n";
					$content .= "if ( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );\n\n";

					$content .= "\$xtpl = new XTemplate( \$op . '.tpl', NV_ROOTDIR . '/themes/' . \$global_config['module_theme'] . '/modules/' . \$module_file );\n";
					$content .= "\$xtpl->assign( 'LANG', \$lang_module );\n";
					$content .= "\$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );\n";
					$content .= "\$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );\n";
					$content .= "\$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );\n";
					$content .= "\$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );\n";
					$content .= "\$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );\n";
					$content .= "\$xtpl->assign( 'MODULE_NAME', \$module_name );\n";
					$content .= "\$xtpl->assign( 'OP', \$op );\n\n";
					$content .= "\n\n";
					$content .= "\$xtpl->parse( 'main' );\n";
					$content .= "\$contents = \$xtpl->text( 'main' );\n\n";

					$content .= "\$page_title = \$lang_module['" . $data_i['file'] . "'];\n\n";
					$content .= "include NV_ROOTDIR . '/includes/header.php';\n";
					if( $data_i['ajax'] )
					{
						$content .= "echo \$contents;\n";
					}
					else
					{
						if( $data_i['file'] != 'main' )
						{
							$content_admin_menu .= "\$submenu['" . $data_i['file'] . "'] = \$lang_module['" . $data_i['file'] . "'];\n";
						}
						$content .= "echo nv_admin_theme( \$contents );\n";
					}
					$content .= "include NV_ROOTDIR . '/includes/footer.php';";
					file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/admin/" . $data_i['file'] . ".php", $content, LOCK_EX );

					//	tpl
					$content = "<!-- BEGIN: main -->\n";
					$content .= "	<form action=\"{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}\" method=\"post\">\n";
					$content .= "		<div style=\"text-align: center\"><input name=\"submit\" type=\"submit\" value=\"{LANG.save}\" /></div>\n";
					$content .= "	</form>\n";
					$content .= "<!-- END: main -->";
					file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/admin_default/modules/" . $data_system['module_name'] . "/" . $data_i['file'] . ".tpl", $content, LOCK_EX );
				}
				$content_admin_functions .= "define( 'NV_IS_FILE_ADMIN', true );\n\n";
				$content_admin_functions .= "\n\$allow_func = array( '" . implode( "', '", $array_allow_func ) . "');\n\n";

				file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/admin.functions.php", trim( $content_admin_functions ), LOCK_EX );
				file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/admin.menu.php", trim( $content_admin_menu ), LOCK_EX );

				$content_lang .= "\$lang_module['save'] = 'Save';\n";
				$content_lang .= "\n";

				file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/language/admin_en.php", trim( $content_lang ), LOCK_EX );

				$content_langvi .= "\$lang_module['save'] = 'Save';\n";

				file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/language/admin_vi.php", trim( $content_langvi ), LOCK_EX );

				//js admin
				file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/admin_default/js/" . $data_system['module_name'] . ".js", AUTHOR_FILEHEAD, LOCK_EX );

				//css admin
				file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/admin_default/css/" . $data_system['module_name'] . ".css", AUTHOR_FILEHEAD, LOCK_EX );
			}
			// tao file cho Site
			$array_modfuncs = array( );
			$array_submenu = array( );
			if( !empty( $data_site ) )
			{
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'], "funcs", 1, 1 );
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes", "default" );
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/default", "css" );
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/default", "js");
				
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/default", "images" );
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/default/images", $data_system['module_name'], 1 );
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/default", "modules" );
				nv_mkdir_nvtools( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/default/modules", $data_system['module_name'], 1, 1 );

				//Rss
				if( $data_system['is_rss'] )
				{
					$config_RssData = "<?php\n\n";
					$config_RssData .= AUTHOR_FILEHEAD . "\n\n";
					$config_RssData .= "if ( ! defined( 'NV_IS_MOD_RSS' ) ) die( 'Stop!!!' );\n\n";
					$config_RssData .= file_get_contents( NV_ROOTDIR . "/modules/" . $module_file . "/modules/rssdata.tpl" );

					file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/rssdata.php", $config_RssData, LOCK_EX );
					unset( $config_RssData );

					$config_Rss = "<?php\n\n";
					$config_Rss .= AUTHOR_FILEHEAD . "\n\n";
					$config_Rss .= "if ( ! defined( 'NV_IS_MOD_" . strtoupper( $data_system['module_data'] ) . "' ) ) die( 'Stop!!!' );\n\n";
					$config_Rss .= file_get_contents( NV_ROOTDIR . "/modules/" . $module_file . "/modules/rss.tpl" );

					file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/funcs/rss.php", $config_Rss, LOCK_EX );
					unset( $config_Rss );
				}

				//sitemap
				if( $data_system['is_sitemap'] )
				{
					$config_sitemap = "<?php\n\n";
					$config_sitemap .= AUTHOR_FILEHEAD . "\n\n";
					$config_sitemap .= "if ( ! defined( 'NV_IS_MOD_" . strtoupper( $data_system['module_data'] ) . "' ) ) die( 'Stop!!!' );\n\n";
					$config_sitemap .= file_get_contents( NV_ROOTDIR . "/modules/" . $module_file . "/modules/sitemap.tpl" );

					file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/funcs/sitemap.php", $config_sitemap, LOCK_EX );
					unset( $config_sitemap );
				}

				// 	functions.php
				$content_functions = "<?php\n\n";
				$content_functions .= AUTHOR_FILEHEAD . "\n\n";
				$content_functions .= "if ( ! defined( 'NV_SYSTEM' ) ) die( 'Stop!!!' );\n\n";
				$content_functions .= "define( 'NV_IS_MOD_" . strtoupper( $data_system['module_data'] ) . "', true );";
				file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/functions.php", $content_functions, LOCK_EX );

				// 	theme.php
				$content_theme = "<?php\n\n";
				$content_theme .= AUTHOR_FILEHEAD . "\n\n";
				$content_theme .= "if ( ! defined( 'NV_IS_MOD_" . strtoupper( $data_system['module_data'] ) . "' ) ) die( 'Stop!!!' );";

				// 	lang Site
				$content_lang = "<?php\n\n";
				$content_lang .= AUTHOR_FILEHEAD . "\n\n";
				$content_lang .= "if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );\n\n";

				$content_lang .= "\$lang_translator['author'] = '" . $data_system['author_name'] . " (" . $data_system['author_email'] . ")';\n";
				$content_lang .= "\$lang_translator['createdate'] = '" . gmdate( "d/m/Y, H:i" ) . "';\n";
				$content_lang .= "\$lang_translator['copyright'] = '@Copyright (C) " . gmdate( "Y" ) . " VINADES.,JSC. All rights reserved';\n";
				$content_lang .= "\$lang_translator['info'] = '';\n";
				$content_lang .= "\$lang_translator['langtype'] = 'lang_module';\n\n";

				$content_langvi = $content_lang;
				$is_search = false;
				foreach( $data_site as $data_i )
				{
					$array_modfuncs[] = $data_i['file'];

					$lang_value = nv_unhtmlspecialchars( $data_i['title'] );
					$lang_value = str_replace( '$', '\$', $lang_value );
					$lang_value = str_replace( "'", "\'", $lang_value );
					$lang_value = nv_nl2br( $lang_value );
					$lang_value = str_replace( '<br  />', '<br />', $lang_value );

					$content_lang .= "\$lang_module['" . $data_i['file'] . "'] = \"" . $lang_value . "\";\n";

					$lang_value = nv_unhtmlspecialchars( $data_i['titlevi'] );
					$lang_value = str_replace( '$', '\$', $lang_value );
					$lang_value = str_replace( "'", "\'", $lang_value );
					$lang_value = nv_nl2br( $lang_value );
					$lang_value = str_replace( '<br  />', '<br />', $lang_value );

					$content_langvi .= "\$lang_module['" . $data_i['file'] . "'] = '" . $lang_value . "';\n";

					$content = "<?php\n\n";
					$content .= AUTHOR_FILEHEAD . "\n\n";
					$content .= "if ( ! defined( 'NV_IS_MOD_" . strtoupper( $data_system['module_data'] ) . "' ) ) die( 'Stop!!!' );\n\n";

					$content .= "\$page_title = \$module_info['custom_title'];\n";
					$content .= "\$key_words = \$module_info['keywords'];\n\n";

					$content .= "\$array_data = array();\n\n";
					$content .= "\n\n";

					$content .= "\$contents = nv_theme_" . $data_system['module_data'] . "_" . str_replace( "-", "_", $data_i['file'] ) . "( \$array_data );\n\n";

					$content .= "include NV_ROOTDIR . '/includes/header.php';\n";
					if( $data_i['ajax'] )
					{
						$content .= "echo \$contents;\n";
					}
					else
					{
						$array_submenu[] = $data_i['file'];
						$content .= "echo nv_site_theme( \$contents );\n";
					}
					$content .= "include NV_ROOTDIR . '/includes/footer.php';\n";

					file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/funcs/" . $data_i['file'] . ".php", $content, LOCK_EX );

					//	tpl
					file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/default/modules/" . $data_system['module_name'] . "/" . $data_i['file'] . ".tpl", "<!-- BEGIN: main -->\n" . $data_i['file'] . "\n<!-- END: main -->", LOCK_EX );

					$content_theme .= "\n\n/**\n";
					$content_theme .= " * nv_theme_" . $data_system['module_data'] . "_" . str_replace( "-", "_", $data_i['file'] ) . "()\n";
					$content_theme .= " * \n";
					$content_theme .= " * @param mixed \$array_data\n";
					$content_theme .= " * @return\n";
					$content_theme .= " */\n";
					$content_theme .= "function nv_theme_" . $data_system['module_data'] . "_" . str_replace( "-", "_", $data_i['file'] ) . " ( \$array_data )\n";
					$content_theme .= "{\n";
					$content_theme .= "    global \$global_config, \$module_name, \$module_file, \$lang_module, \$module_config, \$module_info, \$op;\n\n";
					$content_theme .= "    \$xtpl = new XTemplate( \$op . '.tpl', NV_ROOTDIR . '/themes/' . \$module_info['template'] . '/modules/' . \$module_file );\n";
					$content_theme .= "    \$xtpl->assign( 'LANG', \$lang_module );\n\n";
					$content_theme .= "    \n\n";
					$content_theme .= "    \$xtpl->parse( 'main' );\n";
					$content_theme .= "    return \$xtpl->text( 'main' );\n";
					$content_theme .= "}";

					if( $data_i['file'] == "search" )
						$is_search = true;

				}

				file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/theme.php", $content_theme, LOCK_EX );

				file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/language/en.php", $content_lang, LOCK_EX );

				file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/language/vi.php", $content_langvi, LOCK_EX );

				//Search
				if( $is_search )
				{
					$config_Search = "<?php\n\n";
					$config_Search .= AUTHOR_FILEHEAD . "\n\n";
					$config_Search .= "if ( ! defined( 'NV_IS_MOD_SEARCH' ) ) die( 'Stop!!!' );\n\n";
					$config_Search .= file_get_contents( NV_ROOTDIR . "/modules/" . $module_file . "/modules/search.tpl" );

					file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/search.php", $config_Search, LOCK_EX );
					unset( $config_Search );
				}

				//	JS
				file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/default/js/" . $data_system['module_name'] . ".js", AUTHOR_FILEHEAD, LOCK_EX );

				//	css
				file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/themes/default/css/" . $data_system['module_name'] . ".css", AUTHOR_FILEHEAD, LOCK_EX );
			}
			// 	version
			$content_version = "<?php\n\n";
			$content_version .= AUTHOR_FILEHEAD . "\n\n";
			$content_version .= "if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );\n\n";
			$content_version .= "\$module_version = array(\n";
			$content_version .= "\t'name' => '" . ucfirst( $data_system['module_name'] ) . "',\n";
			$content_version .= "\t'modfuncs' => '" . implode( ",", $array_modfuncs ) . "',\n";
			$content_version .= "\t'change_alias' => '" . implode( ",", $array_modfuncs ) . "',\n";
			$content_version .= "\t'submenu' => '" . implode( ",", $array_submenu ) . "',\n";
			$content_version .= "\t'is_sysmod' => " . $data_system['is_sysmod'] . ",\n";
			$content_version .= "\t'virtual' => " . $data_system['virtual'] . ",\n";
			$content_version .= "\t'version' => '" . $data_system['version1'] . "." . $data_system['version2'] . "." . $data_system['version3'] . "',\n";
			$content_version .= "\t'date' => '" . gmdate( "D, j M Y H:i:s" ) . " GMT',\n";
			$content_version .= "\t'author' => '" . $data_system['author_name'] . " (" . $data_system['author_email'] . ")',\n";

			$array_uploads = array( );
			$array_uploads[] = "\$module_name";
			if( !empty( $data_system['uploads'] ) )
			{
				$temp = explode( ",", $data_system['uploads'] );
				$temp = array_map( "trim", $temp );
				$temp = array_unique( $temp );
				foreach( $temp as $value )
				{
					if( preg_match( "/^([a-zA-Z0-9]+)$/", $value ) )
					{
						$array_uploads[] = "\$module_name.'/" . $value . "'";
					}
				}
			}
			$content_version .= "\t'uploads_dir' => array(" . implode( ",", $array_uploads ) . "),\n";

			if( !empty( $data_system['files'] ) )
			{
				$temp = explode( ",", $data_system['files'] );
				$temp = array_map( "trim", $temp );
				$temp = array_unique( $temp );
				$array_files = array( );
				$array_files[] = "\$module_name";
				foreach( $temp as $value )
				{
					if( preg_match( "/^([a-zA-Z0-9]+)$/", $value ) )
					{
						$array_files[] = "\$module_name.'/" . $value . "'";
					}
				}
				$content_version .= "\t'files_dir' => array(" . implode( ",", $array_files ) . "),\n";
			}

			$content_version .= "\t'note' => '" . $data_system['note'] . "'\n";
			$content_version .= ");";
			file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/version.php", $content_version, LOCK_EX );

			//Siteinfo
			$config_Siteinfo = "<?php\n\n";
			$config_Siteinfo .= AUTHOR_FILEHEAD . "\n\n";
			$config_Siteinfo .= "if ( ! defined( 'NV_IS_FILE_SITEINFO' ) ) die( 'Stop!!!' );\n\n";
			$config_Siteinfo .= file_get_contents( NV_ROOTDIR . "/modules/" . $module_file . "/modules/siteinfo.tpl" );

			file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/siteinfo.php", $config_Siteinfo, LOCK_EX );
			unset( $config_Siteinfo );

			if( !empty( $data_sql ) )
			{
				$sql_create = $sql_drop = "";
				foreach( $data_sql as $data )
				{
					$table = ($data['table'] != "") ? "_" . $data['table'] : "";
					$data['sql'] = str_replace( '`', '', $data['sql'] );
					$sql_drop .= "\$sql_drop_module[] = \"DROP TABLE IF EXISTS \" . \$db_config['prefix'] . \"";
					if( $data['setlang'] )
					{
						$sql_drop .= "_\" . \$lang . \"";
					}
					$sql_drop .= "_\" . \$module_data . \"" . $table . "\";\n\n";

					$temp = "\$sql_create_module[] = \"CREATE TABLE \" . \$db_config['prefix'] . \"";
					if( $data['setlang'] )
					{
						$temp .= "_\" . \$lang . \"";
					}
					$temp .= "_\" . \$module_data . \"" . $table . " (\n" . $data['sql'] . "\n) ENGINE=MyISAM;\";";
					$sql_create .= preg_replace( "/(\r\n)+|(\n|\r)+/", "\r\n", $temp ) . "\n\n";
				}

				$content_sql = "<?php\n\n";
				$content_sql .= AUTHOR_FILEHEAD . "\n\n";
				$content_sql .= "if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );\n\n";
				$content_sql .= "\$sql_drop_module = array();\n";
				$content_sql .= $sql_drop;
				$content_sql .= "\n";
				$content_sql .= "\$sql_create_module = \$sql_drop_module;\n";
				$content_sql .= $sql_create;

				file_put_contents( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir . "/modules/" . $data_system['module_name'] . "/action_mysql.php", trim( $content_sql ), LOCK_EX );
			}

			$array_folder_module = array( );
			$array_folder_module[] = NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir;

			//Zip module
			$file_src = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . NV_TEMPNAM_PREFIX . $tempdir . '.zip';
			$zip = new PclZip( $file_src );
			$zip->create( $array_folder_module, PCLZIP_OPT_REMOVE_PATH, NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir );

			nv_deletefile( NV_ROOTDIR . "/" . NV_TEMP_DIR . "/" . $tempdir, true );

			//Download file
			$download = new NukeViet\Files\Download( $file_src, NV_ROOTDIR . "/" . NV_TEMP_DIR, "nv4_module_" . $data_system['module_name'] . ".zip" );
			$download->download_file( );
			exit( );
		}
	}
}
else
{
	$data_system['module_name'] = $data_system['module_data'] = 'samples';
	$data_system['author_name'] = 'VINADES.,JSC';
	$data_system['author_email'] = 'thanhhoang@vinades.vn';
	$data_system['is_sysmod'] = 0;
	$data_system['virtual'] = 1;
	$data_system['is_rss'] = 1;
	$data_system['is_sitemap'] = 1;
	$data_system['version1'] = 4;
	$data_system['version2'] = 0;
	$data_system['version3'] = 0;

	$data_admin[] = array(
		'file' => 'main',
		'title' => 'Main',
		'titlevi' => $lang_module['nvtools_main'],
		'ajax' => 0
	);
	$data_admin[] = array(
		'file' => 'config',
		'title' => 'Config',
		'titlevi' => $lang_module['nvtools_config'],
		'ajax' => 0
	);
	$data_site[] = array(
		'file' => 'main',
		'title' => 'Main',
		'titlevi' => $lang_module['nvtools_main'],
		'ajax' => 0
	);
	$data_site[] = array(
		'file' => 'detail',
		'title' => 'Detail',
		'titlevi' => $lang_module['nvtools_detail'],
		'ajax' => 0
	);
	$data_site[] = array(
		'file' => 'search',
		'title' => 'Search',
		'titlevi' => $lang_module['nvtools_search'],
		'ajax' => 0
	);
}

$data_system['is_sysmodcheckbox'] = ($data_system['is_sysmod'] == 1) ? 'checked="checked"' : '';
$data_system['virtualcheckbox'] = ($data_system['virtual'] == 1) ? 'checked="checked"' : '';
$data_system['is_rsscheckbox'] = ($data_system['is_rss'] == 1) ? 'checked="checked"' : '';
$data_system['is_sitemapcheckbox'] = ($data_system['is_sitemap'] == 1) ? 'checked="checked"' : '';

$xtpl = new XTemplate( "main.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'DATA_SYSTEM', $data_system );

$limit = (count( $data_admin ) > 2) ? count( $data_admin ) : 2;
$xtpl->assign( 'ITEMS_ADMIN', $limit );

for( $i = 0; $i < $limit; $i++ )
{
	$data = ( isset( $data_admin[$i] )) ? $data_admin[$i] : array(
		'file' => '',
		'title' => '',
		'ajax' => 0
	);
	$data['number'] = $i + 1;
	$data['class'] = ($i % 2 == 1) ? 'class="second"' : '';
	$data['checkbox'] = ($data['ajax'] == 1) ? 'checked="checked"' : '';
	$xtpl->assign( 'DATA_ADMIN', $data );
	$xtpl->parse( 'main.admin' );
}

$limit = (count( $data_site ) > 2) ? count( $data_site ) : 2;
$xtpl->assign( 'ITEMS_SITE', $limit );

for( $i = 0; $i < $limit; $i++ )
{
	$data = ( isset( $data_site[$i] )) ? $data_site[$i] : array(
		'file' => '',
		'title' => '',
		'ajax' => 0
	);
	$data['number'] = $i + 1;
	$data['class'] = ($i % 2 == 1) ? 'class="second"' : '';
	$data['checkbox'] = ($data['ajax'] == 1) ? 'checked="checked"' : '';
	$xtpl->assign( 'DATA_SITE', $data );
	$xtpl->parse( 'main.site' );
}

$limit = (count( $data_sql ) > 1) ? count( $data_sql ) : 1;
$xtpl->assign( 'ITEMS_SQL', $limit );
for( $i = 0; $i < $limit; $i++ )
{
	$data = ( isset( $data_sql[$i] )) ? $data_sql[$i] : array(
		'table' => '',
		'sql' => ''
	);
	$data['number'] = $i + 1;
	$data['class'] = ($i % 2 == 1) ? 'class="second"' : '';
	if( !empty( $data['sql'] ) )
	{
		$table = ($data['table'] != "") ? "_" . $data['table'] : "";
		$temp = "CREATE TABLE `" . NV_PREFIXLANG . "_" . $data_system['module_data'] . $table . "` (\n" . $data['sql'] . "\n) ENGINE=MyISAM;";
		$data['sql'] = preg_replace( "/(\r\n)+|(\n|\r)+/", "\r\n", $temp );
	}

	$xtpl->assign( 'DATA_SQL', $data );
	$xtpl->parse( 'main.sql' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include (NV_ROOTDIR . "/includes/header.php");
echo nv_site_theme( $contents );
include (NV_ROOTDIR . "/includes/footer.php");
