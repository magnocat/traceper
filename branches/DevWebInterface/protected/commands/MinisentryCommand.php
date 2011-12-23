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
		$host = "192.168.1.101";
		$port = 215;

		$icounter=0;
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
		
		 $input = '#RD000001004361412111849153955.6583N03253.2103E000.04500000A00113971Da7LARNU8H40GT20KHU0W000296B15CE086';
/*
		 $input = '#SE00000100454355543020162426015';
		 */

		$input_array = str_split($input);
		$input_length = strlen($input);
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
				echo "Login Message i=$icounter";
				$imei_no=implode('',$data_array);
				$output = $input;
				$output[2]='B';
				$output[8]=1;
				$output[13]=((int) $output[13])+1;
				//echo "$input \n";
				//echo "$output \n";
				socket_write($spawn, $output, strlen ($output)) or die("Could not write
		output\n");

				/*
				 $interval_settings_code='#RC';
				 $interval_settings_session=strval(intval($connecting_session)+1);
				 $interval_settings_serial_no=strval(intval($serial_no)+1);
				 $interval_hour=strval(hour);
				 $interval_minute=strval(minute);
				 $interval_second=strval(second);
				 $interval_settings2=$interval_settings_code.$interval_settings_session.$interval_settings_serial_no.$interval_hour.$interval_minute.$interval_second;
				 */
				$interval_settings = '#RC00001200009000030006';
				socket_write($spawn, $interval_settings, strlen ($interval_settings)) or die("Could not write
		output\n");
				echo "1. adim";
				$other_settings = '#OC000012000140000+905365175576,+905394114712,,028';
				socket_write($spawn, $other_settings, strlen ($other_settings)) or die("Could not write
		output\n");
				echo "2. adim";
				break;

			case "#SE": //Check Connection
				$checked_imei_no=implode('',$data_array);
				echo "Check Connection Message i=$checked_imei_no";
				break;

			case "#SC": //Logout message
				echo "Logout Message i=$icounter";
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

				$icounter = 3;
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
				echo "Invalid Command";
		}
		/*
		 $icounter = $icounter + 1;
		 }
		 */

		//echo "$command_code";
		//echo implode('',$command_code);
		//echo implode(' ',$connecting_session);
		//echo implode(' ',$serial_no);
		//echo implode(' ',$data_array);
		//echo implode(' ',$check_sum);




		// clean up input string
		//$input = trim($input);
		// reverse client input and send back
		//$output = strrev($input) . "\n";
		//socket_write($spawn, $output, strlen ($output)) or die("Could not write
		//output\n");
		// close sockets
		/*
		 socket_close($spawn);
		 socket_close($socket);
		 */
	}
}