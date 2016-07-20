#	
#	/* __NH _1204 _27__ */
#	initial version.
#
#	/* __NH _1205 _02__ */
#	use "#" to skip non-preprocessor files.
#	
#	/* __NH _1205 _07__ */
#	linux version, file list
#
#	/* __NH _1205 _11__ */
#	module version
#
use File::Basename;
use File::Spec;

#if (1) /* __NH _1206 _26__ */ // keep
my $pwd = "customfiles/tools/jxpp/";
unshift(@INC, "$pwd");
$debug = 1;
$logfile = $pwd."jxpp_log.txt";
require "hx_util.pm"; # global $logfile
my $t = &CurrTimeStr();
&logmsg("[jxuniq $t]\n");
&logdebug("pwd = $pwd\n");
#endif /* 1 */

my $tmppath = $pwd."tmp";	
my $cmd_gcc = "gcc";  #$pwd."/tools/MinGW/bin/gcc.exe"); 
my $dfile = $pwd."prj_def.txt";
my $ufile = $pwd."prj_undef.txt";

# gcc add -D -U
my $gcc_d = &get_def_str($dfile);
my $gcc_u = &get_def_str($ufile);

($#ARGV >= 0) || &usage();
for (my $i=0; $i<=$#ARGV; $i++)
{
	chomp($ARGV[$i]);
	my $tmp_dir = $ARGV[$i];
	if (-d $tmp_dir)
	{
		my $tmp_pre_filelist = "${tmppath}/$tmp_dir/xml-source-list";
		my $tmp_filelist     = "${tmppath}/$tmp_dir/xml-source-list-uniq";
		&run_cmd("mkdir -p ${tmppath}/$tmp_dir");
		&run_cmd("find $tmp_dir -name '*.xml' > $tmp_pre_filelist");
		&run_cmd("tr ' ' '\n' < $tmp_pre_filelist |sort -u > $tmp_filelist");
		&pre_filelist($tmp_filelist);
	}
}

sub pre_filelist()
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
			&pp_file($fname);
		}
		else
		{
			&logdebug("Error: not found $fname.\n");
		}
	}
	close(FILE_LIST);
}

sub pp_file()
{
	my ($jfile) = @_;
	my $pure_f = "";
	
	if (!(-e $jfile))
	{
		&logdebug("can not open $jfile.\n");
		return;
	}

	if (!($jfile =~ /(.*?)(\.java)|(\.xml)$/i))
	{
		&logdebug("...skip file type\n");
		return;
	}
	else
	{
		open (JFILE, "$jfile") or die "Error: open $jfile\n";
		my $first_line = <JFILE>;
		close(JFILE);
		if (!($first_line =~ /^\#/))
		{
			&logdebug("...skip no signal\n");
			return;
		}
	}

	&logmsg("$jfile");
	&logdebug("\n");
	chomp($jfile);
	$bname = basename($jfile);
	$pure_f = $tmppath."/".$bname;

	$cfile = "$pure_f".".c";
	#$org_path = dirname($jfile);
	$ifile = "$pure_f".".i";
	$i2file = "$pure_f".".i2";
	&run_cmd("cp -f $jfile $cfile");
	#&logmsg("$cmd_gcc -w -E $gcc_d $gcc_u $cfile -o $ifile");
	&run_cmd("$cmd_gcc -w -E $gcc_d $gcc_u $cfile -o $ifile");
	&logdebug("$i2file\n");
	open(FIN, "$ifile") or die "can not open $ifile.";
	@line = "";
	while (<FIN>)
	{
		if (/^\#\s*(\d+)\s*/)
		{
		}
		elsif (/^$/)
		{
		}
		else
		{
			push @line, $_;
		}
	}
	close(FIN);

	open(FOUT, ">$i2file") or die "can not open $i2file.";
	print FOUT @line;
	close(FOUT);

# /* __NH _1204 _28__ */
# for aapt, copy it to somewhere else.
	my($vol,$dir,$file) = File::Spec->splitpath($jfile);
	&logdebug("(vol,dir,file)=($vol,$dir,$file)\n");

	my $sep = (($dir =~ /^\//) ? "" : "/");
	$ren_jfile = $tmppath."/".$dir.$file.".pp";
	&logdebug("ren_jfile=$ren_jfile\n");
	&run_cmd("mkdir -p ${tmppath}${sep}${dir}");
	&run_cmd("cp -f $jfile $ren_jfile");
	&run_cmd("cp -f $i2file $jfile");
	&run_cmd("chmod 777 $jfile");

	&logmsg("\n");
}

sub get_def_str()
{
	my ($file) = @_;
	my $tmp_def = "";
	my $line = "";
	
	open(F, "$file") or die "can not open $file.";
	while (<F>) 
	{
#		$_ =~ s/\\/ /;
		$_ =~ s/\n/ /;
		$line = $line.$_;
	}
	close(F);

	return $line;
}

sub usage()
{
	print "\nUsage:\n perl xx.pl <src_path>\n";
	&logdebug("usage()\n");
	exit 0;
}

