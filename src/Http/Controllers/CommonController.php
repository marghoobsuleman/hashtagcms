<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers;

use MarghoobSuleman\HashtagCms\Models\Comment;
use MarghoobSuleman\HashtagCms\Models\Contact;
use MarghoobSuleman\HashtagCms\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommonController extends FrontendBaseController
{

    public function __construct(Request $request)
    {

        parent::__construct($request);

    }

    /**
     *
     * Render page (@override)
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        //return app()->HashtagCmsInfoLoader->getInfoKeeper();
        abort(404);

    }


    /**
     * Save contacts
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function contact(Request $request) {


       $rules = ["name" => "required|max:255|string",
            "email" => "required|max:255|email",
            "phone" => "nullable|max:16|string",
            "comment" => "required|string"];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            if($request->ajax()) {
                $msg = array("success"=>false, "message"=>$validator->getMessageBag()->toArray());
                return response()->json($msg, 400);
            } else {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

        }

        $data = $request->all();
        $data['site_id'] = $data['site_id'] ?? htcms_get_site_id();

        $pattern = '/(script.*?(?:\/|&#47;|&#x0002F;)script)/ius';
        $data['comment'] = sanitize($data['comment']);

        Contact::create($data);

        if($request->ajax()) {
            $msg = array("success"=>true, "message"=>"Information have been saved successfully.");
            return response()->json($msg, 200);
        }

        return redirect()->back()->with("success", "Information have been saved successfully.");

    }


    /**
     * Save subscriber
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function subscribe(Request $request) {

        $rules = ["email" => "required|max:255|email"];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            if($request->ajax()) {
                $msg = array("success"=>false, "message"=>$validator->getMessageBag()->toArray());
                return response()->json($msg, 400);
            } else {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

        }

        $data = $request->all();
        $data['site_id'] = $data['site_id'] ?? htcms_get_site_id();

        if(Subscriber::where('email', $data['email'])->where("site_id", htcms_get_site_id())->count()==0) {
            Subscriber::create($data);
            $message = "Thank you.";
        } else {
            $message = "You are already subscribed with us.";
        }

        if($request->ajax()) {
            $msg = array("success"=>true, "message"=>$message);
            return response()->json($msg, 200);
        }

        return redirect()->back()->with("success", $message);
    }


    /**
     * just for testing
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function test() {
        $isError = request()->get("isError");
        //return redirect("/")->with('__hashtagcms_message__', array('message'=>'This is coming from common/test', 'type'=>'success'));
        if($isError) {
            return redirect("/")->with('__hashtagcms_message_error__', 'This is coming from common/test');
        }
        return redirect("/")->with('__hashtagcms_message__', 'This is coming from common/test');
        //
    }
}
