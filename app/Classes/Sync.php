<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class Sync
{
    public function sync()
    {
        try {
            if (isset($_REQUEST['table']) && isset($_REQUEST['record']))
            {

                $table = $_REQUEST['table'];
                $record = $_REQUEST['record'];
                if (DB::table($table)->where('ID', $record['ID'])->exists())
                {
                    DB::table($table)->where('ID', $record['ID'])->update($record);
                    return 'OK';
                }
                else
                {
                    DB::table($table)->insert($record);
                    return 'OK';
                }

            }
            else
            {
                return 'ERROR';
            }

        }
        catch (Exception $e)
        {
            DB::table('errors')->insert(['info' => 'Error in sync: ' . $e->getMessage() , 'record' => json_encode($_REQUEST)]);
            return 'ERROR';
        }
    }
    public function checkStatus()
    {
        if (DB::table('sync_status')->where('running', '1')->exists())
        {
            return 'true';
        }
        else
        {
            return 'false';
        }
    }
    public function setStatus()
    {
        if ($_REQUEST['status'] == 1)
        {
            if (!DB::table('sync_status')->where('request', '1')->exists())
            {
                DB::table('sync_requests')->where('request', '1')->delete();
                DB::table('sync_status')->insert(['running' => '1']);
                return 'true';
            }
            else
            {
                return 'false';
            }
        }
        else
        {
            DB::table('sync_status')->where('running', '1')->delete();
            return 'true';
        }


    }
    public function checkRequest()
    {
        if (DB::table('sync_requests')->where('request', '1')->exists())
        {

            return 'true';
        }
        else
        {
            return 'false';
        }
    }
}
