<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class GetData
{
	public function __construct ( public string $now ) { }
	
	public function run () : ?string
	{
		$url      = env ( 'XML_SOURCE' );
		$response = Http::get ( $url );
		if ( $response->successful () )
		{
			return $response->body ();
		}
		Log::warning ( 'XML source status error: ' . $response->status () );
		
		return NULL;
	}
}
