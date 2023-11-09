<?php

namespace App\Console\Commands;

use App\Actions\GetData;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GetXmlData extends Command
{
	protected $signature   = 'get-xml-data';
	protected $description = 'Get XML data';
	
	public function handle () : void
	{
		$now       = now ()->format ( 'Y-m-d H:i:s' );
		$xmlString = ( new GetData( $now ) )->run ();
		
		if ( $xmlString )
		{
			try
			{
				Storage::put ( 'XmlData/' . $now . '.xml', $xmlString );
			}
			catch ( \Exception $e )
			{
				Log::warning ( 'Storage XML put error: ' . $e->getMessage () );
			}
			$xmlObject = simplexml_load_string ( $xmlString );
			unset( $xmlString );
			try
			{
				Storage::put ( 'JsonData/' . $now . '.json', json_encode ( $xmlObject ) );
			}
			catch ( \Exception $e )
			{
				Log::warning ( 'Storage JSON put error: ' . $e->getMessage () );
			}
			$data       = json_decode ( json_encode ( $xmlObject ), TRUE );
			$insertdata = [];
			try
			{
				foreach ( $data[ 'product' ] as $datum )
				{
					$datum[ 'product_id' ] = $datum[ 'id' ];
					unset( $datum[ 'id' ] );
					$insertdata[] = $datum;
					unset( $datum );
				}
			}
			catch ( \Exception $e )
			{
				Log::warning ( 'Data error: ' . $e->getMessage () );
			}
			$uniqueBy = [ 'product_id' ];
			$update   = [
				'name',
				'description',
				'price',
				'quantity',
			];
			$chunk    = array_chunk ( $insertdata, 10 );
			foreach ( $chunk as $c ) Product::upsert ( $c, $uniqueBy, $update );
			Product::where ( 'updated_at', '<', $now )
				->delete ();
		}
	}
}
