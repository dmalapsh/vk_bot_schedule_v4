<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DtpController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
	public function index(Request $request){
		$coords = explode(",", $request->bbox);
		$dtps = \App\Dtp::whereBetween("latitude", [$coords[0], $coords[2]])
			->whereBetween("longitude", [$coords[1], $coords[3]])
			->get();

		$response = [];
		foreach($dtps as $dtp){
			$img = $this->getUrl($dtp->videos_url);
			$response[] = [
				"type" => "Feature",
				"id"    => $dtp->id,
				"geometry" => [
					"type" => "Point",
					"coordinates" => [
						$dtp->latitude,
						$dtp->longitude
					]
				],
				"properties" => [
					"clusterCaption" => $dtp->title,
					"balloonContentHeader" => $dtp->title,
					"hintContent" => $dtp->title,
					"balloonContentBody" => $dtp->description . "<br><img width='250' src='$img'/>" . "<br> <a target='_blank' href='/dtp/$dtp->id'>Перейти на страницу ДТП</a>",
				]
			];
		}
		return response()->json($response)->setCallback($request->callback);
	}

	public function getUrl($videos){
		$videos = explode(",", $videos);
		$video = $videos[0];
		if(str_contains($video, "youtube.com")){
			$id = parse_url($video, PHP_URL_PATH);
			$id = str_replace("/embed/", "", $id);
			return "https://img.youtube.com/vi/$id/mqdefault.jpg";
		} else {
			return "https://storage.hstock.org/products/1201/f74dcd53-fd48-4678-b349-61e1044dc440-800.png";
		}
	}

	public function show(Request $request){
		$dtp = \App\Dtp::find($request->id);
		return view('dtp', compact("dtp"));
	}
}
