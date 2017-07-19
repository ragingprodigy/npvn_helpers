<?php
declare(strict_types=1);

/**
 * Created by: dapo <o.omonayajo@gmail.com>
 * Created on: 19/07/2017, 12:51 PM
 */

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\SelectableDevice;
use Illuminate\Http\JsonResponse;

/**
 * Class WarehouseController
 * @package App\Http\Controllers
 */
class WarehouseController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function devices(): JsonResponse
    {
        return $this->jsonResponse(SelectableDevice::all());
    }

    /**
     * @param string $imei
     * @return JsonResponse
     */
    public function checkIMEI(string $imei)
    {
        return $this->jsonResponse(
            [ 'exists' => Device::where('imei', $imei)->exists() ]
        );
    }

    /**
     * @param $identifier
     * @return JsonResponse
     */
    public function getDevice($identifier): JsonResponse
    {
        $device = Device::where('uuid', $identifier);
        if ($device === null) {
            $device = Device::where('imei', $identifier)->orWhere('serial', $identifier);
        }

        if ($device === null) {
            return $this->notFound();
        }

        return $this->jsonResponse($device);
    }
}