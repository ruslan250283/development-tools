#	/* __NH _1205 _12__ */

use File::Basename;
use File::Spec;

#if (1) /* __NH _1206 _26__ */ // keep
my $pwd = "customfiles/tools/jxpp/";
unshift(@INC, "$pwd");
$debug = 1;
$logfile = $pwd."jxpp_log.txt";
require "hx_util.pm"; # global $logfile
my $t = &CurrTimeStr();
&logmsg("[jxuniq jxpost $t]\n");
#endif /* 1 */

my $tmppath = $pwd."tmp";	

($#ARGV >= 0) || &usage();
for (my $i=0; $i<=$#ARGV; $i++)
{
	chomp($ARGV[$i]);
	my $tmp_dir = $ARGV[$i];
	if (-d $tmp_dir)
	{
		my $tmp_pre_filelist = "${tmppath}/$tmp_dir/xml-source-list";
		my $tmp_filelist     = "${tmppath}/$tmp_dir/xml-source-list-uniq";
		
		&post_filelist($tmp_filelist);
		&run_cmd("rm -f $tmp_pre_filelist");
		&run_cmd("rm -f $tmp_filelist");
	}
}

sub post_filelist()
{
	my ($filelist) = @_;
	&logdebug("$filelist\n");

	if (!(-e $filelist))
	{
		&logdebug("can not open $filelist.\n");
		exit 0;
	}

	open(FILE_LIST, "$filelist") or die "can not open $filelist.";
	while (<FILE_LIST>)
	{
		my $fname = $_;
		chomp($fname);
		&logdebug("$fname\n");
		if (-e $fname)
		{
			&post_file($fname);
		}
		else
		{
			&logdebug("Error: not found $fname.\n");
		}
	}
	close(FILE_LIST);
}

sub post_file()
{
	my ($jfile) = @_;

	if (!(-e $jfile))
	{
		&logdebug("can not open $jfile.");
		return;
	}

	&logdebug("$jfile\n");
	my($vol,$dir,$file) = File::Spec->splitpath($jfile);
	&logdebug("(vol,dir,file)=($vol,$dir,$file)\n");

	$ren_jfile = $tmppath."/".$dir.$file.".pp";
	&logdebug("$ren_jfile\n");
	if (-e "$ren_jfile")
	{
		if (($jfile =~ /(.*?)(\.java)|(\.xml)$/i))
		{
			&logmsg("$ren_jfile");
			&logdebug("\n");
			&run_cmd("mv -f $ren_jfile $jfile"); # still have one copy .c
			&logmsg("\n");
		}
	}
}

sub usage()
{
	print "\nUsage:\n perl xx.pl <src_path>\n";
	&logdebug("usage()\n");
	exit 0;
}

