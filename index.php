
<?php
$state = isset($_GET['s']) ? $_GET['s'] : '0';
include('config.php');
$config = defined('CONFIG') ? CONFIG : false;
$file_name = defined('SITE_NAME') ? date('ymd') .'_bkp_'. SITE_NAME : '';
$url = defined('SITE_URL') && defined('SITE_DIR') ? 'http://'. SITE_URL . SITE_DIR .'/index.php' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create a backup of <?php echo $site_name; ?><</title>
</head>
<style>
  body {
    background-color: #F1F1F1;
    font-family:'Courier New', Courier, monospace;
    color: #363636;
  }
  body > div {
    max-width: 400px;
    width: 100%;
    margin: 10vh auto;
    text-align: center;
  }
  i {
    font-size: 0.8rem;
  }
  a, button {
    display: block;
    padding: 1rem;
    margin: 2rem 0;
    background-color: #5C0BB3;
    color: #F1F1F1;
    font-family: 'Courier New', Courier, monospace;
    font-size: 1rem;
    text-decoration: none;
    border: 2px solid #5C0BB3;
    cursor: pointer;
  }
  a:hover, button:hover {
    background-color: #D6C4FF;
    color: #5C0BB3;
  }
  form {
    text-align: left;
  }
  fieldset {
    display: none;
    padding: 0;
    border: none;
  }
  fieldset[data-show="is-toggled"] {
    display: block;
  }
  fieldset > div[data-show="is-toggled"] {
    display: none;
  }
  label {
    display: block;
    width: 100%;
    margin-bottom: 2rem;
  }
  input {
    font-size: 1rem;
  }
  input:not([type="checkbox"]) {
    width: 100%;
    padding: 0.5rem;
    box-sizing: border-box;
  }
</style>
<script>
  function toggleDbFields(el, required ='') {
    dbFields = el.parentElement.nextElementSibling.querySelectorAll('input[type="text"]');
    if(dbFields && required > '') {
      dbFields.forEach((inputFields) => {
        inputFields.setAttribute('required', '');
      });
    }
    else if(dbFields && required == '') {
      dbFields.forEach((inputFields) => {
        inputFields.removeAttribute('required');
      });
    }
  }
  function toggleForm(id) {
    let elId = document.querySelector('#' + id);
    let toggleField = elId.parentElement.nextElementSibling;
    if(elId.checked) {
      toggleField.setAttribute('data-show', 'is-toggled');
      if(id == 'site-db') {
        toggleDbFields(elId, 'required')
      }
    }
    else {
      toggleField.setAttribute('data-show', '');
      if(id == 'site-db') {
        toggleDbFields(elId)
      }
    }
  }
</script>
<body>  
  <div>
    <?php if($config == false): ?>
      <h1>Configuration Backup Script</h1>
      <!-- Setup the configuration if doesn't already exists -->
      <?php
      $shell = isset($_POST['shell']) ? $_POST['shell'] : false;
      $site_name = isset($_POST['site-name']) ? addslashes($_POST['site-name']) : '';
      $site_db = isset($_POST['site-db']) ? $_POST['site-db'] : '';
      $site_db_name = isset($_POST['site-db-name']) ? addslashes($_POST['site-db-name']) : '';
      $site_db_user = isset($_POST['site-db-user']) ? addslashes($_POST['site-db-user']) : '';
      $site_db_psw = isset($_POST['site-db-psw']) ? addslashes($_POST['site-db-psw']) : '';
      $site_db_host = isset($_POST['site-db-host']) ? addslashes($_POST['site-db-host']) : '';
      $site_base_dir = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';
      $site_dir = isset($_SERVER['SCRIPT_NAME']) ? substr($_SERVER['SCRIPT_NAME'], 0, -10) : '';
      $site_url = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

      if($site_name > '' && $site_dir && $site_url):
        ?>
        <?php
        $file_config = $site_base_dir . $site_dir . '/config.php';
        $handle = fopen($file_config, 'a');
        $data = 
          "\n## Basic configuration\ndefine('SHELL', '". $shell ."');\ndefine('SITE_NAME', '". $site_name ."');\ndefine('SITE_HAS_DB', '". $site_db ."');\ndefine('SITE_BASE_DIR', '". $site_base_dir ."');\ndefine('SITE_DIR', '". $site_dir ."');\ndefine('SITE_URL', '". $site_url ."');\n##";
        fwrite($handle, $data);
        if($site_db == 'true') {
          $data = "\n## Configuration database\ndefine('DB_NAME', '". $site_db_name ."');\ndefine('DB_USER', '". $site_db_user ."');\ndefine('DB_PASSWORD', '". $site_db_psw ."');\ndefine('DB_HOST', '". $site_db_host ."');\n##";
          fwrite($handle, $data);
        }
        $config = true;
        $data = "\ndefine('CONFIG', '". $config ."');";
        fwrite($handle, $data);
        fclose($handle);
        ?>
        <p>Setup configuration sucessfully</p>
        <a href="<?php echo $url .'?s=0'; ?>" onclick="window.location.reload()">Start your first backup</a>

      <?php else: ?>
        <form action="index.php" method="POST">
          <?php
          ini_set('max_execution_time', '300');
          ini_set('set_time_limit', '0');
          if(is_callable('shell_exec') && false === stripos(ini_get('disable_functions'), 'shell_exec')) {
            $shell_access = true;
          }
          else {
            $shell_access = false;
          }
          ?>
          <input type="hidden" name="shell" id="shell" value="<?php echo $shell_access; ?>">
          <label for="site-name">
            <p>Name of you site</p>
            <input type="text" name="site-name" id="site-name" required>
            <p><i>The name will be used for the backups file name. Enter a short name usign only alphabetic charactes.</i></p>
          </label>
          <label for="site-db">
            <input type="checkbox" name="site-db" id="site-db" value="true" onchange="toggleForm('site-db')">
            Do you use a database for your website?
          </label>
          <fieldset>
            <div class="db-fields">
              <label for="site-db-name">
                <p>Database name</p>
                <input type="text" name="site-db-name" id="site-db-name">
              </label>
              <label for="site-db-user">
                <p>Database user</p>
                <input type="text" name="site-db-user" id="site-db-user">
              </label>
              <label for="site-db-psw">
                <p>Database password</p>
                <input type="text" name="site-db-psw" id="site-db-psw">
              </label>
              <label for="site-db-host">
                <p>Database hostname</p>
                <input type="text" name="site-db-host" id="site-db-host" value="localhost">
                <p><i>Default value is <code>localhost</code></i></p>
              </label>
            </div>
          </fieldset>
          <button type="submit">Save configuration</button>
        </form>      
      <?php endif; ?>

    <?php elseif($config == true): ?>
      <h1>Create a backup (step <?php echo $state; ?>)</h1>
      <?php if($state == '0'): ?>
        <p>Create backup for website "<?php echo SITE_NAME; ?>".</p>
        <p>Ceate backup of all files in direcotroy:</p>
        <a href="<?php echo $url .'?s=1'; ?>">Start</a>
      
      <?php elseif($state == '1'): ?>
        <?php
        if(SHELL == true) {
          $cmd_data = 'tar -cvf '. $file_name .'.tar '. SITE_BASE_DIR .'/*';
          $output_data = shell_exec($cmd_data);
          $bkp_data = $output_data > '' ? true : false;
        }
        elseif(SHELL != true) {
          try {
            $phar = new PharData($file_name .'.tar');
            $phar->buildFromDirectory(SITE_BASE_DIR .'/.');
  
            $bkp_data = true;
            $msg_state_data = 'Compressing files done';
          }
          catch (Exception $e) {
            $bkp_data = false;
            $msg_state_data = 'Error during compressing files: /n'. $e->getMessage();
          }
        }
        ?>
        <?php if($bkp_data == true): ?>
          <p>Created backup of all files sucessfully🎉</p>
          <?php echo SITE_HAS_DB == true ? '<p>Go on with backup of database:</p>' : '<p>Compress data in a ZIP archive and prepare for download:</p>'; ?>
          <a href="<?php echo SITE_HAS_DB == true ? $url .'?s=2' : $url .'?s=3'; ?>">Start</a>
        <?php elseif($bkp_data == false): ?>
          <p>Error during backup: <?php echo $msg_state_db; ?></p>
        <?php endif; ?>

      <?php elseif($state == '2'): ?>
        <?php
        if(SHELL == true) {
          $cmd_db = 'mysqldump '. DB_NAME .' > '. DB_NAME .'.sql -u '. DB_USER .' -p"'. DB_PASSWORD .'"';
          $output_db = shell_exec($cmd_db);
          $bkp_db = $output_db > '' ? true : false;
        }
        elseif(SHELL != true) {
          if(DB_NAME && DB_HOST && DB_USER && DB_PASSWORD) {
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    
            // open mysql connection
            if($mysqli->errno) {
              echo 'Errore di connessione al DB:' . $mysqli->error;
              exit();
            }
            else {
              $mysqli->set_charset('utf8');
            }
            // get all tables
            $tables = array();
            $sql = "SHOW TABLES";
            $res = $mysqli->query($sql);
    
            while ($row = $res->fetch_row()) {
                $tables[] = $row[0];
            }
            // get content for every table
            $sqlScript = "";
            foreach ($tables as $table) {
                
              // prepare SQLscript for creating table structure
              $query = "SHOW CREATE TABLE $table";
              $res = $mysqli->query($query);
              $row = $res->fetch_row();
              
              $sqlScript .= "\n\n" . $row[1] . ";\n\n";
              
              $query = "SELECT * FROM $table";
              $res = $mysqli->query($query);
              
              $columnCount = mysqli_num_fields($res);
              
              // prepare SQLscript for dumping data for each table
              for ($i = 0; $i < $columnCount; $i ++) {
                while ($row = $res->fetch_row()) {
                  $sqlScript .= "INSERT INTO $table VALUES(";
                  for ($j = 0; $j < $columnCount; $j ++) {
                    $row[$j] = $row[$j];
                    
                    if (isset($row[$j])) {
                      $sqlScript .= '"' . addslashes($row[$j]) . '"';
                    }
                    else {
                      $sqlScript .= '""';
                    }
                    if ($j < ($columnCount - 1)) {
                      $sqlScript .= ',';
                    }
                  }
                  $sqlScript .= ");\n";
                }
              }        
              $sqlScript .= "\n"; 
            }
    
            if(!empty($sqlScript)) {
              // Save the SQL script to a backup file
              $fileHandler = fopen(DB_NAME .'.sql', 'w+');
              $number_of_lines = fwrite($fileHandler, $sqlScript);
              fclose($fileHandler);
            }
            $bkp_db = true;
            $msg_state_db = 'Backup of database done';
          }
          else {
            $bkp_db = false;
            $msg_state_db = 'Error during backup of database, the database credentials are not compelte';
          }
        }
        ?>
        <?php if($bkp_db == true): ?>
          <p>Created backup of database sucessfully🎉</p>
          <p>Compress data in a ZIP archive and prepare for download:</p>
          <a href="<?php echo $url .'?s=3'; ?>">Start</a>
        <?php elseif($bkp_db == false): ?>
          <p>Error during backup: <?php echo $msg_state_db; ?></p>
        <?php endif; ?>

      <?php elseif($state == '3'): ?>
        <?php
        // create a ZIP arcive with data and database
        $zip = new ZipArchive;
        if($zip->open($file_name .'.zip', ZipArchive::CREATE) === TRUE) {
          // Add files to the zip file
          $zip->addFile($file_name .'.tar');
          if(SITE_HAS_DB == true) {
            $zip->addFile(DB_NAME .'.sql');
          }
      
          // All files are added, so close the zip file.
          $zip->close();
          $bkp_zip = true;
          $msg_state_zip = 'Compressing files done';
        }
        else {
          $bkp_zip = false;
          $msg_state_zip = 'Error during compressing files';
        }
        ?>
        <?php if($bkp_zip == true): ?>
          <p>Created ZIP archive sucessfully🎉</p>
          <a href="<?php echo $file_name .'.zip'; ?>" target="_blank">Download ZIP</a>
          <p>Delete backup from server:</p>
          <a href="<?php echo $url .'?s=4'; ?>">Finish</a>
        <?php elseif($bkp_zip == false): ?>
          <p>Error during compressing files: <?php echo $msg_state_zip; ?></p>
        <?php endif; ?>

      <?php elseif($state == '4'): ?>
        <?php
        if(unlink($file_name .'.zip')) {
          if(unlink($file_name .'.tar')) {
            if(SITE_HAS_DB == true && unlink(DB_NAME .'.sql')) {
              echo '<p>Files and database deleted from server</p>';
            }
            else if(SITE_HAS_DB == false && unlink(DB_NAME .'.sql')) {
              echo '<p>Files deleted from server</p>';
            }
            else {
              echo '<p>Error during deleting backup database, please check manually!</p>';
            }
          }
          else {
            echo '<p>Error during deleting backup files, please check manually!</p>';
          }
        }
        else {
          echo '<p>Error during deleting ZIP-File, please check manually!</p>';
        }
        ?>

      <?php endif; ?> 
    <?php endif; ?>
  </div>
</body>
</html>