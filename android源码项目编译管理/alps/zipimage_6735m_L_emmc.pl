# /* __NH _1206 _27__ */
# port
#

$debug = 1;

$tools = "../customfiles/tools/jxpp/";
unshift(@INC, "$tools");
require "hx_util.pm";

$logfile = "zipimage.txt";
$t = &CurrTimeStr();
&logmsg("[zipimage $t]\n");


my %info;
#$info{CUSTOM} = $ARGV[0];
#$info{PROJECT} = $ARGV[0];
my $PROJECT = lc($ARGV[0]);


&gen_info();
&zip_image();

sub file_gen_info()
{
	my ($thefile) = @_;
	&logmsg("enter file_gen_info($thefile)\n");

	open (FILE_HANDLE, "<$thefile") or die "cannot open $thefile\n";
	while (<FILE_HANDLE>) {
	  if (/^\s*(\S+)\s*=\s*(\S+)/) {
	    $keyname = uc($1);
	    #print "$keyname\n";
	    defined($info{$keyname}) && &logmsg("$1 redefined in $thefile!\n");
	    $info{$keyname} = uc($2);
	  	#print "$info{$keyname}\n";
	  }
	}
	close FILE_HANDLE;
	&logmsg("exit file_gen_info\n");
}

sub gen_info()
{
	&logmsg("enter gen_info\n");
	#&file_gen_info("mediatek/config/out/$PROJECT/ProjectConfig.mk");
	&file_gen_info("out/target/product/$PROJECT/system/data/misc/ProjectConfig.mk");
	#&file_gen_info("mediatek/config/common/ProjectConfig.mk");
	&logmsg("MTK_CUSTOM = $info{MTK_CUSTOM}\n");
	$info{VERNO} = $info{CUSTOM_BUILD_VERNO};
	$info{VERNO} =~ s/\./_/g;
	if ($info{VERNO} eq "")
	{
		$info{VERNO} = "COMMON";
	}

	$info{LOGO} = $info{BOOT_LOGO};
	$info{LOGO} =~ s/\./_/g;
	
	
	&file_gen_info("out/target/product/$PROJECT/system/build.prop");
	$info{VERNO} = $info{'RO.CUSTOM.BUILD.VERSION'};
	$info{VERNO} =~ s/\./_/g;
	#print "new $info{VERNO}\n";
	##add time
	$info{TIMESTAMP} = $info{'RO.BUILD.DATE.UTC'};
	$info{TIMESTAMP} =~ s/\./_/g;
	
	&gen_zip_name_new();

	&logmsg("exit gen_info\n");
}

sub get_flash_size()
{
	my $thefile = $scaterfile_fullpath;
	&logmsg("get_flash_size($thefile)\n");

	open (FILE_HANDLE, "<$thefile") or die "can not open $thefile\n";
	while (<FILE_HANDLE>)
	{
		if (/\A; SCHEME   : external (\S+)MB flash memory and (\S+)MB SRAM/)
		{
			$ROM_SIZE = uc($1);
			$RAM_SIZE = uc($2);

			if ($ROM_SIZE >= 20 && $ROM_SIZE <= 32)
			{
				$ROM_SIZE = 32;
			}
			elsif ($ROM_SIZE >= 14 && $ROM_SIZE <= 16)
			{
				$ROM_SIZE = 16;
			}
			elsif ($ROM_SIZE >= 7 && $ROM_SIZE <= 8)
			{
				$ROM_SIZE = 8;
			}

			$info{FLASH_SIZE_STR} .= $ROM_SIZE * 8 . $RAM_SIZE * 8;
			&logmsg("ROM_SIZE = $ROM_SIZE, RAM_SIZE = $RAM_SIZE\n");
			&logmsg("FLASH_SIZE_STR = $info{FLASH_SIZE_STR}\n");
			last;
		}
		elsif (/\A; SCHEME   : external NAND flash and (\S+)MB LPSDRAM/)
		{
			$RAM_SIZE = uc($1);
			$info{FLASH_SIZE_STR} .= "NAND_". $RAM_SIZE * 8;
			&logmsg("RAM_SIZE = $RAM_SIZE\n");
			&logmsg("FLASH_SIZE_STR = $info{FLASH_SIZE_STR}\n");
			last;
		}
	}
	close FILE_HANDLE;
}

sub gen_zip_name_new()
{	
	$info{zip_name_v1}="";
	$info{zip_name_v1} .="MT6735m_";
	$info{zip_name_v1} .="$info{BOOT_LOGO}_";
	$info{zip_name_v1} .=$info{VERNO};
	$info{zip_name_v1} .="__$info{TIMESTAMP}";
	#$info{zip_name_v1} .= substr($info{VERNO}, $sub_len, length($info{VERNO})-$sub_len);
	#print "$info{zip_name_v1}\n";
}

sub gen_zip_name()
{
#		&get_flash_size();
	my $sub_len = 7;
	if (!($info{PRJ_CUSTOM} eq ""))
	{
	$info{zip_name_v1} = $info{PRJ_CUSTOM};
	}
	else
	{
		if ($info{VERNO} =~ /^([a-zA-Z0-9]+_[a-zA-Z0-9]+)_/)
		{
			$sub_len = length($1);
		}
		$info{zip_name_v1} = substr($info{VERNO}, 0, $sub_len);
	}

	$info{ZIP_COMMENT} .= "$info{VERNO}\n功能描述:\n\n" ;

	if (!($info{MTK_PLATFORM} eq ""))
	{
	$info{ZIP_COMMENT} .= "$info{MTK_PLATFORM}平台,\n" ;
	}
	if ($info{GEMINI} =~ /yes/)
	{
	$info{ZIP_COMMENT} .= "双" ;
	}
	else
	{
	$info{ZIP_COMMENT} .= "单";
	}
	$info{ZIP_COMMENT} .= "卡单通,\n" ;

	if (!($info{CUSTOM_HAL_BLUETOOTH} =~ /NONE/))
	{
	$info{zip_name_v1} .= "_BT";
	}
	else
	{
	$info{ZIP_COMMENT} .= "不";
	}
	$info{ZIP_COMMENT} .= "带蓝牙,\n" ;

	if (!($info{MTK_FM_CHIP} =~ /NONE/))
	{
	$info{zip_name_v1} .= "_FM" ;
	}
	else
	{
	$info{ZIP_COMMENT} .= "不";
	}
	$info{ZIP_COMMENT} .= "带FM,\n" ;

	if (!($info{CUSTOM_HAL_MATV} =~ /NONE/))
	{
		$info{zip_name_v1} .= "_$info{CUSTOM_HAL_MATV}" ;
		$info{ZIP_COMMENT} .= "带TV,\n" ;

		if ($info{MTK_TVOUT_SUPPORT} =~ /yes/)
		{
		$info{zip_name_v1} .= "_TVO";
		}
		else
		{
		$info{ZIP_COMMENT} .= "不" ;
		}
		$info{ZIP_COMMENT} .= "带电视输出,\n" ;
	}
	else
	{
	$info{ZIP_COMMENT} .= "不带TV,\n" ;
	}

	if ($info{MTK_WLAN_SUPPORT} =~ /yes/)
	{
	$info{zip_name_v1} .= "_WIFI" ;
	}
	else
	{
	$info{ZIP_COMMENT} .= "不" ;
	}
	$info{ZIP_COMMENT} .= "带WIFI,\n" ;
	if ($info{DUAL_CAMERA_SUPPORT} =~ /TRUE/)
	{
	$info{zip_name_v1} .= "_DC" ;
	$info{ZIP_COMMENT} .= "双" ;
	}
	else
	{
	$info{ZIP_COMMENT} .= "单" ;
	}
	$info{ZIP_COMMENT} .= "摄像头,\n" ;

	if (!($info{CUSTOM_KERNEL_TOUCHPANEL} =~ /NONE/))
	{
	$info{zip_name_v1} .= "_TP" ;
	}
	else
	{
	$info{ZIP_COMMENT} .= "不" ;
	}
	$info{ZIP_COMMENT} .= "带触摸,\n" ;
	if (!($info{MTK_GPS_CHIP} =~ /NONE/))
	{
	#$info{zip_name_v1} .= "_GPS$info{GPS_SUPPORT}" ;
	}
	else
	{
	$info{ZIP_COMMENT} .= "不" ;
	}
	$info{ZIP_COMMENT} .= "带GPS,\n" ;
#		if ($info{TORCH_CONTROL} =~ /TRUE/)
#		{
#		$info{zip_name_v1} .= "_TORCH" ;
#		}
#		else
#		{
#		$info{ZIP_COMMENT} .= "不" ;
#		}
#		$info{ZIP_COMMENT} .= "带手电筒,\n" ;

	if ($info{MTK_2SDCARD_SWAP} =~ /yes/)
	{
	$info{zip_name_v1} .= "_2TCARD" ;
	$info{ZIP_COMMENT} .= "双" ;
	}
	else
	{
	$info{ZIP_COMMENT} .= "单" ;
	}
	$info{ZIP_COMMENT} .= "T卡,\n" ;

	if (!($info{FLASH_SIZE_STR} eq ""))
	{
	$info{zip_name_v1} .= "_$info{FLASH_SIZE_STR}" ;
	$info{ZIP_COMMENT} .= "$info{FLASH_SIZE_STR} FLASH,\n" ;
	}

	my $lcdtype = "WVGA";
	if ($info{LCM_WIDTH} == 480 && $info{LCM_HEIGHT} == 800)
	{
	$lcdtype = "WVGA";
	}
	$info{zip_name_v1} .= "_LCD_${lcdtype}$info{LCM_WIDTH}X$info{LCM_HEIGHT}";
	$info{ZIP_COMMENT} .= "LCD ${lcdtype}$info{LCM_WIDTH}X$info{LCM_HEIGHT}\n" ;

	$info{zip_name_v1} .= substr($info{VERNO}, $sub_len, length($info{VERNO})-$sub_len);
}

sub zip_image()
{
	&logmsg("enter zip_image\n");

	my $n_verno = $info{VERNO};
	my $src_path = "out/target/product/$PROJECT/";
	my $binroot = "bin";

	my $modem_name = lc($info{CUSTOM_MODEM});


	&run_cmd("mkdir -p $binroot");
	&run_cmd("chmod 777 ${src_path}*");
	
	my $bintime = &release_time_str();
	my $release_zip_name = "$binroot/$info{zip_name_v1}_$bintime.zip";
	my $project_out_dir="out/target/product/$PROJECT/";
	
	print "Archiving($release_zip_name)......\n";
	
	&run_cmd("mkdir $binroot/temp");
	&run_cmd("find  $project_out_dir/obj/ -type f -name 'BPLGUInfoCustomAppSrcP*' | xargs -i cp {} $binroot/temp ");
	&run_cmd("find  $project_out_dir/obj/ -type f -name 'APDB_MT6735*' | xargs -i cp {} $binroot/temp ");
	&run_cmd("cp $project_out_dir/MT6735M_Android_scatter.txt $binroot/temp");
	&run_cmd("cp $project_out_dir/ota_scatter.txt $binroot/temp");
	&run_cmd("cp $project_out_dir/secro.img $binroot/temp");
	&run_cmd("cp $project_out_dir/trustzone.bin $binroot/temp");
	&run_cmd("cp $project_out_dir/lk.bin $binroot/temp");
	&run_cmd("cp $project_out_dir/logo.bin $binroot/temp");
	&run_cmd("cp $project_out_dir/preloader_huay6735m_65u_b_l1.bin $binroot/temp");
	&run_cmd("cp $project_out_dir/cache.img $binroot/temp");
	&run_cmd("cp $project_out_dir/ramdisk.img $binroot/temp");
	&run_cmd("cp $project_out_dir/boot.img $binroot/temp");
	&run_cmd("cp $project_out_dir/system.img $binroot/temp");
	&run_cmd("cp $project_out_dir/recovery.img $binroot/temp");
	&run_cmd("cp $project_out_dir/ramdisk-recovery.img $binroot/temp");
	&run_cmd("cp $project_out_dir/userdata.img $binroot/temp");
	&run_cmd("rm -rf $binroot/temp/APDB_MT6735*_ENUM");

	&run_cmd("mv $binroot/temp $binroot/$info{zip_name_v1}_$bintime");
	chdir "$binroot";
	&run_cmd("zip -r $info{zip_name_v1}_$bintime.zip $info{zip_name_v1}_$bintime");
	
	
	print "Done.\n";
	&run_cmd("rm -rf $info{zip_name_v1}_$bintime");
	&logmsg("exit zip_image\n");
}






