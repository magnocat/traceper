<?php
/**
 * MinisentryCommand class file.
 *
 */
class MinisentryCommand extends CConsoleCommand
{
	/**
	 * Execute the action.
	 * @param array command line parameters specific for this command
	 */
	public function run($args)
	{
		// set some variables
		//$host = "127.0.0.1";
		$host = "192.168.1.2";
		$port = 4122;

		//$icounter=0;
		/*
		 // don't timeout!
		 set_time_limit(0);
		 // create socket
		 $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create
		 socket\n");
		 // bind socket to port
		 $result = socket_bind($socket, $host, $port) or die("Could not bind to
		 socket\n");

		 $icounter=0;

		 while ($icounter < 3)
		 {
		 // start listening for connections
		 $result = socket_listen($socket, 3) or die("Could not set up socket
		 listener\n");
		 // accept incoming connections
		 // spawn another socket to handle communication
		 $spawn = socket_accept($socket) or die("Could not accept incoming
		 connection\n");

		 // read client input
		 $input = socket_read($spawn, 1024) or die("Could not read input\n");

		 $handle = fopen("d:\\resource.txt", "a+");
		 fwrite($handle,$input);

		 /*
		 $imei_ary = array();
		 foreach (str_split($input) as $chr)
		 $imei_ary[] = sprintf("%c", ord($chr));
		 */

		/*
		$input = '#RD000001004361412111849153955.6583N03253.2103E000.04500000A00113971Da7LARNU8H40GT20KHU0W000296B15CE086';
		echo $input;
		*/
		
		// Create the server
		try {
			$server = new Socket();
		} catch (SocketException $e) {
			echo "Can't start server, " . $e->getMessage();
		}

		// Start the listen loop
		try {
			$server->listen($host, $port);
		} catch (SocketException $e) {
			echo "Can't listen, " . $e->getMessage();
		}
		/*
		 $input = '#SE00000100454355543020162426015';
		 */
		
	}
	
}




class Socket {
	/**
	 * Domain type to use when creating the socket
	 * @var int
	 */
	public $domain = AF_INET;
	/**
	 * The stream type to use when creating the socket
	 * @var int
	 */
	public $type = SOCK_STREAM;
	/**
	 * The protocol to use when creating the socket
	 * @var int
	 */
	public $protocol = SOL_TCP;

	/**
	 * Stores a reference to the created socket
	 * @var Resource
	 */
	private $link = null;
	/**
	 * Array of connected children
	 * @var array
	 */
	private $threads = array();
	/**
	 * Bool which determines if the socket is listening or not
	 * @var boolean
	 */
	private $listening = false;

	/**
	 * Creates a new Socket.
	 *
	 * @param array $args
	 * @param int $args[domain] AF_INET|AF_INET6|AF_UNIX
	 * @param int $args[type] SOCK_STREAM|SOCK_DGRAM|SOCK_SEQPACKET|SOCK_RAW|SOCK_UDM
	 * @param int $args[protocol] SOL_TCP|SOL_UDP
	 * @return Socket
	 */
	public function __construct(array $args = null) {
		// Default socket info
		$defaults = array(
            "domain" => AF_INET,
            "type" => SOCK_STREAM,
            "protocol" => SOL_TCP
		);
		if($args == null) {
			$args = array();
		}
		// Merge $args in to $defaults
		$args = array_merge($defaults, $args);

		// Store these values for later, just in case
		$this->domain = $args['domain'];
		$this->type = $args['type'];
		$this->protocol = $args['protocol'];

		//if(($this->link = socket_create($this->domain, $this->type, $this->protocol)) === false) {
		if(($this->link = socket_create(AF_INET, SOCK_STREAM, 0)) === false) {
			throw new SocketException("Unable to create Socket. PHP said, " . $this->getLastError(), socket_last_error());
		}
	}
	/**
	 * At destruct, close the socket
	 */
	public function __destruct() {
		@$this->close();
	}
	
	
private function parseRequests($input)
	{
		echo $input;
		$input_array = str_split($input);
		$input_length = strlen($input);
		$output = NULL;
		$i=0;
		foreach (str_split($input) as $chr)
		{
			if ($i < 3)
			{
				$command_code[] = sprintf('%c', ord($input_array[$i]));
			}
			else if (($i >= 3) && ($i < 9))
			{
				$connecting_session[] = sprintf("%c", ord($input_array[$i]));
			}
			else if (($i >= 9) && ($i < 14))
			{
				$serial_no[] = sprintf("%c", ord($input_array[$i]));
			}
			else if (($i >= 14) && ($i < ($input_length - 3)))
			{
				$data_array[] = sprintf("%c", ord($input_array[$i]));
			}
			else if  ($i < $input_length)
			{
				$check_sum[] = sprintf("%c", ord($input_array[$i]));
			}

			$i = $i + 1;
		}

		switch (implode('',$command_code)) {
			case "#SA": //Login message
				//echo "Login Message i=$icounter";
				$imei_no=implode('',$data_array);
				$sql = sprintf("SELECT Id from traceper_users WHERE deviceId = '%s' LIMIT 1;",
								  	$imei_no);
				
				$result = Yii::app()->db->createCommand($sql)->queryScalar();
				
				if ($result != false)
				{
					$output = $input;
					$output[2]='B';
					$output[8]=1;
					$output[13]=((int) $output[13])+1;
				}
				/*
				socket_write($spawn, $output, strlen ($output)) or die("Could not write
		output\n");
		*/

				/*
				 $interval_settings_code='#RC';
				 $interval_settings_session=strval(intval($connecting_session)+1);
				 $interval_settings_serial_no=strval(intval($serial_no)+1);
				 $interval_hour=strval(hour);
				 $interval_minute=strval(minute);
				 $interval_second=strval(second);
				 $interval_settings2=$interval_settings_code.$interval_settings_session.$interval_settings_serial_no.$interval_hour.$interval_minute.$interval_second;
				 */
				//$interval_settings = '#RC00001200009000030006';
				/*
				socket_write($spawn, $interval_settings, strlen ($interval_settings)) or die("Could not write
		output\n");
		
				echo "1. adim";
				$other_settings = '#OC000012000140000+905365175576,+905394114712,,028';
				socket_write($spawn, $other_settings, strlen ($other_settings)) or die("Could not write
		output\n");
				echo "2. adim";
				*/
				break;

			case "#SE": //Check Connection
				$checked_imei_no=implode('',$data_array);
				echo "Check Connection Message i=$checked_imei_no";
				break;

			case "#SC": //Logout message
				//echo "Logout Message i=$icounter";
				break;

			case "#RD": //position report
				//echo "Position Report i=$icounter";
				$report_data=implode('',$data_array);
				$i=0;
				foreach (str_split($report_data) as $chr)
				{
					if ($i < 2)
					{
						$day_string[] = sprintf('%c', ord($report_data[$i]));
						if ($i == 1)
						{
							$temp=implode('',$day_string);
							$day= intval($temp);
						}
					}
					else if (($i >= 2) && ($i < 4))
					{
						$month_string[] = sprintf('%c', ord($report_data[$i]));
						if ($i == 3)
						{
							$temp=implode('',$month_string);
							$month= intval($temp);
						}
					}
					else if (($i >= 4) && ($i < 6))
					{
						$year_string[] = sprintf('%c', ord($report_data[$i]));
						if ($i == 5)
						{
							$temp=implode('',$year_string);
							$year= 2000 + intval($temp);
						}
					}
					else if (($i >= 6) && ($i < 8))
					{
						$hour_string[] = sprintf('%c', ord($report_data[$i]));
						if ($i == 7)
						{
							$temp=implode('',$hour_string);
							$hour= intval($temp);
						}
					}
					else if (($i >= 8) && ($i < 10))
					{
						$minute_string[] = sprintf('%c', ord($report_data[$i]));
						if ($i == 9)
						{
							$temp=implode('',$minute_string);
							$minute= intval($temp);
						}
					}
					else if (($i >= 10) && ($i < 12))
					{
						$second_string[] = sprintf('%c', ord($report_data[$i]));
						if ($i == 11)
						{
							$temp=implode('',$second_string);
							$second= intval($temp);
						}
					}
					else if (($i >= 12) && ($i < 14))
					{
						$lat_degree_string[] = sprintf('%c', ord($report_data[$i]));
						if ($i == 13)
						{
							$temp=implode('',$lat_degree_string);
							$latitude_degree= intval($temp);
						}
					}
					else if (($i >= 14) && ($i < 22))
					{
						if ($i == 21)
						{
							$temp=implode('',$lat_min_string);
							$latitude_minute= floatval($temp);
							$latitude = $latitude_degree + ($latitude_minute/60);
							if ($report_data[$i] == 'S')
							{
								$latitude = (-1)*$latitude;
							}
						}
						else
						{
							$lat_min_string[] = sprintf('%c', ord($report_data[$i]));
						}
					}
					else if (($i >= 22) && ($i < 25))
					{
						$long_degree_string[] = sprintf('%c', ord($report_data[$i]));
						if ($i == 24)
						{
							$temp=implode('',$long_degree_string);
							$longitude_degree= intval($temp);
						}
					}
					else if (($i >= 25) && ($i < 33))
					{
						if ($i == 32)
						{
							$temp=implode('',$long_minute_string);
							$longitude_minute= floatval($temp);
							$longitude = $longitude_degree + ($longitude_minute/60);
							if ($report_data[$i] == 'W')
							{
								$longitude = (-1)*$longitude;
							}
						}
						else
						{
							$long_minute_string[] = sprintf('%c', ord($report_data[$i]));
						}
					}
					else if (($i >= 33) && ($i < 38))
					{
						$speed_over_ground_string[] = sprintf('%c', ord($report_data[$i]));
						if ($i == 37)
						{
							$temp=implode('',$speed_over_ground_string);
							$speed_over_ground= floatval($temp);
						}
					}
					else if (($i >= 38) && ($i < 40))
					{
						$coarse_over_ground_string[] = sprintf('%c', ord($report_data[$i]));
						if ($i == 39)
						{
							$temp=implode('',$coarse_over_ground_string);
							$coarse_over_ground= intval($temp);
						}
					}
					$i = $i + 1;
				}

				//$icounter = 3;
				echo "$day.$month.$year $hour.$minute.$second $latitude $longitude";

				$imei_no = "355543020162426";
				echo $imei_no;
				$altitude = 0;
				$calculatedTime = sprintf('%d-%d-%d %d:%d:%d',$year,$month,$day,$hour,$minute,$second);
				$sql = sprintf('UPDATE traceper_users
								SET
								  	latitude = %f , '
								  	.'	longitude = %f , '
								  	.'	altitude = %f ,	'
								  	.'	dataArrivedTime = NOW(), '
								  	.'    dataCalculatedTime = "%s" '
								  	.' WHERE '
								  	.' deviceId = "%s" '
								  	.' LIMIT 1;',
								  	$latitude, $longitude, $altitude, $calculatedTime, $imei_no);
								  	echo $sql;
								  	$effectedRows = Yii::app()->db->createCommand($sql)->execute();
								  	echo $effectedRows;



								  	break;

			default:
				//echo "Invalid Command";
				break;
				
		}
		return $output;
		 		
	}
	
	
	/**
	 * After calling this method, the Socket will start to listen on the port
	 * specified or the default port.
	 *
	 * @see Socket::$port
	 * @param string $host
	 * @param int $port
	 */
	public function listen($host = "localhost", $port = 9999) {
		if($this->link === null) {
			throw new SocketException("No socket available, cannot listen");
		}

		// Set a valid port to listen on
		if($port <= 1024) {
			$port = 9999;
		}

		socket_set_nonblock($this->link);

		// Bind to the host/port
		if(!socket_bind($this->link, $host, $port)) {
			throw new SocketException("Cannot bind to $host:$port. PHP said, " . $this->getLastError($this->link));
		}
		// Try to listen
		if(!socket_listen($this->link)) {
			throw new SocketException("Cannot listen on $host:$port. PHP said, " . $this->getLastError($this->link));
		}

		echo "Listening on $host:$port\n";

		$this->listening = true;

		// Start main loop
		while($this->listening) {
			// Accept new connections
			if(($thread = @socket_accept($this->link)) !== false) {
				$child = new ChildSocket($thread);
				array_push($this->threads, $child);

				echo "Accepted child, " . $child->getInfo() . "\n";
			}

			// Loop through children, listen for read
			foreach($this->threads as $index => $child) {
				try {
					$msg = $child->read();
					$response = $this->parseRequests($msg);
				} catch (SocketCloseException $e) {
					// Child socket closed unexpectedly, remove from active
					// threads

					echo "Terminating child at $index\n";
					unset($this->threads[$index]);
					continue;
				}
				//$msg = trim($msg);

				if($response !== NULL && !empty($response)) {
					/*
					switch($command) {
						case "end":
							$this->killAll();
							die("Kill message received\n");
							break;
					}
					echo "Received message: $msg\n";
					*/

					$this->send($child, $response);
				}
			}
		}
	}
	/**
	 * Closes the listening socket
	 *
	 * @return void
	 */
	public function close() {
		$this->listening = false;

		// @see http://www.php.net/manual/en/function.socket-close.php#66810
		$socketOptions = array('l_onoff' => 1, 'l_linger' => 0);
		socket_set_option($this->link, SOL_SOCKET, SO_LINGER, $socketOptions);

		socket_close($this->link);
	}
	/**
	 * Sends a message to a child. Set child as "all" to send to all children.
	 *
	 * @param string $message
	 * @return void
	 */
	public function send($child, $message) {
		if($this->link === null) {
			throw new SocketException("Socket not connected");
		}
		if(empty($message)) {
			return;
		}
		if(is_string($child) && strcasecmp($child, "all") === 0) {
			foreach($this->threads as $thread) {
				$thread->write($message . "\n");
			}
		}
		else {
			$child->write($message . "\n");
		}

	}
	/**
	 * Terminates all active child connections
	 *
	 * @return void;
	 */
	public function killAll() {
		foreach($this->threads as $child) {
			$child->close();
		}
		$this->listening = false;
		$this->close();
	}
	/**
	 * Returns the last error on the socket specified. If no socket is specified
	 * the last error that occured is returned.
	 *
	 * @param Resource $socket
	 * @return string
	 */
	public function getLastError($socket = null) {
		if(empty($socket)) {
			return socket_strerror(socket_last_error());
		}
		else {
			return socket_strerror(socket_last_error($socket));
		}
	}
}

class ChildSocket {
	/**
	 * Stores a reference to the created socket
	 * @var Resource
	 */
	private $link = null;

	/**
	 * Connection reset by peer error number
	 * @var int
	 */
	const PEER_RESET = 104;

	public function __construct($thread = null) {
		if($thread === null || !is_resource($thread)) {
			throw new SocketException("No socket available, cannot create Child");
		}
		$this->link = $thread;
	}
	/**
	 * Sends a message to the socket
	 *
	 * @param string $message
	 * @return boolean
	 */
	public function write($message) {
		if($this->link == null) {
			throw new SocketException("Socket not connected");
		}
		if(empty($message)) {
			return false;
		}
		$wrote = socket_write($this->link, $message, strlen($message));

		if($wrote === false) {
			throw new SocketException("Failed to write to socket.\n PHP said: " . $this->getLastError());
		}

		return (strlen($message) == $wrote);
	}
	/**
	 * Reads from the Socket, returns false if there is nothing to read
	 *
	 * @param int $bufferSize
	 * @return mixed
	 */
	public function read($bufferSize = 1024) {
		if($this->link == null) {
			throw new SocketException("Socket not connected");
		}
		if(empty($bufferSize)) {
			$bufferSize = 1024;
		}

		$buffer = false;
		do {
			$in = "";
			$in = @socket_read($this->link, $bufferSize);

			// Connection reset
			if($this->getLastErrorNo() == self::PEER_RESET) {
				throw new SocketCloseException("Connection reset by peer");
				break;
			}

			if(!empty($in)) {
				if($buffer === false) {
					$buffer = "";
				}
				$buffer .= $in;
			}
			// Socket error, close
			else if($in === '') {
				throw new SocketCloseException("Socket closed unexpectedly");
				break;
			}
		} while(!empty($in));

		/**
		 * Sometimes when the connection is closed unexpectedly, like
		 * if someone presses the exit button in the window, a series of
		 * question marks (?) are sent as binary characters to the server.
		 * Converting them to hex, returns 04 then to ASCII returns (?); So,
		 * if the returned buffer is not false, and the resulting text is only
		 * made up of ?'s then kill the buffer.
		 */

		if($buffer !== false) {
			$tmp = bin2hex($buffer);
			if(($cnt = preg_match_all("/(04)/", $tmp, $matches)) > 0 && $cnt*2 == strlen($tmp)) {
				$buffer = false;
			}
		}

		return $buffer;
	}
	/**
	 * At destruct, close the socket
	 */
	public function __destruct() {
		@$this->close();
	}
	/**
	 * Closes the socket
	 *
	 * @return void
	 */
	public function close() {
		// @see http://www.php.net/manual/en/function.socket-close.php#66810
		$socketOptions = array('l_onoff' => 1, 'l_linger' => 0);
		@socket_set_option($this->link, SOL_SOCKET, SO_LINGER, $socketOptions);
		@socket_close($this->link);
	}
	/**
	 * Returns a string which contains the connection info
	 *
	 * @return string
	 */
	public function getInfo() {
		$IP = "0.0.0.0";
		$port = 0;

		if($this->link == null) {
			throw new SocketException("Socket not connected");
		}

		socket_getsockname($this->link, $IP, $port);

		return "IP: $IP:$port";
	}
	/**
	 * Returns the last error number
	 *
	 * @return int
	 */
	public function getLastErrorNo() {
		return socket_last_error($this->link);
	}
	/**
	 * Returns the last error this socket has received
	 *
	 * @return string
	 */
	public function getLastError() {
		return socket_strerror(socket_last_error($this->link));
	}
}

class SocketException extends Exception {
	private $logPath = "/var/log/php/socket.log";
	public function __construct($message = "", $code = 0) {
		if(file_exists("/var/log")) {
			if(!file_exists(dirname($this->logPath))) {
				$success = @mkdir(dirname($this->logPath));

				if(!$success) {
					$message .= "\n";
					$message .= "Unable to log error, check log path.\n";
				}
			}

			@error_log($message, 3, $this->logPath);
		}

		parent::__construct($message, $code);
	}
}
class SocketCloseException extends SocketException {
	public function __construct($message = "", $code = 0) {
		parent::__construct($message, $code);
	}
}
