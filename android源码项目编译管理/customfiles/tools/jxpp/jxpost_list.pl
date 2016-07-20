#	/* __NH _1205 _12__ */

use File::Basename;
use File::Spec;

#if (1) /* __NH _1206 _26__ */ // keep
my $pwd = "customfiles/tools/jxpp/";
unshift(@INC, "$pwd");
$debug = 0;
$logfile = $pwd."jxpp_log.txt";
require "hx_util.pm"; # global $logfile
my $t = &CurrTimeStr();
&logmsg("[jxpost $t]\n");
#endif /* 1 */

my $tmppath = $pwd."tmp";	

($#ARGV == 0) || &usage();
my $filelist = $ARGV[0];
&logdebug("$filelist\n");

if (!(-e $filelist))
{
	&logdebug("can not open $filelist.\n");
	exit 0;
}


open(FILE_LIST, "$filelist") or die "can not open $filelist.";
while (<FILE_LIST>)
{
	$fname = $_;
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
	exit 1;
}

