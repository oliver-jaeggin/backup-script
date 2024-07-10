<?php
$state = isset($_GET['s']) ? $_GET['s'] : '0';
file_exists('config.php') ? include('config.php') : '';
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
  <title>Create a backup of "<?php echo defined('SITE_NAME') ? SITE_NAME : ''; ?>"</title>
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
    display: flex;
    justify-content: center;
    align-content: center;
    gap: 12px;
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
  a svg, button svg {
    visibility: hidden;
    margin-left: -30px;
  }
  a[disabled], button:disabled {
    background-color: grey;
    border-color: grey;
    color: #363636;
    opacity: 0.7;
    cursor: not-allowed;
  }
  a[disabled] svg, button:disabled svg {
    visibility: visible;
    animation: 3s linear 0s infinite normal spinning-button;
  }
  pre {
    padding: 0.2rem 0.4rem;
    background-color: lightgray;
    border-radius: 0.2rem;
    font-size: 12px;
    white-space: break-spaces;

  }
  @keyframes spinning-button {
    from {
      transform: rotate(0deg);
    }
    to {
      transform: rotate(360deg);
    }
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
          "<?php\n## Basic configuration\ndefine('SHELL', '". $shell ."');\ndefine('SITE_NAME', '". $site_name ."');\ndefine('SITE_HAS_DB', '". $site_db ."');\ndefine('SITE_BASE_DIR', '". $site_base_dir ."');\ndefine('SITE_DIR', '". $site_dir ."');\ndefine('SITE_URL', '". $site_url ."');\n##";
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
          <button type="submit" onclick="this.setAttribute('disabled', '')">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M17.5 9.16675C17.279 9.16675 17.067 9.25455 16.9108 9.41083C16.7545 9.56711 16.6667 9.77907 16.6667 10.0001C16.6766 11.5609 16.1419 13.0764 15.1547 14.2855C14.1675 15.4945 12.7896 16.3215 11.2583 16.6239C9.72702 16.9264 8.13817 16.6854 6.76544 15.9424C5.39271 15.1994 4.32203 14.0011 3.73779 12.5536C3.15356 11.1062 3.09235 9.50034 3.56471 8.01264C4.03706 6.52494 5.01342 5.24853 6.32561 4.40323C7.6378 3.55793 9.20369 3.19668 10.7536 3.38167C12.3035 3.56667 13.7403 4.28635 14.8167 5.41675H12.8167C12.5957 5.41675 12.3837 5.50455 12.2274 5.66083C12.0712 5.81711 11.9834 6.02907 11.9834 6.25008C11.9834 6.47109 12.0712 6.68306 12.2274 6.83934C12.3837 6.99562 12.5957 7.08341 12.8167 7.08341H16.5917C16.8127 7.08341 17.0247 6.99562 17.1809 6.83934C17.3372 6.68306 17.425 6.47109 17.425 6.25008V2.50008C17.425 2.27907 17.3372 2.06711 17.1809 1.91083C17.0247 1.75455 16.8127 1.66675 16.5917 1.66675C16.3707 1.66675 16.1587 1.75455 16.0024 1.91083C15.8462 2.06711 15.7584 2.27907 15.7584 2.50008V3.97508C14.3706 2.64846 12.5673 1.84171 10.6534 1.69119C8.73943 1.54067 6.83221 2.05562 5.25412 3.14899C3.67603 4.24236 2.52386 5.84709 1.99238 7.69191C1.4609 9.53673 1.5827 11.5085 2.3372 13.2739C3.09169 15.0392 4.43261 16.49 6.13327 17.3808C7.83393 18.2716 9.79002 18.5479 11.6709 18.163C13.5518 17.778 15.242 16.7555 16.456 15.2681C17.6699 13.7808 18.3331 11.9199 18.3334 10.0001C18.3334 9.77907 18.2456 9.56711 18.0893 9.41083C17.933 9.25455 17.721 9.16675 17.5 9.16675Z" fill="currentColor"/>
            </svg>
            <span>Save configuration</span>
          </button>
        </form>      
      <?php endif; ?>

    <?php elseif($config == true): ?>
      <h1>Create a backup (step <?php echo $state; ?>)</h1>
      <?php if($state == '0'): ?>
        <p>Create backup for website "<?php echo SITE_NAME; ?>".</p>
        <p>Ceate backup of all files in direcotroy:</p>
        <pre><?php echo SITE_BASE_DIR; ?></pre>
        <a href="<?php echo $url .'?s=1'; ?>" onclick="this.setAttribute('disabled', '')">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M17.5 9.16675C17.279 9.16675 17.067 9.25455 16.9108 9.41083C16.7545 9.56711 16.6667 9.77907 16.6667 10.0001C16.6766 11.5609 16.1419 13.0764 15.1547 14.2855C14.1675 15.4945 12.7896 16.3215 11.2583 16.6239C9.72702 16.9264 8.13817 16.6854 6.76544 15.9424C5.39271 15.1994 4.32203 14.0011 3.73779 12.5536C3.15356 11.1062 3.09235 9.50034 3.56471 8.01264C4.03706 6.52494 5.01342 5.24853 6.32561 4.40323C7.6378 3.55793 9.20369 3.19668 10.7536 3.38167C12.3035 3.56667 13.7403 4.28635 14.8167 5.41675H12.8167C12.5957 5.41675 12.3837 5.50455 12.2274 5.66083C12.0712 5.81711 11.9834 6.02907 11.9834 6.25008C11.9834 6.47109 12.0712 6.68306 12.2274 6.83934C12.3837 6.99562 12.5957 7.08341 12.8167 7.08341H16.5917C16.8127 7.08341 17.0247 6.99562 17.1809 6.83934C17.3372 6.68306 17.425 6.47109 17.425 6.25008V2.50008C17.425 2.27907 17.3372 2.06711 17.1809 1.91083C17.0247 1.75455 16.8127 1.66675 16.5917 1.66675C16.3707 1.66675 16.1587 1.75455 16.0024 1.91083C15.8462 2.06711 15.7584 2.27907 15.7584 2.50008V3.97508C14.3706 2.64846 12.5673 1.84171 10.6534 1.69119C8.73943 1.54067 6.83221 2.05562 5.25412 3.14899C3.67603 4.24236 2.52386 5.84709 1.99238 7.69191C1.4609 9.53673 1.5827 11.5085 2.3372 13.2739C3.09169 15.0392 4.43261 16.49 6.13327 17.3808C7.83393 18.2716 9.79002 18.5479 11.6709 18.163C13.5518 17.778 15.242 16.7555 16.456 15.2681C17.6699 13.7808 18.3331 11.9199 18.3334 10.0001C18.3334 9.77907 18.2456 9.56711 18.0893 9.41083C17.933 9.25455 17.721 9.16675 17.5 9.16675Z" fill="currentColor"/>
          </svg>
          <span>Backup files</span>
        </a>
      
      <?php elseif($state == '1'): ?>
        <?php
        if(SHELL == true) {
          $cmd_data = 'tar --index-file=error_tar -v -c -f '. $file_name .'.tar '. SITE_BASE_DIR .'/';
          $output_data = shell_exec($cmd_data);
          $log_data = file_get_contents('error_tar');
          if($log_data > '') {
            $bkp_data = true;
            $msg_state_data = 'Compressing files done';
          }
          else {
            $bkp_data = false;
            $msg_state_data = 'Error during compressing files with the following command:<br><pre>'. $cmd_data .'</pre>';
          }
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
          <?php echo SITE_HAS_DB == true ? '<p>Go on with backup of database:</p><pre>'. DB_NAME .'</pre>' : '<p>Compress data in a ZIP archive and prepare for download:</p>'; ?>
          <a href="<?php echo SITE_HAS_DB == true ? $url .'?s=2' : $url .'?s=3'; ?>" onclick="this.setAttribute('disabled', '')">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M17.5 9.16675C17.279 9.16675 17.067 9.25455 16.9108 9.41083C16.7545 9.56711 16.6667 9.77907 16.6667 10.0001C16.6766 11.5609 16.1419 13.0764 15.1547 14.2855C14.1675 15.4945 12.7896 16.3215 11.2583 16.6239C9.72702 16.9264 8.13817 16.6854 6.76544 15.9424C5.39271 15.1994 4.32203 14.0011 3.73779 12.5536C3.15356 11.1062 3.09235 9.50034 3.56471 8.01264C4.03706 6.52494 5.01342 5.24853 6.32561 4.40323C7.6378 3.55793 9.20369 3.19668 10.7536 3.38167C12.3035 3.56667 13.7403 4.28635 14.8167 5.41675H12.8167C12.5957 5.41675 12.3837 5.50455 12.2274 5.66083C12.0712 5.81711 11.9834 6.02907 11.9834 6.25008C11.9834 6.47109 12.0712 6.68306 12.2274 6.83934C12.3837 6.99562 12.5957 7.08341 12.8167 7.08341H16.5917C16.8127 7.08341 17.0247 6.99562 17.1809 6.83934C17.3372 6.68306 17.425 6.47109 17.425 6.25008V2.50008C17.425 2.27907 17.3372 2.06711 17.1809 1.91083C17.0247 1.75455 16.8127 1.66675 16.5917 1.66675C16.3707 1.66675 16.1587 1.75455 16.0024 1.91083C15.8462 2.06711 15.7584 2.27907 15.7584 2.50008V3.97508C14.3706 2.64846 12.5673 1.84171 10.6534 1.69119C8.73943 1.54067 6.83221 2.05562 5.25412 3.14899C3.67603 4.24236 2.52386 5.84709 1.99238 7.69191C1.4609 9.53673 1.5827 11.5085 2.3372 13.2739C3.09169 15.0392 4.43261 16.49 6.13327 17.3808C7.83393 18.2716 9.79002 18.5479 11.6709 18.163C13.5518 17.778 15.242 16.7555 16.456 15.2681C17.6699 13.7808 18.3331 11.9199 18.3334 10.0001C18.3334 9.77907 18.2456 9.56711 18.0893 9.41083C17.933 9.25455 17.721 9.16675 17.5 9.16675Z" fill="currentColor"/>
            </svg>
            <span><?php echo SITE_HAS_DB == true ? 'Backup database' : 'Create ZIP archive'; ?></span>
          </a>
        <?php elseif($bkp_data == false): ?>
          <p>Error during backup: <?php echo $msg_state_db; ?></p>
        <?php endif; ?>

      <?php elseif($state == '2'): ?>
        <?php
        if(SHELL == true) {
          $cmd_db = 'mysqldump --log-error=error_mysqldump '. DB_NAME .' > '. DB_NAME .'.sql -u '. DB_USER .' -p\''. DB_PASSWORD .'\'';
          $output_db = shell_exec($cmd_db);
          $log_db = file_get_contents('error_mysqldump');
          if($log_db > '') {
            $bkp_db = false;
            $msg_state_db = 'Error during backup of database:<br><pre>'. $log_db .'</pre>';
          }
          else {
            $bkp_db = true;
            $msg_state_db = 'Backup of database done';
          }
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
          <a href="<?php echo $url .'?s=3'; ?>" onclick="this.setAttribute('disabled', '')">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M17.5 9.16675C17.279 9.16675 17.067 9.25455 16.9108 9.41083C16.7545 9.56711 16.6667 9.77907 16.6667 10.0001C16.6766 11.5609 16.1419 13.0764 15.1547 14.2855C14.1675 15.4945 12.7896 16.3215 11.2583 16.6239C9.72702 16.9264 8.13817 16.6854 6.76544 15.9424C5.39271 15.1994 4.32203 14.0011 3.73779 12.5536C3.15356 11.1062 3.09235 9.50034 3.56471 8.01264C4.03706 6.52494 5.01342 5.24853 6.32561 4.40323C7.6378 3.55793 9.20369 3.19668 10.7536 3.38167C12.3035 3.56667 13.7403 4.28635 14.8167 5.41675H12.8167C12.5957 5.41675 12.3837 5.50455 12.2274 5.66083C12.0712 5.81711 11.9834 6.02907 11.9834 6.25008C11.9834 6.47109 12.0712 6.68306 12.2274 6.83934C12.3837 6.99562 12.5957 7.08341 12.8167 7.08341H16.5917C16.8127 7.08341 17.0247 6.99562 17.1809 6.83934C17.3372 6.68306 17.425 6.47109 17.425 6.25008V2.50008C17.425 2.27907 17.3372 2.06711 17.1809 1.91083C17.0247 1.75455 16.8127 1.66675 16.5917 1.66675C16.3707 1.66675 16.1587 1.75455 16.0024 1.91083C15.8462 2.06711 15.7584 2.27907 15.7584 2.50008V3.97508C14.3706 2.64846 12.5673 1.84171 10.6534 1.69119C8.73943 1.54067 6.83221 2.05562 5.25412 3.14899C3.67603 4.24236 2.52386 5.84709 1.99238 7.69191C1.4609 9.53673 1.5827 11.5085 2.3372 13.2739C3.09169 15.0392 4.43261 16.49 6.13327 17.3808C7.83393 18.2716 9.79002 18.5479 11.6709 18.163C13.5518 17.778 15.242 16.7555 16.456 15.2681C17.6699 13.7808 18.3331 11.9199 18.3334 10.0001C18.3334 9.77907 18.2456 9.56711 18.0893 9.41083C17.933 9.25455 17.721 9.16675 17.5 9.16675Z" fill="currentColor"/>
            </svg>
            <span>Create ZIP archive</span>
          </a>
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
          <a href="<?php echo $url .'?s=4'; ?>" onclick="this.setAttribute('disabled', '')">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M17.5 9.16675C17.279 9.16675 17.067 9.25455 16.9108 9.41083C16.7545 9.56711 16.6667 9.77907 16.6667 10.0001C16.6766 11.5609 16.1419 13.0764 15.1547 14.2855C14.1675 15.4945 12.7896 16.3215 11.2583 16.6239C9.72702 16.9264 8.13817 16.6854 6.76544 15.9424C5.39271 15.1994 4.32203 14.0011 3.73779 12.5536C3.15356 11.1062 3.09235 9.50034 3.56471 8.01264C4.03706 6.52494 5.01342 5.24853 6.32561 4.40323C7.6378 3.55793 9.20369 3.19668 10.7536 3.38167C12.3035 3.56667 13.7403 4.28635 14.8167 5.41675H12.8167C12.5957 5.41675 12.3837 5.50455 12.2274 5.66083C12.0712 5.81711 11.9834 6.02907 11.9834 6.25008C11.9834 6.47109 12.0712 6.68306 12.2274 6.83934C12.3837 6.99562 12.5957 7.08341 12.8167 7.08341H16.5917C16.8127 7.08341 17.0247 6.99562 17.1809 6.83934C17.3372 6.68306 17.425 6.47109 17.425 6.25008V2.50008C17.425 2.27907 17.3372 2.06711 17.1809 1.91083C17.0247 1.75455 16.8127 1.66675 16.5917 1.66675C16.3707 1.66675 16.1587 1.75455 16.0024 1.91083C15.8462 2.06711 15.7584 2.27907 15.7584 2.50008V3.97508C14.3706 2.64846 12.5673 1.84171 10.6534 1.69119C8.73943 1.54067 6.83221 2.05562 5.25412 3.14899C3.67603 4.24236 2.52386 5.84709 1.99238 7.69191C1.4609 9.53673 1.5827 11.5085 2.3372 13.2739C3.09169 15.0392 4.43261 16.49 6.13327 17.3808C7.83393 18.2716 9.79002 18.5479 11.6709 18.163C13.5518 17.778 15.242 16.7555 16.456 15.2681C17.6699 13.7808 18.3331 11.9199 18.3334 10.0001C18.3334 9.77907 18.2456 9.56711 18.0893 9.41083C17.933 9.25455 17.721 9.16675 17.5 9.16675Z" fill="currentColor"/>
            </svg>
            <span>Finish and cleanup</span>
          </a>
        <?php elseif($bkp_zip == false): ?>
          <p>Error during compressing files: <?php echo $msg_state_zip; ?></p>
        <?php endif; ?>

      <?php elseif($state == '4'): ?>
        <?php
        // cleanup created files
        if(unlink($file_name .'.zip')): ?>
          <?php if(unlink($file_name .'.tar')): ?>
            <?php if(SITE_HAS_DB == true && unlink(DB_NAME .'.sql')): ?>
              <p>Files and database deleted from server</p>
              <a href="https://<?php echo SITE_URL; ?>/">Show website <?php echo SITE_URL; ?></a>
            <?php elseif(SITE_HAS_DB == false): ?>
              <p>Files deleted from server</p>
              <a href="https://<?php echo SITE_URL; ?>/">Show website <?php echo SITE_URL; ?></a>
            <?php else: ?>
              <p>Error during deleting backup database, please check manually!</p>
            <?php endif; ?>
          <?php else: ?>
            <p>Error during deleting backup files, please check manually!</p>
          <?php endif; ?>
        <?php else: ?>
          <p>Error during deleting ZIP-File, please check manually!</p>
        <?php endif; ?>
        <?php
        // cleanup log files
        if(file_exists('error_tar')) {
          unlink('error_tar');
        }
        if(file_exists('error_mysqldump')) {
          unlink('error_mysqldump');
        }
        ?>

      <?php endif; ?> 
    <?php endif; ?>
  </div>
</body>
</html>