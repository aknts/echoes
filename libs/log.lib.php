<?php
// Start Log class
class Log {

// Setup path where logs file located.
const path=rootDir.'echoes/logs/';

// Function that checks if today's log file exists and if not creates it.
 public function checkDayLogExists() {
	if (!file_exists(self::path.date("dmY").'.log')) {
		fopen(self::path.date("dmY").'.log', "w");
	}
 }

// Function that writes to today's log file a message and some data.
// The message is being marked with the [M] and data with [D].
 public function writeLog($message,$data=NULL) {
	$file = fopen(self::path.date("dmY").'.log', "a");
	fwrite($file,date("[D M j G:i:s Y]").'[M]:'.$message."\r\n");
	if ($data != NULL) {
		fwrite($file,date("[D M j G:i:s Y]").'[D]:'.$data."\r\n");
	}
 } 

// Function that returns an array of all the logs files that exists inside the log directory.
 public function listLogFiles() {
	$files = array_slice(scandir(self::path),2);
	return $files;
 }

// Function that gets all the text from the log and returns it a dump for further use.
 public function printLog ($file) {
	$text = file_get_contents(self::path.$file);
	return $text;
 }

// Function that deletes a log file.
// Filename is supplied as a variable.
 public function deleteLog ($file) {
	unlink(self::path.$file);
 }

}
?>
