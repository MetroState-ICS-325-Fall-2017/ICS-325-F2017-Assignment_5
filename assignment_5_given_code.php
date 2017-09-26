<?php

/* Assignment 5 Given Code */

// The following is the printToTerminal() method code you MUST use.
// Place it in your Report class defintion
/* public function printToTerminal()
{
    echo "Report title: {$this->getTitle()}\n";
    echo "Report generated at: {$this->getGenerationTimeStamp()}\n";
    echo "Report description: {$this->getReportDescription()}\n";
    echo "Report parameters:\n{$this->getParameterString()}\n\n";
    echo "Report result:\n{$this->getReportResultString()}\n";
}
*/

// to get the date in the correct format, use the following code:
/* date(DATE_RFC2822); */

// An example of filter_var:
// http://php.net/manual/en/function.filter-var.php
// This will take the input value from $input_value and make sure it
// is within the range 1 through 5.
// The value returned to $filter_input_value is the filtered value.
// In this case, it will be the same asn $input_value if $input_value
// is within the correct range.  If not, it will return false.
/* $filtered_input_value = filter_var($input_value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 5, 'max_range' => 10]]); */


/* The rest code interacts with the classes you need to write. */
// So, you don't need to modify or change anything after this code.
// However, you may want to comment out all the required grading code
// until you have written the class that it needs.

// set the default timezone to UTC (Central Time)
date_default_timezone_set('America/Chicago');

/* OUTPUT:
* Report title: Sum Calculator
* Report generated at: Tue, 13 Sep 2016 14:45:09 -0500
* Report description: Sums the given integers.
* Report parameters:
* INTEGERS: 2, 4, 6
*
* Report result:
* 12
*/
$my_sum_report = new SumReport([2, 4, 6]);
$my_sum_report->printToTerminal();
echo "\n-----------------------------------------------\n";

/* OUTPUT:
* Report title: Minimum and Maximum Finder
* Report generated at: Tue, 13 Sep 2016 14:45:09 -0500
* Report description: Find the minimum and maximum of the given integers.
* Report parameters:
* INTEGERS: 3, 7, 8, 9, 27
*
* Report result:
* MINIMUM: 3
* MAXIMUM: 27
*/
$my_min_max_report = new MinMaxReport([8, 3, 9, 27, 7]);
$my_min_max_report->printToTerminal();
echo "\n-----------------------------------------------\n";

/* OUTPUT:
* Report title: Number Range Generation
* Report generated at: Tue, 13 Sep 2016 14:45:09 -0500
* Report description: Prints every integer from START to END separated by a space.
* Report parameters:
* START: 4
* END: 21
*
* Report result:
* 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21
*/
$my_range_report = new RangeReport(4, 21);
$my_range_report->printToTerminal();
echo "\n-----------------------------------------------\n";

// OUTPUT: Sorry, there was a problem running RangeReport: Bad start value.
try {
    $my_range_report = new RangeReport(-2, 9);
    $my_range_report->printToTerminal();
} catch (Exception $e) {
    echo "Sorry, there was a problem running RangeReport: " . $e->getMessage() . "\n";
}

// OUTPUT: Sorry, there was a problem running RangeReport: Bad end value.
try {
    $my_range_report = new RangeReport(4, 102);
    $my_range_report->printToTerminal();
} catch (Exception $e) {
    echo "Sorry, there was a problem running RangeReport: " . $e->getMessage() . "\n";
}

// OUTPUT: Sorry, there was a problem running RangeReport: Bad start value.
try {
    $my_range_report = new RangeReport('a string', 102);
    $my_range_report->printToTerminal();
} catch (Exception $e) {
    echo "Sorry, there was a problem running RangeReport: " . $e->getMessage() . "\n";
}
