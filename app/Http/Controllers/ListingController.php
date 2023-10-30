<?php

namespace App\Http\Controllers;

use App\Models\Listing;

class ListingController extends Controller
{
    public function index()
    {
        return view('listings.index');
    }

    public function create()
    {
        return view('listings.create');
    }

    public function show(Listing $listing)
    {
        return view('listings.show', compact('listing'));
    }
    public function savenotesvalue()
    {
       $notes = $_GET['notes'];
       $id = $_GET['id'];
       $result = Listing::where('id',$id)->first();
       if(!empty($result)){
        Listing::where('id',$id)->update(array('notes' => $notes));
       }
    }
}
