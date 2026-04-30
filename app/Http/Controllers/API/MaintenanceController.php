<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use Illuminate\Http\Request;
class MaintenanceController extends Controller{

public function index()  {

        $maintenances = Maintenance::query()
            ->whereIn('platform', ['mobile'])
            ->get()
            ->keyBy('platform');

         return response()->json($maintenances, 200);
    
    }





}
