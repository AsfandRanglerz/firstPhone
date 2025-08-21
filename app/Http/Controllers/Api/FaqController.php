<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        try {
            $faqs = Faq::select('id', 'question', 'description', 'position')
                ->orderBy('position', 'asc')
                ->get();
            if ($faqs->isEmpty()) {
                return response()->json(['message' => 'No FAQs found'], 404);
            }
            return ResponseHelper::success($faqs, 'FAQs retrieved successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to retrieve FAQs: ' . $e->getMessage());
        }
    }
}
