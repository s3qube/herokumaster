<?

set_time_limit(0);

$imagesDir = "/Users/timedgar/Sites/OPM/resources/files/visuals/";

$fileExt = array (

	"image/pjpeg" => ".jpg",
	"image/jpeg" => ".jpg",
	"image/gif" => ".gif",
	"image/png" => ".png",
	"image/x-png" => ".png"	

);

$con = mysql_connect("localhost","root","jamesking");

if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("bravado_opm", $con);


$sql = "SELECT opm_images.*, OCTET_LENGTH(opm_images.image) as image_filesize FROM opm_images WHERE movedtofile = 0";

$result = mysql_query($sql);

$count = 0;

while($row = mysql_fetch_assoc($result)) {

	$filepath = $imagesDir . $row['imageid'];

	$fp = fopen($filepath, 'w');
	fwrite($fp, $row['image']);
	fclose($fp);
	
	// check if filesizes match up, if so… confirm move.
	
	if (filesize($filepath) == $row['image_filesize']) {
	
		$sql = "UPDATE opm_images SET movedtofile = 1 WHERE imageid = " . $row['imageid'];
		mysql_query($sql);
		
		$count++;
		
		echo "Moved imageid # " . $row['imageid'] . "\n";
			
	}
	
	
	
}

mysql_close($con);

echo "\n\nMoved " . $count . " images.";


?>