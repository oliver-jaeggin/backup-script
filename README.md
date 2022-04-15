# Backup Script - Execute and download a backpu from an applikation directly in the browser
## Features
- Lightweight script
- Easy setup
- Creates a archive of all files of an application
- Creates a database dump
- Download all data as a ZIP file to store localy

## Installation
1. Downlaod the repository.
2. Upload the ZIP file into the root folder of your application and extract the archive.

> The script works aswell if you upload it in a subfolder of, but it's recommended to upload directly in your root folder.

3. Start the setup using your `your-domain.tld/backup-script`. If you uploaded it in a subfolder, so complete the path in the url with the subfolders as example `your-domain.tld/my/subfolders/backup-script`.
4. Setup the configuration which is explained in the next chapter

## Configuration
First time you call the backup script it will be created a configuration file. The script detects if it's possible use shell commands or if your server doesn't allow shell access.

> Note the performance is much better using shell commands. If your webhosting offers access please activate `shell_exec` in your php.ini before starting the configuration.

For simply applications you just have to provide a name which will be used to name the archive files. If your application uses a database you need to fillup the database credentials.

## Create a backup
If the configurations is done you will be forwarded to create the first backup. To start again just call the backup script like `your-domain.tld/backup-script` and follow the steps.

1. Step - Create a .tar archive of all files of your application
2. Step - Create a dump of the database if your application uses a database
3. Step - Create a ZIP archive of this two files
4. Step - Provide a download link and a button to delete the files from the server