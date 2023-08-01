<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/sync', function () {
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
});


Route::post('/sync/check-request', function () {
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
});

Route::post('/sync/check-status', function () {
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
});

Route::post('/sync/set-status', function () {
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
        DB::table('errors')->insert(['info' => 'Error in sync: ' . $e->getMessage() , 'record' => json_encode($_REQUEST), 'date' => date('Y-m-d H:i:s')]);
        return 'ERROR';
    }
});
