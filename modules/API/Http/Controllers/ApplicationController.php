<?php

namespace Juzaweb\API\Http\Controllers;

use Illuminate\Http\Request;
use Juzaweb\Applications\Models\Application;
use Juzaweb\CMS\Http\Controllers\ApiController;

class ApplicationController extends ApiController
{

    
    /**
     *  @OA\SecurityScheme(
     *    securityScheme="token",
     *    in="header",
     *    name="token",
     *    type="http",
     *    scheme="bearer",
     *    bearerFormat="JWT",
     * ),
     * @OA\Get(
     *      path="/api/admin/applications",
     *      tags={"Admin / Applications"},
     *       security={{"token":{}}}
     *      summary="Get list of Form Applications",
     *      operationId="admin.applications.index",
     *      @OA\Parameter(
     *          name="type",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(ref="#/components/parameters/start_date"),
     *      @OA\Parameter(ref="#/components/parameters/end_date"),
     *      @OA\Response(response=200, ref="#/components/responses/success_list"),
     *      @OA\Response(response=500, ref="#/components/responses/error_500")
     *  )
     */
    public function index(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $limit = $request->input('limit', 15);

            $query = Application::query();
            if ($startDate) {
                $query->whereRaw('DATE(created_at) >= ?', [$startDate]);
            }

            if ($endDate) {
                $query->whereRaw('DATE(created_at) <= ?', [$endDate]);
            }

            $applications = $query->paginate($limit);
            $applications->load('files');
            // Modify the file path to full URL
            $applications->transform(function ($application) {
                $application->files->transform(function ($file) {
                    $file->full_url = route('private-files', $file->path);
                    return $file;
                });
                return $application;
            });
            return response()->json($applications);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($application)
    {
        try {

            if (isset($application['error'])) {
                return response()->json($application, 404);
            }

            $application->load('files');
                $application->files->transform(function ($file) {
                    $file->full_url = route('private-files', $file->path);
                    return $file;
                });
           

            return response()->json($application);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
