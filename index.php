<!DOCTYPE html>
<html>
<head>
  <title>It-jobbank jobs</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <!--
  <link rel="stylesheet" href="https://www.it-jobbank.dk/css/_less/bootstrap-themes/itjobbank.css?v=efdd044">
  -->
  <link rel="stylesheet" href="assests/itjobbank.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>
<body>

<?php
include_once('simple_html_dom.php');

// Create database
include 'create_db.php';

// Create connection
include 'dbc.php';

$db = new dbObj();
$conn =  $db->getConnstring();

function execSql($con, $sql, $message) {
    if (!mysqli_query($con, $sql)) {
        echo $message . mysqli_error($conn);
    }
}

// Create table itjobs
$sql1 = "CREATE TABLE IF NOT EXISTS `itjobs` (
        `id` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
        `title` VARCHAR(255) NOT NULL,
        `company` VARCHAR(255) NOT NULL,
        `location` VARCHAR(255) NOT NULL,
        `published` VARCHAR(255) NOT NULL,
        `url` VARCHAR(255) NOT NULL,
        `logourl` VARCHAR(255) NOT NULL,
        `description` VARCHAR(1020) NOT NULL,
        `added_date` TIMESTAMP
        )
        COMMENT='It jobbank webudvikling'
          DEFAULT CHARACTER SET utf8 COLLATE utf8_danish_ci";

mysqli_query($conn, $sql1) or die(mysqli_error($conn)); 
//execSql($conn, $sql1, "Kan ikke oprette tabellen: ");

// Empty table itjobs
$sql2 = "TRUNCATE TABLE itjobs";

mysqli_query($conn, $sql2) or die(mysqli_error($conn)); 

// Change character set to utf8
mysqli_set_charset($conn,"utf8");

// Create DOM from URL or file
$html = file_get_html('https://www.it-jobbank.dk/job/software-webudvikling');

$i = 0;

foreach($html->find('div.result div.hidden-xs') as $job) {
    
    $i++;
    
    // Get job title
    $item['title'] = $job->find('h3.title', 0)->plaintext;

	// Get job company
    $item['company'] = $job->find('div.info', 0)->plaintext;

	// Get job location
    $item['location'] = $job->find('p.location', 0)->plaintext;

	// Get job published date
    $item['published'] = $job->find('time.published', 0)->innertext;

	// Get job description
    $item['description'] = $job->find('div.description p', 0)->plaintext;

	// Get job url
    $item['url'] = $job->find('div.right a', 0)->href;

    // Get company logo url
    $item['logoUrl'] = $job->find('div.right img', 0)->src;

    // Converts the character encoding
    $curTitle       = mb_convert_encoding($item['title'], 'ISO-8859-1', 'UTF-8');
    $curCompany     = mb_convert_encoding($item['company'], 'ISO-8859-1', 'UTF-8');
    $curLocation    = mb_convert_encoding($item['location'], 'ISO-8859-1', 'UTF-8');
    $curPublished   = mb_convert_encoding($item['published'], 'ISO-8859-1', 'UTF-8');
    $curDescription = mb_convert_encoding($item['description'], 'ISO-8859-1', 'UTF-8');
    $curUrl         = mb_convert_encoding($item['url'], 'ISO-8859-1', 'UTF-8');
    $curLogoUrl     = mb_convert_encoding($item['logoUrl'], 'ISO-8859-1', 'UTF-8');

    // Escapes special characters, here single quote
    $curTitle       = mysqli_real_escape_string($conn, $curTitle);
    $curCompany     = mysqli_real_escape_string($conn, $curCompany);
    $curLocation    = mysqli_real_escape_string($conn, $curLocation);
    $curPublished   = mysqli_real_escape_string($conn, $curPublished);
    $curDescription = mysqli_real_escape_string($conn, $curDescription);
    $curUrl         = mysqli_real_escape_string($conn, $curUrl);
    $curLogoUrl     = mysqli_real_escape_string($conn, $curLogoUrl);
    
    $jobs[] = $item;

    $sql = "INSERT INTO itjobs (title, company, location, published, description, url, logourl)
    VALUES ('$curTitle', '$curCompany', '$curLocation', '$curPublished', '$curDescription', '$curUrl', '$curLogoUrl')";
    
    mysqli_query($conn, $sql) or die(mysqli_error($conn)); 
    
	// Show only top 10 jobs
	if ($i == 10) {
        break;
    }
}

mysqli_close($conn);

// Convert the Associative Array into JSON
$jobJSON = json_encode($jobs);

//echo $jobJSON;

// Get the Rest API's url
$url = "http://localhost/boersen_wissam_v1/api/v1/jobs";
$response = file_get_contents($url);

$jsonObject = json_decode($response, true);

//echo $response;

$urlbase = "https://www.it-jobbank.dk";

?>

<div class="container">
    
    <div class="row">
        <div class="col-sm-12">
            <div class="pull-left">
                <div class="search-title--sub">De første <span>10</span> scrabbede jobs fra IT-jobbank</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <div class="search">

                <div class="results">
                
<!-- Loop throught the object rows --> 
<?php foreach($jsonObject as $obj) { ?>

    <div class="result">
        <div class="table-row hidden-xs">
            <div class="left">
                <h3 class="title">
                    <a href="<?php echo $obj['url']; ?>" target="_blank"><?php echo $obj['title']; ?></a>
                </h3>
                <div class="info"><?php echo $obj['company']; ?></div>
                <p class="location">
                    <img src="<?php echo $urlbase; ?>/res/itjobbank/public_html/assets/images/location.png" width="6" height="8" alt="">
                    <?php echo $obj['location']; ?>
                </p>

                <div class="description">
                    <p><?php echo $obj['description']; ?></p>
                    <time class="published"><?php echo $obj['published']; ?></time>
                </div>
            </div>
            <div class="right">
                <img class="company-logo img-responsive" src="<?php echo $urlbase . $obj['logourl']; ?>">          
                <a class="btn btn-default btn-sm btn-block" href="<?php echo $obj['url']; ?>" target="_blank">Se job</a>
            </div>
        </div>
        <!-- mobile result -->
        <div class="visible-xs-block">
            <div class="title-row">
                <h3 class="title"><a href="<?php echo $obj['url']; ?>" target="_blank"><?php echo $obj['title']; ?></a></h3>
            </div>
            <div class="logo-info-row">
                <div class="company-logo">
                    <img class="img-responsive" src="<?php echo $urlbase . $obj['logourl']; ?>">
                </div>
                <div class="info"><?php echo $obj['company']; ?><br><?php echo $obj['location']; ?></div>
            </div>
            <div class="description">
                <p><?php echo $obj['description']; ?></p>
                <time class="published"><?php echo $obj['published']; ?></time>
            </div>
            <a class="btn btn-default btn-sm btn-block" href="<?php echo $obj['url']; ?>" target="_blank">Se job</a>
        </div>
        <!-- end of mobile result -->
    </div>

<?php
} // End of foreach($jsonObject as $obj)

?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>