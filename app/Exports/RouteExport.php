<?php

namespace App\Exports;

use App\Models\BranchConnectivity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Session;
use Helper;

class RouteExport implements FromCollection, WithHeadings,ShouldQueue
{
    /**
    * @return \Illuminate\Support\Collection
    */   
    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();
        $branches = Location::all();
        $connected_hubs = BranchConnectivity::all();

        $graph = [];
        foreach ($connected_hubs as $hub) {
            $location = $hub->efpl_hub;
            $neighbors = explode(',', $hub->direct_connectivity);
            $graph[$location] = $neighbors;
        }

        $locations = [];
        foreach ($connected_hubs as $hub) {
            $location = $hub->efpl_hub;
            // $neighbors = explode(',', $hub->direct_connectivity);
            $locations[$location] = $location;
        }


        foreach ($locations as $startkey => $startingLocation) {
            foreach ($locations as $endkey => $endingLocation) {
                if ($startingLocation !== $endingLocation) {

                     // Initialize visited and route arrays
                     $visited = array_fill_keys(array_keys($graph), false);
    
                     $route = [];
                     // Find routes using DFS
                     $routes = $this->findRoutes($graph, $startingLocation, $endingLocation, $visited, $route);
     
                     $start_branch = DB::table('locations')->where('id', $startingLocation)->first();
                     $end_branch = DB::table('locations')->where('id', $endingLocation)->first();
     
                     // Add the routes to the response string
                     // $routeData .= "<h3>Routes from $start_branch->name to $end_branch->name:</h3><br/>";
         
                     if (empty($routes)) {
                         // $routeData .= "<p>No route available.</p>";
                     } else {
                         foreach ($routes as $route) {
                             $branch_name = [];
                            
                             foreach($route as $key => $r){
                             $getbranch = DB::table('locations')->where('id', $r)->first();
                             $branch_name[]= $getbranch->name;
                             }
                
                $arr[] = [
                    'from'=> $start_branch->name,
                    'to'=> $end_branch->name,
                    'route'=> implode('  >  ', $branch_name),

                ];
            }
        }                 
        return collect($arr);
    }
    
    public function headings(): array
    {
        return [
            'from',
            'to',
            'route',
            
        ];
    }
}
