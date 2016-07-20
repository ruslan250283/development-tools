#!/usr/bin/perl
# 
#	/* __NH _1205 _12__ */
#	create.
#	/* __NH _1205 _15__ */
#	version 0.1 
#	/* __NH _1205 _30__ */
#	res
#	/* __NH _1206 _05__ */
#	verno
#	/* __NH _1207 _19__ */
#	jxpp remove java FeatureOption define
#

use Scalar::Util qw(looks_like_number);

$debug = 1;

# check input
#($#ARGV == 2) || &usage();
($#ARGV == 1) || &usage();

my $project = lc($ARGV[0]);
my $PRJ_NAME = uc($ARGV[1]);
#my $PRJ_CMD = lc($ARGV[2]);
my $PRJ_FILE = "../customfiles/$PRJ_NAME.mk";
my $PHONE_PROJECT_VERNO = "";

#test_wtwd 20121020
$def_wallpaper_files="../customfiles/default/wallpaper/laucher_wallpaper/drawable-nodpi";
$proj_wallpaper_files="../customfiles/$PRJ_NAME/wallpaper/laucher_wallpaper/drawable-nodpi";
$wp_wallpaper_files="packages/apps/Launcher3/WallpaperPicker/res/drawable-nodpi";


$def_wallpaper_xmlfile="../customfiles/default/wallpaper/laucher_wallpaper/wallpapers.xml";
$proj_wallpaper_xmlfile="../customfiles/$PRJ_NAME/wallpaper/laucher_wallpaper/wallpapers.xml";
$wp_wallpaper_xmlfiles="packages/apps/Launcher3/WallpaperPicker/res/values-nodpi";


#test_wtwd 20121024
$def_poweronoff_files="../customfiles/default/poweronoff";
$proj_poweronoff_files="../customfiles/$PRJ_NAME/poweronoff";
$wp_poweronoff_files="bootable/bootloader/lk/dev/logo";
$wp_poweronoff_media_files="frameworks/base/data/sounds";


die "Error: not found $PRJ_FILE\n"
if (!-e "$PRJ_FILE");


$tools = "../customfiles/tools/jxpp/";
unshift(@INC, "$tools");
require "hx_util.pm";

$logfile = "z${PRJ_NAME}.txt";
&logmsg("[$PRJ_NAME ".&CurrTimeStr."]\n");
if ($project =~ /huay/)
{
	&update_verno_new();
	#exit 0;
}

# java
&convert_to_auto_add_mk();
&run_cmd("make -f hxbuild.mk PRJ_MAK_FILE=${PRJ_FILE}.bak");
if ($project =~ /huay/)
{
#	&cp_managed_alps();
}

#if ($PRJ_CMD =~ /jxpp/)
#{
#	exit 0;
#}

# resource
&update_res_files();
#if ($PRJ_CMD =~ /res/)
#{
#	exit 0;
#}

#gcz_added_20140125
print "starting compile at:   " . &CurrTimeStr . "\n";	

#test_wtwd 20121018
&update_wallpaper();

&update_poweronoff_files();

# custom project
my $des_path = "device/huaying/$project";
&logmsg("$des_path\n");
if (! -d $des_path)
{
	&logmsg("Error: path not exist $des_path\n");
	exit 0;
}

#&update_default_timezone();
#&update_default_input_method();
#&update_default_time_12_24();


#test_wtwd 20120911
&update_verno_mk($PHONE_PROJECT_VERNO);

&run_cmd("cp -f ${PRJ_FILE}.bak $des_path/ProjectConfig.mk");
&run_cmd("rm -f ${PRJ_FILE}.tmp");
&run_cmd("rm -f ${PRJ_FILE}.bak");


#if ($PRJ_CMD =~ /nomake/)
#{
#	exit 0;
#}

#&run_cmd("./mk $project c,r javaoptgen >log.txt 2>&1"); # javaoptgen uboot
# ./mk hexing75_ics clean
# "./mk banyan_addon >log.txt 2>&1"
if ($project =~ /banyan_addon/)
{
&run_cmd("./mk $project >log.txt 2>&1");
}
else
{
	print "copy file ok=====\n";
#&run_cmd("./mk $project $PRJ_CMD >log.txt 2>&1");

&run_cmd("make clean");
&run_cmd("make -j32 2>&1 | tee build.log");
}

# ref \mediatek\build\tools\showRslt.pl
my $chkBin = 1;
my $chkFile;
if ($project =~ /banyan_addon/)
{
$chkFile = "out/host/linux-x86/sdk_addon/mtk_sdk_api_addon-10.1.zip";
}
else
{
$chkFile = "out/target/product/${project}/system.img";
}

if (!-e $chkFile || -z $chkFile) {
    $chkBin = 0;
}
if ($chkBin == 1) {
  print "                    ==> [OK]    " . &CurrTimeStr . "\n";
} else {
  print "                    ==> [FAIL]  " . &CurrTimeStr . "\n";
}
exit 0;

my %by_value;
my %by_name_value;
my %by_name;
my $validated = 0;

sub cp_managed_alps()
{
	my $managed_folder = "../customfiles/managed_alps";
	my $filelist_pre = "${managed_folder}/pound.lis";
	my $filelist = "${managed_folder}/pound_uniq.lis";

	&run_cmd("find $managed_folder -name '*.aidl' > $filelist_pre");
	&run_cmd("find $managed_folder -name '*.xml' >> $filelist_pre");
	&run_cmd("find $managed_folder -name '*.java' >> $filelist_pre");
	&run_cmd("tr ' ' '\n' < $filelist_pre |sort -u > $filelist");

	&logdebug("$filelist\n");

	if (!(-e $filelist))
	{
		&logdebug("can not open $filelist.\n");
		exit 0;
	}

	&run_cmd("/usr/bin/perl ../customfiles/tools/jxpp/jxpre_list_managed.pl $filelist");
}

sub cur_date_str() 
{
	my($sec, $min, $hour, $mday, $mon, $year) = localtime(time);
	$year += 1900;
	#$year =~ s/\d\d//;

	my $logTime = (sprintf "%2.2d%2.2d%2.2d,%2.2d:%2.2d", $year, $mon+1, $mday, $hour, $min);
	&logmsg("logTime: $logTime\n");
	return (sprintf "%2.2d%2.2d%2.2d", $year, $mon+1, $mday);
}

sub	verno_stab()
{
	my $str;
	$_ = $_[0];
	$str = $_;
	&logmsg("in	: $str\n");
	#while($str =~ s/[-]/[_]/g){}
	$_ = $str;
	$_;
}

sub update_verno_mk()
{
	my $default = "CUSTOM_BUILD_VERNO = $_[0]\n";
	my $replace = 0;
	my $F;
	my $file_lines = "";
	#my $fname = "mediatek/config/common/ProjectConfig.mk";
	#test_wtwd 20120911
	my $fname = "${PRJ_FILE}.bak";

	open F, "<$fname" or die "Error: open $fname\n";
	while (<F>)
	{
		my $line = $_;
		if (/^\s*CUSTOM_BUILD_VERNO\s*=\s*/)
		{
			$line = $default;
			$replace = 1;
		}
		$file_lines .= $line;
	}
	if ($replace ne 1)
	{
		&logmsg("Error: Not found CUSTOM_BUILD_VERNO\n");
	}
	close(F);

	open F, ">$fname" or die "Error: open $fname\n";
	print F $file_lines;
	close(F);
}

sub update_verno_new()
{
	my $F;
	my $file_lines = "";
	my $fname = $PRJ_FILE;
	my $ver_str = "";
	
	open (F, "<$fname") or die "cannot open $fname\n";
	while (<F>) 
	{
		my $line = $_;
		if (/^\s*VERNO_SEQ\s*=\s*(\S+)/) 
		{
			# comment has already remove by this pattern
			my $str = $1;
			my $new_verno = $line;

			&logmsg("$line");
			#if ($str =~ /(.*V)(\d{3})_(\d{8})$/)	# 
			#if ($str =~ /(.*V)(\d{2})_(\d{8})$/)
			if ($str =~ /(.*)_(\d{8})$/)
			{
				my $stab = $1;
				#my $snum = $2;
				my $date = $2;
				my $new_stab = $1;
				
				#print "$1;$2;$3\n";
				#&logmsg("old = ($stab, $snum, $date)\n");
				#$snum = $snum + 1;
				#if ($snum == 4)
				#{
					#$snum = $snum + 1;
				#}
				#elsif ($snum == 13)
				#{
					#$snum = $snum + 2;
				#}
				#elsif ($snum > 999)
				#{
					#$snum = 1;
				#}	
				$date = &cur_date_str();
				$new_stab = &verno_stab($stab);

				#&logmsg("new = ($new_stab, $snum, $date)\n");
				#$ver_str = (sprintf "%s%03d_%s", $new_stab, $snum, $date);
				$ver_str = (sprintf "%s_%s", $new_stab,$date);
				$new_verno = "VERNO_SEQ   =   $ver_str\n";
				
				&logmsg("$new_verno");
			}	
			$line = "#".$line.$new_verno;
		}
		$file_lines .= $line;
	}
	close F;	

	&logmsg("write new verno_seq to cfg file.\n");
	open F,">$fname" or die "Can't open $fname";
	print F $file_lines;
	close F;

#test_wtwd 20120906
	$PHONE_PROJECT_VERNO = $ver_str;
	#&update_verno_mk($ver_str);

	# force update build.prop
	&run_cmd("rm -f out/target/product/$project/system/build.prop");
}

sub update_verno()
{
	my $F;
	my $file_lines = "";
	my $fname = $PRJ_FILE;
	my $ver_str = "";
	
	open (F, "<$fname") or die "cannot open $fname\n";
	while (<F>) 
	{
		my $line = $_;
		if (/^\s*VERNO_SEQ\s*=\s*(\S+)/) 
		{
			# comment has already remove by this pattern
			my $str = $1;
			my $new_verno = $line;

			&logmsg("$line");
			if ($str =~ /(.*V)(\d{3})_(\d{8})$/)	# 
			{
				my $stab = $1;
				my $snum = $2;
				my $date = $3;
				my $new_stab = $1;
				
				print "$1;$2;$3\n";
				#&logmsg("old = ($stab, $snum, $date)\n");
				$snum = $snum + 1;
				if ($snum == 4)
				{
					$snum = $snum + 1;
				}
				elsif ($snum == 13)
				{
					$snum = $snum + 2;
				}
				elsif ($snum > 999)
				{
					$snum = 1;
				}	
				$date = &cur_date_str();
				$new_stab = &verno_stab($stab);

				#&logmsg("new = ($new_stab, $snum, $date)\n");
				$ver_str = (sprintf "%s%03d_%s", $new_stab, $snum, $date);
				$new_verno = "VERNO_SEQ   =   $ver_str\n";
				
				&logmsg("$new_verno");
			}	
			$line = "#".$line.$new_verno;
		}
		$file_lines .= $line;
	}
	close F;	

	&logmsg("write new verno_seq to cfg file.\n");
	open F,">$fname" or die "Can't open $fname";
	print F $file_lines;
	close F;

#test_wtwd 20120906
	$PHONE_PROJECT_VERNO = $ver_str;
	#&update_verno_mk($ver_str);

	# force update build.prop
	&run_cmd("rm -f out/target/product/$project/system/build.prop");
}

sub update_default_input_method()
{
	my $replace = 0;
	my $file_lines = "";
	my $default = "    <string name=\"default_input_method\" translatable=\"false\">$by_value{DEFAULT_INPUT_METHOD}</string>\n";
	my $fname = "frameworks/base/packages/SettingsProvider/res/values/defaults.xml";

	if ($default eq "")
	{
		return;
	}

	open F, "<$fname" or die "Error: open $fname\n";
	while (<F>)
	{
		my $line = $_;
		if (/^\s*<string name="default_input_method" translatable="false">(.*)<\/string>\s*$/)
		{
			$line = $default;
			$replace = 1;
		}
		$file_lines .= $line;
	}
	if ($replace ne 1)
	{
		&logmsg("Error: Not found default_input_method\n");
	}
	close(F);

	open F, ">$fname" or die "Error: open $fname\n";
	print F $file_lines;
	close(F);
	
}

sub update_default_time_12_24()
{
	my $replace = 0;
	my $file_lines = "";
	my $default = "    <string name=\"time_12_24\" translatable=\"false\">$by_value{HX_DEF_TIME_FORMAT}</string>\n";
	my $fname = "frameworks/base/packages/SettingsProvider/res/values/defaults.xml";

	if ($default eq "")
	{
		return;
	}

	open F, "<$fname" or die "Error: open $fname\n";
	while (<F>)
	{
		my $line = $_;
		if (/^\s*<string name="time_12_24" translatable="false">(.*)<\/string>\s*$/)
		{
			$line = $default;
			$replace = 1;
		}
		$file_lines .= $line;
	}
	if ($replace ne 1)
	{
		&logmsg("Error: Not found time_12_24\n");
	}
	close(F);

	open F, ">$fname" or die "Error: open $fname\n";
	print F $file_lines;
	close(F);
	
}

sub update_default_timezone()
{
	my $replace = 0;
	my $file_lines = "";
	my $default = "persist.sys.timezone=$by_value{HX_DEF_CURRENT_CITY}\n";
	my $fname = "$des_path/system.prop";

	if ($default eq "")
	{
		return;
	}

	open F, "<$fname" or die "Error: open $fname\n";
	while (<F>)
	{
		my $line = $_;
		if (/^\s*persist.sys.defaulttimezone\s*=\s*(\S+)/)
		{
			$line = $default;
			$replace = 1;
		}
		$file_lines .= $line;
	}
	if ($replace ne 1)
	{
		$file_lines .= $default;
	}
	close(F);

	open F, ">$fname" or die "Error: open $fname\n";
	print F $file_lines;
	close(F);
	
}

#test_wtwd 20121018
sub update_wallpaper()
{

	&run_cmd("rm -rf $wp_wallpaper_files");
	&run_cmd("rm -f $wp_wallpaper_xmlfiles");

	#if(-e "/home/hx/tmp_img/abc")
	if(-e $proj_wallpaper_xmlfile)
	{
		print "file exits\n";

		&run_cmd("cp -rf $proj_wallpaper_files $wp_wallpaper_files");
		&run_cmd("cp -rf $proj_wallpaper_xmlfile $wp_wallpaper_xmlfiles");
	}
	else
	{
		print "file not exits\n";

		&run_cmd("cp -rf $def_wallpaper_files $wp_wallpaper_files");
		&run_cmd("cp -rf $def_wallpaper_xmlfile $wp_wallpaper_xmlfiles");
	}
}


#test_wtwd 20121018
sub update_poweronoff_files()
{

	#&run_cmd("rm -rf $wp_poweronoff_files");

	&run_cmd("cp -rf $def_poweronoff_files/logo/* $wp_poweronoff_files");

	&run_cmd("cp -rf $proj_poweronoff_files/logo/* $wp_poweronoff_files");

	&run_cmd("cp -rf $def_poweronoff_files/media/* $wp_poweronoff_media_files");

	&run_cmd("cp -rf $proj_poweronoff_files/media/* $wp_poweronoff_media_files");
	

		
	
}




# merge .mk file
sub convert_to_auto_add_mk()
{
	my $file_lines = "";
	&logmsg("convert_to_auto_add_mk $PRJ_FILE.\n");

	open F, "<$PRJ_FILE" or die "Error: open $PRJ_FILE\n";
	while (<F>)
	{
		my $line = $_;

		if (/^\s*include\s*(\S+)/)
		{
			my $f1 = $1;
			open F1, "<$f1" or die "Error: open $f1\n";
			while (<F1>)
			{
				$file_lines .= $_;
			}
			close(F1);
		}
		else
		{
			$file_lines .= $line;
		}
	}
	close(F);

	my $fname = "${PRJ_FILE}.tmp";
	open F, ">$fname" or die "Error: open $fname\n";
	print F $file_lines;
	close(F);

	#
	$file_lines = "";
	
	open F, "<$fname" or die "Error: open $fname\n";
	while (<F>)
	{
		chomp;
		if (/^\# Do not remove - insert/)
		{
			$validated = 1;
			&logmsg("$PRJ_FILE tested.\n");
			last;
		}

		next if(/^\s*\#/);
		next if(/^\s*$/);
		if (/^\s*(\S+)\s*=\s*(\S+)/) 
		{
			#&logmsg(">> $1 = $2.\n");
			my ($k, $value)= ($1, $2);
			if (looks_like_number($value))
			{
				$by_name_value{$k} = $value;
				&logmsg("by_name_value $k = [$value]\n");
			}
			elsif ($value =~ /^(yes|no)$/)
			{
				$by_name{$k} = $value;
				&logmsg("by_name $k = $value\n");
			}
			else
			{
				$by_value{$k} = $value;
				&logmsg("by_value $k = $value\n");
			}
		}
	}
	close(F);

	if ($validated ne 1)
	{
		&logmsg("$PRJ_FILE not valid, Please check.\n");
		exit 0;
	}

	my $cust_line_by_name = "";
	my $cust_line_by_value = "";
	my $cust_line_by_name_value = "";
	
	# convert to project
	open F, "<$fname" or die "Error: open $fname\n";
	while (<F>)
	{
		my $line = $_;
		if (/^\s*AUTO_ADD_GLOBAL_DEFINE_BY_NAME\s*=\s*(\w+)/)
		{
			foreach my $v (keys %{by_name})
			{
				$cust_line_by_name .= " " . $v;
			}
			chomp($line);
			$line .= $cust_line_by_name . "\n";
		}
		elsif (/^\s*AUTO_ADD_GLOBAL_DEFINE_BY_VALUE\s*=\s*(\w+)/)
		{
			foreach my $v (keys %{by_value})
			{
				if ($v eq "HX_DEF_CURRENT_CITY")
				{
					# skip;
				}
				elsif ($v eq "DEFAULT_INPUT_METHOD")
				{
					# skip;
				}
				elsif ($v eq "VERNO_SEQ")
				{
					#add by wzb for skip version
				}
				else
				{
					$cust_line_by_value .= " " . $v;
				}
			}
			chomp($line);
			$line .= $cust_line_by_value . "\n";
		}
		elsif (/^\s*AUTO_ADD_GLOBAL_DEFINE_BY_NAME_VALUE\s*=\s*(\w+)/)
		{
			foreach my $v (keys %{by_name_value})
			{
				$cust_line_by_name_value .= " " . $v;
			}
			chomp($line);
			$line .= $cust_line_by_name_value . "\n";
		}
		$file_lines .= $line;
	}
	close(F);

	$fname = "${PRJ_FILE}.bak";
	open F, ">$fname" or die "Error: open $fname\n";
	print F $file_lines;
#if (1) /* __NH _1207 _19__ */
	print F "\n";
	print F "JXPP_AUTO_ADD_GLOBAL_DEFINE_BY_NAME = ${cust_line_by_name}\n";
	print F "JXPP_AUTO_ADD_GLOBAL_DEFINE_BY_VALUE = ${cust_line_by_value}\n";
	print F "JXPP_AUTO_ADD_GLOBAL_DEFINE_BY_NAME_VALUE = ${cust_line_by_name_value}\n";
#endif /* 1 */
	close(F);

}

my @g_files = ();
sub update_res_files()
{
#		@g_files = qw
#		(
#			bootanimation.zip
#			shutanimation.zip
#		);
#		&do_update_file("poweronoff", "frameworks/base/data/sounds");
	#&run_cmd("cp -f ../customfiles/default/apk/AllAudio.mk frameworks/base/data/sounds/AllAudio.mk");
	#&run_cmd("cp -f ../customfiles/$PRJ_NAME/apk/AllAudio.mk frameworks/base/data/sounds/AllAudio.mk");
#	&run_cmd("cp -f ../customfiles/default/apk/AllAudio.mk frameworks/base/data/sounds/AllAudio.mk");
#	&run_cmd("cp -ar ../customfiles/default/packages/* packages/");
#	&run_cmd("cp -ar ../customfiles/$PRJ_NAME/packages/* packages/");

#	&run_cmd("cp -ar ../customfiles/default/signal/* frameworks/base/packages/SystemUI/res/drawable-hdpi/");
#	&run_cmd("cp -ar ../customfiles/$PRJ_NAME/signal/* frameworks/base/packages/SystemUI/res/drawable-hdpi/");

#add by luwl
#&run_cmd("mv external/chromium_org/third_party/angle/git external/chromium_org/third_party/angle/.git");
##for compile err
&run_cmd("mkdir external/chromium_org/third_party/angle/.git");
&run_cmd("touch external/chromium_org/third_party/angle/.git/index");
	&run_cmd("cp -f ../customfiles/default/phone_ext/codegen/codegen.dws bootable/bootloader/lk/target/$project/dct/dct/codegen.dws");
	&run_cmd("cp -f ../customfiles/default/phone_ext/codegen/codegen.dws bootable/bootloader/preloader/custom/$project/dct/dct/codegen.dws");
	&run_cmd("cp -f ../customfiles/default/phone_ext/codegen/codegen.dws kernel-3.10/drivers/misc/mediatek/mach/mt6735/$project/dct/dct/codegen.dws");
	&run_cmd("cp -f ../customfiles/default/phone_ext/codegen/codegen.dws vendor/mediatek/proprietary/custom/$project/kernel/dct/dct/codegen.dws");
	&run_cmd("cp -f ../customfiles/default/phone_ext/memorydevice/custom_MemoryDevice.h bootable/bootloader/preloader/custom/$project/inc/custom_MemoryDevice.h");
#	&run_cmd("cp -f ../customfiles/default/phone_ext/audio_volume/audio_volume_custom_default.h mediatek/custom/common/cgen/inc/audio_volume_custom_default.h");
	&run_cmd("cp -f ../customfiles/default/phone_ext/audio_volume/audio_ver1_volume_custom_default.h vendor/mediatek/proprietary/custom/common/cgen/cfgdefault/audio_ver1_volume_custom_default.h");


	&run_cmd("cp -f ../customfiles/$PRJ_NAME/phone_ext/codegen/codegen.dws bootable/bootloader/lk/target/$project/dct/dct/codegen.dws");
	&run_cmd("cp -f ../customfiles/$PRJ_NAME/phone_ext/codegen/codegen.dws bootable/bootloader/preloader/custom/$project/dct/dct/codegen.dws");
	&run_cmd("cp -f ../customfiles/$PRJ_NAME/phone_ext/codegen/codegen.dws kernel-3.10/drivers/misc/mediatek/mach/mt6735/$project/dct/dct/codegen.dws");
	&run_cmd("cp -f ../customfiles/$PRJ_NAME/phone_ext/codegen/codegen.dws vendor/mediatek/proprietary/custom/$project/kernel/dct/dct/codegen.dws");
	&run_cmd("cp -f ../customfiles/$PRJ_NAME/phone_ext/memorydevice/custom_MemoryDevice.h bootable/bootloader/preloader/custom/$project/inc/custom_MemoryDevice.h");
#	&run_cmd("cp -f ../customfiles/$PRJ_NAME/phone_ext/audio_volume/audio_volume_custom_default.h mediatek/custom/common/cgen/inc/audio_volume_custom_default.h");
	&run_cmd("cp -f ../customfiles/$PRJ_NAME/phone_ext/audio_volume/audio_ver1_volume_custom_default.h vendor/mediatek/proprietary/custom/common/cgen/cfgdefault/audio_ver1_volume_custom_default.h");


	&run_cmd("rm -f out/target/product/$project/system/media/bootanimation.zip");
	&run_cmd("rm -f out/target/product/$project/system/media/bootaudio.mp3");
	&run_cmd("rm -f out/target/product/$project/system/media/shutanimation.zip");
	&run_cmd("rm -f out/target/product/$project/system/media/shutaudio.mp3");

#test_wtwd 20121207
	&run_cmd("cp -rf ../customfiles/default/alps ../");
	&run_cmd("cp -rf ../customfiles/$PRJ_NAME/alps ../");
	
#if (1) /* __NH _1207 _04__ */ for rollback, run once.
# cp -ar ../customfiles/default/frameworks_org/* frameworks/
# cp -ar ../customfiles/default/packages_org/* packages/
#endif /* 1 */
	&do_update_file_etc();
}

sub do_update_file()
{
	my ($sub, $des_path) = @_;
	my $default_path = "../customfiles/default/$sub";
	my $src_path = "../customfiles/$PRJ_NAME/$sub";
	
	(!-d $src_path) && die "Error: $src_path: $!";
	(!-d $des_path) && die "Error: $des_path: $!";
	
	foreach (@g_files) 
	{
		&run_cmd("rm -f $des_path/$_");
		&run_cmd("cp -f $default_path/$_ $des_path/$_");
		&run_cmd("cp -f $src_path/$_ $des_path/$_");
	}
}

sub do_update_file_etc()
{
	my $local = "../customfiles/$PRJ_NAME/";
	my $fname = "$local/file_list.txt";
	if (-e $fname)
	{
		open F, "<$fname" or die "Error: $fname: $!";
		while (<F>)
		{
			if (/^\s*#/)
			{
				next;
			}
			
			chomp;
			my ($src_file, $des_file) = split /:/;
			&logdebug("[$src_file][$des_file]\n");
			if (($src_file eq "") && !($des_file eq ""))
			{
				# use for project cmd /* __NH _1207 _04__ */
				&run_cmd($local.$des_file);
				next;
			}
			elsif (($src_file eq "") || ($des_file eq ""))
			{
				next;
			}

			my $default_file = "../customfiles/default/".$src_file;
			$src_file = $local.$src_file;
			&logdebug("[$src_file][$des_file][$default_file]\n");
			#(!-e $default_file) && die "Error: $default_file: $!";
			(!-e $src_file) && die "Error: $src_file: $!";
			#(!-e $des_file) && die "Error: $des_file: $!";

			&run_cmd("rm -f $des_file");
			&run_cmd("cp -f $default_file $des_file");
			&run_cmd("cp -f $src_file $des_file");
		}
		close(F);
	}
}

sub usage()
{
	print "\nUsage:\n perl xx.pl <PRJ_TYPE> <PRJ_NAME>\n";
	&logdebug("usage()\n");
	exit 1;
}


