<?php

use App\Models\CollectionCenter;
use App\Models\LocalGovernment;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

function geocodeAddress(string $address)
{
    $response = [
        'results' => []
    ];

    try {
        $response = \GoogleMaps::load('geocoding')
           ->setParam([
               'address' => $address,
               'components'  => [
                   'country' => 'NG',
               ]
           ])
           ->get('results.geometry.location');
    } catch (Exception $e) {
        echo $e->getMessage() . '\n';
    }

    return $response;
}

Artisan::command('get-geocode {address}', function ($address) {
    $response = \GoogleMaps::load('geocoding')
        ->setParam([
            'address' => $address,
            'components'  => [
                'country' => 'NG'
            ]
        ])
        ->get('results.geometry');

    $this->comment(print_r($response, true));
});

Artisan::command('load-lgas', function () {
    Excel::selectSheetsByIndex(2)->load('npvn_location_data.xlsx', function (LaravelExcelReader $reader) {
        $reader->noHeading();
        $rows = $reader->get();

        foreach ($rows as $key => $location) {
            if ($location[0] !== null) {
//                LocalGovernment::create(['name' => $location[0]]);
                $lga = LocalGovernment::whereName($location[0])->first();
                if ($lga === null) {
                    $this->comment(sprintf('Unable to load: %s', $location[0]));
                    continue;
                }

                $lga->state = strtoupper($location[1]);
                $lga->save();
            }
        }

        $this->comment('Done');
    });
});

/**
 * @param array $coordinates
 *
 * @return bool
 */
function validateRegion(array $coordinates): bool
{
    [ $lat, $lng ] = $coordinates;
    return $lat >= 4.2698571 && $lat <= 13.8856449 && $lng >= 2.676932 && $lng <= 14.677841;
}

Artisan::command('lga-geocoding-task', function () {
    $lgas = LocalGovernment::get();
    foreach ($lgas as $lga) {
        if ($lga->geocoded_address !== null && validateRegion(explode(', ', $lga->geocoded_address))) {
            continue;
        }

        // Attempt to Geocode Addresses
        $this->comment(sprintf('Doing lookup for %s', $lga->name));
        $lookup = geocodeAddress($lga->name);

        if (count($lookup['results']) > 0) {
            $lga->geocoded_address = implode(', ', $lookup['results'][0]['geometry']['location']);
            $lga->save();
        }
    }
});

Artisan::command('code {name} {state}', function ($name, $state) {
    $lga = LocalGovernment::whereName($name)->where('geocoded_address', null)->first();
    if ($lga !== null) {
        // Attempt to Geocode Addresses
        $this->comment(sprintf('Doing lookup for %s, %s', $name, $state));
        $lookup = geocodeAddress(sprintf('%s, %s', $name, $state));

        if (count($lookup['results']) > 0) {
            $lga->geocoded_address = implode(', ', $lookup['results'][0]['geometry']['location']);
            $this->comment(sprintf('Coded %s, %s as %s', $name, $state, $lga->geocoded_address));
            $lga->save();
        }
    }
});

/**
 * @param array $fromLocation
 * @param array $toLocation
 *
 * @return float
 */
function distance(array $fromLocation, array $toLocation)
{
    [ $lat1, $lon1 ] = $fromLocation;
    [ $lat2, $lon2 ] = $toLocation;

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1))
        * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;

    return ($miles * 1.609344);
}

Artisan::command('location-compute', function () {
    $lgas = LocalGovernment::where('geocoded_address', '<>', null)->limit(4)->get();
    $centers = CollectionCenter::where('geocoded_address', '<>', null)->get();

    foreach ($lgas as $lga) {
        $distances = [];
        foreach ($centers as $center) {
            $distances[$center->address] = distance(
                explode(', ', $lga->geocoded_address),
                explode(', ', $center->geocoded_address)
            );
        }

        asort($distances);

        $this->comment($lga->name . ' => ' . print_r($distances, true));
    }
});

Artisan::command('pickup-locations', function () {
    $locations = LocalGovernment::get();
    $centers = CollectionCenter::with('lga')->get();

    $data = [];

    $locations->each(function ($location) use ($centers, &$data) {
        $distances = [];

        $filteredCenters = $centers->filter(function ($ct) use ($location) {
            return $ct->state === $location->state;
        })->all();

        $this->comment(sprintf('%s has %d centeres in %s state', $location->name, count($filteredCenters),
                               $location->state));

        foreach ($filteredCenters as $center) {
            $consideredLocation = $center->geocoded_address ?? $center->lga->geocoded_address ?? '';

            if (!empty($consideredLocation)) {
                $distances[$center->address] = distance(
                    explode(', ', $location->geocoded_address),
                    explode(
                        ', ',
                        $consideredLocation
                    )
                );
            }
        }

        asort($distances);
        $picked = array_slice($distances, 0, 5, true);
        $ct = [];

        foreach ($picked as $loc => $dist) {
            $ct[] = [
                'name' => $loc,
                'dist' => $dist
            ];
        }

        $data[] = [
            'LOCAL GOVERNMENT'  => $location->name,
            'CENTER 1'          => $ct[0]['name']??'',
            'DISTANCE 1'        => $ct[0]['dist']??'',
            'CENTER 2'          => $ct[1]['name']??'',
            'DISTANCE 2'        => $ct[1]['dist']??'',
            'CENTER 3'          => $ct[2]['name']??'',
            'DISTANCE 3'        => $ct[2]['dist']??'',
            'CENTER 4'          => $ct[3]['name']??'',
            'DISTANCE 4'        => $ct[3]['dist']??'',
            'CENTER 5'          => $ct[4]['name']??'',
            'DISTANCE 5'        => $ct[4]['dist']??'',
        ];
    });

    Excel::create('pickup-locations-state-bound', function ($excel) use ($data) {
        $excel->sheet('All LGAs', function ($sheet) use ($data) {
            $sheet->with($data);
            $sheet->cells('A1:K1', function ($cells) {
                $cells->setFontWeight('bold');
            });
        });
    })->save('xlsx');
});

Artisan::command('get-nearest {lga}', function ($lga) {
    $lga = LocalGovernment::whereName(strtoupper($lga))->first();

    if ($lga) {
        $centers = CollectionCenter::where('geocoded_address', '<>', null)->get();
        $distances = [];

        foreach ($centers as $center) {
            $distances[$center->address] = distance(
                explode(', ', $lga->geocoded_address),
                explode(', ', $center->geocoded_address)
            );
        }

        asort($distances);

        $this->comment($lga->name . ' => ' . print_r(array_slice($distances, 0, 5, true), true));
    }
});

Artisan::command('location-cleanup', function () {
    $centers = CollectionCenter::where('geocoded_address', null)->get();

    foreach ($centers as $center) {
        // Attempt to Geocode Addresses
        $this->comment(sprintf('Doing lookup for %s', $center->address));
        $lookup = geocodeAddress($center->address);
        $this->comment(sprintf('Lookup Complete!'));

        if (count($lookup['results']) > 0) {
            $center->geocoded_address = implode(', ', $lookup['results'][0]['geometry']['location']);
            $center->save();

            $this->comment('Coordinates updated');
        }
    }
});

Artisan::command('device-location-task', function () {
    Excel::selectSheetsByIndex(0)->load('npvn_location_data.xlsx', function (LaravelExcelReader $reader) {
        $reader->noHeading();
        $rows = $reader->get();

        foreach ($rows as $key => $location) {
            if ($location[0] !== null) {
                $ct = CollectionCenter::whereAddress(strtoupper($location[0]))->first();

                if ($ct && $ct->geocoded_address !== null) {
                    continue;
                }

                // Attempt to Geocode Addresses
                $this->comment(sprintf('Doing lookup for %s', $location[0]));
                $lookup = geocodeAddress($location[0]);

                $this->comment(sprintf('Lookup Complete!'));

                if (count($lookup['results']) > 0) {
                    $center = $ct ?? CollectionCenter::create([
                        'address' => strtoupper($location[0])
                    ]);

                    $center->geocoded_address = implode(', ', $lookup['results'][0]['geometry']['location']);
                    $center->save();
                } else {
                    $center = $ct ?? CollectionCenter::create(['address' => strtoupper($location[0])]);
                }

                if (!$center->lga_id) {
                    $this->info('Setting LGA ID');
                    // Find LGA
                    $lga = LocalGovernment::whereName(strtoupper($location[1]))->first();

                    if ($lga) {
                        $center->lga_id = $lga->id;
                        $center->save();
                    }
                }
            }
        }

        $this->comment('Done!');
    });
})->describe('Determine Device Pickup Locations');
