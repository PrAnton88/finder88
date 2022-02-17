<?php
	fopen("/rninit.txt","w+");
	// system('id'); >> uid=1051943(oto016) gid=1049089 groups=1049089
	
	// chown -R oto016:1049089 /path/to/your/dir
	// chmod 0760 -R /path/to/your/dir
?>