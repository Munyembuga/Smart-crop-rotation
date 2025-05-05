<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    public function addFarm(Request $request){
        $incomingFields =  $request->validate([
           'description' => 'required',
           'plot_number' => 'required',
           'location' => 'required'
        ]);
        $incomingFields['user_id'] = auth()->id();
        Farm::create($incomingFields);
        
        return redirect('/farm');
    }
    
}
