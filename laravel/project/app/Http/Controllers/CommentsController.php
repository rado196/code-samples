<?php

namespace App\Http\Controllers;

use App\Models\InstructorRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
  /**
   * GET: /api/comments
   */
  public function getComments(Request $request)
  {
    $comments = InstructorRating::query()->get();

    return response()->json([
      'status' => 'success',
      'comments' => $comments,
    ]);
  }

  /**
   * GET: /api/comments/{id}
   */
  public function getComment(Request $request, $id)
  {
    $comment = InstructorRating::find($id);

    return response()->json([
      'status' => 'success',
      'comment' => $comment,
    ]);
  }

  /**
   * POST: /api/comments
   */
  public function make(Request $request)
  {
    $instructorId = $request->post('instructor_id');
    $studentId = Auth::id();
    $rating = $request->post('rating');
    $comment = $request->post('comment');

    InstructorRating::create([
      'instructor_id' => $instructorId,
      'student_id' => $studentId,
      'rating' => $rating,
      'comment' => $comment,
    ]);

    return response()->json([
      'status' => 'success',
    ]);
  }
}
