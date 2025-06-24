<?php
namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChecklistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $checklists = auth()->user()->checklists;

        return response()->json([
            'statusCode' => 200,
            'message' => 'Success',
            'data' => $checklists->map(function ($checklist) {
                return [
                    'id' => $checklist->id,
                    'name' => $checklist->name,
                    'items' => null,
                    'checklistCompletionStatus' => false
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $checklist = Checklist::create([
            'name' => $request->name,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'statusCode' => 201,
            'message' => 'Checklist created successfully',
            'data' => [
                'id' => $checklist->id,
                'name' => $checklist->name,
                'items' => null,
                'checklistCompletionStatus' => false
            ]
        ], 201);
    }

    public function destroy($id)
    {
        $checklist = Checklist::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$checklist) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Checklist not found'
            ], 404);
        }

        $checklist->delete();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Checklist deleted successfully'
        ]);
    }
}