<?php
namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\ChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChecklistItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index($checklistId)
    {
        $checklist = Checklist::where('id', $checklistId)
            ->where('user_id', auth()->id())
            ->with('items')
            ->first();

        if (!$checklist) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Checklist not found'
            ], 404);
        }

        return response()->json([
            'statusCode' => 200,
            'message' => 'Success',
            'data' => $checklist->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->item_name,
                    'itemCompletionStatus' => $item->is_completed
                ];
            })
        ]);
    }

    public function store(Request $request, $checklistId)
    {
        $checklist = Checklist::where('id', $checklistId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$checklist) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Checklist not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'itemName' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $item = ChecklistItem::create([
            'item_name' => $request->itemName,
            'checklist_id' => $checklistId,
        ]);

        return response()->json([
            'statusCode' => 201,
            'message' => 'Item created successfully',
            'data' => [
                'id' => $item->id,
                'name' => $item->item_name,
                'itemCompletionStatus' => $item->is_completed
            ]
        ], 201);
    }

    public function show($checklistId, $itemId)
    {
        $checklist = Checklist::where('id', $checklistId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$checklist) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Checklist not found'
            ], 404);
        }

        $item = ChecklistItem::where('id', $itemId)
            ->where('checklist_id', $checklistId)
            ->first();

        if (!$item) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Item not found'
            ], 404);
        }

        return response()->json([
            'statusCode' => 200,
            'message' => 'Success',
            'data' => [
                'id' => $item->id,
                'name' => $item->item_name,
                'itemCompletionStatus' => $item->is_completed
            ]
        ]);
    }

    public function updateStatus($checklistId, $itemId)
    {
        $checklist = Checklist::where('id', $checklistId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$checklist) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Checklist not found'
            ], 404);
        }

        $item = ChecklistItem::where('id', $itemId)
            ->where('checklist_id', $checklistId)
            ->first();

        if (!$item) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Item not found'
            ], 404);
        }

        $item->is_completed = !$item->is_completed;
        $item->save();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Item status updated successfully',
            'data' => [
                'id' => $item->id,
                'name' => $item->item_name,
                'itemCompletionStatus' => $item->is_completed
            ]
        ]);
    }

    public function rename(Request $request, $checklistId, $itemId)
    {
        $checklist = Checklist::where('id', $checklistId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$checklist) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Checklist not found'
            ], 404);
        }

        $item = ChecklistItem::where('id', $itemId)
            ->where('checklist_id', $checklistId)
            ->first();

        if (!$item) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Item not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'itemName' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $item->item_name = $request->itemName;
        $item->save();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Item renamed successfully',
            'data' => [
                'id' => $item->id,
                'name' => $item->item_name,
                'itemCompletionStatus' => $item->is_completed
            ]
        ]);
    }

    public function destroy($checklistId, $itemId)
    {
        $checklist = Checklist::where('id', $checklistId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$checklist) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Checklist not found'
            ], 404);
        }

        $item = ChecklistItem::where('id', $itemId)
            ->where('checklist_id', $checklistId)
            ->first();

        if (!$item) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Item not found'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Item deleted successfully'
        ]);
    }
}