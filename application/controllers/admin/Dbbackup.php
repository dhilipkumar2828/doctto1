<?php
/*
*  Controller Name : Dbbackup
*  Location  : application/controllers/admin/Dbbackup.php
*/
class Dbbackup extends CI_Controller{
 function index(){
  // Load the DB utility class
  $this->load->dbutil();
  $prefs = array(
   'format'      => 'zip',             // gzip, zip, txt
   'filename'    => 'backup_'.date('m_d_Y_H_i_s').'.sql',    // File name - NEEDED ONLY WITH ZIP FILES
   'add_drop'    => TRUE,              // Whether to add DROP TABLE statements to backup file
   'add_insert'  => TRUE,              // Whether to add INSERT data to backup file
   'newline'     => "\n"               // Newline character used in backup file
  );
  // Backup your entire database and assign it to a variable
  $backup =$this->dbutil->backup($prefs);

  // Load the file helper and write the file to your server
  $this->load->helper('file');
  write_file('/path/to/'.'dbbackup_'.date('m_d_Y_H_i_s').'.zip', $backup);

  // Load the download helper and send the file to your desktop
  $this->load->helper('download');
  force_download('dbbackup_'.date('m_d_Y_H_i_s').'.zip', $backup);
 }
}
?>