<?php

namespace Tests\Feature;

use App\Actions\GetData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileCheck extends TestCase
{
	/**
	 * A basic feature test example.
	 */
	public function test_example () : void
	{
		$url = env ( 'XML_SOURCE' );
		Http::fake ( [
			$url => Http::response ( '<?xml version="1.0" encoding="UTF-8"?>
		<products>
		    <product>
		      <id>1</id>
		      <name>Product_1</name>
		      <description>This is a sample description for product 1</description>
		      <price>250</price>
		      <quantity>18</quantity>
		      <photo_url>http://example.com/images/product_1.jpg</photo_url>
		    </product>
		    <product>
		      <id>2</id>
		      <name>Product_2</name>
		      <description>This is a sample description for product 2</description>
		      <price>102</price>
		      <quantity>3</quantity>
		      <photo_url>http://example.com/images/product_2.jpg</photo_url>
		    </product>
    </products>', 200 ),
		] );
		$now       = now ()->format ( 'Y-m-d H:i:s' );
		$xmlString = ( new GetData( $now ) )->run ();
		if ( $xmlString )
		{
			Storage::put ( 'XmlData/' . $now . '.xml', $xmlString );
			$xmlObject = simplexml_load_string ( $xmlString );
			unset( $xmlString );
			Storage::put ( 'JsonData/' . $now . '.json', json_encode ( $xmlObject ) );
		}
		Storage::disk ( 'local' )
			->assertExists ( 'XmlData/' . $now . '.xml' );
		Storage::disk ( 'local' )
			->assertExists ( 'JsonData/' . $now . '.json' );
		Storage::delete ( 'XmlData/' . $now . '.xml' );
		Storage::delete ( 'JsonData/' . $now . '.json' );
	}
}
