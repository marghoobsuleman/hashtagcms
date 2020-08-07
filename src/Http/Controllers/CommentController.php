<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers;

use MarghoobSuleman\HashtagCms\Core\ModuleLoader;
use MarghoobSuleman\HashtagCms\Models\Comment;
use MarghoobSuleman\HashtagCms\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends FrontendBaseController
{

    /**
     *
     * Render page (@override)
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {

       return  redirect()->intended("/");

    }

    /**
     * Save comments
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function saveComment(Request $request) {

        $rules = ["name" => "required|max:255|string",
            "email" => "required|max:255|email",
            "comment" => "required|string"];

        $validator = Validator::make($request->all(), $rules);

        //If error
        if ($validator->fails()) {

            if($request->ajax()) {
                $msg = array("success"=>false, "errors"=>$validator->getMessageBag()->toArray());
                return response()->json($msg, 400);
            } else {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

        }

        $data = $request->all();
        $data['site_id'] = $data['site_id'] ?? htcms_get_site_id();

        $user = auth()->user();
        if($user != null) {
            $data['user_id'] = $user->id;
        }

        $message = "Comment has been posted.";
        $success = true;
        try {
            Comment::create($data);
        }  catch (\Exception $exception) {
            $message = $exception->getMessage();
            $success = false;
        }

        $count = Comment::count();

        $rData = array("success"=>$success, "message"=>$message, "data"=>$data, "count"=>$count);

        if($request->ajax()) {
            return response()->json($rData, 200);
        }

        return redirect()->back()->with("success", $rData);
    }

    /**
     * Fetch comments
     * @param Request $request
     * @param int $category_id
     * @param int $page_id
     * @return array
     */
    public function getComments(Request $request):array {

        $data = $request->all();

        $category_id = $data["category_id"] ?? 0;
        $page_id = $data["page_id"] ?? 0;
        $order = $data["order"] ?? "desc";

        $comment = new Comment;

        if ($category_id > 0) {
            $comment = $comment->where("category_id", "=", $category_id);
        }

        if($page_id > 0) {
            $comment = $comment->where("page_id", "=", $page_id);
        }

        $data = [];
        if($page_id >0 || $page_id>0) {
            $comment = $comment->orderBy("id", $order);
            $data = $comment->get()->toArray();
        }

        return $data;

    }
}
