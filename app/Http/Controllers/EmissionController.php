<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\EmissionServiceFactory;
use App\Enums\EmissionType;
use App\Constants\ResponseMessage;
use App\Constants\HttpCode;
use App\DataTransferObjects\EmissionRequestDTO;

class EmissionController extends Controller
{
    protected EmissionServiceFactory $factory;

    public function __construct()
    {
        $this->factory = new EmissionServiceFactory();
    }

    public function calculate(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => ['required', 'string', Rule::in(EmissionType::values())],
                'parameters' => 'required|array',
            ]);

            $dto = EmissionRequestDTO::fromRequest($validated);
            $service = $this->factory->make($dto->getType());

            $result = $service->calculate([
                'type' => $dto->getType(),
                'parameters' => $dto->getParameters(),
            ]);

            return response()->json($result, $result['success'] ? HttpCode::OK : $result['status']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => ResponseMessage::VALIDATION_FAILED,
                'errors' => $e->errors()
            ], HttpCode::UNPROCESSABLE_ENTITY);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], HttpCode::BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json([
                'message' => ResponseMessage::INTERNAL_SERVER_ERROR,
                'error' => $e->getMessage()
            ], HttpCode::INTERNAL_SERVER_ERROR);
        }
    }
}
