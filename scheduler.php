 <?php
	function ShiftId($FROM) {
		$dbServername = "cse-cmpsc431";
		$dbusername = "kkt5170";
		$dbpassword = "947145565";
		$dbName = "kkt5170";
		$conn = mysqli_connect ( $dbServername, $dbusername, $dbpassword, $dbName );
		// get shiftId
		$query = ("SELECT SHIFTS_ID FROM SHIFTS WHERE `FROM` = '$FROM'");
		$result = mysqli_query ( $conn, $query );
		// gets shift id
		if (! $result) {
			echo " Query Failed. Unable to retrieve shiftId: " . mysqli_errno ( $conn ) . " - " . mysqli_error ( $conn ) . "\n";
		} else {
			while ( $row = mysqli_fetch_array ( $result ) ) {
				$SHIFTS_ID = $row ["SHIFTS_ID"];
			}
			echo "shiftId = " . $SHIFTS_ID . "\n\n";
			return $SHIFTS_ID;
		}
	}
	function dayoffchecker($empid, $date) {
		$dbServername = "cse-cmpsc431";
		$dbusername = "kkt5170";
		$dbpassword = "947145565";
		$dbName = "kkt5170";
		$conn = mysqli_connect ( $dbServername, $dbusername, $dbpassword, $dbName );
		$names = array ();
		// check if employee is already scheduled
		$query = ("SELECT empid FROM daysoffrequests WHERE `date` = '$date'");
		$result = mysqli_query ( $conn, $query );
		// echo "\ndayOff.empId QUERY IS:\n" . $query . "\n\n";
		if (! $result) {
			echo "Query Failed. Unable to retrieve employee list: " . mysqli_errno ( $conn ) . " - " . mysqli_error ( $conn ) . "\n";
		} else {
			// echo "\$names ARRAY IS:\n";
			while ( $row = mysqli_fetch_array ( $result ) ) {
				// echo $row ["empId"] . "\n";
				array_push ( $names, $row ["empid"] );
			}
			// echo "END OF ARAY \n";
			if (in_array ( $empid, $names )) {
				echo "Employee requested day off. Cannot schedule.\n";
				return FALSE;
			} else {
				echo "Employee did not request day off. Can schedule\n";
				return TRUE;
			}
		}
	}
	function checkemptype($emptype, $empid) {
		$dbServername = "cse-cmpsc431";
		$dbusername = "kkt5170";
		$dbpassword = "947145565";
		$dbName = "kkt5170";
		$conn = mysqli_connect ( $dbServername, $dbusername, $dbpassword, $dbName );
		$emptypecheck = "";

		$query = ("SELECT emptype FROM EMPLOYEES WHERE empid = '$empid'");
		$result = mysqli_query ( $conn, $query );
		if (! $result) {
			echo "Query failed. " . mysqli_errno ( $conn ) . " wah " . mysqli_error ( $conn ) . "\n";
		} else {
			while ( $row = mysqli_fetch_array ( $result ) ) {
				$emptypecheck = $row ["emptype"];
			}
			echo "\nemptype is" . $emptypecheck . "\n";
		}
		if ($emptype === $emptypecheck) {
			echo "empid" . $empid . "is" . $emptype . "\n";
			return TRUE;
		} else {
			echo "empid" . $empid . "is wrong";
			return FALSE;
		}
	}
	// check if people need to be scheduled
	function scheduleCount($date, $SHIFTS_ID, $departmentName, $emptype) {
		$dbServername = "cse-cmpsc431";
		$dbusername = "kkt5170";
		$dbpassword = "947145565";
		$dbName = "kkt5170";
		$conn = mysqli_connect ( $dbServername, $dbusername, $dbpassword, $dbName );
		$total = 0;

		$query = ("SELECT totalpeople FROM ECOUNT, SHIFTS WHERE ECOUNT.SHIFTS_ID = SHIFTS.SHIFTS_ID AND `date` = '$date' AND SHIFTS.SHIFTS_ID = '$SHIFTS_ID' AND departmentName = '$departmentName' AND emptype = '$emptype'");
		$result = mysqli_query ( $conn, $query );
		if (! $result) {
			echo "Unable to retrieve totalpeople from ECOUNT\r\nQuery Failed: " . mysqli_error ( $conn );
		} else {
			while ( $row = mysqli_fetch_array ( $result ) ) {
				$total = $row ["totalpeople"];
			}
			if ($total  > 0) {
				echo "Finally Successful\n";
				return TRUE;
			} else {
				echo "Enough People\n";
				return FALSE;			
			}
		}
	}
	function ScheduleId() {
		$dbServername = "cse-cmpsc431";
		$dbusername = "kkt5170";
		$dbpassword = "947145565";
		$dbName = "kkt5170";
		$conn = mysqli_connect ( $dbServername, $dbusername, $dbpassword, $dbName );
		$query = ("SELECT SCHEDULE_ID FROM SCHEDULE");
		$result = mysqli_query ( $conn, $query );
		if (! $result) {
			echo "Query Failed. Unable to retrieve scheduleId: " . mysqli_errno ( $conn ) . " - " . mysqli_error ( $conn ) . "\n";
		} else {
			while ( $row = mysqli_fetch_array ( $result ) ) {
				$SCHEDULE_ID = $row ["SCHEDULE_ID"];
			}
			echo "\nscheduleId = " . $SCHEDULE_ID . "\n";
		}
		return $SCHEDULE_ID;
	}
	function scheduleChecker($empid, $date, $SHIFTS_ID) {
		$dbServername = "cse-cmpsc431";
		$dbusername = "kkt5170";
		$dbpassword = "947145565";
		$dbName = "kkt5170";
		$conn = mysqli_connect ( $dbServername, $dbusername, $dbpassword, $dbName );
		$query = ("SELECT EMPLOYEES.empid FROM EMPLOYEES, SCHEDULE, SHIFTS WHERE EMPLOYEES.empid = SCHEDULE.empid AND `date` = '$date'");
		$result = mysqli_query ( $conn, $query );
		$schedulepeople = array ();
		echo "\nEMPLOYEE.empid QUERY IS:\n" . $query . "\n";
		// create a list of employees
		if (! $result) {
			echo "Unable to retrieve first and last name Query Failed: " . mysqli_errno ( $conn ) . " - " . mysqli_error ( $conn ) . "\n";
		} else {
			echo "\nEMPLOYEE ARRAY IS:\n";
			while ( $row = mysqli_fetch_array ( $result ) ) {
				array_push ( $schedulepeople, $row ["empid"] );
				echo $row ["empid"] . "\n";
			}
			echo "END OF ARRAY \n\n";
			// checks if empid is in array names
			if (in_array ( $empid, $schedulepeople )) {
				echo "EMPLOYEE ALREADY WORKING\n";
				return FALSE;
			}
		}
		if ($SHIFTS_ID == "1" || $SHIFTS_ID == "2") {
			$prevShift = "";
			$query = ("SELECT SHIFTS_ID FROM SCHEDULE WHERE STR_TO_DATE(`date`, '%m/%d/%Y') = DATE_SUB(STR_TO_DATE('$date', '%m/%d/%Y'), INTERVAL 1 DAY) AND empid = '$empid'");
			$result = mysqli_query ( $conn, $query );
			// echo "\nprev shiftId QUERY IS:\n" . $query . "\n\n";
			if (! $result) {
				echo "Query Failed. Unable to retrieve day previous shiftId" . mysqli_errno ( $conn ) . " - " . mysqli_error ( $conn ) . "\n";
			} else {
				while ( $row = mysqli_fetch_array ( $result ) ) {
					$prevShift = $row ["SHIFTS_ID"];
				}
				if ($prevShift == "5") {
					echo "Employee is working 11pm on previous day. Cannot schedule for 8am shift.\n";
					return FALSE;
				} else {
					echo "Employee is not working 11pm on previous day. Can schedule for 8am shift.\n";
				}
			}
		}
		// check if employee is working at 8am
		if ($SHIFTS_ID == "5" || $SHIFTS_ID == "4") {
			$nextShift = "";
			$query = ("SELECT SHIFTS_ID FROM SCHEDULE WHERE STR_TO_DATE(`date`, '%m/%d/%Y') = DATE_ADD(STR_TO_DATE('$date', '%m/%d/%Y'), INTERVAL 1 DAY) AND empid = '$empid'");
			$result = mysqli_query ( $conn, $query );
			// echo "\nafter shiftId QUERY IS:\n" . $query . "\n\n";
			if (! $result) {
				echo "Query Failed. Unable to retrieve day" . mysqli_errno ( $conn ) . " - " . mysqli_error ( $conn ) . "\n";
			} else {
				while ( $row = mysqli_fetch_array ( $result ) ) {
					$nextShift = $row ["SHIFTS_ID"];
				}
				if ($nextShift == "1" || $nextShift == "2") {
					echo "Employee is working 8am on the next day. Cannot schedule for 11pm shift.\n";
					return FALSE;
				} else {
					echo "Employee is not working 8am on the next day. Can schedule for 11pm shift.\n";
				}
			}
		}
		return TRUE;
	}
	function requestDayOff($empid, $date) {
		$dbServername = "cse-cmpsc431";
		$dbusername = "kkt5170";
		$dbpassword = "947145565";
		$dbName = "kkt5170";
		$conn = mysqli_connect ( $dbServername, $dbusername, $dbpassword, $dbName );
		if (! scheduleChecker ( $empid, $date, "0" )) {
			echo "Day off Application Succesfully";
		} else {
			$query = ("INSERT INTO Dayoff (empid, `date`) VALUES ('$empid', '$date')");
		}
		$result = mysqli_query ( $conn, $query );
		if (! result) {
			echo "Query failed. Unable to request for dayoff" . mysqli_errno ( $conn ) . " - " . mysqli_error ( $conn ) . "\n";
		} else {
			echo "Successfully request for day for this person empid" . $empid . "on" . $date . ".\n";
		}
	}
	function autoscheduler($emptype, $dept, $date, $SHIFTS_ID) {
		$dbServername = "cse-cmpsc431";
		$dbusername = "kkt5170";
		$dbpassword = "947145565";
		$dbName = "kkt5170";
		$conn = mysqli_connect ( $dbServername, $dbusername, $dbpassword, $dbName );
		$possibleEMP = array ();
		if (scheduleCount ( $date, $SHIFTS_ID, $dept, $emptype )) {
			$query = ("SELECT EMPLOYEES.empid FROM EMPLOYEES WHERE EMPLOYEES.emptype = '$emptype'");
			$result = mysqli_query ( $conn, $query );
			if (! $result) {
				echo "Query Failed. Unable to retrieve scheduleId: " . mysqli_errno ( $conn ) . " - " . mysqli_error ( $conn ) . "\n";
			} else {
				while ( $row = mysqli_fetch_array ( $result ) ) {
					array_push ( $possibleEMP, $row ["empid"] );
				}
				for($i = 0; $i < sizeof ( $possibleEMP ); $i ++) {
					if (schedule ( $emptype, $dept, $date, $possibleEMP [$i], $SHIFTS_ID )) {
						break;
					}
				}
			}
		}
	}
	function schedule($emptype, $dept, $date, $empid, $SHIFTS_ID) {
		$dbServername = "cse-cmpsc431";
		$dbusername = "kkt5170";
		$dbpassword = "947145565";
		$dbName = "kkt5170";
		$conn = mysqli_connect ( $dbServername, $dbusername, $dbpassword, $dbName );
		$SCHEDULE_ID = (ScheduleId () + 1);
		
		if (dayoffchecker ( $empid, $date )) {
			if (checkemptype ( $emptype, $empid )) {
				if (scheduleCount ( $date, $SHIFTS_ID, $dept, $emptype )) {
					if (scheduleChecker ( $empid, $date, $SHIFTS_ID )) {
						$query = ("INSERT INTO SCHEDULE (SCHEDULE_ID, empid, date, SHIFTS_ID, dept) VALUES ('$SCHEDULE_ID', '$empid', '$date', '$SHIFTS_ID', '$dept')");

						$result = mysqli_query ( $conn, $query );
						if (! $result) {
							echo " Query Failed. Unable to schedule employee: " . mysqli_errno ( $conn ) . " - " . mysqli_error ( $conn ) . "\n";
						} else {
							echo " Succesfully scheduled empId: " . $empid . " from dept: " . $dept . " on: " . $date . " at: " . $SHIFTS_ID . "\n";

							$query = ("UPDATE ECOUNT set totalpeople = totalpeople - 1 WHERE ECOUNT.departmentname = '$dept' AND ECOUNT.date = '$date' AND ECOUNT.SHIFTS_ID = '$SHIFTS_ID' AND emptype = '$emptype'");
							$result = mysqli_query ( $conn, $query );
							if (! $result) {
								echo " Query Failed. Unable to update count: " . mysqli_errno ( $conn ) . " - " . mysqli_error ( $conn ) . "\n";
							}
							echo "\n" . $query . "\n\n";
							return TRUE;
						}
					}
				}
			}
		}
		return FALSE;
	}
	function unschedule($emptype, $dept, $date, $empid, $SHIFTS_ID) {
		$dbServername = "cse-cmpsc431";
		$dbusername = "kkt5170";
		$dbpassword = "947145565";
		$dbName = "kkt5170";
		$conn = mysqli_connect ( $dbServername, $dbusername, $dbpassword, $dbName );
		$SCHEDULE_ID = (ScheduleId () + 1);
		if (scheduleChecker ( $empid, $date, $SHIFTS_ID )) {
			$query = ("DELETE FROM SCHEDULE WHERE dept = '$dept' AND `date` = '$date' AND empid = '$empid' AND schedule_Id = '$SCHEDULE_ID'");
			$result = mysqli_query ( $conn, $query );
			if (! $result) {
				echo " Query Failed. Unable to unschedule employee: " . mysqli_errno ( $conn ) . " - " . mysqli_error ( $conn ) . "\n";
			} else {
				echo " empid: " . $empid . " from dept: " . $dept . " has been unscheduled from: " . $date . " shift: " . $SHIFTS_ID . "\n";
				$query = ("UPDATE ECOUNT set totalpeople = totalpeople + 1 WHERE ECOUNT.departmentname = '$dept' AND ECOUNT.date = '$date', AND ECOUNT.SHIFTS_ID = '$SHIFTS_ID' AND emptype = 'emptype'");
				$result = mysqli_query ( $conn, $query );
				if (! $result) {
					echo " Query Failed. Unable to schedule employee: " . mysqli_errno ( $conn ) . " - " . mysqli_error ( $conn ) . "\n";
				}
			}
		} else {
			echo "Employee is not scheduled\n";
		}
	}
	// disconnect database
	function closeDatebase() {
		$conn = mysqli_connect ( $dbServername, $dbusername, $dbpassword, $dbName );
		mysqli_close ( $conn );
	}

	// SHIFTSID("07AM");
	// SHIFTSID("03PM");
	// scheduleCount ( "11/03/2018", "07AM", "EMERGENCY", "LPN" );
	// unschedule("LPN", "EMERGENCY", "9/12/2018", "941663", "07AM");
	// conectedConnected Successfully
	// scheduleId = 434
	// shiftId = 2
	// UNSCHEDULE SPECIFIC PERSON SUCCESSFULLY
	// schedule("LPN", "EMERGENCY", "9/12/2018", "941663", "07AM");
	/*
	 * scheduleId = 434
	 * shiftId = 2
	 * emptype isLPN
	 * empid941663isLPN
	 * Finally Successful Succesfully scheduled empId: 941663 from dept: EMERGENCY on: 9/12/2018 at: 07AM
	 */

autoscheduler ( "RN", "EMERGENCY", "12/24/2020", "3" );
