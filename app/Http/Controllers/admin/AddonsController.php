<?php



namespace App\Http\Controllers\admin;



use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Addons;

use App\Category;

use App\Cart;

use App\Item;

use Validator;

class AddonsController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        $getcategory = Category::where('is_available','1')->get();

        $getaddons = Addons::select('addons.*')->with('category')->with('item')->join('categories','addons.cat_id','=','categories.id')->where('categories.is_available','1')->where('addons.is_deleted','2')->get();

        return view('addons',compact('getcategory','getaddons'));

    }



    public function getitem(Request $request)

    {

        $getitem = Item::select('id','item_name')->where('cat_id',$request->cat_id)->where('item_status','1')->get();

        return json_encode($getitem);

    }



    public function list()

    {

        $getaddons = Addons::select('addons.*')->join('categories','addons.cat_id','=','categories.id')->where('categories.is_available','1')->where('addons.is_deleted','2')->get();

        return view('theme.addonstable',compact('getaddons'));

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        //

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $s

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        $validation = Validator::make($request->all(),[

          'name' => 'required',

          'cat_id' => 'required',

          'item_id' => 'required',

          'type' => 'required',

        ]);

        $error_array = array();

        $success_output = '';

        if ($validation->fails())

        {

            foreach($validation->messages()->getMessages() as $field_name => $messages)

            {

                $error_array[] = $messages;

            }

        }

        else

        {

            if ($request->type == "free") {

                $price = "0";

            } else {

                $price = $request->price;

            }

            $addons = new Addons;

            $addons->cat_id =$request->cat_id;

            $addons->item_id =$request->item_id;

            $addons->name =$request->name;

            $addons->price =$price;

            $addons->save();

            $success_output = 'Addons Added Successfully!';

        }

        $output = array(

            'error'     =>  $error_array,

            'success'   =>  $success_output

        );

        echo json_encode($output);

    }



    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show(Request $request)

    {

        $addons = Addons::findorFail($request->id);

        $getitem = Item::select('id','item_name')->where('cat_id',$addons->cat_id)->get();

        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'addons fetch successfully', 'ResponseData' => $addons,'item'=>$getitem], 200);

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit(Request $req)

    {

        //

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request)

    {



        $validation = Validator::make($request->all(),[

            'name' => 'required',

            'cat_id' => 'required',

            'item_id' => 'required',

            'type' => 'required',

        ]);



        $error_array = array();

        $success_output = '';

        if ($validation->fails())

        {

            foreach($validation->messages()->getMessages() as $field_name => $messages)

            {

                $error_array[] = $messages;

            }

        }

        else

        {

            $UpdateCart = Cart::where('addons_id', 'LIKE', '%' . $request->id . '%')

                            ->delete();

            $addons = new Addons;

            $addons->exists = true;

            $addons->id = $request->id;



            if ($request->type == "free") {

                $price = "0";

            } else {

                $price = $request->price;

            }

            $addons->cat_id =$request->cat_id;

            $addons->item_id =$request->item_id;

            $addons->name =$request->name;

            $addons->price =$price;

            $addons->save();           



            $success_output = 'Addons updated Successfully!';

        }

        $output = array(

            'error'     =>  $error_array,

            'success'   =>  $success_output

        );

        echo json_encode($output);

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy(Request $request)

    {

        $addons=Addons::where('id', $request->id)->delete();

        if ($addons) {

            return 1;

        } else {

            return 0;

        }

    }



    public function status(Request $request)

    {

        

        $category = Addons::where('id', $request->id)->update( array('is_available'=>$request->status) );



        $UpdateCart = Cart::where('addons_id', 'LIKE', '%' . $request->id . '%')->update( array('is_available'=>$request->status) );

        if ($category) {

            return 1;

        } else {

            return 0;

        }

    }



    public function delete(Request $request)

    {

        $UpdateDetails = Addons::where('id', $request->id)

                    ->update(['is_deleted' => '1']);

        $UpdateCart = Cart::where('addons_id', 'LIKE', '%' . $request->id . '%')

                            ->delete();

        if ($UpdateDetails) {

            return 1;

        } else {

            return 0;

        }

    }

}

