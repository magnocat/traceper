package server;

public class minisentry {
	
	public static String parseRequests(String input, mySqlInterface mysqlDB)
	{
		char[] input_array = input.toCharArray();
		String imei_no="";
		String command_code="";
		String connecting_session="";
		String serial_no="";
		String data_array="";
		String check_sum="";
		int input_length = input.length();
		String output="";
		int i=0;
		String query;

		for (char c : input_array)	    
		{
			if (i < 3)
			{
				command_code += c; 
			}
			else if ((i >= 3) && (i < 9))
			{
				connecting_session += c;
			}
			else if ((i >= 9) && (i < 14))
			{
				serial_no += c;
			}
			else if ((i >= 14) && (i < (input_length - 3)))
			{
				data_array += c;
			}
			else if  (i < input_length)
			{
				check_sum += c;
			}

			i = i + 1;
		}

		switch (command_code) {
		case "#SA": //Login message
			//echo "Login Message i=$icounter";
			imei_no = data_array;
			query="SELECT Id FROM traceper_users WHERE deviceId ="+imei_no+"LIMIT 1;";
			if (mysqlDB.runQuery(query) > 0)
			{
				output = input;
				/*
				$output[2]='B';
				$output[8]=1;
				$output[13]=((int) $output[13])+1;
				*/
				/*
				socket_write($spawn, $output, strlen ($output)) or die("Could not write
			            output\n");
			            */
			}
			
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
                            $other_settings = '#OC000012000140000+905365175576,            +905394114712      ,,028';
                            socket_write($spawn, $other_settings, strlen ($other_settings)) or die("Could not write
            output\n");
                            echo "2. adim";
			 */
			break;

		case "#SE": //Check Connection
			String checked_imei_no = data_array;
			//echo "Check Connection Message i=$checked_imei_no";
			break;

		case "#SC": //Logout message
			//echo "Logout Message i=$icounter";
			break;

		case "#RD": //position report
			//echo "Position Report i=$icounter";
			char[] report_data=data_array.toCharArray();
			String day_string="";
			String month_string="";
			String year_string="";
			String hour_string="";
			String minute_string="";
			String second_string="";
			String lat_degree_string="";
			String lat_min_string="";
			String long_degree_string="";
			String long_minute_string="";
			String speed_over_ground_string="";
			String coarse_over_ground_string="";
			int latitude_degree = 0;
			int longitude_degree = 0;
			
			int day = 0;
			int month = 0;
			int year = 0;
			int hour = 0;
			int minute = 0;
			int second = 0;
			double latitude = 0.0;
			double longitude = 0.0;
			
			i=0;
			for (char c_2 : report_data)	
			{
				if (i < 2)
				{
					day_string += c_2;
					if (i == 1)
					{
						day = Integer.parseInt(day_string);
					}
				}
				else if ((i >= 2) && (i < 4))
				{
					month_string += c_2;
					if (i == 3)
					{
						month = Integer.parseInt(month_string);						
					}
				}
				else if ((i >= 4) && (i < 6))
				{
					year_string += c_2;
					if (i == 5)
					{						
						year= 2000 + Integer.parseInt(year_string);
						//System.out.print("year="+year);
					}
				}
				else if ((i >= 6) && (i < 8))
				{
					hour_string += c_2;
					if (i == 7)
					{
						hour= Integer.parseInt(hour_string);
					}
				}
				else if ((i >= 8) && (i < 10))
				{
					minute_string += c_2;
					if (i == 9)
					{
						minute= Integer.parseInt(minute_string);
					}
				}
				else if ((i >= 10) && (i < 12))
				{
					second_string += c_2;
					if (i == 11)
					{
						second= Integer.parseInt(second_string);
					}
				}
				else if ((i >= 12) && (i < 14))
				{
					lat_degree_string += c_2;
					if (i == 13)
					{
						latitude_degree = Integer.parseInt(lat_degree_string);
					}
				}
				else if ((i >= 14) && (i < 22))
				{
					if (i == 21)
					{
						float latitude_minute= Float.parseFloat(lat_min_string);
						latitude = latitude_degree + (latitude_minute/60);
						if (c_2 == 'S')
						{
							latitude = (-1)*latitude;
						}
						//System.out.print(latitude);
					}
					else
					{
						lat_min_string += c_2;
					}
				}
				else if ((i >= 22) && (i < 25))
				{
					long_degree_string += c_2;
					if (i == 24)
					{
						longitude_degree = Integer.parseInt(long_degree_string);
					}
				}
				else if ((i >= 25) && (i < 33))
				{
					if (i == 32)
					{
						float longitude_minute= Float.parseFloat(long_minute_string);
						longitude = longitude_degree + (longitude_minute/60);
						if (c_2 == 'W')
						{
							longitude = (-1)*longitude;
						}
					}
					else
					{
						long_minute_string += c_2;
					}
				}
				else if ((i >= 33) && (i < 38))
				{
					speed_over_ground_string += c_2;
					if (i == 37)
					{
						float speed_over_ground= Float.parseFloat(speed_over_ground_string);
					}
				}
				else if ((i >= 38) && (i < 40))
				{
					coarse_over_ground_string += c_2;
					if (i == 39)
					{
						float coarse_over_ground= Float.parseFloat(coarse_over_ground_string);
					}
				}
				i = i + 1;
			}

			//$icounter = 3;
			//System.out.print(latitude);
			imei_no = "355543020162426";
			mysqlDB.updateLocationRecord(latitude, longitude, 0.0, year, month, day, hour, minute, second, imei_no);
			/*TODO: JDBC
			$imei_no = "355543020162426";
			echo $imei_no;
			$altitude = 0;
			$calculatedTime = sprintf('%d-%d-%d %d:%d:%d',$year,$month,$day,$hour,$minute,$second);
			$sql = sprintf('UPDATE traceper_users
					SET
					latitude = %f , '
					.'      longitude = %f , '
					.'      altitude = %f , '
					.'      dataArrivedTime = NOW(), '
					.'    dataCalculatedTime = "%s" '
					.' WHERE '
					.' deviceId = "%s" '
					.' LIMIT 1;',
					$latitude, $longitude, $altitude, $calculatedTime, $imei_no);
			echo $sql;
			$effectedRows = Yii::app()->db->createCommand($sql)->execute();
			echo $effectedRows;
			 */



			break;

		default:
			//echo "Invalid Command";
			break;

		}
		return output;

	}

}
