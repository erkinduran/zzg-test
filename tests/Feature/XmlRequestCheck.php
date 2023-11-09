<?php

namespace Tests\Feature;

use App\Actions\GetData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class XmlRequestCheck extends TestCase
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
		$xmlObject = simplexml_load_string ( $xmlString );
		$data      = json_decode ( json_encode ( $xmlObject ), TRUE );
		$this->assertSame ( '1', $data[ 'product' ][ 0 ][ 'id' ] );
		$this->assertSame ( 'Product_1', $data[ 'product' ][ 0 ][ 'name' ] );
	}
}
