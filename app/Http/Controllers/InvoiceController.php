<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Dompdf\Dompdf;

class InvoiceController extends Controller
{
    public function add(Request $request)
    {
        $invoice_no = Invoice::getNextInvoiceNumber();
        return view('Invoice.add',compact('invoice_no'));

    }
    public function store(Request $request)
    {
        $status = '0';

        if($request->input('gst_inculsive')=='on')
        {
            $status = '1';
        }
        else{
            $status = '0';
        }

        $invoice = new Invoice();
        $invoice->invoice_no = Invoice::getNextInvoiceNumber();
        $invoice->invoice_date= $request->input('date');
        $invoice->customer_name=$request->input('customer_name');
        $invoice->customer_mo= $request->input('customer_no');
        $invoice->subtotal = $request->input('total_amount');
        $invoice->gst = $request->input('gst');
        $invoice->gst_amount =$request->input('gst_total_amount');
        $invoice->grand_total = $request->input('sub_total_amount');
        $invoice->gst_inculsive = $status;
        $invoice->save();

        foreach ($request->item_description as $key => $itemDescription) {
            $invoice1 = new InvoiceItems();
            $invoice1->invoice_id = $invoice->id;
            $invoice1->item_description = $itemDescription;
            $invoice1->price = $request->price[$key];
            $invoice1->qty = $request->qty[$key];
            $invoice1->total = $request->price[$key] * $request->qty[$key];
            $invoice1->save();
        }
        $status = true;
        return redirect()->back()->with(['message' => $status ? 'Add Successfully' : 'Failed to Add']);

    }

    public function index(Request $request){
        if($request->ajax()){


            $data = Invoice::with('invoicesitems')->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('invoicesitems', function($data) {
                    $items = '';
                    foreach ($data->invoicesitems as $invoiceItem) {
                        $items .= $invoiceItem->item_description . ' - Qty: ' . $invoiceItem->qty . ' - Price: ' . $invoiceItem->price . ' - Total: ' . $invoiceItem->total . '<br>';
                    }
                    return $items;
                })
                ->addColumn('action', function($data){
                    $return = '<div class="btn-group">';




                        $return .= '<a href="'.route('invoice.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                        <i class="fa fa-pencil"></i>
                                    </a> &nbsp;';

                        $return .= '<a href="'.route('invoice.pdf', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                    <i class="fa  fa-download"></i>
                                </a> &nbsp;';

                        $return .= '  <a class="btn btn-default btn-xs" href="javascript:;" onclick="change_status(this);"  data-id="' . base64_encode($data->id) . '"><i class="fa fa-trash"></i></a>';


                    $return .= '</div>';

                    return $return;
                })




                ->rawColumns(['action','invoicesitems'])
                ->make(true);
        }
        return view('invoice.list');
    }

    public function generatePDF(Request $request,$id)
    {
        $id = base64_decode($request->id);

        $data = Invoice::with('invoicesitems')->where('id',$id)->first();

        $invoiceNumber = $data->invoice_no;
        $invoiceDate = $data->invoice_date;
        $customerName = $data->customer_name;
        $customerNo = $data->customer_mo;
        $invoiceItems = $data->invoicesitems;
        $subtotal = $data->subtotal;
        $gst = $data->gst;
        $gst_amount = $data->gst_amount;
        $grand_total = $data->grand_total;


        $html = view('invoice.template', compact('invoiceNumber', 'invoiceDate', 'customerName', 'customerNo', 'invoiceItems', 'subtotal', 'gst', 'gst_amount','grand_total'))->render();
        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->render();
        return $pdf->stream('invoice.pdf');
    }
    public function change_status(Request $request){
            if (!$request->ajax()) { exit('No direct script access allowed'); }

            $id = base64_decode($request->id);
            $data = Giftcard::where(['id' => $id])->first();

            if(!empty($data)){
                $update = Giftcard::where(['id' => $id])->update(['status' => $request->status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                if($update){
                    return response()->json(['code' => 200]);
                }else{
                    return response()->json(['code' => 201]);
                }
            }else{
                return response()->json(['code' => 201]);
            }

        }


        public function destory(Request $request){
            if (!$request->ajax()) {
                return response()->json(['message' => 'No direct script access allowed','status'=>403]);
            }

            try {
                $id = base64_decode($request->id);
                $invoice = Invoice::findOrFail($id);
                $invoice->delete();

                return response()->json(['message' => 'Invoice and its items deleted successfully','status'=>200] );
            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to delete invoice','status'=>500]);
            }

        }

        public function edit(Request $request,$id)
        {
            $id = base64_decode($request->id);
            $data = Invoice::with('invoicesitems')->where('id',$id)->first();
            return view('Invoice.edit',compact('data'));
        }

        public function update(Request $request,$id)
        {
            $status = '0';

            if($request->input('gst_inculsive')=='on')
            {
                $status = '1';
            }
            else{
                $status = '0';
            }



            $invoice = Invoice::where('id',$id)->first();

            $invoice->invoice_date= $request->input('date');
            $invoice->customer_name=$request->input('customer_name');
            $invoice->customer_mo= $request->input('customer_no');
            $invoice->subtotal = $request->input('total_amount');
            $invoice->gst = $request->input('gst');
            $invoice->gst_amount =$request->input('gst_total_amount');
            $invoice->grand_total = $request->input('sub_total_amount');
            $invoice->gst_inculsive = $status;
            $invoice->save();

            $invoice->invoicesitems()->delete();
            foreach ($request->item_description as $key => $itemDescription) {
                $invoice1 = new InvoiceItems();
                $invoice1->invoice_id = $invoice->id;
                $invoice1->item_description = $itemDescription;
                $invoice1->price = $request->price[$key];
                $invoice1->qty = $request->qty[$key];
                $invoice1->total = $request->price[$key] * $request->qty[$key];
                $invoice1->save();
            }
            $status = true;
            return redirect()->route('invoice.list')->with(['message' => $status ? 'Edited Successfully' : 'Failed to Edit']);


        }







    public function custom_validation(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'customer_name' => 'required|string',
            'customer_no' => 'required|numeric|min:1|max:9999999999',

            'gst' => 'required|numeric|min:0',
            'item_description.*' => 'required|string',
            'price.*' => 'required|numeric|min:0',
            'qty.*' => 'required|numeric|min:1',
        ], [
            'date.required' => 'Invoice date is required.',
            'date.date' => 'Invoice date must be a valid date.',
            'customer_name.required' => 'Customer name is required.',
            'customer_name.string' => 'Customer name must be a string.',
            'customer_no.required' => 'Customer number is required.',
            'customer_no.numeric' => 'Customer number must be numeric.',
            'customer_no.min' => 'Customer number must be at least :min digits.',
            'customer_no.max' => 'Customer number must not exceed :max digits.',
            'gst.required' => 'GST is required.',
            'gst.numeric' => 'GST must be numeric.',
            'gst.min' => 'GST must be at least :min.',
            'item_description.*.required' => 'Item description is required.',
            'item_description.*.string' => 'Item description must be a string.',
            'price.*.required' => 'Price is required for all items.',
            'price.*.numeric' => 'Price must be numeric.',
            'price.*.min' => 'Price must be at least :min for all items.',
            'qty.*.required' => 'Quantity is required for all items.',
            'qty.*.numeric' => 'Quantity must be numeric for all items.',
            'qty.*.min' => 'Quantity must be at least :min for all items.',
        ]);




        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        return response()->json(['message' => 'Validation passed!']);

    }
}
