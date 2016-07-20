#	/* __NH _1205 _12__ */

sub logmsg()
{
	my $FILE_LOGMSG;
	my ($str) = @_;
	open (FILE_LOGMSG ,">>$logfile") or die "Error: open $logfile.\n";
	print FILE_LOGMSG "$str";
	close FILE_LOGMSG;
}

sub logdebug()
{
	if($debug eq 1)
	{
		&logmsg(@_);
	}
}

sub run_cmd()
{
	my $ret = system("$_[0]");
	&logdebug("$_[0]\n");
	return $ret;
}

sub CurrTimeStr {
  my($sec, $min, $hour, $mday, $mon, $year) = localtime(time);
  $year += 1900;
  return (sprintf "%4.4d-%2.2d-%2.2d %2.2d:%2.2d:%2.2d", $year, $mon+1, $mday, $hour, $min, $sec);
}

sub release_time_str
{
	my($sec, $min, $hour, $mday, $mon, $year) = localtime(time);

	return (sprintf "%2.2d%2.2d", $hour, $min);
}

1;

