<?php
declare(strict_types=1);

/**
 * Created by: dapo <o.omonayajo@gmail.com>
 * Created on: 19/07/2017, 12:51 PM
 */

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\SelectableDevice;
use App\Models\Unbundling;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function enroll(Request $request): JsonResponse
    {
        $this->validate($request, [
            'imei' => 'required|string',
            'misdn' => 'required|string|min:11|max:11'
        ]);

        $device = Device::byIMEI($request->get('imei'));
        if (!$device || !$device->unbundled) {
            return $this->jsonResponse(['message' => 'Device not Unbundled yet'], 400);
        }

        $device->misdn = $request->get('misdn');
        $device->enrolled_by = $this->getUserId();
        $device->date_enrolled = Carbon::now();
        $device->enrolled = true;

        $device->save();

        return $this->jsonResponse($this->fetchDevice($device->uuid));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function registerDevice(Request $request): JsonResponse
    {
        $this->validate($request, [
            'device'    => 'required|int',
            'imei'      => 'required|string', // TODO: Add length validation
            'serial'    => 'required', // TODO: Add length validation
        ]);

        $payload = $request->only(['device', 'imei', 'serial']);
        $payload['available_device_id'] = $payload['device'];
        $payload['uuid'] = Uuid::uuid4()->toString();
        $payload['added_by'] = $this->getUserId();

        unset($payload['device']);

        $device = Device::create($payload);

        if (!$device->exists()) {
            throw new \Exception('Unable to create device record');
        }

        return $this->jsonResponse($device);
    }

    /**
     * @param string $uuid
     * @return JsonResponse
     */
    public function unbundle(string $uuid): JsonResponse
    {
        $device = Device::with('unbundling')->whereUuid($uuid)->first();

        if ($device->unbundling === null) {
            throw new NotFoundHttpException('Device has not been checked');
        }

        $device->unbundling->certified_by = $this->getUserId();
        $device->unbundling->save();

        $device->unbundled = true;
        $device->save();

        return $this->jsonResponse($this->fetchDevice($device->uuid));
    }

    /**
     * @param string $uuid
     * @param string $field
     * @param int $value
     * @return JsonResponse
     */
    public function unbundling(string $uuid, string $field, int $value): JsonResponse
    {
        /** @var Device $device */
        $device = Device::with('unbundling')->whereUuid($uuid)->first();

        if ($device->unbundling === null) {
            $device->unbundling()->create([
                $field => $value
            ]);
        } else {
            $device->unbundling->{$field} = $value;
            $device->unbundling->save();
        }

        return $this->jsonResponse($this->fetchDevice($device->uuid));
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
    public function getDevice(string $identifier): JsonResponse
    {
        /** @var Device $device */
        try {
            $device = $this->fetchDevice($identifier);
        } catch (\Exception $e) {
            return $this->jsonResponse(['message' => $e->getMessage()], 404);
        }

        return $this->jsonResponse($device);
    }

    private function fetchDevice(string $identifier)
    {
        $device = Device::where('uuid', $identifier)->first();

        if ($device === null) {
            $device = Device::where('imei', $identifier)->orWhere('serial', $identifier)->first();
            if (!$device) {
                throw new \Exception('Device not found!');
            }
        }

        $device->load(['creator', 'updater', 'deleter', 'device', 'unbundling', 'unbundling.user', 'enroller']);

        return $device;
    }
}