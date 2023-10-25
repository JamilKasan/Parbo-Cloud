<?php
function convertStrToDateTime($string) : string
{
    $dateArray = str_split($string);
    $year = $dateArray[0] . $dateArray[1] . $dateArray[2] . $dateArray[3];
    $month = $dateArray[4] . $dateArray[5];
    $day = $dateArray[6] . $dateArray[7];
    $hour = $dateArray[8] . $dateArray[9];
    $minute = $dateArray[10] . $dateArray[11];
    $second = $dateArray[12] . $dateArray[13];
    $dateTime = $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute . ':' . $second;
    $time_input = strtotime($dateTime);
    $date_input = date('Y-m-d H:i:s', $time_input);
    return $date_input;
}

function convertCsvToArray($file): array
{
    $fileArray = [];
    while(!feof($file))
    {

        try {
            foreach (fgetcsv($file) as $item)
            {
                $details = explode(',', $item);
                $fileArray[] = ['name' => $details[3] . ' ' . $details[4], 'datetime' => convertStrToDateTime($details[0])];
            }
        }
        catch (\Exception $e)
        {

        }
    }
    fclose($file);
    return $fileArray;
}

function listEmployees($data) :array
{
    $skip = [];
    foreach ($data as $datum) {
        if (!in_array($datum['name'], $skip)) {
            $skip[] = $datum['name'];
        }
    }
    return $skip;
}
function calculateEmployeeTimeSheet($employee, $timeArray)
{
    $dateArray = groupDateTime($timeArray, $employee);
    $timeAttendace = determineTimeAttendance($dateArray);
    return $timeAttendace;
}

function getDates($employee, $timeArray) : array
{
    $dates = [];
    foreach ($timeArray as $time)
    {
        if (!in_array(date('Y-m-d', strtotime($time)), $dates))
        {
            $dates[] = date('Y-m-d', strtotime($time));
        }
    }
    return $dates;
}

function groupEmployeeData($employee, $data) : array
{
    $timeArray = [];
    foreach ($data as $datum)
    {
        if ($employee == $datum['name'])
        {
            $timeArray[] = $datum['datetime'];
        }
    }
    calculateEmployeeTimeSheet($employee, $timeArray);
    return  calculateEmployeeTimeSheet($employee, $timeArray);
}



function groupDateTime($timeArray, $employee) : array
{
    $dates = getDates($employee, $timeArray);
    $dateArray = [];
    foreach ($dates as $date) {
        foreach ($timeArray as $time) {
            if (str_contains($time, $date)) {
                $dateArray[$date][] = $time;
            }
        }
    }
    return $dateArray;
}

function determineTimeAttendance($dateArray) :array
{
    $timeAttendance = [];

    foreach ($dateArray as $key =>  $array)
    {
        $earliest = strtotime($array[0]);
        foreach ($array as $item)
        {
            $time = strtotime($item);
            if ($time < $earliest and $time != $earliest )
            {
                $earliest =  $time;
            }
        }
        $timeAttendance[$key]['in'] = date('Y-m-d H:i:s', $earliest);
    }

    foreach ($dateArray as $key =>  $array)
    {
        $latest = strtotime($array[0]);
        foreach ($array as $item)
        {
            $time = strtotime($item);
            if ($time > $latest and $time != $latest and $time != $earliest)
            {
                $latest =  $time;
            }
        }
        $timeAttendance[$key]['out'] =   date('Y-m-d H:i:s', $latest);
        if ($timeAttendance[$key]['out'] == $timeAttendance[$key]['in'])
        {
            unset($timeAttendance[$key]['out']);
        }
    }

    return $timeAttendance;

}

function logTimeAttendance($file) :void
{
    $data = convertCsvToArray($file);
    logEmployees($data);
    foreach (listEmployees($data) as $employee)
    {
        $timeAttendance = groupEmployeeData($employee, $data);
        foreach ($timeAttendance as $key => $item)
        {
            if (!\App\Models\TimeAttendance::query()->where('name', $employee)->where('date', $key)->exists())
            {
                \App\Models\TimeAttendance::query()->where('name', $employee)->where('date', $key)->delete();
                $employee2 = \App\Models\Employee::query()->where('name', $employee)->first();
                $timeAttendance = new \App\Models\TimeAttendance();
                $timeAttendance->employee_id = $employee2->_id;
                $timeAttendance->name = $employee;
                $timeAttendance->date = $key;
                $timeAttendance->in = date('H:i', strtotime($item['in']));
                if (isset($item['out']))
                {
                    $timeAttendance->out = date('H:i', strtotime($item['out']));
                }
                $timeAttendance->save();
            }
        }
    }

}

function logEmployees($data) :bool
{
    $employees = listEmployees($data);
    foreach (listEmployees($data) as $employee)
    {
        if (!\App\Models\Employee::query()->where('name', $employee)->exists())
        {
            $employee2 = new \App\Models\Employee();
            $employee2->name = $employee;
            $employee2->save();
        }
    }
    return true;
}

